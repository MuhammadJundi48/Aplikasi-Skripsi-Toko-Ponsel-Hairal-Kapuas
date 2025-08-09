<?php
/* ==========================================================
   Modul : Data Pengguna (role sebagai combobox)
   Tabel : tb_pengguna
   Akses : admin
   CRUD  : ya
   Cetak : tidak
   ========================================================== */

$pageTitle    = 'Data Pengguna';
$tableName    = 'tb_pengguna';
$allowedRoles = ['admin'];
$viewOnly     = false;
$printable    = false;

require_once '../../inc/page_template.php';

/* ==== JavaScript: ganti input[name=role] -> <select> ==== */
?>
<script>
document.addEventListener('DOMContentLoaded', () => {

  const ROLES = ['admin','manager','marketing','kasir'];

  function upgradeRoleInputs() {
    document.querySelectorAll('input[name="role"]').forEach(inp => {
      const current = inp.value.trim().toLowerCase();
      const select  = document.createElement('select');
      select.name   = 'role';
      select.className = 'form-select';
      select.required  = true;

      ROLES.forEach(r => {
        const opt = document.createElement('option');
        opt.value = r;
        opt.text  = r;
        if (r === current) opt.selected = true;
        select.appendChild(opt);
      });

      // Replace input with select
      inp.parentNode.replaceChild(select, inp);
    });
  }

  // Jalankan saat halaman selesai dimuat
  upgradeRoleInputs();

  // Jalankan setiap kali modal Add/Edit muncul
  document.querySelectorAll('#addModal, [id^="e"]').forEach(modal => {
    modal.addEventListener('shown.bs.modal', upgradeRoleInputs);
  });
});
</script>
