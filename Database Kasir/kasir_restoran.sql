-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 21 Jan 2025 pada 05.48
-- Versi server: 10.4.22-MariaDB
-- Versi PHP: 7.3.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kasir_restoran`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `level`
--

CREATE TABLE `level` (
  `id_level` int(11) NOT NULL,
  `nama_level` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `level`
--

INSERT INTO `level` (`id_level`, `nama_level`) VALUES
(1, 'Administrator'),
(2, 'Waiter'),
(3, 'Kasir'),
(4, 'Owner'),
(5, 'Pelanggan');

-- --------------------------------------------------------

--
-- Struktur dari tabel `masakan`
--

CREATE TABLE `masakan` (
  `id_masakan` int(11) NOT NULL,
  `nama_masakan` varchar(150) NOT NULL,
  `harga` varchar(150) NOT NULL,
  `stok` int(11) NOT NULL,
  `status_masakan` varchar(150) NOT NULL,
  `gambar_masakan` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `masakan`
--

INSERT INTO `masakan` (`id_masakan`, `nama_masakan`, `harga`, `stok`, `status_masakan`, `gambar_masakan`) VALUES
(8, 'Sate Ayam', '11000', 0, 'tersedia', 'Sate Ayam.jpeg'),
(18, 'Ayam Geprek', '11000', 0, 'tersedia', 'Ayam Geprek.jpeg'),
(23, 'wdfs', '10000', 3, 'tersedia', 'wdfs.jpg'),
(25, 'qswdefg', '99999', 1, 'tersedia', 'no_image.png');

-- --------------------------------------------------------

--
-- Struktur dari tabel `order_pesanan`
--

CREATE TABLE `order_pesanan` (
  `id_order` int(11) NOT NULL,
  `id_admin` int(11) DEFAULT NULL,
  `id_pengunjung` int(11) NOT NULL,
  `waktu_pesan` datetime NOT NULL,
  `no_meja` int(11) NOT NULL,
  `total_harga` varchar(150) NOT NULL,
  `uang_bayar` varchar(150) DEFAULT NULL,
  `uang_kembali` varchar(150) DEFAULT NULL,
  `status_order` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `order_pesanan`
--

INSERT INTO `order_pesanan` (`id_order`, `id_admin`, `id_pengunjung`, `waktu_pesan`, `no_meja`, `total_harga`, `uang_bayar`, `uang_kembali`, `status_order`) VALUES
(44, 61, 61, '2025-01-14 22:03:34', 0, '22000', '23000', '1000', 'sudah bayar'),
(46, 61, 61, '2025-01-15 20:11:23', 0, '132000', '150000', '18000', 'sudah bayar'),
(47, 61, 61, '2025-01-15 20:16:22', 0, '99000', '100000', '1000', 'sudah bayar'),
(49, 61, 61, '2025-01-15 20:35:54', 0, '11000', '11000', '0', 'sudah bayar'),
(50, 61, 61, '2025-01-15 20:36:50', 0, '11000', '11000', '0', 'sudah bayar');

-- --------------------------------------------------------

--
-- Struktur dari tabel `stok_menu`
--

CREATE TABLE `stok_menu` (
  `id_stok` int(11) NOT NULL,
  `id_pesan` int(11) NOT NULL,
  `jumlah_terjual` int(11) DEFAULT NULL,
  `status_cetak` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `stok_menu`
--

INSERT INTO `stok_menu` (`id_stok`, `id_pesan`, `jumlah_terjual`, `status_cetak`) VALUES
(1, 46, 1, 'belum cetak'),
(2, 47, 2, 'belum cetak'),
(3, 48, 2, 'belum cetak'),
(4, 49, 1, 'belum cetak'),
(5, 50, 2, 'belum cetak'),
(6, 51, 1, 'belum cetak'),
(7, 52, 1, 'belum cetak'),
(8, 53, 0, 'belum cetak'),
(9, 54, 0, 'belum cetak'),
(10, 55, 0, 'belum cetak'),
(11, 56, 2, 'belum cetak'),
(12, 57, 1, 'belum cetak'),
(13, 58, 6, 'belum cetak'),
(14, 59, 1, 'belum cetak'),
(15, 60, 1, 'belum cetak'),
(16, 61, 1, 'belum cetak'),
(17, 62, 2, 'belum cetak'),
(18, 63, 1, 'belum cetak'),
(19, 64, 1, 'belum cetak'),
(20, 65, 1, 'belum cetak'),
(21, 66, 1, 'belum cetak'),
(22, 67, 1, 'belum cetak'),
(23, 68, 1, 'belum cetak'),
(24, 69, 1, 'belum cetak'),
(25, 70, 3, 'belum cetak'),
(26, 71, 2, 'belum cetak'),
(27, 72, 4, 'belum cetak'),
(28, 73, 2, 'belum cetak'),
(29, 74, 0, 'belum cetak'),
(30, 75, 2, 'belum cetak'),
(31, 76, 13, 'belum cetak'),
(32, 77, 2, 'belum cetak'),
(33, 78, 4, 'belum cetak'),
(34, 79, 1, 'belum cetak'),
(35, 80, 1, 'belum cetak'),
(36, 0, 0, 'belum cetak'),
(37, 81, 1, 'belum cetak'),
(38, 82, 1, 'belum cetak'),
(39, 83, 2, 'belum cetak'),
(40, 84, 3, 'belum cetak'),
(41, 85, 2, 'belum cetak'),
(42, 86, 2, 'belum cetak'),
(43, 87, 12, 'belum cetak'),
(44, 88, 9, 'belum cetak'),
(45, 89, 1, 'belum cetak'),
(46, 90, 1, 'belum cetak'),
(47, 91, 0, 'belum cetak'),
(48, 92, 0, 'belum cetak'),
(49, 93, 0, 'belum cetak'),
(50, 98, 1, 'belum cetak'),
(51, 100, 1, 'belum cetak'),
(52, 101, 1, 'belum cetak'),
(53, 102, 1, 'belum cetak'),
(54, 103, 0, 'belum cetak'),
(55, 104, 0, 'belum cetak'),
(56, 105, 1, 'belum cetak'),
(57, 107, 2, 'belum cetak');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_pesan`
--

CREATE TABLE `tb_pesan` (
  `id_pesan` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_order` int(11) DEFAULT NULL,
  `id_masakan` int(11) NOT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `status_pesan` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `tb_pesan`
--

INSERT INTO `tb_pesan` (`id_pesan`, `id_user`, `id_order`, `id_masakan`, `jumlah`, `status_pesan`) VALUES
(74, 1, 0, 8, 0, 'sudah'),
(85, 61, 44, 18, 2, 'sudah'),
(87, 61, 46, 18, 12, 'sudah'),
(88, 61, 47, 8, 9, 'sudah'),
(89, 61, 49, 18, 1, 'sudah'),
(90, 61, 50, 18, 1, 'sudah'),
(91, 61, 0, 18, 0, 'sudah'),
(92, 61, 0, 23, 0, 'sudah'),
(94, 61, NULL, 18, 1, 'sudah'),
(95, 61, NULL, 18, 1, 'sudah'),
(96, 61, NULL, 23, 1, 'sudah'),
(106, 81, NULL, 23, 5, 'sudah');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `username` varchar(150) NOT NULL,
  `password` varchar(150) NOT NULL,
  `nama_user` varchar(150) NOT NULL,
  `id_level` int(11) NOT NULL,
  `status` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id_user`, `username`, `password`, `nama_user`, `id_level`, `status`) VALUES
(74, 'owner', '$2y$10$8r4GvuUPLa.QCQD3gOlL0.Akc0njpNKyuCh6Vn2oQHC1mniyiBa1u', 'hii', 4, 'aktif'),
(75, 'kasir123', '$2y$10$ZUbEQxwD10MaMNmROouKgO9aj7ac1.gMRzhaggm3hdO8NHbIjVI/m', 'Kasir', 3, 'aktif'),
(77, 'waiter', '$2y$10$C2my59dyz595KjSzRKmmtOYCoGlQg96tAeNJfgK/kSXx/wlmavtxq', 'Waiter', 2, 'aktif'),
(79, 'pelanggan', '$2y$10$/Zq61MQu/Ejb3LlQPQHVGOggxy0S6kfbz/qljUQPLtDks8XKELOF6', 'Pelee', 5, 'aktif'),
(81, 'admin', '$2y$10$9DQGPW8vSDq9UhVO09but..S6rT/.n9XVG/fNvtRWN4HsIWmzX3Qe', 'ciko', 1, 'aktif');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `level`
--
ALTER TABLE `level`
  ADD PRIMARY KEY (`id_level`);

--
-- Indeks untuk tabel `masakan`
--
ALTER TABLE `masakan`
  ADD PRIMARY KEY (`id_masakan`);

--
-- Indeks untuk tabel `order_pesanan`
--
ALTER TABLE `order_pesanan`
  ADD PRIMARY KEY (`id_order`),
  ADD KEY `id_admin` (`id_admin`),
  ADD KEY `id_pengunjung` (`id_pengunjung`);

--
-- Indeks untuk tabel `stok_menu`
--
ALTER TABLE `stok_menu`
  ADD PRIMARY KEY (`id_stok`);

--
-- Indeks untuk tabel `tb_pesan`
--
ALTER TABLE `tb_pesan`
  ADD PRIMARY KEY (`id_pesan`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `id_level` (`id_level`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `level`
--
ALTER TABLE `level`
  MODIFY `id_level` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `masakan`
--
ALTER TABLE `masakan`
  MODIFY `id_masakan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT untuk tabel `order_pesanan`
--
ALTER TABLE `order_pesanan`
  MODIFY `id_order` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT untuk tabel `stok_menu`
--
ALTER TABLE `stok_menu`
  MODIFY `id_stok` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT untuk tabel `tb_pesan`
--
ALTER TABLE `tb_pesan`
  MODIFY `id_pesan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`id_level`) REFERENCES `level` (`id_level`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
