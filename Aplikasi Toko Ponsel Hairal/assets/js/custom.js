// File: assets/js/custom.js
// Fungsi: Menandai baris dengan stok = 1 (baris warna kuning)

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('table.table').forEach(table => {
    let stokIndex = -1;

    // Cari index kolom 'Stok' (tepat)
    table.querySelectorAll('thead th').forEach((th, i) => {
      if (th.textContent.trim().toLowerCase() === 'stok') {
        stokIndex = i;
      }
    });

    if (stokIndex === -1) return;

    // Tandai baris dengan stok 1
    table.querySelectorAll('tbody tr').forEach(tr => {
      const cell = tr.children[stokIndex];
      if (cell && cell.textContent.trim() === '1') {
        tr.classList.add('table-warning');
      }
    });
  });
});
