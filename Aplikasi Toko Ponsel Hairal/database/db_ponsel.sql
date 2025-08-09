-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 09 Agu 2025 pada 00.55
-- Versi server: 10.4.24-MariaDB
-- Versi PHP: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_ponsel`
--

DELIMITER $$
--
-- Prosedur
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_sync_view_to_table` ()   BEGIN
  -- 1. Sinkronisasi barang terlaris per bulan
  DELETE FROM tb_barang_terlaris_per_bulan;
  INSERT INTO tb_barang_terlaris_per_bulan (id_ponsel, id_aksesoris, periode_date, periode)
  SELECT id_ponsel, id_aksesoris, periode_date, periode FROM vw_barang_terlaris;

  -- 2. Sinkronisasi barang paling tidak laku
  DELETE FROM tb_barang_paling_tidak_laku;
  INSERT INTO tb_barang_paling_tidak_laku (id_ponsel, id_aksesoris, periode_date, periode)
  SELECT id_ponsel, id_aksesoris, periode_date, periode FROM vw_barang_tidak_laku;

  -- 3. Sinkronisasi jumlah beli pelanggan
  DELETE FROM tb_pelanggan;
  INSERT INTO tb_pelanggan (id_pelanggan, nama_pelanggan, alamat, jumlah_beli_ponsel, jumlah_beli_aksesoris, total_jumlah_beli)
  SELECT id_pelanggan, nama_pelanggan, alamat, jumlah_beli_ponsel, jumlah_beli_aksesoris, total_jumlah_beli
  FROM vw_pelanggan_jumlah_beli;

  -- 4. Sinkronisasi laporan keuangan
  DELETE FROM tb_laporan_keuangan;
  INSERT INTO tb_laporan_keuangan (tanggal, pendapatan_kotor, pengeluaran, pendapatan_bersih)
  SELECT tanggal, pendapatan_kotor, pengeluaran, (pendapatan_kotor - pengeluaran) AS pendapatan_bersih
  FROM vw_laporan_keuangan;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_barang_paling_tidak_laku`
--

CREATE TABLE `tb_barang_paling_tidak_laku` (
  `id` int(11) NOT NULL,
  `id_ponsel` int(11) DEFAULT NULL,
  `id_aksesoris` int(11) DEFAULT NULL,
  `periode_date` date DEFAULT NULL,
  `periode` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `tb_barang_paling_tidak_laku`
--

INSERT INTO `tb_barang_paling_tidak_laku` (`id`, `id_ponsel`, `id_aksesoris`, `periode_date`, `periode`) VALUES
(1, 4, NULL, '2025-03-01', 'Maret 2025'),
(2, 4, NULL, '2025-07-01', 'Juli 2025'),
(3, 4, NULL, '2025-08-01', 'Agustus 2025'),
(4, 5, NULL, '2025-04-01', 'April 2025'),
(5, 6, NULL, '2025-04-01', 'April 2025'),
(6, 6, NULL, '2025-05-01', 'Mei 2025'),
(7, 6, NULL, '2025-07-01', 'Juli 2025'),
(8, 7, NULL, '2025-02-01', 'Februari 2025'),
(9, 8, NULL, '2025-02-01', 'Februari 2025'),
(10, 8, NULL, '2025-03-01', 'Maret 2025'),
(11, 8, NULL, '2025-05-01', 'Mei 2025'),
(12, 16, NULL, '2025-08-01', 'Agustus 2025'),
(13, NULL, 1, '2025-02-01', 'Februari 2025'),
(14, NULL, 2, '2025-02-01', 'Februari 2025'),
(15, NULL, 3, '2025-02-01', 'Februari 2025'),
(16, NULL, 4, '2025-02-01', 'Februari 2025'),
(17, NULL, 4, '2025-04-01', 'April 2025'),
(18, NULL, 4, '2025-06-01', 'Juni 2025'),
(19, NULL, 5, '2025-03-01', 'Maret 2025'),
(20, NULL, 5, '2025-05-01', 'Mei 2025'),
(21, NULL, 6, '2025-02-01', 'Februari 2025'),
(22, NULL, 7, '2025-03-01', 'Maret 2025'),
(23, NULL, 7, '2025-06-01', 'Juni 2025'),
(24, NULL, 8, '2025-03-01', 'Maret 2025'),
(25, NULL, 9, '2025-02-01', 'Februari 2025'),
(26, NULL, 10, '2025-02-01', 'Februari 2025'),
(27, NULL, 11, '2025-02-01', 'Februari 2025'),
(28, NULL, 12, '2025-02-01', 'Februari 2025'),
(29, NULL, 13, '2025-02-01', 'Februari 2025'),
(30, NULL, 14, '2025-02-01', 'Februari 2025'),
(31, NULL, 15, '2025-02-01', 'Februari 2025'),
(32, NULL, 16, '2025-02-01', 'Februari 2025'),
(33, NULL, 16, '2025-05-01', 'Mei 2025'),
(34, NULL, 17, '2025-02-01', 'Februari 2025'),
(35, NULL, 17, '2025-04-01', 'April 2025'),
(36, NULL, 18, '2025-03-01', 'Maret 2025'),
(37, NULL, 19, '2025-02-01', 'Februari 2025'),
(38, NULL, 20, '2025-03-01', 'Maret 2025');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_barang_terlaris_per_bulan`
--

CREATE TABLE `tb_barang_terlaris_per_bulan` (
  `id` int(11) NOT NULL,
  `id_ponsel` int(11) DEFAULT NULL,
  `id_aksesoris` int(11) DEFAULT NULL,
  `periode_date` date DEFAULT NULL,
  `periode` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `tb_barang_terlaris_per_bulan`
--

INSERT INTO `tb_barang_terlaris_per_bulan` (`id`, `id_ponsel`, `id_aksesoris`, `periode_date`, `periode`) VALUES
(1, 5, NULL, '2025-02-01', 'Februari 2025'),
(2, 6, NULL, '2025-02-01', 'Februari 2025');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_biodata_staf`
--

CREATE TABLE `tb_biodata_staf` (
  `id_staf` int(11) NOT NULL,
  `nama_staf` varchar(100) DEFAULT NULL,
  `tempat_lahir` varchar(100) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `jenis_kelamin` enum('Laki-Laki','Perempuan') DEFAULT NULL,
  `posisi` varchar(100) DEFAULT NULL,
  `gaji` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `tb_biodata_staf`
--

INSERT INTO `tb_biodata_staf` (`id_staf`, `nama_staf`, `tempat_lahir`, `tanggal_lahir`, `jenis_kelamin`, `posisi`, `gaji`) VALUES
(1, 'Suryawan', 'Banjarmasin', '1975-05-02', 'Laki-Laki', 'Pemilik', NULL),
(2, 'Muhammad Rizki', 'Kuala Kapuas', '2000-05-03', 'Laki-Laki', 'Karyawan', '50000.00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_inventaris_aksesoris`
--

CREATE TABLE `tb_inventaris_aksesoris` (
  `id_aksesoris` int(11) NOT NULL,
  `nama_aksesoris` varchar(100) NOT NULL,
  `stok` int(11) NOT NULL,
  `harga_modal` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `tb_inventaris_aksesoris`
--

INSERT INTO `tb_inventaris_aksesoris` (`id_aksesoris`, `nama_aksesoris`, `stok`, `harga_modal`) VALUES
(1, 'Anti Gores Infinix Hot 30', 50, '7000.00'),
(2, 'Case Redmi 5A', 5, '7000.00'),
(3, 'Headset Iron Bass', 20, '20000.00'),
(4, 'Kabel Data Wellcome', 20, '7000.00'),
(5, 'Powerbank Robot 10,5 MAH', 3, '150000.00'),
(6, 'Case Oppo A37', 5, '7000.00'),
(7, 'Flashdisk Robot 4 GB', 15, '28000.00'),
(8, 'Baterai Nokia BL-5C', 20, '12000.00'),
(9, 'Headset JBL', 15, '5000.00'),
(10, 'Case J2 Prime', 3, '5000.00'),
(11, 'Charge Jepit', 20, '15000.00'),
(12, 'Charge Vivo Original', 5, '90000.00'),
(13, 'Powerbank Rexi 10,5 MAH', 5, '90000.00'),
(14, 'Kabel Data Type C', 15, '7000.00'),
(15, 'Memory Robot 4GB', 20, '30000.00'),
(16, 'Charge Robot', 10, '23000.00'),
(17, 'Headset Big Bass', 15, '25000.00'),
(18, 'Charger Mobil Rexi', 10, '25000.00'),
(19, 'Case Vivo Y12', 3, '5000.00'),
(20, 'Headset King Bass', 10, '20000.00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_inventaris_ponsel`
--

CREATE TABLE `tb_inventaris_ponsel` (
  `id_ponsel` int(11) NOT NULL,
  `nama_ponsel` varchar(100) NOT NULL,
  `stok` int(11) NOT NULL,
  `harga_modal` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `tb_inventaris_ponsel`
--

INSERT INTO `tb_inventaris_ponsel` (`id_ponsel`, `nama_ponsel`, `stok`, `harga_modal`) VALUES
(1, 'Poco M3 Pro', 1, '2136000.00'),
(2, 'Oppo A18', 1, '1295000.00'),
(3, 'Samsung Galaxy A04e', 1, '1180000.00'),
(4, 'Infinix Smart 7', 5, '1170000.00'),
(5, 'Itel A70', 12, '850000.00'),
(6, 'Nokia 105', 6, '220000.00'),
(7, 'Redmi 12 C', 2, '1100000.00'),
(8, 'Redmi A2', 6, '900000.00'),
(9, 'Infinix Smart 8', 5, '1130000.00'),
(10, 'Vivo Y17 S', 2, '1300000.00'),
(11, 'Oppo A76', 1, '3000000.00'),
(12, 'Realme 9', 1, '3299000.00'),
(13, 'Realme Note 50', 1, '1250000.00'),
(14, 'Xiaomi Poco M3 Pro', 1, '2235000.00'),
(15, 'Oppo A16e', 2, '1400000.00'),
(16, 'Realme C11', 3, '1050000.00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_laporan_keuangan`
--

CREATE TABLE `tb_laporan_keuangan` (
  `id_laporan_keuangan` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `pendapatan_kotor` decimal(15,2) DEFAULT NULL,
  `pengeluaran` decimal(15,2) DEFAULT NULL,
  `pendapatan_bersih` decimal(15,2) GENERATED ALWAYS AS (`pendapatan_kotor` - `pengeluaran`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `tb_laporan_keuangan`
--

INSERT INTO `tb_laporan_keuangan` (`id_laporan_keuangan`, `tanggal`, `pendapatan_kotor`, `pengeluaran`) VALUES
(1, '2025-02-03', '270000.00', '50000.00'),
(2, '2025-02-05', '290000.00', '50000.00'),
(3, '2025-02-06', '25000.00', '40000.00'),
(4, '2025-02-07', '1300000.00', '50000.00'),
(5, '2025-02-08', '45000.00', '40000.00'),
(6, '2025-02-10', '3920000.00', '50000.00'),
(7, '2025-02-11', '2830000.00', '50000.00'),
(8, '2025-02-12', '25000.00', '40000.00'),
(9, '2025-02-15', '380000.00', '50000.00'),
(10, '2025-02-18', '1525000.00', '50000.00'),
(11, '2025-02-21', '125000.00', '50000.00'),
(12, '2025-02-24', '130000.00', '50000.00'),
(13, '2025-02-25', '150000.00', '50000.00'),
(14, '2025-02-27', '30000.00', '50000.00'),
(15, '2025-02-28', '65000.00', '50000.00'),
(16, '2025-03-02', '2350000.00', '50000.00'),
(17, '2025-03-04', '80000.00', '50000.00'),
(18, '2025-03-09', '1000000.00', '50000.00'),
(19, '2025-03-10', '40000.00', '40000.00'),
(20, '2025-03-11', '1200000.00', '50000.00'),
(21, '2025-03-12', '30000.00', '40000.00'),
(22, '2025-03-16', '20000.00', '40000.00'),
(23, '2025-03-17', '1300000.00', '50000.00'),
(24, '2025-03-31', '1300000.00', '50000.00'),
(25, '2025-04-03', '50000.00', '40000.00'),
(26, '2025-04-04', '1330000.00', '50000.00'),
(27, '2025-05-09', '1000000.00', '50000.00'),
(28, '2025-05-16', '295000.00', '50000.00'),
(29, '2025-05-26', '180000.00', '50000.00'),
(30, '2025-06-03', '140000.00', '50000.00'),
(31, '2025-06-13', '1450000.00', '50000.00'),
(32, '2025-06-16', '1325000.00', '50000.00'),
(33, '2025-07-04', '1510000.00', '50000.00'),
(34, '2025-08-02', '1200000.00', '50000.00'),
(35, '2025-08-03', '1265000.00', '50000.00');

--
-- Trigger `tb_laporan_keuangan`
--
DELIMITER $$
CREATE TRIGGER `trg_before_insert_laporan_keuangan` BEFORE INSERT ON `tb_laporan_keuangan` FOR EACH ROW BEGIN
  SET NEW.pendapatan_bersih = NEW.pendapatan_kotor - NEW.pengeluaran;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_before_update_laporan_keuangan` BEFORE UPDATE ON `tb_laporan_keuangan` FOR EACH ROW BEGIN
  SET NEW.pendapatan_bersih = NEW.pendapatan_kotor - NEW.pengeluaran;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_pelanggan`
--

CREATE TABLE `tb_pelanggan` (
  `id_pelanggan` int(11) NOT NULL,
  `nama_pelanggan` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL,
  `jumlah_beli_ponsel` int(11) DEFAULT 0,
  `jumlah_beli_aksesoris` int(11) DEFAULT 0,
  `total_jumlah_beli` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `tb_pelanggan`
--

INSERT INTO `tb_pelanggan` (`id_pelanggan`, `nama_pelanggan`, `alamat`, `jumlah_beli_ponsel`, `jumlah_beli_aksesoris`, `total_jumlah_beli`) VALUES
(1, 'Rizki', 'Kapuas', 2, 3, 5),
(2, 'Yadi', 'Kapuas', 3, 3, 6),
(3, 'Kamil', 'Kapuas', 1, 3, 4),
(4, 'Akmal', 'Kapuas', 2, 2, 4),
(5, 'Rumin', 'Tabukan', 3, 2, 5),
(6, 'Nia', 'Tabukan', 3, 4, 7),
(7, 'Amin', 'Tabukan', 3, 2, 5),
(8, 'Isur', 'Palingkau', 1, 3, 4),
(9, 'Sabrani', 'Palingkau', 4, 2, 6),
(10, 'Iyan', 'Pulau Petak', 2, 2, 4);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_pengguna`
--

CREATE TABLE `tb_pengguna` (
  `id_pengguna` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','manager','marketing','kasir') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `tb_pengguna`
--

INSERT INTO `tb_pengguna` (`id_pengguna`, `username`, `password`, `role`) VALUES
(1, 'admin', '$2a$12$EX.zQmBymVq0heJoVK3rJ.N2MYj.m3esS17H2n3Az0gnqW.iZV/Yy', 'admin'),
(2, 'manager', '$2a$12$ngOTGGg2rSt34MdEPXX6GOZyWY.g5/Zmd7qqjcmItvTNTR0H3V4B6', 'manager'),
(3, 'marketing', '$2a$12$Jaa.mv6P11LZknKkleAzI.M8G1QdtBm1JKMACB7E/TCNn76FaEIa.', 'marketing'),
(4, 'kasir', '$2a$12$.d5T.qHf5POU.5eY3DjGNONzuALNxxiFg7f4NVf7pffOPN7OWAAKC', 'kasir');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_penjualan_aksesoris`
--

CREATE TABLE `tb_penjualan_aksesoris` (
  `id_penjualan_aksesoris` int(11) NOT NULL,
  `tanggal_penjualan` date NOT NULL,
  `id_aksesoris` int(11) NOT NULL,
  `id_pelanggan` int(11) NOT NULL,
  `harga_terjual` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `tb_penjualan_aksesoris`
--

INSERT INTO `tb_penjualan_aksesoris` (`id_penjualan_aksesoris`, `tanggal_penjualan`, `id_aksesoris`, `id_pelanggan`, `harga_terjual`) VALUES
(1, '2025-02-05', 1, 3, '20000.00'),
(2, '2025-02-06', 2, 6, '25000.00'),
(3, '2025-02-08', 6, 9, '45000.00'),
(4, '2025-02-10', 10, 6, '20000.00'),
(5, '2025-02-11', 13, 2, '180000.00'),
(6, '2025-02-12', 3, 8, '25000.00'),
(7, '2025-02-15', 16, 10, '60000.00'),
(8, '2025-02-15', 14, 2, '60000.00'),
(9, '2025-02-18', 9, 5, '30000.00'),
(10, '2025-02-18', 4, 1, '25000.00'),
(11, '2025-02-21', 19, 6, '25000.00'),
(12, '2025-02-24', 17, 1, '130000.00'),
(13, '2025-02-25', 12, 7, '150000.00'),
(14, '2025-02-27', 11, 1, '30000.00'),
(15, '2025-02-28', 15, 5, '65000.00'),
(16, '2025-03-04', 18, 3, '40000.00'),
(17, '2025-03-04', 7, 9, '40000.00'),
(18, '2025-03-10', 20, 10, '40000.00'),
(19, '2025-03-12', 5, 4, '30000.00'),
(20, '2025-03-16', 8, 8, '20000.00'),
(21, '2025-04-03', 17, 8, '50000.00'),
(22, '2025-04-04', 4, 6, '20000.00'),
(23, '2025-05-16', 16, 7, '35000.00'),
(24, '2025-05-26', 5, 2, '180000.00'),
(25, '2025-06-03', 7, 3, '140000.00'),
(26, '2025-06-16', 4, 4, '25000.00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_penjualan_ponsel`
--

CREATE TABLE `tb_penjualan_ponsel` (
  `id_penjualan_ponsel` int(11) NOT NULL,
  `tanggal_penjualan` date NOT NULL,
  `id_ponsel` int(11) NOT NULL,
  `id_pelanggan` int(11) NOT NULL,
  `harga_terjual` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `tb_penjualan_ponsel`
--

INSERT INTO `tb_penjualan_ponsel` (`id_penjualan_ponsel`, `tanggal_penjualan`, `id_ponsel`, `id_pelanggan`, `harga_terjual`) VALUES
(1, '2025-02-05', 6, 2, '270000.00'),
(2, '2025-02-07', 5, 5, '1300000.00'),
(3, '2025-02-10', 5, 9, '1200000.00'),
(4, '2025-02-11', 4, 7, '1100000.00'),
(5, '2025-02-11', 7, 6, '1550000.00'),
(6, '2025-02-15', 6, 1, '260000.00'),
(7, '2025-02-18', 4, 8, '1200000.00'),
(8, '2025-02-18', 6, 5, '270000.00'),
(9, '2025-02-21', 8, 10, '100000.00'),
(10, '2025-03-09', 8, 4, '1000000.00'),
(11, '2025-03-11', 4, 7, '1200000.00'),
(12, '2025-03-17', 9, 3, '1300000.00'),
(13, '2025-03-31', 9, 4, '1300000.00'),
(14, '2025-02-10', 5, 2, '1400000.00'),
(15, '2025-02-10', 5, 7, '1300000.00'),
(16, '2025-03-02', 5, 6, '1100000.00'),
(17, '2025-03-02', 5, 9, '1250000.00'),
(18, '2025-04-04', 6, 1, '260000.00'),
(19, '2025-04-04', 5, 9, '1050000.00'),
(20, '2025-05-09', 8, 2, '1000000.00'),
(21, '2025-05-16', 6, 10, '260000.00'),
(22, '2025-06-13', 9, 5, '1450000.00'),
(23, '2025-06-16', 9, 9, '1300000.00'),
(24, '2025-02-03', 6, 6, '270000.00'),
(25, '2025-07-04', 4, 9, '1250000.00'),
(26, '2025-07-04', 6, 3, '260000.00'),
(27, '2025-08-02', 4, 6, '1200000.00'),
(28, '2025-08-03', 16, 7, '1265000.00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_supplier`
--

CREATE TABLE `tb_supplier` (
  `id_supplier` int(11) NOT NULL,
  `nama_supplier` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `tb_supplier`
--

INSERT INTO `tb_supplier` (`id_supplier`, `nama_supplier`, `alamat`) VALUES
(1, 'PT Hang Bright Electronic', 'Banjarmasin'),
(2, 'Bintang Mahameru Utama', 'Banjarmasin'),
(3, 'Sinar Jaya Komunika', 'Banjarmasin'),
(4, 'PT Max Telekom', 'Palangkaraya'),
(5, 'Synnex Metrodata Indonesia', 'Palangkaraya'),
(6, 'Suka Guna Indonesia', 'Banjarmasin'),
(7, 'Ponsel Dragon', 'Banjarmasin'),
(8, 'PT Wellcomm Indonesia', 'Banjarmasin');

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `vw_barang_terlaris`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `vw_barang_terlaris` (
`id` int(11)
,`id_ponsel` int(11)
,`id_aksesoris` int(11)
,`periode_date` varchar(10)
,`periode` varchar(14)
);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `vw_barang_tidak_laku`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `vw_barang_tidak_laku` (
`id` int(11)
,`id_ponsel` int(11)
,`id_aksesoris` int(11)
,`periode_date` varchar(10)
,`periode` varchar(14)
);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `vw_laporan_keuangan`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `vw_laporan_keuangan` (
`id_laporan_keuangan` int(11)
,`tanggal` date
,`pendapatan_kotor` decimal(59,2)
,`pengeluaran` decimal(15,2)
,`pendapatan_bersih` decimal(60,2)
);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `vw_pelanggan_jumlah_beli`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `vw_pelanggan_jumlah_beli` (
`id_pelanggan` int(11)
,`nama_pelanggan` varchar(100)
,`jumlah_beli_ponsel` bigint(21)
,`jumlah_beli_aksesoris` bigint(21)
,`total_jumlah_beli` bigint(22)
);

-- --------------------------------------------------------

--
-- Struktur untuk view `vw_barang_terlaris`
--
DROP TABLE IF EXISTS `vw_barang_terlaris`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_barang_terlaris`  AS SELECT `btpb`.`id` AS `id`, `pa`.`id_ponsel` AS `id_ponsel`, `pa`.`id_aksesoris` AS `id_aksesoris`, `pa`.`periode_date` AS `periode_date`, concat(case month(`pa`.`periode_date`) when 1 then 'Januari' when 2 then 'Februari' when 3 then 'Maret' when 4 then 'April' when 5 then 'Mei' when 6 then 'Juni' when 7 then 'Juli' when 8 then 'Agustus' when 9 then 'September' when 10 then 'Oktober' when 11 then 'November' when 12 then 'Desember' end,' ',year(`pa`.`periode_date`)) AS `periode` FROM ((select `tb_penjualan_ponsel`.`id_ponsel` AS `id_ponsel`,NULL AS `id_aksesoris`,date_format(`tb_penjualan_ponsel`.`tanggal_penjualan`,'%Y-%m-01') AS `periode_date` from `tb_penjualan_ponsel` group by `tb_penjualan_ponsel`.`id_ponsel`,year(`tb_penjualan_ponsel`.`tanggal_penjualan`),month(`tb_penjualan_ponsel`.`tanggal_penjualan`) having count(0) >= 4 union all select NULL AS `id_ponsel`,`tb_penjualan_aksesoris`.`id_aksesoris` AS `id_aksesoris`,date_format(`tb_penjualan_aksesoris`.`tanggal_penjualan`,'%Y-%m-01') AS `periode_date` from `tb_penjualan_aksesoris` group by `tb_penjualan_aksesoris`.`id_aksesoris`,year(`tb_penjualan_aksesoris`.`tanggal_penjualan`),month(`tb_penjualan_aksesoris`.`tanggal_penjualan`) having count(0) >= 4) `pa` left join `tb_barang_terlaris_per_bulan` `btpb` on(`btpb`.`id_ponsel` <=> `pa`.`id_ponsel` and `btpb`.`id_aksesoris` <=> `pa`.`id_aksesoris` and `btpb`.`periode_date` = `pa`.`periode_date`))  ;

-- --------------------------------------------------------

--
-- Struktur untuk view `vw_barang_tidak_laku`
--
DROP TABLE IF EXISTS `vw_barang_tidak_laku`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_barang_tidak_laku`  AS SELECT `bptl`.`id` AS `id`, `pa`.`id_ponsel` AS `id_ponsel`, `pa`.`id_aksesoris` AS `id_aksesoris`, `pa`.`periode_date` AS `periode_date`, concat(case month(`pa`.`periode_date`) when 1 then 'Januari' when 2 then 'Februari' when 3 then 'Maret' when 4 then 'April' when 5 then 'Mei' when 6 then 'Juni' when 7 then 'Juli' when 8 then 'Agustus' when 9 then 'September' when 10 then 'Oktober' when 11 then 'November' when 12 then 'Desember' end,' ',year(`pa`.`periode_date`)) AS `periode` FROM ((select `tb_penjualan_ponsel`.`id_ponsel` AS `id_ponsel`,NULL AS `id_aksesoris`,date_format(`tb_penjualan_ponsel`.`tanggal_penjualan`,'%Y-%m-01') AS `periode_date` from `tb_penjualan_ponsel` group by `tb_penjualan_ponsel`.`id_ponsel`,year(`tb_penjualan_ponsel`.`tanggal_penjualan`),month(`tb_penjualan_ponsel`.`tanggal_penjualan`) having count(0) = 1 union all select NULL AS `id_ponsel`,`tb_penjualan_aksesoris`.`id_aksesoris` AS `id_aksesoris`,date_format(`tb_penjualan_aksesoris`.`tanggal_penjualan`,'%Y-%m-01') AS `periode_date` from `tb_penjualan_aksesoris` group by `tb_penjualan_aksesoris`.`id_aksesoris`,year(`tb_penjualan_aksesoris`.`tanggal_penjualan`),month(`tb_penjualan_aksesoris`.`tanggal_penjualan`) having count(0) = 1) `pa` left join `tb_barang_paling_tidak_laku` `bptl` on(`bptl`.`id_ponsel` <=> `pa`.`id_ponsel` and `bptl`.`id_aksesoris` <=> `pa`.`id_aksesoris` and `bptl`.`periode_date` = `pa`.`periode_date`))  ;

-- --------------------------------------------------------

--
-- Struktur untuk view `vw_laporan_keuangan`
--
DROP TABLE IF EXISTS `vw_laporan_keuangan`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_laporan_keuangan`  AS SELECT `l`.`id_laporan_keuangan` AS `id_laporan_keuangan`, `t`.`tanggal` AS `tanggal`, `t`.`pendapatan_kotor` AS `pendapatan_kotor`, ifnull(`l`.`pengeluaran`,0) AS `pengeluaran`, `t`.`pendapatan_kotor`- ifnull(`l`.`pengeluaran`,0) AS `pendapatan_bersih` FROM ((select `semua_pendapatan`.`tanggal` AS `tanggal`,sum(`semua_pendapatan`.`pendapatan`) AS `pendapatan_kotor` from (select `tb_penjualan_ponsel`.`tanggal_penjualan` AS `tanggal`,sum(`tb_penjualan_ponsel`.`harga_terjual`) AS `pendapatan` from `tb_penjualan_ponsel` group by `tb_penjualan_ponsel`.`tanggal_penjualan` union all select `tb_penjualan_aksesoris`.`tanggal_penjualan` AS `tanggal`,sum(`tb_penjualan_aksesoris`.`harga_terjual`) AS `pendapatan` from `tb_penjualan_aksesoris` group by `tb_penjualan_aksesoris`.`tanggal_penjualan`) `semua_pendapatan` group by `semua_pendapatan`.`tanggal`) `t` left join `tb_laporan_keuangan` `l` on(`l`.`tanggal` = `t`.`tanggal`))  ;

-- --------------------------------------------------------

--
-- Struktur untuk view `vw_pelanggan_jumlah_beli`
--
DROP TABLE IF EXISTS `vw_pelanggan_jumlah_beli`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_pelanggan_jumlah_beli`  AS SELECT `p`.`id_pelanggan` AS `id_pelanggan`, `p`.`nama_pelanggan` AS `nama_pelanggan`, coalesce(count(distinct `pp`.`id_penjualan_ponsel`),0) AS `jumlah_beli_ponsel`, coalesce(count(distinct `pa`.`id_penjualan_aksesoris`),0) AS `jumlah_beli_aksesoris`, coalesce(count(distinct `pp`.`id_penjualan_ponsel`),0) + coalesce(count(distinct `pa`.`id_penjualan_aksesoris`),0) AS `total_jumlah_beli` FROM ((`tb_pelanggan` `p` left join `tb_penjualan_ponsel` `pp` on(`p`.`id_pelanggan` = `pp`.`id_pelanggan`)) left join `tb_penjualan_aksesoris` `pa` on(`p`.`id_pelanggan` = `pa`.`id_pelanggan`)) GROUP BY `p`.`id_pelanggan``id_pelanggan`  ;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `tb_barang_paling_tidak_laku`
--
ALTER TABLE `tb_barang_paling_tidak_laku`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_ponsel` (`id_ponsel`),
  ADD KEY `id_aksesoris` (`id_aksesoris`);

--
-- Indeks untuk tabel `tb_barang_terlaris_per_bulan`
--
ALTER TABLE `tb_barang_terlaris_per_bulan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_ponsel` (`id_ponsel`),
  ADD KEY `id_aksesoris` (`id_aksesoris`);

--
-- Indeks untuk tabel `tb_biodata_staf`
--
ALTER TABLE `tb_biodata_staf`
  ADD PRIMARY KEY (`id_staf`);

--
-- Indeks untuk tabel `tb_inventaris_aksesoris`
--
ALTER TABLE `tb_inventaris_aksesoris`
  ADD PRIMARY KEY (`id_aksesoris`);

--
-- Indeks untuk tabel `tb_inventaris_ponsel`
--
ALTER TABLE `tb_inventaris_ponsel`
  ADD PRIMARY KEY (`id_ponsel`);

--
-- Indeks untuk tabel `tb_laporan_keuangan`
--
ALTER TABLE `tb_laporan_keuangan`
  ADD PRIMARY KEY (`id_laporan_keuangan`);

--
-- Indeks untuk tabel `tb_pelanggan`
--
ALTER TABLE `tb_pelanggan`
  ADD PRIMARY KEY (`id_pelanggan`);

--
-- Indeks untuk tabel `tb_pengguna`
--
ALTER TABLE `tb_pengguna`
  ADD PRIMARY KEY (`id_pengguna`);

--
-- Indeks untuk tabel `tb_penjualan_aksesoris`
--
ALTER TABLE `tb_penjualan_aksesoris`
  ADD PRIMARY KEY (`id_penjualan_aksesoris`),
  ADD KEY `id_aksesoris` (`id_aksesoris`),
  ADD KEY `id_pelanggan` (`id_pelanggan`);

--
-- Indeks untuk tabel `tb_penjualan_ponsel`
--
ALTER TABLE `tb_penjualan_ponsel`
  ADD PRIMARY KEY (`id_penjualan_ponsel`),
  ADD KEY `id_ponsel` (`id_ponsel`),
  ADD KEY `id_pelanggan` (`id_pelanggan`);

--
-- Indeks untuk tabel `tb_supplier`
--
ALTER TABLE `tb_supplier`
  ADD PRIMARY KEY (`id_supplier`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `tb_barang_paling_tidak_laku`
--
ALTER TABLE `tb_barang_paling_tidak_laku`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=337;

--
-- AUTO_INCREMENT untuk tabel `tb_barang_terlaris_per_bulan`
--
ALTER TABLE `tb_barang_terlaris_per_bulan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT untuk tabel `tb_biodata_staf`
--
ALTER TABLE `tb_biodata_staf`
  MODIFY `id_staf` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `tb_inventaris_aksesoris`
--
ALTER TABLE `tb_inventaris_aksesoris`
  MODIFY `id_aksesoris` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `tb_inventaris_ponsel`
--
ALTER TABLE `tb_inventaris_ponsel`
  MODIFY `id_ponsel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `tb_laporan_keuangan`
--
ALTER TABLE `tb_laporan_keuangan`
  MODIFY `id_laporan_keuangan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=225;

--
-- AUTO_INCREMENT untuk tabel `tb_pelanggan`
--
ALTER TABLE `tb_pelanggan`
  MODIFY `id_pelanggan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `tb_pengguna`
--
ALTER TABLE `tb_pengguna`
  MODIFY `id_pengguna` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `tb_penjualan_aksesoris`
--
ALTER TABLE `tb_penjualan_aksesoris`
  MODIFY `id_penjualan_aksesoris` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT untuk tabel `tb_penjualan_ponsel`
--
ALTER TABLE `tb_penjualan_ponsel`
  MODIFY `id_penjualan_ponsel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT untuk tabel `tb_supplier`
--
ALTER TABLE `tb_supplier`
  MODIFY `id_supplier` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `tb_barang_paling_tidak_laku`
--
ALTER TABLE `tb_barang_paling_tidak_laku`
  ADD CONSTRAINT `tb_barang_paling_tidak_laku_ibfk_1` FOREIGN KEY (`id_ponsel`) REFERENCES `tb_inventaris_ponsel` (`id_ponsel`),
  ADD CONSTRAINT `tb_barang_paling_tidak_laku_ibfk_2` FOREIGN KEY (`id_aksesoris`) REFERENCES `tb_inventaris_aksesoris` (`id_aksesoris`);

--
-- Ketidakleluasaan untuk tabel `tb_barang_terlaris_per_bulan`
--
ALTER TABLE `tb_barang_terlaris_per_bulan`
  ADD CONSTRAINT `tb_barang_terlaris_per_bulan_ibfk_1` FOREIGN KEY (`id_ponsel`) REFERENCES `tb_inventaris_ponsel` (`id_ponsel`),
  ADD CONSTRAINT `tb_barang_terlaris_per_bulan_ibfk_2` FOREIGN KEY (`id_aksesoris`) REFERENCES `tb_inventaris_aksesoris` (`id_aksesoris`);

--
-- Ketidakleluasaan untuk tabel `tb_penjualan_aksesoris`
--
ALTER TABLE `tb_penjualan_aksesoris`
  ADD CONSTRAINT `tb_penjualan_aksesoris_ibfk_1` FOREIGN KEY (`id_aksesoris`) REFERENCES `tb_inventaris_aksesoris` (`id_aksesoris`),
  ADD CONSTRAINT `tb_penjualan_aksesoris_ibfk_2` FOREIGN KEY (`id_pelanggan`) REFERENCES `tb_pelanggan` (`id_pelanggan`);

--
-- Ketidakleluasaan untuk tabel `tb_penjualan_ponsel`
--
ALTER TABLE `tb_penjualan_ponsel`
  ADD CONSTRAINT `tb_penjualan_ponsel_ibfk_1` FOREIGN KEY (`id_ponsel`) REFERENCES `tb_inventaris_ponsel` (`id_ponsel`),
  ADD CONSTRAINT `tb_penjualan_ponsel_ibfk_2` FOREIGN KEY (`id_pelanggan`) REFERENCES `tb_pelanggan` (`id_pelanggan`);

DELIMITER $$
--
-- Event
--
CREATE DEFINER=`root`@`localhost` EVENT `evt_refresh_summary_daily` ON SCHEDULE EVERY 1 DAY STARTS '2025-07-31 17:50:53' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
  -- Kosongkan dan isi ulang dari view
  DELETE FROM tb_barang_terlaris_per_bulan;
  INSERT INTO tb_barang_terlaris_per_bulan (id_ponsel, id_aksesoris, periode_date, periode)
  SELECT id_ponsel, id_aksesoris, periode_date, periode FROM vw_barang_terlaris;

  DELETE FROM tb_barang_paling_tidak_laku;
  INSERT INTO tb_barang_paling_tidak_laku (id_ponsel, id_aksesoris, periode_date, periode)
  SELECT id_ponsel, id_aksesoris, periode_date, periode FROM vw_barang_tidak_laku;

  DELETE FROM tb_pelanggan;
  INSERT INTO tb_pelanggan (id_pelanggan, nama_pelanggan, alamat, jumlah_beli_ponsel, jumlah_beli_aksesoris, total_jumlah_beli)
  SELECT id_pelanggan, nama_pelanggan, alamat, jumlah_beli_ponsel, jumlah_beli_aksesoris, total_jumlah_beli FROM vw_pelanggan_jumlah_beli;

  DELETE FROM tb_laporan_keuangan;
  INSERT INTO tb_laporan_keuangan (id_laporan_keuangan, tanggal, pendapatan_kotor, pengeluaran, pendapatan_bersih)
  SELECT id_laporan_keuangan, tanggal, pendapatan_kotor, pengeluaran, pendapatan_bersih FROM vw_laporan_keuangan;
END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
