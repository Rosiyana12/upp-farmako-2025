-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 22 Apr 2025 pada 20.18
-- Versi server: 10.4.28-MariaDB
-- Versi PHP: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `e-tiket`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `jadwal`
--

CREATE TABLE `jadwal` (
  `id` int(11) NOT NULL,
  `id_kereta` int(11) NOT NULL,
  `asal` varchar(100) NOT NULL,
  `tujuan` varchar(100) NOT NULL,
  `waktu_berangkat` datetime NOT NULL,
  `waktu_tiba` datetime NOT NULL,
  `harga` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `jadwal`
--

INSERT INTO `jadwal` (`id`, `id_kereta`, `asal`, `tujuan`, `waktu_berangkat`, `waktu_tiba`, `harga`) VALUES
(4, 9, 'bogor-kota', 'palembang', '2025-04-20 12:32:00', '2025-04-20 16:36:00', 715.00),
(5, 11, 'semarang', 'tanggung', '2025-04-20 11:37:00', '2025-04-20 12:38:00', 715.00),
(6, 9, 'bogor-kota', 'palembang', '2025-04-20 01:16:00', '2025-04-20 00:16:00', 3.00),
(7, 9, 'bogor', 'tanggung', '2025-04-21 13:45:00', '2025-04-21 17:49:00', 715.00);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kereta`
--

CREATE TABLE `kereta` (
  `id` int(11) NOT NULL,
  `nama_kereta` varchar(100) NOT NULL,
  `jenis` enum('Ekonomi','Bisnis','Eksekutif') NOT NULL,
  `kapasitas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kereta`
--

INSERT INTO `kereta` (`id`, `nama_kereta`, `jenis`, `kapasitas`) VALUES
(8, 'Argo', 'Ekonomi', 50),
(9, 'Gajayana,', 'Ekonomi', 100),
(10, 'Bima', 'Eksekutif', 50),
(11, 'Parahyangan', 'Eksekutif', 100);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kursi_terisi`
--

CREATE TABLE `kursi_terisi` (
  `id` int(11) NOT NULL,
  `idjadwal` int(11) NOT NULL,
  `no_kursi` varchar(20) NOT NULL,
  `id_pemesanan` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id` int(11) NOT NULL,
  `id_pemesanan` int(11) NOT NULL,
  `status` enum('proses','lunas') DEFAULT 'proses',
  `bukti` varchar(255) NOT NULL,
  `waktu_bayar` datetime NOT NULL,
  `metode_bayar` enum('Bank BCA','OVO','DANA') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pembayaran`
--

INSERT INTO `pembayaran` (`id`, `id_pemesanan`, `status`, `bukti`, `waktu_bayar`, `metode_bayar`) VALUES
(1, 40, 'proses', '1745145105_Screenshot (2).png', '2025-04-21 00:31:45', 'Bank BCA'),
(2, 41, 'proses', '1745145904_Screenshot (6).png', '2025-04-21 00:45:04', 'OVO'),
(3, 44, 'proses', '1745168693_Screenshot (1).png', '2025-04-21 07:04:53', 'Bank BCA'),
(4, 44, 'lunas', '1745168839_Screenshot (1).png', '2025-04-21 07:07:19', 'Bank BCA'),
(5, 50, 'lunas', '1745181150_bukti.png', '2025-04-21 10:32:30', 'DANA'),
(6, 51, 'lunas', '1745192839_bukti.png', '2025-04-21 13:47:19', 'OVO'),
(7, 51, 'lunas', '1745195083_bukti.png', '2025-04-21 14:24:43', 'OVO');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pemesanan`
--

CREATE TABLE `pemesanan` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_jadwal` int(11) NOT NULL,
  `jumlah_tiket` int(11) NOT NULL,
  `total_bayar` decimal(10,2) NOT NULL,
  `status_bayar` enum('belum bayar','sudah bayar') NOT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `metode_pembayaran` varchar(50) DEFAULT NULL,
  `waktu_pemesanan` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pemesanan`
--

INSERT INTO `pemesanan` (`id`, `id_user`, `id_jadwal`, `jumlah_tiket`, `total_bayar`, `status_bayar`, `bukti_pembayaran`, `metode_pembayaran`, `waktu_pemesanan`) VALUES
(3, 2, 5, 2, 1430.00, '', NULL, NULL, '2025-04-19 05:58:51'),
(4, 2, 5, 3, 2145.00, 'belum bayar', NULL, NULL, '2025-04-19 06:02:01'),
(5, 2, 5, 3, 2145.00, 'belum bayar', NULL, NULL, '2025-04-19 06:07:40'),
(6, 2, 5, 1, 715.00, '', NULL, NULL, '2025-04-19 06:12:31'),
(7, 2, 5, 1, 715.00, 'belum bayar', NULL, NULL, '2025-04-19 06:43:06'),
(8, 3, 5, 1, 715.00, 'belum bayar', NULL, NULL, '2025-04-19 07:38:33'),
(9, 3, 5, 1, 715.00, 'belum bayar', NULL, NULL, '2025-04-19 09:12:12'),
(10, 2, 5, 1, 715.00, 'belum bayar', NULL, NULL, '2025-04-19 10:08:08'),
(11, 2, 5, 1, 715.00, 'belum bayar', NULL, NULL, '2025-04-19 10:22:24'),
(12, 4, 5, 1, 715.00, 'belum bayar', NULL, NULL, '2025-04-19 10:55:44'),
(13, 4, 5, 1, 715.00, 'belum bayar', NULL, NULL, '2025-04-19 10:58:31'),
(14, 4, 5, 1, 715.00, 'belum bayar', NULL, NULL, '2025-04-19 11:00:28'),
(15, 4, 5, 1, 715.00, 'belum bayar', NULL, NULL, '2025-04-19 11:04:16'),
(16, 4, 5, 1, 715.00, 'belum bayar', NULL, NULL, '2025-04-19 11:30:56'),
(17, 4, 5, 1, 715.00, 'belum bayar', NULL, NULL, '2025-04-19 11:35:55'),
(18, 4, 5, 1, 715.00, 'belum bayar', NULL, NULL, '2025-04-19 11:36:24'),
(19, 2, 5, 1, 715.00, 'belum bayar', NULL, NULL, '2025-04-19 12:01:42'),
(20, 2, 5, 1, 715.00, 'belum bayar', NULL, NULL, '2025-04-19 12:03:17'),
(21, 2, 5, 1, 715.00, 'belum bayar', NULL, NULL, '2025-04-19 12:13:47'),
(22, 2, 5, 1, 715.00, 'belum bayar', NULL, NULL, '2025-04-19 12:14:29'),
(23, 2, 5, 1, 715.00, 'belum bayar', NULL, NULL, '2025-04-19 12:19:25'),
(24, 2, 5, 1, 715.00, 'belum bayar', NULL, NULL, '2025-04-19 12:22:25'),
(25, 2, 5, 1, 715.00, 'belum bayar', NULL, NULL, '2025-04-19 12:23:26'),
(26, 2, 5, 1, 715.00, 'belum bayar', NULL, NULL, '2025-04-19 12:23:52'),
(27, 2, 5, 1, 715.00, 'belum bayar', NULL, NULL, '2025-04-19 12:30:01'),
(28, 2, 5, 1, 715.00, 'belum bayar', NULL, NULL, '2025-04-19 12:32:39'),
(29, 2, 5, 1, 715.00, 'belum bayar', NULL, NULL, '2025-04-19 12:45:11'),
(30, 2, 5, 1, 715.00, 'belum bayar', NULL, NULL, '2025-04-19 12:46:07'),
(31, 2, 5, 1, 715.00, 'belum bayar', NULL, NULL, '2025-04-19 12:47:23'),
(32, 2, 5, 1, 715.00, '', '1745115941_Screenshot (2).png', 'OVO', '2025-04-19 12:48:36'),
(33, 2, 5, 1, 715.00, 'belum bayar', NULL, NULL, '2025-04-19 12:54:54'),
(34, 2, 5, 1, 715.00, '', '1745117131_Screenshot (6).png', 'Bank BCA', '2025-04-19 14:27:04'),
(35, 2, 5, 1, 715.00, '', '1745118154_Screenshot (1).png', 'OVO', '2025-04-19 15:02:22'),
(36, 2, 5, 1, 715.00, '', '1745121366_Screenshot (5).png', 'OVO', '2025-04-19 15:55:53'),
(37, 2, 6, 1, 3.00, 'belum bayar', NULL, NULL, '2025-04-19 17:12:11'),
(38, 2, 6, 1, 3.00, '', '1745143582_Screenshot (6).png', 'Bank BCA', '2025-04-19 21:57:58'),
(39, 3, 4, 1, 715.00, '', '1745144913_Screenshot (6).png', 'Bank BCA', '2025-04-19 22:28:21'),
(40, 3, 4, 1, 715.00, 'belum bayar', NULL, NULL, '2025-04-19 22:31:32'),
(41, 3, 4, 1, 715.00, '', NULL, NULL, '2025-04-19 22:44:52'),
(42, 3, 4, 1, 715.00, '', NULL, NULL, '2025-04-19 23:21:03'),
(43, 3, 4, 1, 715.00, '', NULL, NULL, '2025-04-19 23:26:46'),
(44, 3, 4, 1, 715.00, '', NULL, NULL, '2025-04-20 05:04:35'),
(45, 2, 5, 1, 715.00, '', NULL, NULL, '2025-04-20 07:43:12'),
(46, 2, 4, 1, 715.00, '', NULL, NULL, '2025-04-20 08:20:23'),
(47, 2, 5, 1, 715.00, '', NULL, NULL, '2025-04-20 08:22:45'),
(48, 2, 5, 1, 715.00, '', NULL, NULL, '2025-04-20 08:23:29'),
(49, 2, 5, 1, 715.00, '', NULL, NULL, '2025-04-20 08:25:16'),
(50, 2, 4, 1, 715.00, '', NULL, NULL, '2025-04-20 08:27:02'),
(51, 2, 4, 1, 715.00, '', NULL, NULL, '2025-04-20 11:47:04'),
(52, 2, 4, 1, 715.00, '', NULL, NULL, '2025-04-20 12:16:28'),
(53, 2, 4, 1, 715.00, '', NULL, NULL, '2025-04-20 12:18:10');

-- --------------------------------------------------------

--
-- Struktur dari tabel `penumpang`
--

CREATE TABLE `penumpang` (
  `Id` int(11) NOT NULL,
  `Id_pemesanan` int(11) NOT NULL,
  `Nama_penumpang` varchar(255) NOT NULL,
  `No_kursi` varchar(10) NOT NULL,
  `id_jadwal` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `penumpang`
--

INSERT INTO `penumpang` (`Id`, `Id_pemesanan`, `Nama_penumpang`, `No_kursi`, `id_jadwal`) VALUES
(3, 3, 'Rosiyana Lapindo', '1', NULL),
(4, 3, 'Rosiyana Lapindo', '2', NULL),
(5, 4, 'oci', '3', NULL),
(6, 4, 'caca', '4', NULL),
(7, 4, 'Rosiyana Lapindo', '5', NULL),
(8, 5, 'oci', '92', NULL),
(9, 6, 'ociy', '6', NULL),
(10, 7, 'Rosiyana Lapindo', '7', NULL),
(11, 8, 'fatahh', '91', NULL),
(12, 9, 'fatahh', '93', NULL),
(13, 10, 'fatahh', '8', NULL),
(14, 11, 'ociy', '39', NULL),
(15, 12, 'alfatah', '76', NULL),
(16, 13, 'fatur', '64', NULL),
(17, 14, 'alfatah', '10', NULL),
(18, 15, 'ociy', '33', NULL),
(19, 16, 'ociy', '9', NULL),
(20, 17, 'ociy', '9', NULL),
(21, 18, 'oci', '65', NULL),
(22, 19, 'fatahh', '75', NULL),
(23, 20, 'fatahh', '75', NULL),
(24, 21, 'fatur', '85', NULL),
(25, 22, 'fatur', '85', NULL),
(26, 23, 'fatur', '86', NULL),
(27, 24, 'fatur', '86', NULL),
(28, 25, 'Rosiyana Lapindo', '16', NULL),
(29, 26, 'Rosiyana Lapindo', '16', NULL),
(30, 27, 'oci', '82', NULL),
(31, 28, 'Rosiyana Lapindo', '20', NULL),
(32, 29, 'Rosiyana Lapindo', '56', NULL),
(33, 30, 'Rosiyana Lapindo', '79', NULL),
(34, 31, 'Rosiyana Lapindo', '74', NULL),
(35, 32, 'Rosiyana Lapindo', '13', NULL),
(36, 33, 'Rosiyana Lapindo', '67', NULL),
(37, 34, 'fatur', '68', NULL),
(38, 35, 'oci', '94', NULL),
(39, 36, 'Rosiyana Lapindo', '31', NULL),
(40, 37, 'fatahh', '97', NULL),
(41, 38, 'fatur', '79', NULL),
(42, 39, 'ociy', '91', NULL),
(43, 40, 'oci', '73', NULL),
(44, 41, 'Rosiyana Lapindo', '79', NULL),
(45, 42, 'fatur', '86', NULL),
(46, 43, 'Rosiyana Lapindo', '92', NULL),
(47, 44, 'fatahh', '43', NULL),
(48, 45, 'Rosiyana Lapindo', '81', NULL),
(49, 46, 'Rosiyana Lapindo', '74', NULL),
(50, 47, 'fatahh', '73', NULL),
(51, 48, 'Rosiyana Lapindo', '15', NULL),
(52, 49, 'Rosiyana Lapindo', '62', NULL),
(53, 50, 'Rosiyana Lapindo', '75', NULL),
(54, 51, 'Rosiyana Lapindo', '19', NULL),
(55, 52, 'Rosiyana Lapindo', '80', NULL),
(56, 53, 'Rosiyana Lapindo', '1', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','pengguna') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'asep septiawan', 'admin@admin.com', '$2y$10$8g260H.FIdc7e5Hfjr2/beO7ta/PNw3YSkeBsIR0ERlf3DdSar1CG', 'admin', '2025-04-18 22:18:10'),
(2, 'wilian jeft simth', 'wiliam@gmail.com', '$2y$10$6uMyhhoXFgj1U1SHNbFPIuQo2dxRL3gHo/eyYsGFiVF0e6E4soRd2', 'pengguna', '2025-04-19 01:14:54'),
(3, 'faturllah', 'fatur@gmail.com', '$2y$10$TT2yAJJQEiWjTKiEbzUk4uiIDxMTwMzDp768zviaQuRkjocbHKLaq', 'pengguna', '2025-04-19 19:31:33'),
(4, 'fatihh alfatah', 'fatih@gmail.com', '$2y$10$xwkqV3J6dhA/0lLrpd8AoOoeNniXmRxjsCkKRaaCkgu6zJAkkqMge', 'pengguna', '2025-04-19 22:53:53'),
(5, 'asep septiawan', 'asep@kasir.com', '$2y$10$EOfK4xCWbYHKZKP7ithnuu.lP4d4Z0mzkW/OJheIb5KgkIkpDC.NK', '', '2025-04-20 21:01:44'),
(6, 'asep septiawan', 'asep@gmail.com', '$2y$10$MM6E0vxzWCQUS0h3k2Vp8uhvpYNc1.yxY9jECCtc0SWQBG7Rrc2le', '', '2025-04-20 21:02:01');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_kereta_id` (`id_kereta`);

--
-- Indeks untuk tabel `kereta`
--
ALTER TABLE `kereta`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kursi_terisi`
--
ALTER TABLE `kursi_terisi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idjadwal` (`idjadwal`),
  ADD KEY `fk_kursi_terisi_pemesanan` (`id_pemesanan`);

--
-- Indeks untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pemesanan` (`id_pemesanan`);

--
-- Indeks untuk tabel `pemesanan`
--
ALTER TABLE `pemesanan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user` (`id_user`),
  ADD KEY `FK_jadwal_id` (`id_jadwal`);

--
-- Indeks untuk tabel `penumpang`
--
ALTER TABLE `penumpang`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `fk_penumpang_jadwal` (`id_jadwal`),
  ADD KEY `Id_pemesanan` (`Id_pemesanan`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `kereta`
--
ALTER TABLE `kereta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `kursi_terisi`
--
ALTER TABLE `kursi_terisi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `pemesanan`
--
ALTER TABLE `pemesanan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT untuk tabel `penumpang`
--
ALTER TABLE `penumpang`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  ADD CONSTRAINT `FK_kereta_id` FOREIGN KEY (`id_kereta`) REFERENCES `kereta` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_jadwal_kereta` FOREIGN KEY (`id_kereta`) REFERENCES `kereta` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_kereta` FOREIGN KEY (`id_kereta`) REFERENCES `kereta` (`id`),
  ADD CONSTRAINT `jadwal_ibfk_1` FOREIGN KEY (`id_kereta`) REFERENCES `kereta` (`id`),
  ADD CONSTRAINT `jadwal_ibfk_2` FOREIGN KEY (`id_kereta`) REFERENCES `kereta` (`id`);

--
-- Ketidakleluasaan untuk tabel `kursi_terisi`
--
ALTER TABLE `kursi_terisi`
  ADD CONSTRAINT `fk_kursi_terisi_pemesanan` FOREIGN KEY (`id_pemesanan`) REFERENCES `pemesanan` (`id`),
  ADD CONSTRAINT `kursi_terisi_ibfk_1` FOREIGN KEY (`idjadwal`) REFERENCES `jadwal` (`id`);

--
-- Ketidakleluasaan untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_pemesanan`) REFERENCES `pemesanan` (`id`),
  ADD CONSTRAINT `pembayaran_ibfk_2` FOREIGN KEY (`id_pemesanan`) REFERENCES `pemesanan` (`id`);

--
-- Ketidakleluasaan untuk tabel `pemesanan`
--
ALTER TABLE `pemesanan`
  ADD CONSTRAINT `FK_jadwal_id` FOREIGN KEY (`id_jadwal`) REFERENCES `jadwal` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pemesanan_jadwal` FOREIGN KEY (`id_jadwal`) REFERENCES `jadwal` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pemesanan_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `pemesanan_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `pemesanan_ibfk_2` FOREIGN KEY (`id_jadwal`) REFERENCES `jadwal` (`id`),
  ADD CONSTRAINT `pemesanan_ibfk_3` FOREIGN KEY (`id_jadwal`) REFERENCES `jadwal` (`id`);

--
-- Ketidakleluasaan untuk tabel `penumpang`
--
ALTER TABLE `penumpang`
  ADD CONSTRAINT `fk_pemesanan` FOREIGN KEY (`Id_pemesanan`) REFERENCES `pemesanan` (`id`),
  ADD CONSTRAINT `fk_penumpang_jadwal` FOREIGN KEY (`id_jadwal`) REFERENCES `jadwal` (`id`),
  ADD CONSTRAINT `fk_penumpang_pemesanan` FOREIGN KEY (`Id_pemesanan`) REFERENCES `pemesanan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `penumpang_ibfk_1` FOREIGN KEY (`Id_pemesanan`) REFERENCES `pemesanan` (`id`),
  ADD CONSTRAINT `penumpang_ibfk_2` FOREIGN KEY (`Id_pemesanan`) REFERENCES `pemesanan` (`id`),
  ADD CONSTRAINT `penumpang_ibfk_3` FOREIGN KEY (`Id_pemesanan`) REFERENCES `pemesanan` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
