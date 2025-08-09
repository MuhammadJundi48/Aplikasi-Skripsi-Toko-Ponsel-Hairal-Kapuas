<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
redirectIfNotLoggedIn();
checkRole($allowedRoles ?? []);

$currentMonthYear = $GLOBALS['customVars']['currentMonthYear'] ?? date('Y-m');

$inputTypes  = $GLOBALS['inputTypes'] ?? [];
$customInput = $GLOBALS['customInput'] ?? [];

if (!isset($viewOnly)) {
  $viewOnly = ($disableAdd ?? false) && ($disableDelete ?? false);
}

$printable      = $printable     ?? false;
$hideFields     = $hideFields    ?? [];
$readOnly       = $readOnly      ?? [];
$noInputFields  = $noInputFields ?? [];
$disableSearch  = $disableSearch ?? false;
$filterParams   = $filterParams  ?? [];

function formatField($field, $value) {
  $rupiahFields = [
    'harga_modal',
    'harga_terjual',
    'gaji',
    'pendapatan_kotor',
    'pengeluaran',
    'pendapatan_bersih',
  ];

  if (in_array($field, $rupiahFields)) {
    return number_format((float)$value, 0, ',', '.');
  }

  return htmlspecialchars($value);
}

function labelize(string $col): string {
  return ucwords(str_replace('_', ' ', $col));
}

function getFilterValues() {
  $result = [];
  foreach ($GLOBALS['filterParams'] as $f) {
    $result[$f['name']] = $_GET[$f['name']] ?? '';
  }
  return $result;
}

function renderFilterAndSearch($search) {
  global $filterParams;
  echo '<form class="row g-2 mb-3 align-items-end justify-content-between" method="get">';
  echo '<div class="col-auto">';
  echo '<label class="form-label">Cari</label>';
  echo '<input class="form-control" name="search" value="'.htmlspecialchars($search).'" placeholder="Cari…">';
  echo '</div>';
  echo '<div class="col d-flex justify-content-end flex-wrap gap-2">';
  foreach ($filterParams as $f) {
    $val = $_GET[$f['name']] ?? '';
    echo '<div>';
    echo '<label class="form-label">'.htmlspecialchars($f['label']).'</label>';
    if ($f['type'] === 'select') {
      echo '<select class="form-select" name="'.htmlspecialchars($f['name']).'">';
      echo '<option value="">-- '.htmlspecialchars($f['label']).' --</option>';
      foreach ($f['options'] as $k => $label) {
        $sel = ($k === $val) ? 'selected' : '';
        echo "<option value=\"".htmlspecialchars($k)."\" $sel>".htmlspecialchars($label)."</option>";
      }
      echo '</select>';
    } elseif ($f['type'] === 'custom' && isset($f['html'])) {
      echo str_replace('{value}', htmlspecialchars($val), $f['html']);
    } else {
      echo '<input class="form-control" name="'.htmlspecialchars($f['name']).'" value="'.htmlspecialchars($val).'">';
    }
    echo '</div>';
  }
  echo '<div class="d-flex align-items-end">';
  echo '<button class="btn btn-primary"><i class="bi bi-search"></i> Tampilkan</button>';
  echo '</div>';
  echo '</div>';
  echo '</form>';
}

// ===== Metadata tabel =====
$cols      = $pdo->query("SHOW COLUMNS FROM `$tableName`")->fetchAll(PDO::FETCH_ASSOC);
$pk        = array_column(array_filter($cols, fn($c)=>$c['Extra']==='auto_increment'),'Field')[0] ?? $cols[0]['Field'];
$colFields = array_column($cols,'Field');

// Integrasi custom fields
$customFields = $GLOBALS['customFields'] ?? [];
$customSelect = $GLOBALS['customSelect'] ?? '';
$customJoin   = $GLOBALS['customJoin'] ?? '';
$colFields = array_merge($colFields, array_keys($customFields));

// ===== CRUD =====
if (!$viewOnly) {
  if (isset($_POST['add'])) {
    $skip   = array_merge([$pk], $readOnly, $noInputFields, $hideFields);
    $colsIn = array_values(array_diff($colFields, $skip));
    if ($colsIn) {
      $place = implode(',', array_map(fn($c)=>":$c", $colsIn));
      $sql   = "INSERT INTO `$tableName` (".implode(',', $colsIn).") VALUES ($place)";
      $pdo->prepare($sql)->execute(array_intersect_key($_POST, array_flip($colsIn)));
    }
    header('Location: '.$_SERVER['PHP_SELF']); exit;
  }

  if (isset($_POST['update'])) {
    $skip   = array_merge([$pk], $readOnly, $noInputFields, $hideFields);
    $colsUp = array_values(array_diff($colFields, $skip));
    if ($colsUp) {
      $place = implode(',', array_map(fn($c) => "$c = :$c", $colsUp));
      $sql   = "UPDATE `$tableName` SET $place WHERE $pk = :idpk";
      $data = array_intersect_key($_POST, array_flip($colsUp));
      $data['idpk'] = $_POST['id'];
      $pdo->prepare($sql)->execute($data);
    }
    header('Location: '.$_SERVER['PHP_SELF']); exit;
  }

  if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM `$tableName` WHERE $pk=?")->execute([$_GET['delete']]);
    header('Location: '.$_SERVER['PHP_SELF']); exit;
  }
}

require_once __DIR__ . '/header.php';

// ===== Filter + Search SQL =====
$search         = $_GET['search'] ?? '';
$sort           = $_GET['sort']   ?? $pk;
$order          = (($_GET['order'] ?? 'asc') === 'desc') ? 'DESC' : 'ASC';
$collate        = 'utf8mb4_general_ci';
$filterValues   = getFilterValues();
$whereParts     = $GLOBALS['whereExtra'] ?? [];
$params         = $GLOBALS['paramsExtra'] ?? [];

// SELECT columns
$selectCols = implode(', ', array_map(function ($c) use ($tableName, $customFields) {
  if (isset($customFields[$c])) {
    return $c; // jangan prefix kolom custom
  }
  return strpos($c, '.') !== false ? $c : "`$tableName`.`$c`";
}, $colFields));

if ($customSelect) {
  $selectCols .= ', ' . $customSelect;
}

// Untuk search
$searchableCols = implode(', ', array_map(function ($c) {
  return $c;
}, $colFields));

if (!$disableSearch && $search !== '') {
  $whereParts[] = "CONCAT_WS(' ', $searchableCols) COLLATE $collate LIKE CONVERT(:s USING utf8mb4) COLLATE $collate";
  $params['s']  = '%' . $search . '%';
}

foreach ($filterParams as $f) {
  $name = $f['name'];
  $val  = $_GET[$name] ?? '';
  if ($val === '') continue;
  if (isset($f['query']) && is_callable($f['query'])) {
    $whereParts[] = $f['query']($val);
  } else {
    $whereParts[] = "`$name` = :filter_$name";
    $params["filter_$name"] = $val;
  }
}

$whereSql = $whereParts ? implode(' AND ', $whereParts) : '1';

if (isset($_GET['sort']) && isset($_GET['order'])) {
  $orderSql = "ORDER BY `$sort` $order";
} elseif (isset($GLOBALS['customOrder'])) {
  $orderSql = "ORDER BY " . $GLOBALS['customOrder'];
} else {
  $orderSql = "ORDER BY `$pk` ASC";
}

$stmt = $pdo->prepare("SELECT $selectCols FROM `$tableName` $customJoin WHERE $whereSql $orderSql");
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$qs = http_build_query(array_merge(
  ['search' => $search, 'sort' => $sort, 'order' => strtolower($order)],
  $filterValues
));
?>

<!-- UI Output tetap -->
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="m-0"><?= htmlspecialchars($pageTitle ?? 'Daftar') ?></h3>
  <?php if ($printable): ?>
    <a target="_blank" href="print.php?<?= $qs ?>" class="btn btn-outline-secondary">
      <i class="bi bi-printer"></i> Cetak
    </a>
  <?php endif; ?>
</div>

<?php if (!$disableSearch || $filterParams): ?>
  <?php renderFilterAndSearch($search); ?>
<?php endif; ?>

<?php if (!$viewOnly): ?>
  <button class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#addModal">
    <i class="bi bi-plus-circle"></i> Tambah Data
  </button>
<?php endif; ?>

<div class="table-responsive">
<table class="table table-striped table-hover">
  <thead class="table-light">
    <tr>
      <?php
$forceVisibleFields = ['id_ponsel', 'id_aksesoris', 'id_pelanggan'];
foreach ($colFields as $f):
  if (in_array($f, $hideFields) && !in_array($f, $forceVisibleFields)) continue;
?>
        <th><a href="?sort=<?= $f ?>&order=<?= $order==='ASC'?'desc':'asc' ?>&search=<?= urlencode($search) ?>"><?= $customFields[$f] ?? labelize($f) ?></a></th>
      <?php endforeach; ?>
      <?php if (!$viewOnly): ?><th style="width:110px">Aksi</th><?php endif; ?>
    </tr>
  </thead>
<tbody>
  <?php $modals = ''; foreach ($rows as $r): ?>
  <tr>
    <?php
$forceVisibleFields = ['id_ponsel', 'id_aksesoris', 'id_pelanggan'];
foreach ($colFields as $f):
  if (in_array($f, $hideFields) && !in_array($f, $forceVisibleFields)) continue;
?>
<?php if ($f === 'pengeluaran'): ?>
  <td>
    <?php
      $rowMonthYear = date('Y-m', strtotime($r['tanggal']));
      $canUpdate = ($rowMonthYear === $currentMonthYear);
    ?>
    <form method="post" style="display: flex; gap: 4px; align-items: center;">
      <input type="hidden" name="tanggal" value="<?= htmlspecialchars($r['tanggal']) ?>">
      <input type="number" name="pengeluaran" step="0.01"
             value="<?= htmlspecialchars($r[$f]) ?>"
             class="form-control form-control-sm"
             style="width:100px;" <?= $canUpdate ? '' : 'readonly' ?>>
      <?php if ($canUpdate): ?>
        <button class="btn btn-sm btn-success" type="submit" name="update">Update</button>
      <?php endif; ?>
    </form>
  </td>
      <?php else: ?>
        <td><?= formatField($f, $r[$f]) ?></td>
      <?php endif; ?>
    <?php endforeach; ?>
      <?php if (!$viewOnly): ?>
      <td>
        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#e<?= $r[$pk] ?>">Edit</button>
        <a class="btn btn-sm btn-danger" href="?delete=<?= $r[$pk] ?>" onclick="return confirm('Hapus data?')">Hapus</a>
      </td>
      <?php endif; ?>
    </tr>
    <?php ob_start(); ?>
<div class="modal fade" id="e<?= $r[$pk] ?>" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <form class="modal-content" method="post">
      <div class="modal-header">
        <h5 class="modal-title">Edit Data</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" value="<?= $r[$pk] ?>">
        <?php foreach ($colFields as $f):
          if ($f===$pk || in_array($f,$hideFields) || in_array($f,$noInputFields)) continue; ?>
          <div class="mb-2">
            <label class="form-label"><?= labelize($f) ?></label>
<?php
$type = $inputTypes[$f] ?? 'text';
$readonlyAttr = in_array($f, $readOnly) ? 'readonly' : '';
$value = htmlspecialchars($r[$f]);

if (isset($customInput[$f])) {
  echo $customInput[$f]($f, $r[$f]);
} else {
  echo "<input class='form-control' type='$type' name='$f' value='$value' $readonlyAttr required>";
}
?>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="modal-footer"><button class="btn btn-primary" name="update">Simpan</button></div>
    </form>
  </div>
</div>
<?php $modals .= ob_get_clean(); ?>
<?php endforeach; ?>
  </tbody>
</table>
</div>

<?php if (!$viewOnly): ?>
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <form class="modal-content" method="post" autocomplete="off">
      <div class="modal-header"><h5 class="modal-title">Tambah Data</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <?php foreach ($colFields as $f):
          if ($f===$pk || in_array($f,$hideFields) || in_array($f,$readOnly) || in_array($f,$noInputFields)) continue; ?>
          <div class="mb-2">
            <label class="form-label"><?= labelize($f) ?></label>
<?php
$type = $inputTypes[$f] ?? 'text';

if (isset($customInput[$f])) {
  echo $customInput[$f]($f, '');
} else {
  echo "<input class='form-control' type='$type' name='$f' required>";
}
?>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="modal-footer"><button class="btn btn-primary" name="add">Simpan</button></div>
    </form>
  </div>
</div>
<?php endif; ?>
<?= $modals ?>
<?php require_once __DIR__ . '/footer.php'; ?>