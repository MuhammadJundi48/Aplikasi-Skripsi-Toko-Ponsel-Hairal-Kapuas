<?php
/* ==========================================================
   Modul : Biodata Staf  (dengan combobox jenis_kelamin)
   Tabel : tb_biodata_staf
   Akses : admin
   ========================================================== */
$pageTitle    = 'Biodata Staf';
$tableName    = 'tb_biodata_staf';
$allowedRoles = ['admin'];
$viewOnly     = false;
$printable    = true;

/* ← tidak perlu $readOnly di sini */
require_once '../../inc/page_template.php';
?>

<!-- ========== JS untuk mengganti input jenis_kelamin → <select>
                     dan input tanggal_lahir → type="date" ========== -->
<script>
document.addEventListener('DOMContentLoaded', () => {

  /* ---- ganti semua input[name=jenis_kelamin] menjadi <select> ---- */
  function replaceGenderInputs() {
    document.querySelectorAll('input[name="jenis_kelamin"]').forEach(input => {
      const current = input.value.trim();
      const select  = document.createElement('select');
      select.name   = 'jenis_kelamin';
      select.required = true;
      select.className = 'form-select';

      ['Laki-laki','Perempuan'].forEach(opt => {
        const option = document.createElement('option');
        option.value = opt;
        option.text  = opt;
        if (opt === current) option.selected = true;
        select.appendChild(option);
      });

      input.parentNode.replaceChild(select, input);
    });
  }

  /* ---- ubah input[name=tanggal_lahir] menjadi type="date" -------- */
  function fixDateInputs() {
    document.querySelectorAll('input[name="tanggal_lahir"]').forEach(inp => {
      inp.type = 'date';                 // gantikan type=text → date
      if (inp.value && inp.value.length === 10) return;        // sudah YYYY-MM-DD
      // jika value datang dalam format lain (mis. DD/MM/YYYY), kosongkan saja
      if (inp.value) inp.value = '';
    });
  }

  /* panggil saat DOM siap */
  replaceGenderInputs();
  fixDateInputs();

  /* panggil lagi tiap modal Add / Edit ditampilkan */
  document.querySelectorAll('#addModal, [id^="e"]').forEach(m => {
    m.addEventListener('shown.bs.modal', () => {
      replaceGenderInputs();
      fixDateInputs();
    });
  });
});
</script>