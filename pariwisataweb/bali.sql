-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 06 Jun 2024 pada 15.06
-- Versi server: 10.4.28-MariaDB
-- Versi PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bali`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `destination`
--

CREATE TABLE `destination` (
  `id_des` int(11) NOT NULL,
  `nama_des` varchar(100) NOT NULL,
  `gambar` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `destination`
--

INSERT INTO `destination` (`id_des`, `nama_des`, `gambar`) VALUES
(1, 'PANTAI KUTA', 'kuta.png'),
(2, 'PURA TANAH LOT', 'tanahlot.png'),
(3, 'PULAU NISA PENINDA', 'peninda.png'),
(4, 'GUNUNG BATUR', 'batur.png'),
(5, 'PANTAI PANDAWA', 'pandawa.png'),
(6, 'GARUDA WISNU KENCANA', 'garuda.png');

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail`
--

CREATE TABLE `detail` (
  `id_detail` int(11) NOT NULL,
  `id_des` int(11) NOT NULL,
  `desc` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `detail`
--

INSERT INTO `detail` (`id_detail`, `id_des`, `desc`) VALUES
(1, 1, '<div>\r\n          <p>\r\n            Pantai Kuta terkenal memiliki ombak yang bagus untuk\r\n            olahraga selancar (surfing), terutama bagi peselancar pemula.\r\n            Selain keindahan pantai, wisata pantai Kuta juga menawarkan\r\n            berbagai jenis hiburan seperti bar, restoran, pertokoan, restoran,\r\n            hotel, dan toko-toko kelontong, serta pedagang kaki lima di\r\n            sepanjang pantai menuju Pantai Legian.\r\n          </p>\r\n        </div>\r\n        <div>\r\n          <h2>Akses</h2>\r\n          <p>\r\n            Pantai Kuta dapat ditempuh dengan waktu sekitar 10 menit\r\n            dari Bandara Internasional Ngurah Rai dalam kondisi jalanan\r\n            lancar.\r\n          </p>\r\n        </div>\r\n        <div>\r\n          <h2>Fasilitas</h2>\r\n          <p>\r\n            Sebagai tempat wisata pantai, pantai Kuta dilengkapi lahan parkir\r\n            di sepanjang pantai, kamar mandi umum, payung pantai, kios makanan\r\n            dan minuman, serta tempat penyewaan papan selancar.\r\n          </p>\r\n        </div>'),
(2, 2, '<div>\r\n          <p>\r\n            Pantai Kuta terkenal memiliki ombak yang bagus untuk\r\n            olahraga selancar (surfing), terutama bagi peselancar pemula.\r\n            Selain keindahan pantai, wisata pantai Kuta juga menawarkan\r\n            berbagai jenis hiburan seperti bar, restoran, pertokoan, restoran,\r\n            hotel, dan toko-toko kelontong, serta pedagang kaki lima di\r\n            sepanjang pantai menuju Pantai Legian.\r\n          </p>\r\n        </div>\r\n        <div>\r\n          <h2>Akses</h2>\r\n          <p>\r\n            Pantai Kuta dapat ditempuh dengan waktu sekitar 10 menit\r\n            dari Bandara Internasional Ngurah Rai dalam kondisi jalanan\r\n            lancar.\r\n          </p>\r\n        </div>\r\n        <div>\r\n          <h2>Fasilitas</h2>\r\n          <p>\r\n            Sebagai tempat wisata pantai, pantai Kuta dilengkapi lahan parkir\r\n            di sepanjang pantai, kamar mandi umum, payung pantai, kios makanan\r\n            dan minuman, serta tempat penyewaan papan selancar.\r\n          </p>\r\n        </div>');

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_image`
--

CREATE TABLE `detail_image` (
  `id_img` int(11) NOT NULL,
  `id_detail` int(11) NOT NULL,
  `gambar` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `detail_image`
--

INSERT INTO `detail_image` (`id_img`, `id_detail`, `gambar`) VALUES
(1, 1, 'garuda.png'),
(2, 1, 'kuta.png'),
(3, 2, 'batur.png');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `destination`
--
ALTER TABLE `destination`
  ADD PRIMARY KEY (`id_des`);

--
-- Indeks untuk tabel `detail`
--
ALTER TABLE `detail`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_des` (`id_des`);

--
-- Indeks untuk tabel `detail_image`
--
ALTER TABLE `detail_image`
  ADD PRIMARY KEY (`id_img`),
  ADD KEY `id_detail` (`id_detail`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `destination`
--
ALTER TABLE `destination`
  MODIFY `id_des` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `detail`
--
ALTER TABLE `detail`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `detail_image`
--
ALTER TABLE `detail_image`
  MODIFY `id_img` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `detail`
--
ALTER TABLE `detail`
  ADD CONSTRAINT `id_des` FOREIGN KEY (`id_des`) REFERENCES `destination` (`id_des`);

--
-- Ketidakleluasaan untuk tabel `detail_image`
--
ALTER TABLE `detail_image`
  ADD CONSTRAINT `id_detail` FOREIGN KEY (`id_detail`) REFERENCES `detail` (`id_detail`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
