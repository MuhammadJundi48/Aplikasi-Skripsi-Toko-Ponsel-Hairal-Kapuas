<?php
/* -------------------------------------------------------------
   Grafik Keuangan per Bulan
   Folder : /pages/grafik/   File : keuangan.php
-------------------------------------------------------------- */

require_once '../../inc/db.php';      // koneksi PDO → $pdo
require_once '../../inc/auth.php';    // proteksi login/role
checkRole(['admin','manager','marketing']);

$pageTitle = 'Grafik Keuangan';
require_once '../../inc/header.php';

/* ---------- 1) Tentukan kolom tanggal ------------ */
$candidates = ['tanggal', 'tgl', 'created_at'];
$dateCol    = null;

foreach ($candidates as $col) {
  $st = $pdo->query("SHOW COLUMNS FROM tb_laporan_keuangan LIKE '$col'");
  if ($st && $st->rowCount()) {
    $dateCol = $col;
    break;
  }
}

if (!$dateCol) {
  echo '<div class="alert alert-danger">
          Tidak ditemukan kolom tanggal di <b>tb_laporan_keuangan</b>.<br>
          Tambahkan nama kolom ke array <code>$candidates</code>.
        </div>';
  require_once '../../inc/footer.php'; exit;
}

/* ---------- 2) Ambil total pendapatan per bulan ------------ */
$sql = "
    SELECT DATE_FORMAT($dateCol,'%Y-%m') AS bulan,
           SUM(pendapatan_kotor)        AS total_kotor,
           SUM(pendapatan_bersih)       AS total_bersih,
           SUM(pengeluaran)             AS total_pengeluaran
    FROM   tb_laporan_keuangan
    GROUP  BY bulan
    ORDER  BY bulan
";

$q = $pdo->query($sql);

$labels = $dataKotor = $dataBersih = $dataPengeluaran = [];
foreach ($q as $row) {
  $labels[]         = $row['bulan'];
  $dataKotor[]      = (int)$row['total_kotor'];
  $dataBersih[]     = (int)$row['total_bersih'];
  $dataPengeluaran[]= (int)$row['total_pengeluaran'];
}
?>
<h3 class="mb-4">Grafik Keuangan per Bulan</h3>
<canvas id="chartKeuangan" height="80"></canvas>

<script>
const ctx = document.getElementById('chartKeuangan');
new Chart(ctx, {
  type: 'bar',
  data: {
    labels: <?= json_encode($labels) ?>,
    datasets: [
      {
        label: 'Pendapatan Kotor',
        data: <?= json_encode($dataKotor) ?>,
        backgroundColor: 'rgba(220,53,69,0.6)', // merah Bootstrap
        borderColor: 'rgba(220,53,69,1)',
        borderWidth: 1
      },
      {
        label: 'Pengeluaran',
        data: <?= json_encode($dataPengeluaran) ?>,
        backgroundColor: 'rgba(111,66,193,0.6)', // ungu
        borderColor: 'rgba(111,66,193,1)',
        borderWidth: 1
      },
      {
        label: 'Pendapatan Bersih',
        data: <?= json_encode($dataBersih) ?>,
        backgroundColor: 'rgba(255,193,7,0.6)', // oranye Bootstrap (warning)
        borderColor: 'rgba(255,193,7,1)',
        borderWidth: 1
      }
    ]
  },
  options: {
    responsive: true,
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          callback: function(value) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
          }
        }
      }
    },
    plugins: {
      tooltip: {
        callbacks: {
          label: function(context) {
            return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
          }
        }
      }
    }
  }
});
</script>

<?php require_once '../../inc/footer.php'; ?>/inc/footer.php'; ?>