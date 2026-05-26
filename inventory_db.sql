-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 26 Bulan Mei 2026 pada 06.51
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `inventory_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `barang`
--

CREATE TABLE `barang` (
  `id` int(11) NOT NULL,
  `kode_barang` varchar(50) NOT NULL,
  `nama_barang` varchar(150) NOT NULL,
  `kategori_id` int(11) DEFAULT NULL,
  `merek` varchar(50) NOT NULL,
  `satuan` varchar(50) DEFAULT NULL,
  `lokasi_rak` varchar(100) DEFAULT NULL,
  `stok` int(11) NOT NULL,
  `minimum_stok` int(11) DEFAULT 5
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `barang`
--

INSERT INTO `barang` (`id`, `kode_barang`, `nama_barang`, `kategori_id`, `merek`, `satuan`, `lokasi_rak`, `stok`, `minimum_stok`) VALUES
(1, 'JOE-16007', 'Oil Filter', 3, 'Jimco', 'PCS', 'A2', 18, 5),
(3, 'JFC-16006', 'Fuel Filter', 3, 'Jimco', 'PCS', 'A2', 36, 5),
(5, 'C-1013', 'Oil Filter', 3, 'Sakura', 'PCS', 'B3', 4, 5),
(7, 'JOC-88022', 'Oil Filter', 3, 'Jimco', 'PCS', 'A2', 2, 5),
(8, 'JOC-14005', 'Oil Filter', 3, 'Jimco', 'PCS', 'A2', 8, 5),
(9, 'JOE-14005', 'Oil Filter', 3, 'Jimco', 'PCS', 'A2', 4, 5),
(10, 'JOE-14002', 'Oil Filter', 3, 'Jimco', 'PCS', 'A2', 2, 5),
(11, 'JOC-16007', 'Oil Filter', 3, 'Jimco', 'PCS', 'A2', 4, 5),
(12, '1-13240117-0', 'Oil Filter', 3, 'Jimco', 'PCS', 'A2', 2, 5),
(13, 'JOC-88012', 'Oil Filter', 3, 'Jimco', 'PCS', 'A2', 1, 5),
(14, 'JOC-88021', 'Oil Filter', 3, 'Jimco', 'PCS', 'A2', 1, 5),
(15, 'JOC-20001', 'Oil Filter', 3, 'Jimco', 'PCS', 'A2', 12, 5),
(16, 'JOC-20004', 'Oil Filter', 3, 'Jimco', 'PCS', 'A2', 13, 5),
(17, 'JOC-12011', 'Oil Filter', 3, 'Jimco', 'PCS', 'A2', 1, 5),
(18, 'JFC-88017', 'Fuel Filter', 3, 'Jimco', 'PCS', 'A2', 2, 5),
(19, 'JFE-88009', 'Fuel Filter', 3, 'Jimco', 'PCS', 'A2', 1, 5),
(20, 'JFC_16005', 'Fuel Filter', 3, 'Jimco', 'PCS', 'A2', 3, 5),
(21, 'JFE-10005', 'Fuel Filter', 3, 'Jimco', 'PCS', 'A2', 2, 5),
(22, 'JFE-16002', 'Fuel FIlter', 3, 'Jimco', 'PCS', 'A2', 1, 5),
(23, 'JFC-88054', 'Fuel Filter', 3, 'Jimco', 'PCS', 'A2', 4, 5),
(24, 'JFC-88006', 'Fuel Filter', 3, 'Jimco', 'PCS', 'A2', 1, 5),
(25, 'JFC-88024', 'Fuel Filter', 3, 'Jimco', 'PCS', 'A2', 1, 5),
(26, 'JFC-88023', 'Fuel Filter', 3, 'Jimco', 'PCS', 'A2', 10, 5),
(27, 'JFC-16002', 'Fuel Filter', 3, 'Jimco', 'PCS', 'A2', 14, 5),
(28, 'JFE-16003', 'Fuel Filter', 3, 'Jimco', 'PCS', 'A2', 1, 5),
(29, 'JFC-14002', 'Fuel Filter', 3, 'Jimco', 'PCS', 'A2', 2, 5),
(30, 'JFC-14000', 'Fuel Filter', 3, 'Jimco', 'PCS', 'A2', 1, 5),
(31, 'JHC-88002', 'Hydraulic Filter', 3, 'Jimco', 'PCS', 'A2', 5, 5),
(32, 'JHC-88004', 'Hydraulic Filter', 3, 'Jimco', 'PCS', 'A2', 4, 5),
(33, 'JAE-88134', 'Air Filter', 3, 'Jimco', 'PCS', 'A2', 7, 5),
(34, 'JAE-88070', 'Air Filter', 3, 'Jimco', 'PCS', 'A1', 7, 5),
(35, 'JAE-88119', 'Air Filter', 3, 'Jimco', 'PCS', 'A1', 4, 5),
(36, 'JAE-88055', 'Air Filter', 3, 'Jimco', 'PCS', 'A1', 4, 5),
(37, 'JAE-88090-S', 'Air Filter', 3, 'Jimco', 'PCS', 'A1', 1, 5),
(38, 'JAE-14011', 'Air Filter', 3, 'Jimco', 'PCS', 'A1', 7, 5),
(39, 'JAE-88197', 'Air Filter', 3, 'Jimco', 'PCS', 'A1', 6, 5),
(40, 'JAE-88101', 'Air Filter', 3, 'Jimco', 'PCS', 'A1', 1, 5),
(41, 'JAE-88132', 'Air Filter', 3, 'Jimco', 'PCS', 'A1', 2, 5),
(42, 'JAE-88047', 'Air Filter', 3, 'Jimco', 'PCS', 'B1', 2, 5),
(43, 'JAE-88135', 'Air Filter', 3, 'Jimco', 'PCS', 'B1', 2, 5),
(44, 'JAE-88050', 'Air Filter', 3, 'Jimco', 'PCS', 'B1', 2, 5),
(45, 'JAE-12009-1', 'Air Filter', 3, 'Jimco', 'PCS', 'B1', 1, 5),
(46, 'JAE-88051', 'Air Filter', 3, 'Jimco', 'PCS', 'B1', 2, 5),
(47, 'P502083', 'Oil Filter', 3, 'Donaldson', 'PCS', 'A3', 2, 5),
(48, 'P554004', 'Oil Filter', 3, 'Donaldson', 'PCS', 'A3', 6, 5),
(49, 'P559418', 'Oil Filter', 3, 'Donaldson', 'PCS', 'A3', 1, 5),
(50, 'P554005', 'Oil Filter', 3, 'Donaldson', 'PCS', 'A3', 3, 5),
(51, 'P550162', 'Oil Filter', 3, 'Donaldson', 'PCS', 'A3', 4, 5),
(52, 'P550425', 'Oil Filter', 3, 'Donaldson', 'PCS', 'A3', 1, 5),
(53, 'P550066', 'Oil Filter', 3, 'Donaldson', 'PCS', 'A3', 3, 5),
(54, 'P502405', 'Oil Filter', 3, 'Donaldson', 'PCS', 'A3', 3, 5),
(55, 'P551251', 'Oil Filter', 3, 'Donaldson', 'PCS', 'A3', 1, 5),
(56, 'P554770', 'Oil Filter', 3, 'Donaldson', 'PCS', 'A3', 2, 5),
(57, 'J86-10230', 'Oil Filter', 3, 'Donaldson', 'PCS', 'A3', 6, 5),
(58, 'P551670', 'Oil Filter', 3, 'Donaldson', 'PCS', 'A3', 3, 5),
(59, 'J86-12190', 'Oil Filter', 3, 'Donaldson', 'PCS', 'A3', 1, 5),
(60, 'J86-11120', 'Oil Filter', 3, 'Donaldson', 'PCS', 'A3', 1, 5),
(61, 'J86-10460', 'Oil Filter', 3, 'Donaldson', 'PCS', 'A3', 4, 5),
(62, 'P558615', 'Oil Filter', 3, 'Donaldson', 'PCS', 'A3', 12, 5),
(63, 'J86-21202', 'Fuel Filter', 3, 'Donaldson', 'PCS', 'A3', 9, 5),
(64, 'P551314', 'Fuel Filter', 3, 'Donaldson', 'PCS', 'A3', 8, 5),
(65, 'J86-21105D', 'Fuel Filter', 3, 'Donaldson', 'PCS', 'A3', 12, 5),
(66, 'P550625', 'Fuel Filter', 3, 'Donaldson', 'PCS', 'A3', 1, 5),
(67, 'P553004', 'Fuel Filter', 3, 'Donaldson', 'PCS', 'A3', 9, 5),
(68, 'P502466', 'Fuel Filter', 3, 'Donaldson', 'PCS', 'A3', 14, 5),
(69, 'P550391', 'Fuel Filter', 3, 'Donaldson', 'PCS', 'A3', 2, 5),
(70, 'J86-20085', 'Fuel Filter', 3, 'Donaldson', 'PCS', 'A3', 2, 5),
(71, 'P552020PM', 'Fuel Filter / Water Separator', 3, 'Donaldson', 'PCS', 'A3', 7, 5),
(72, 'J86-21160', 'Fuel Filter / Water Separator', 3, 'Donaldson', 'PCS', 'A3', 6, 5),
(73, 'J86-29208', 'Fuel Filter / Water Separator', 3, 'Donaldson', 'PCS', 'A3', 1, 5),
(74, 'J86-20770', 'Fuel Filter / Water Separator', 3, 'Donaldson', 'PCS', 'A3', 2, 5),
(75, 'J86-20386', 'Fuel Filter / Water Separator', 3, 'Donaldson', 'PCS', 'A3', 1, 5),
(76, 'J86-40071', 'Coolant Filter', 3, 'Donaldson', 'PCS', 'A3', 1, 5),
(77, 'P554075', 'Coolant Filter', 3, 'Donaldson', 'PCS', 'A3', 1, 5),
(78, 'J86-40075', 'Coolant Filter', 3, 'Donaldson', 'PCS', 'A3', 7, 5),
(79, 'P551348', 'Hydraulic Filter', 3, 'Donaldson', 'PCS', 'B3', 4, 5),
(80, 'P177047', 'Hydraulic Filter', 3, 'Donaldson', 'PCS', 'B3', 1, 5),
(81, 'P502446', 'Hydraulic Filter', 3, 'Donaldson', 'PCS', 'B3', 2, 5),
(82, 'P550084', 'Hydraulic Filter', 3, 'Donaldson', 'PCS', 'B3', 6, 5),
(83, 'P181191', 'Air Filter', 3, 'Donaldson', 'PCS', 'B1', 5, 5),
(84, 'P821938', 'Air Filter', 3, 'Donaldson', 'PCS', 'B1', 2, 5),
(85, 'P782105', 'Air Filter', 3, 'Donaldson', 'PCS', 'B1', 1, 5),
(86, 'P537877', 'Air Filter', 3, 'Donaldson', 'PCS', 'B1', 3, 5),
(87, 'P828889', 'Air Filter', 3, 'Donaldson', 'PCS', 'B1', 1, 5),
(88, 'P532504', 'Air Filter', 3, 'Donaldson', 'PCS', 'B1', 2, 5),
(89, 'P822768', 'Air Filter', 3, 'Donaldson', 'PCS', 'B1', 3, 5),
(90, 'P821963', 'Air Filter', 3, 'Donaldson', 'PCS', 'B1', 6, 5),
(91, 'P522452', 'Air Filter', 3, 'Donaldson', 'PCS', 'B1', 7, 5),
(92, 'P822686', 'Air Filter', 3, 'Donaldson', 'PCS', 'B1', 1, 5),
(93, 'P829333', 'Air Filter', 3, 'Donaldson', 'PCS', 'B1', 3, 5),
(94, 'C-1316', 'Oil Filter', 3, 'Sakura', 'PCS', 'B3', 11, 5),
(95, 'O-1808', 'Oil Filter', 3, 'Sakura', 'PCS', 'B3', 9, 5),
(96, 'C-1004', 'Oil Filter', 3, 'Sakura', 'PCS', 'B3', 20, 5),
(97, 'C-6105', 'Oil Filter', 3, 'Sakura', 'PCS', 'B3', 6, 5),
(98, 'C-1318', 'Oil Filter', 3, 'Sakura', 'PCS', 'B3', 1, 5),
(99, 'C-1007', 'Oil Filter', 3, 'Sakura', 'PCS', 'B3', 2, 5),
(100, 'O-1522', 'Oil Filter', 3, 'Sakura', 'PCS', 'B3', 3, 5),
(101, 'F-1004', 'Oil Filter', 3, 'Sakura', 'PCS', 'B3', 1, 5),
(102, 'O-1011', 'Oil Filter', 3, 'Sakura', 'PCS', 'B3', 2, 5),
(103, 'O-1012-S', 'Oil Filter', 3, 'Sakura', 'PCS', 'B3', 1, 5),
(104, 'O-1301', 'Oil Filter', 3, 'Sakura', 'PCS', 'B3', 1, 5),
(105, 'O-1805-1', 'Oil Filter', 3, 'Sakura', 'PCS', 'B3', 1, 5),
(106, 'FC-1104', 'Fuel Filter', 3, 'Sakura', 'PCS', 'B3', 15, 5),
(107, 'FC-1008', 'Fuel Filter', 3, 'Sakura', 'PCS', 'B3', 9, 5),
(108, 'FC-1005', 'Fuel Filter', 3, 'Sakura', 'PCS', 'B3', 1, 5),
(109, 'FC-5501', 'Fuel Filter', 3, 'Sakura', 'PCS', 'B3', 3, 5),
(110, 'EF-1301', 'Fuel Filter', 3, 'Sakura', 'PCS', 'B3', 2, 5),
(111, 'FC-1001', 'Fuel Filter', 3, 'Sakura', 'PCS', 'B3', 6, 5),
(112, 'FC-6203', 'Fuel Filter', 3, 'Sakura', 'PCS', 'B3', 3, 5),
(113, 'FC-1109', 'Fuel Filter', 3, 'Sakura', 'PCS', 'B3', 2, 5),
(114, 'FC-13200', 'Fuel Filter', 3, 'Sakura', 'PCS', 'B3', 1, 5),
(115, 'FC-1703', 'Fuel Filter', 3, 'Sakura', 'PCS', 'B3', 1, 5),
(116, 'FC-2701', 'Fuel Filter', 3, 'Sakura', 'PCS', 'B3', 3, 5),
(117, 'SFC-1306-30', 'Fuel Filter / Water Separator', 3, 'Sakura', 'PCS', 'B3', 2, 5),
(118, 'A-8505', 'Air Filter', 3, 'Sakura', 'PCS', 'B1', 3, 5),
(119, '15607-2190L', 'Oil Filter', 3, 'HOP', 'PCS', 'B2', 8, 5),
(120, '15607-LCD80', 'Oil Filter', 3, 'HOP', 'PCS', 'B2', 2, 5),
(121, '23401-1440L', 'Fuel Filter', 3, 'HOP', 'PCS', 'B2', 20, 5),
(122, 'LF3325', 'Oil Filter', 3, 'Fleetguard', 'PCS', 'B2', 6, 5),
(123, 'LF9009', 'Oil Filter', 3, 'Fleetguard', 'PCS', 'B2', 6, 5),
(124, 'LF14000NN', 'Oil Filter', 3, 'Fleetguard', 'PCS', 'B2', 3, 5),
(125, 'LF670', 'Oil Filter', 3, 'Fleetguard', 'PCS', 'B2', 2, 5),
(126, 'LF777', 'Oil Filter', 3, 'Fleetguard', 'PCS', 'B2', 2, 5),
(127, 'FF202', 'Fuel Filter', 3, 'Fleetguard', 'PCS', 'B2', 3, 5),
(128, 'FF5488', 'Fuel Filter', 3, 'Fleetguard', 'PCS', 'B2', 10, 5),
(129, 'FF105D', 'Fuel Filter', 3, 'Fleetguard', 'PCS', 'B2', 2, 5),
(130, 'HF6005', 'Hydraulic Filter', 3, 'Fleetguard', 'PCS', 'B2', 1, 5),
(131, 'HF6561', 'Hydraulic Filter', 3, 'Fleetguard', 'PCS', 'B2', 3, 5),
(132, 'WF2076', 'Coolant Filter', 3, 'Fleetguard', 'PCS', 'B2', 5, 5),
(133, 'WF2126', 'Coolant Filter', 3, 'Fleetguard', 'PCS', 'B2', 1, 5),
(134, 'FS19732', 'Fuel Filter / Water Separator', 3, 'Fleetguard', 'PCS', 'B2', 2, 5),
(135, 'FS1006', 'Fuel Filter / Water Separator', 3, 'Fleetguard', 'PCS', 'B2', 2, 5),
(136, 'FS19816', 'Fuel Filter / Water Separator', 3, 'Fleetguard', 'PCS', 'B2', 20, 5),
(137, 'SPH 9606', 'Hydraulic Filter', 3, 'SF Filter', 'PCS', 'C3', 7, 5),
(138, 'TO-90915-INV-1800', 'Oil Filter', 3, 'Aspira', 'PCS', 'C3', 2, 5),
(139, '01174416', 'Oil Filter', 3, 'Deutz', 'PCS', 'C3', 8, 5),
(140, '01174696', 'Fuel Filter', 3, 'Deutz', 'PCS', 'C3', 11, 5),
(141, '90915-YZZD2-82', 'Oil Filter', 3, 'Toyota', 'PCS', 'C3', 4, 5),
(142, '51459', 'Oil Filter', 3, 'Wix', 'PCS', 'C3', 5, 5),
(143, 'MN-55440-1299', 'Hydraulic Filter', 3, 'Wix', 'PCS', 'C3', 3, 5),
(144, 'HF35255', 'Hydraulic Filter', 3, 'Fleetguard', 'PCS', 'C3', 10, 5),
(145, 'C-SP-10L-10', 'Hydraulic Filter', 3, 'Yamashin', 'PCS', 'C3', 5, 5);

-- --------------------------------------------------------

--
-- Struktur dari tabel `barang_keluar`
--

CREATE TABLE `barang_keluar` (
  `id` int(11) NOT NULL,
  `barang_id` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `diminta_oleh` varchar(100) NOT NULL,
  `tanggal` date NOT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `barang_keluar`
--

INSERT INTO `barang_keluar` (`id`, `barang_id`, `jumlah`, `diminta_oleh`, `tanggal`, `keterangan`) VALUES
(3, 3, 2, 'Putra Prawira', '2026-05-20', 'MEI - TL - 023');

-- --------------------------------------------------------

--
-- Struktur dari tabel `barang_masuk`
--

CREATE TABLE `barang_masuk` (
  `id` int(11) NOT NULL,
  `barang_id` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `nomor_po` varchar(100) DEFAULT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `barang_masuk`
--

INSERT INTO `barang_masuk` (`id`, `barang_id`, `jumlah`, `tanggal`, `nomor_po`, `keterangan`) VALUES
(1, 5, 2, '2026-05-19', '', 'Ex Project Balongan');

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail`
--

CREATE TABLE `detail` (
  `id` int(11) NOT NULL,
  `barang_id` int(11) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `nomor_surat_jalan` varchar(100) DEFAULT NULL,
  `nomor_po` varchar(100) DEFAULT NULL,
  `persamaan_produk` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `detail`
--

INSERT INTO `detail` (`id`, `barang_id`, `gambar`, `nomor_surat_jalan`, `nomor_po`, `persamaan_produk`, `created_at`) VALUES
(3, 5, '1779436808_6a100d08c02c0.jpg', '', 'PO-MEI-001', 'AY100-MT029 dari Nissan', '2026-05-22 08:00:08'),
(4, 94, NULL, '', '', '', '2026-05-23 03:07:20'),
(5, 95, NULL, '', '', '', '2026-05-23 03:13:55'),
(6, 133, NULL, '', '', '', '2026-05-23 03:19:00'),
(7, 132, NULL, '', '', '', '2026-05-23 03:28:30'),
(8, 117, NULL, '', '', '', '2026-05-23 03:29:36'),
(9, 93, NULL, '', '', '', '2026-05-23 03:34:16'),
(10, 87, NULL, '', '', '', '2026-05-23 03:38:13'),
(11, 12, NULL, '', '', '', '2026-05-23 03:40:29'),
(12, 137, NULL, '', '', '3743801600', '2026-05-25 04:40:15'),
(13, 138, NULL, '', '', '', '2026-05-25 04:46:34'),
(14, 139, NULL, '', '', '', '2026-05-25 07:51:04'),
(15, 140, NULL, '', '', '', '2026-05-25 08:04:25'),
(16, 141, NULL, '', '', '', '2026-05-26 01:45:53'),
(17, 142, NULL, '', '', '', '2026-05-26 02:00:46'),
(18, 143, NULL, '', '', '', '2026-05-26 02:10:44'),
(19, 123, NULL, '', '', '', '2026-05-26 02:14:16'),
(20, 144, NULL, '', '', '', '2026-05-26 02:23:43'),
(21, 145, NULL, '', '', '', '2026-05-26 03:02:31');

-- --------------------------------------------------------

--
-- Struktur dari tabel `gambar_produk`
--

CREATE TABLE `gambar_produk` (
  `id` int(11) NOT NULL,
  `barang_id` int(11) NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `urutan` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `gambar_produk`
--

INSERT INTO `gambar_produk` (`id`, `barang_id`, `nama_file`, `created_at`, `urutan`) VALUES
(7, 5, 'C-1013.jpg', '2026-05-23 02:48:49', 0),
(8, 5, 'C-1013_2.jpg', '2026-05-23 02:48:49', 0),
(9, 94, 'C-1316_02.jpg', '2026-05-23 03:07:20', 1),
(10, 94, 'C-1316.jpg', '2026-05-23 03:07:20', 0),
(11, 95, 'O-1808_2.jpg', '2026-05-23 03:13:55', 0),
(12, 95, 'O-1808.jpg', '2026-05-23 03:13:55', 0),
(13, 133, 'WF2126.jpg', '2026-05-23 03:19:00', 0),
(14, 132, 'WF2076.jpg', '2026-05-23 03:28:30', 0),
(15, 117, 'SFC-1306-30_2.jpg', '2026-05-23 03:29:36', 0),
(16, 117, 'SFC-1306-30.jpg', '2026-05-23 03:29:36', 0),
(17, 93, 'P829333.jpg', '2026-05-23 03:34:16', 0),
(18, 87, 'P828889.jpg', '2026-05-23 03:38:13', 0),
(20, 137, '1779684015_6a13d2af6b8fc_SPH_9606.png', '2026-05-25 04:40:15', 0),
(23, 138, 'TO-90915-INV-1800.png', '2026-05-25 07:48:20', 0),
(24, 139, '1779695464_6a13ff68165a9_01174416.png', '2026-05-25 07:51:04', 0),
(25, 140, '1779696265_6a140289563d8_01174696.png', '2026-05-25 08:04:25', 0),
(30, 141, '90915-YZZD2-82-removebg-preview.png', '2026-05-26 01:56:37', 0),
(31, 141, '90915-YZZD2-82_2-removebg-preview.png', '2026-05-26 01:56:37', 0),
(32, 142, '1779760846_6a14fece92418_51459.jpg', '2026-05-26 02:00:46', 0),
(33, 143, 'MN-55440-1299_2-removebg-preview.png', '2026-05-26 02:52:39', 1),
(34, 143, 'MN-55440-1299-removebg-preview.png', '2026-05-26 02:52:39', 0),
(36, 145, '1779764551_6a150d4787c66_C-SP-10L-10.png', '2026-05-26 03:02:31', 0),
(37, 144, 'HF35255.jpg', '2026-05-26 03:07:04', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategori`
--

INSERT INTO `kategori` (`id`, `nama_kategori`) VALUES
(7, 'Battery'),
(9, 'Consumable'),
(3, 'Filter');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `level` enum('admin','staff') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama`, `username`, `password`, `level`) VALUES
(3, 'Administrator', 'admin', '$2y$10$0Ui1QcSBdv5xSDEOBDPFme1lq9vX6cvetL3Xc3Nevgr8/m4AOQjY6', 'admin'),
(4, 'Staff Gudang', 'staff', '$2y$10$0Ui1QcSBdv5xSDEOBDPFme1lq9vX6cvetL3Xc3Nevgr8/m4AOQjY6', 'staff');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_barang` (`kode_barang`),
  ADD UNIQUE KEY `kode_barang_2` (`kode_barang`),
  ADD UNIQUE KEY `kode_barang_3` (`kode_barang`),
  ADD KEY `idx_barang_nama` (`nama_barang`),
  ADD KEY `idx_barang_kode` (`kode_barang`),
  ADD KEY `idx_nama_barang` (`nama_barang`),
  ADD KEY `idx_kode_barang` (`kode_barang`),
  ADD KEY `idx_merek` (`merek`),
  ADD KEY `idx_barang_merek` (`merek`),
  ADD KEY `idx_barang_kategori` (`kategori_id`),
  ADD KEY `idx_barang_stok` (`stok`);

--
-- Indeks untuk tabel `barang_keluar`
--
ALTER TABLE `barang_keluar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `barang_id` (`barang_id`);

--
-- Indeks untuk tabel `barang_masuk`
--
ALTER TABLE `barang_masuk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `barang_id` (`barang_id`);

--
-- Indeks untuk tabel `detail`
--
ALTER TABLE `detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_detail_barang` (`barang_id`);

--
-- Indeks untuk tabel `gambar_produk`
--
ALTER TABLE `gambar_produk`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_kategori` (`nama_kategori`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `barang`
--
ALTER TABLE `barang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146;

--
-- AUTO_INCREMENT untuk tabel `barang_keluar`
--
ALTER TABLE `barang_keluar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `barang_masuk`
--
ALTER TABLE `barang_masuk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `detail`
--
ALTER TABLE `detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT untuk tabel `gambar_produk`
--
ALTER TABLE `gambar_produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `barang`
--
ALTER TABLE `barang`
  ADD CONSTRAINT `barang_ibfk_1` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`);

--
-- Ketidakleluasaan untuk tabel `barang_keluar`
--
ALTER TABLE `barang_keluar`
  ADD CONSTRAINT `barang_keluar_ibfk_1` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`);

--
-- Ketidakleluasaan untuk tabel `barang_masuk`
--
ALTER TABLE `barang_masuk`
  ADD CONSTRAINT `barang_masuk_ibfk_1` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`);

--
-- Ketidakleluasaan untuk tabel `detail`
--
ALTER TABLE `detail`
  ADD CONSTRAINT `fk_detail_barang` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
