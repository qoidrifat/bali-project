-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 08 Jun 2024 pada 20.28
-- Versi server: 10.4.25-MariaDB
-- Versi PHP: 8.1.10

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
-- Struktur dari tabel `bookings`
--

CREATE TABLE `bookings` (
  `id_bookings` int(11) NOT NULL,
  `id_destinations` int(11) NOT NULL,
  `check_in_date` date NOT NULL,
  `check_out_date` date DEFAULT NULL,
  `rooms` int(11) NOT NULL,
  `id_hotel` int(11) NOT NULL,
  `detail` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `bookings`
--

INSERT INTO `bookings` (`id_bookings`, `id_destinations`, `check_in_date`, `check_out_date`, `rooms`, `id_hotel`, `detail`) VALUES
(1, 1, '2024-06-10', '2024-06-12', 1, 1, 'https://www.tiket.com/hotel/indonesia/hotel-1'),
(2, 1, '2024-06-11', '2024-06-13', 1, 2, 'https://www.tiket.com/hotel/indonesia/hotel-2'),
(3, 1, '2024-06-12', '2024-06-14', 1, 3, 'https://www.tiket.com/hotel/indonesia/hotel-3'),
(4, 1, '2024-06-13', '2024-06-15', 1, 4, 'https://www.tiket.com/hotel/indonesia/hotel-4'),
(5, 1, '2024-06-14', '2024-06-16', 1, 5, 'https://www.tiket.com/hotel/indonesia/hotel-5'),
(6, 1, '2024-06-15', '2024-06-17', 1, 6, 'https://www.tiket.com/hotel/indonesia/hotel-6'),
(7, 1, '2024-06-16', '2024-06-18', 1, 7, 'https://www.tiket.com/hotel/indonesia/hotel-7'),
(8, 1, '2024-06-17', '2024-06-19', 1, 8, 'https://www.tiket.com/hotel/indonesia/hotel-8'),
(9, 1, '2024-06-18', '2024-06-20', 1, 9, 'https://www.tiket.com/hotel/indonesia/hotel-9'),
(10, 1, '2024-06-19', '2024-06-21', 1, 10, 'https://www.tiket.com/hotel/indonesia/hotel-10'),
(11, 2, '2024-06-10', '2024-06-12', 1, 6, 'https://www.tiket.com/hotel/indonesia/neo-denpasar-108001534490316188?room=1&adult=1&checkin=2024-06-10&checkout=2024-06-12&referrer=https%3A%2F%2Fwww.tiket.com%2Fhotel%2Fsearch%3Froom%3D1%26adult%3D1%26checkin%3D2024-06-10%26checkout%3D2024-06-12%26type%3DCITY%26q%3DDenpasar%26id%3Ddenpasar-108001534490277507%26label%3DCITY&utm_page=searchResultPage'),
(12, 2, '2024-06-11', '2024-06-13', 1, 7, 'https://www.tiket.com/hotel/indonesia/prime-plaza-hotel-sanur-bali-formerly-sanur-paradise-plaza-hotel-108001534490296281?room=1&adult=1&checkin=2024-06-11&checkout=2024-06-13&referrer=https%3A%2F%2Fwww.tiket.com%2Fhotel%2Fsearch%3Froom%3D1%26adult%3D1%26checkin%3D2024-06-11%26checkout%3D2024-06-13%26type%3DCITY%26q%3DDenpasar%26id%3Ddenpasar-108001534490277507%26label%3DCITY&utm_page=searchResultPage'),
(13, 2, '2024-06-12', '2024-06-14', 1, 8, 'https://www.tiket.com/hotel/indonesia/aston-denpasar-hotel-and-convention-center-108001534490295182?room=1&adult=1&checkin=2024-06-12&checkout=2024-06-14&referrer=https%3A%2F%2Fwww.tiket.com%2Fhotel%2Fsearch%3Froom%3D1%26adult%3D1%26checkin%3D2024-06-12%26checkout%3D2024-06-14%26type%3DCITY%26q%3DDenpasar%26id%3Ddenpasar-108001534490277507%26label%3DCITY&utm_page=searchResultPage'),
(14, 2, '2024-06-13', '2024-06-15', 1, 9, 'https://www.tiket.com/hotel/indonesia/the-cakra-hotel-108001534490302960?room=1&adult=1&checkin=2024-06-13&checkout=2024-06-15&referrer=https%3A%2F%2Fwww.tiket.com%2Fhotel%2Fsearch%3Froom%3D1%26adult%3D1%26checkin%3D2024-06-13%26checkout%3D2024-06-15%26type%3DCITY%26q%3DDenpasar%26id%3Ddenpasar-108001534490277507%26label%3DCITY&utm_page=searchResultPage'),
(15, 2, '2024-06-14', '2024-06-16', 1, 10, 'https://www.tiket.com/hotel/indonesia/sanur-resort-watujimbar-705001714630217624?room=1&adult=1&checkin=2024-06-14&checkout=2024-06-16&referrer=https%3A%2F%2Fwww.tiket.com%2Fhotel%2Fsearch%3Froom%3D1%26adult%3D1%26checkin%3D2024-06-14%26checkout%3D2024-06-16%26type%3DCITY%26q%3DDenpasar%26id%3Ddenpasar-108001534490277507%26label%3DCITY&utm_page=searchResultPage'),
(16, 2, '2024-06-15', '2024-06-17', 1, 6, 'https://www.tiket.com/hotel/indonesia/neo-denpasar-108001534490316188?room=1&adult=1&checkin=2024-06-15&checkout=2024-06-17&referrer=https%3A%2F%2Fwww.tiket.com%2Fhotel%2Fsearch%3Froom%3D1%26adult%3D1%26checkin%3D2024-06-15%26checkout%3D2024-06-17%26type%3DCITY%26q%3DDenpasar%26id%3Ddenpasar-108001534490277507%26label%3DCITY&utm_page=searchResultPage'),
(17, 2, '2024-06-16', '2024-06-18', 1, 7, 'https://www.tiket.com/hotel/indonesia/prime-plaza-hotel-sanur-bali-formerly-sanur-paradise-plaza-hotel-108001534490296281?room=1&adult=1&checkin=2024-06-16&checkout=2024-06-18&referrer=https%3A%2F%2Fwww.tiket.com%2Fhotel%2Fsearch%3Froom%3D1%26adult%3D1%26checkin%3D2024-06-16%26checkout%3D2024-06-18%26type%3DCITY%26q%3DDenpasar%26id%3Ddenpasar-108001534490277507%26label%3DCITY&utm_page=searchResultPage'),
(18, 2, '2024-06-17', '2024-06-19', 1, 10, 'https://www.tiket.com/hotel/indonesia/sanur-resort-watujimbar-705001714630217624?room=1&adult=1&checkin=2024-06-17&checkout=2024-06-19&referrer=https%3A%2F%2Fwww.tiket.com%2Fhotel%2Fsearch%3Froom%3D1%26adult%3D1%26checkin%3D2024-06-17%26checkout%3D2024-06-19%26type%3DCITY%26q%3DDenpasar%26id%3Ddenpasar-108001534490277507%26label%3DCITY&utm_page=searchResultPage'),
(19, 2, '2024-06-18', '2024-06-20', 1, 9, 'https://www.tiket.com/hotel/indonesia/the-cakra-hotel-108001534490302960?room=1&adult=1&checkin=2024-06-18&checkout=2024-06-20&referrer=https%3A%2F%2Fwww.tiket.com%2Fhotel%2Fsearch%3Froom%3D1%26adult%3D1%26checkin%3D2024-06-18%26checkout%3D2024-06-20%26type%3DCITY%26q%3DDenpasar%26id%3Ddenpasar-108001534490277507%26label%3DCITY&utm_page=searchResultPage'),
(20, 2, '2024-06-19', '2024-06-21', 1, 8, 'https://www.tiket.com/hotel/indonesia/aston-denpasar-hotel-and-convention-center-108001534490295182?room=1&adult=1&checkin=2024-06-19&checkout=2024-06-21&referrer=https%3A%2F%2Fwww.tiket.com%2Fhotel%2Fsearch%3Froom%3D1%26adult%3D1%26checkin%3D2024-06-19%26checkout%3D2024-06-21%26type%3DCITY%26q%3DDenpasar%26id%3Ddenpasar-108001534490277507%26label%3DCITY&utm_page=searchResultPage');

-- --------------------------------------------------------

--
-- Struktur dari tabel `buses`
--

CREATE TABLE `buses` (
  `buses_id` int(11) NOT NULL,
  `operator` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `buses`
--

INSERT INTO `buses` (`buses_id`, `operator`) VALUES
(1, 'Angkasa Trans Jaya'),
(2, 'MTrans'),
(3, 'Ladju Transport'),
(4, 'Wafaa Holiday'),
(5, 'Sarwonadhi Trans'),
(6, 'Gunung Harta'),
(7, 'Bali Trans');

-- --------------------------------------------------------

--
-- Struktur dari tabel `destination`
--

CREATE TABLE `destination` (
  `id_des` int(11) NOT NULL,
  `nama_des` varchar(100) NOT NULL,
  `gambar` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
-- Struktur dari tabel `destinations`
--

CREATE TABLE `destinations` (
  `id_destinations` int(11) NOT NULL,
  `destinations` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `destinations`
--

INSERT INTO `destinations` (`id_destinations`, `destinations`) VALUES
(1, '1-Surabaya'),
(2, '2-Denpasar');

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail`
--

CREATE TABLE `detail` (
  `id_detail` int(11) NOT NULL,
  `id_des` int(11) NOT NULL,
  `desc` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `detail_image`
--

INSERT INTO `detail_image` (`id_img`, `id_detail`, `gambar`) VALUES
(1, 1, 'garuda.png'),
(2, 1, 'kuta.png'),
(3, 2, 'batur.png');

-- --------------------------------------------------------

--
-- Struktur dari tabel `from_city`
--

CREATE TABLE `from_city` (
  `from_id` int(11) NOT NULL,
  `city` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `from_city`
--

INSERT INTO `from_city` (`from_id`, `city`) VALUES
(1, 'Surabaya'),
(2, 'Denpasar');

-- --------------------------------------------------------

--
-- Struktur dari tabel `hotel`
--

CREATE TABLE `hotel` (
  `id_hotel` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `hotel`
--

INSERT INTO `hotel` (`id_hotel`, `name`) VALUES
(1, 'd\'primahotel Jemursari Surabaya'),
(2, 'The Life Styles Hotel City Center Surabaya'),
(3, 'Quest Hotel Darmo - Surabaya by ASTON'),
(4, 'Garden Palace Hotel Surabaya'),
(5, 'MaxOneHotels at Tidar Surabaya'),
(6, 'Neo Denpasar Hotel by ASTON'),
(7, 'Prime Plaza Hotel Sanur - Bali'),
(8, 'ASTON Denpasar Hotel and Convention Center'),
(9, 'The Cakra Hotel'),
(10, 'Sanur Resort Watujimbar');

-- --------------------------------------------------------

--
-- Struktur dari tabel `routes`
--

CREATE TABLE `routes` (
  `routes_id` int(11) NOT NULL,
  `from_id` int(11) NOT NULL,
  `to_id` int(11) NOT NULL,
  `departure_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `jumlah_kursi` int(11) NOT NULL,
  `buses_id` int(11) DEFAULT NULL,
  `detail` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `routes`
--

INSERT INTO `routes` (`routes_id`, `from_id`, `to_id`, `departure_date`, `return_date`, `jumlah_kursi`, `buses_id`, `detail`) VALUES
(1, 1, 2, '2024-06-10', NULL, 1, 6, 'https://www.tiket.com/bus-travel/detail?origin=63d0b5bb2cbd5656536b9f28&destination=63d0b5be2cbd5656536ba1ac&departureDate=2024-06-10&returnDate=2024-06-08&adult=1&tripType=oneway&journeyType=depart&scheduleId=redbus-1076765449090817402-27542-286697-27558-2274266-735254ac320bef419eae61782bd03d11&scheduleHash=892ad406dbaedf01ef595d1aa742f6dbb319f88b'),
(2, 2, 1, '2024-06-12', '2024-06-14', 2, 1, 'https://www.tiket.com/bus-travel/detail?origin=63d0b5be2cbd5656536ba1ac&destination=63d0b5bb2cbd5656536b9f28&departureDate=2024-06-12&returnDate=2024-06-08&adult=2&tripType=oneway&journeyType=depart&scheduleId=redbus-1076763749110581728-27558-8686279-27542-3647358-735254ac320bef419eae61782bd03d11&scheduleHash=4bf5860076ca0de72e45d5057a00aeffab2351d6'),
(5, 1, 2, '2024-06-11', NULL, 2, 2, 'https://www.tiket.com/bus-travel/detail?origin=63d0b5bb2cbd5656536b9f28&destination=63d0b5be2cbd5656536ba1ac&departureDate=2024-06-11&returnDate=2024-06-08&adult=1&tripType=oneway&journeyType=depart&scheduleId=redbus-1076765449100723755-27542-8686469-27558-2870301-735254ac320bef419eae61782bd03d11&scheduleHash=9ca3572d548fa1d0ecf538e49a302307428d6db4'),
(6, 1, 2, '2024-06-13', NULL, 1, 3, 'https://www.tiket.com/bus-travel/detail?origin=63d0b5bb2cbd5656536b9f28&destination=63d0b5be2cbd5656536ba1ac&departureDate=2024-06-13&returnDate=2024-06-08&adult=1&tripType=oneway&journeyType=depart&scheduleId=redbus-1076765449120150883-27542-269822-27558-1102001-735254ac320bef419eae61782bd03d11&scheduleHash=d7c4b04be73018f7fe3ccd20fb70d6edb9725010'),
(7, 1, 2, '2024-06-14', NULL, 2, 4, 'https://www.tiket.com/bus-travel/detail?origin=63d0b5bb2cbd5656536b9f28&destination=63d0b5be2cbd5656536ba1ac&departureDate=2024-06-14&returnDate=2024-06-08&adult=2&tripType=oneway&journeyType=depart&scheduleId=redbus-1076765449131166815-27542-12500578-27558-12500942-735254ac320bef419eae61782bd03d11&scheduleHash=954e5b3cac03827a6a7075d0afe36c2aae4e422b'),
(8, 1, 2, '2024-06-15', NULL, 1, 7, 'https://www.tiket.com/bus-travel/detail?origin=63d0b5bb2cbd5656536b9f28&destination=63d0b5be2cbd5656536ba1ac&departureDate=2024-06-15&returnDate=2024-06-08&adult=1&tripType=oneway&journeyType=depart&scheduleId=redbus-1076765449140679165-27542-7515124-27558-7515130-735254ac320bef419eae61782bd03d11&scheduleHash=5cf25951cbed86ca585d00f627489c870da6d943'),
(9, 1, 2, '2024-06-16', NULL, 1, 2, 'https://www.tiket.com/bus-travel/detail?origin=63d0b5bb2cbd5656536b9f28&destination=63d0b5be2cbd5656536ba1ac&departureDate=2024-06-16&returnDate=2024-06-08&adult=1&tripType=oneway&journeyType=depart&scheduleId=redbus-1076765449150723755-27542-8686469-27558-2870301-735254ac320bef419eae61782bd03d11&scheduleHash=4f6e16790aaa548db34fe54feb5d5c3b01347310'),
(10, 1, 2, '2024-06-17', NULL, 2, 3, 'https://www.tiket.com/bus-travel/detail?origin=63d0b5bb2cbd5656536b9f28&destination=63d0b5be2cbd5656536ba1ac&departureDate=2024-06-17&returnDate=2024-06-08&adult=1&tripType=oneway&journeyType=depart&scheduleId=redbus-1076765449160150883-27542-4635520-27558-1102001-735254ac320bef419eae61782bd03d11&scheduleHash=52a49747375c303a006985fd5e9180071e4ebeed'),
(13, 1, 2, '2024-06-18', NULL, 1, 6, 'https://www.tiket.com/bus-travel/detail?origin=63d0b5bb2cbd5656536b9f28&destination=63d0b5be2cbd5656536ba1ac&departureDate=2024-06-18&returnDate=2024-06-08&adult=1&tripType=oneway&journeyType=depart&scheduleId=redbus-1076765449170174433-27542-286697-27558-2274266-735254ac320bef419eae61782bd03d11&scheduleHash=47e2469e5f1af228f1a3236a3c50e927a5848aa0'),
(14, 1, 2, '2024-06-19', NULL, 2, 5, 'https://www.tiket.com/bus-travel/detail?origin=63d0b5bb2cbd5656536b9f28&destination=63d0b5be2cbd5656536ba1ac&departureDate=2024-06-19&returnDate=2024-06-08&adult=1&tripType=oneway&journeyType=depart&scheduleId=redbus-1076765449180410058-27542-1104344-27558-1104334-735254ac320bef419eae61782bd03d11&scheduleHash=92b64f54ce9f0cb0efb8f8e0998428fde8923568'),
(34, 1, 2, '2024-06-12', NULL, 1, 5, 'https://www.tiket.com/bus-travel/detail?origin=63d0b5bb2cbd5656536b9f28&destination=63d0b5be2cbd5656536ba1ac&departureDate=2024-06-12&returnDate=2024-06-08&adult=1&tripType=oneway&journeyType=depart&scheduleId=redbus-1076765449110410058-27542-1104344-27558-1104333-735254ac320bef419eae61782bd03d11&scheduleHash=58810eff85566c29a6eb19cc34232a01ae74ff76'),
(36, 2, 1, '2024-06-10', NULL, 1, 2, 'https://www.tiket.com/bus-travel/detail?origin=63d0b5be2cbd5656536ba1ac&destination=63d0b5bb2cbd5656536b9f28&departureDate=2024-06-10&returnDate=2024-06-08&adult=2&tripType=oneway&journeyType=depart&scheduleId=redbus-1076763749090723759-27558-8686215-27542-2870422-735254ac320bef419eae61782bd03d11&scheduleHash=d326a86498553e30718901e2f4f951f8acacbfc4'),
(37, 2, 1, '2024-06-11', NULL, 2, 1, 'https://www.tiket.com/bus-travel/detail?origin=63d0b5be2cbd5656536ba1ac&destination=63d0b5bb2cbd5656536b9f28&departureDate=2024-06-11&returnDate=2024-06-08&adult=2&tripType=oneway&journeyType=depart&scheduleId=redbus-1076763749100581728-27558-8686267-27542-3647358-735254ac320bef419eae61782bd03d11&scheduleHash=c59e7828b29045c30df654d99f0c2fce0f90a809'),
(38, 2, 1, '2024-06-12', NULL, 1, 5, 'https://www.tiket.com/bus-travel/detail?origin=63d0b5be2cbd5656536ba1ac&destination=63d0b5bb2cbd5656536b9f28&departureDate=2024-06-12&returnDate=2024-06-08&adult=2&tripType=oneway&journeyType=depart&scheduleId=redbus-1076763749110409823-27558-1104333-27542-3292406-735254ac320bef419eae61782bd03d11&scheduleHash=da42ed2a7edb69890e5b88e1f81a5fd049f9a685'),
(39, 2, 1, '2024-06-13', NULL, 2, 3, 'https://www.tiket.com/bus-travel/detail?origin=63d0b5be2cbd5656536ba1ac&destination=63d0b5bb2cbd5656536b9f28&departureDate=2024-06-13&returnDate=2024-06-08&adult=2&tripType=oneway&journeyType=depart&scheduleId=redbus-1076763749120151056-27558-1102001-27542-269822-735254ac320bef419eae61782bd03d11&scheduleHash=e04237adc86e1493d3f7d93041dc50c93dd49c8'),
(40, 2, 1, '2024-06-14', NULL, 1, 7, 'https://www.tiket.com/bus-travel/detail?origin=63d0b5be2cbd5656536ba1ac&destination=63d0b5bb2cbd5656536b9f28&departureDate=2024-06-14&returnDate=2024-06-08&adult=1&tripType=oneway&journeyType=depart&scheduleId=redbus-1076763749130679213-27558-7515130-27542-7515140-735254ac320bef419eae61782bd03d11&scheduleHash=409773918e010d49e98e780e1e3b8d7eb30f8ad5'),
(41, 2, 1, '2024-06-15', NULL, 1, 3, 'https://www.tiket.com/bus-travel/detail?origin=63d0b5be2cbd5656536ba1ac&destination=63d0b5bb2cbd5656536b9f28&departureDate=2024-06-15&returnDate=2024-06-08&adult=1&tripType=oneway&journeyType=depart&scheduleId=redbus-1076763749140151056-27558-1102001-27542-269822-735254ac320bef419eae61782bd03d11&scheduleHash=82e83fec33db97a65f6ab9d0562eae3d4ba22025'),
(42, 2, 1, '2024-06-16', NULL, 2, 6, 'https://www.tiket.com/bus-travel/detail?origin=63d0b5be2cbd5656536ba1ac&destination=63d0b5bb2cbd5656536b9f28&departureDate=2024-06-16&returnDate=2024-06-08&adult=2&tripType=oneway&journeyType=depart&scheduleId=redbus-1076763749150817356-27558-2274266-27542-286678-735254ac320bef419eae61782bd03d11&scheduleHash=5ab3d8e2244a305a0eb8a6c7a2882f1b86719866'),
(43, 2, 1, '2024-06-17', NULL, 2, 7, 'https://www.tiket.com/bus-travel/detail?origin=63d0b5be2cbd5656536ba1ac&destination=63d0b5bb2cbd5656536b9f28&departureDate=2024-06-17&returnDate=2024-06-08&adult=2&tripType=oneway&journeyType=depart&scheduleId=redbus-1076763749160679213-27558-7515130-27542-7515124-735254ac320bef419eae61782bd03d11&scheduleHash=3f51d57cfd137f481be3c23aa45d25b364d4fa41'),
(44, 2, 1, '2024-06-18', NULL, 1, 2, 'https://www.tiket.com/bus-travel/detail?origin=63d0b5be2cbd5656536ba1ac&destination=63d0b5bb2cbd5656536b9f28&departureDate=2024-06-18&returnDate=2024-06-08&adult=1&tripType=oneway&journeyType=depart&scheduleId=redbus-1076763749170511156-27558-8686216-27542-2870422-735254ac320bef419eae61782bd03d11&scheduleHash=c29ff0805b63fe124d523f2022248e3aac0db492'),
(45, 2, 1, '2024-06-19', NULL, 1, 5, 'https://www.tiket.com/bus-travel/detail?origin=63d0b5be2cbd5656536ba1ac&destination=63d0b5bb2cbd5656536b9f28&departureDate=2024-06-19&returnDate=2024-06-08&adult=2&tripType=oneway&journeyType=depart&scheduleId=redbus-1076763749180409823-27558-1104333-27542-1104288-735254ac320bef419eae61782bd03d11&scheduleHash=fe163193a7330a32d43156bcf987fc009151387d');

-- --------------------------------------------------------

--
-- Struktur dari tabel `to_city`
--

CREATE TABLE `to_city` (
  `to_id` int(11) NOT NULL,
  `city` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `to_city`
--

INSERT INTO `to_city` (`to_id`, `city`) VALUES
(1, 'Surabaya'),
(2, 'Denpasar');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id_bookings`),
  ADD KEY `id_hotel` (`id_hotel`),
  ADD KEY `id_destinations` (`id_destinations`);

--
-- Indeks untuk tabel `buses`
--
ALTER TABLE `buses`
  ADD PRIMARY KEY (`buses_id`);

--
-- Indeks untuk tabel `destination`
--
ALTER TABLE `destination`
  ADD PRIMARY KEY (`id_des`);

--
-- Indeks untuk tabel `destinations`
--
ALTER TABLE `destinations`
  ADD PRIMARY KEY (`id_destinations`);

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
-- Indeks untuk tabel `from_city`
--
ALTER TABLE `from_city`
  ADD PRIMARY KEY (`from_id`);

--
-- Indeks untuk tabel `hotel`
--
ALTER TABLE `hotel`
  ADD PRIMARY KEY (`id_hotel`);

--
-- Indeks untuk tabel `routes`
--
ALTER TABLE `routes`
  ADD PRIMARY KEY (`routes_id`),
  ADD KEY `bus_id` (`buses_id`),
  ADD KEY `from_id` (`from_id`),
  ADD KEY `to_id` (`to_id`);

--
-- Indeks untuk tabel `to_city`
--
ALTER TABLE `to_city`
  ADD PRIMARY KEY (`to_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id_bookings` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `buses`
--
ALTER TABLE `buses`
  MODIFY `buses_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `destination`
--
ALTER TABLE `destination`
  MODIFY `id_des` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `destinations`
--
ALTER TABLE `destinations`
  MODIFY `id_destinations` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
-- AUTO_INCREMENT untuk tabel `from_city`
--
ALTER TABLE `from_city`
  MODIFY `from_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `hotel`
--
ALTER TABLE `hotel`
  MODIFY `id_hotel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `routes`
--
ALTER TABLE `routes`
  MODIFY `routes_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT untuk tabel `to_city`
--
ALTER TABLE `to_city`
  MODIFY `to_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`id_hotel`) REFERENCES `hotel` (`id_hotel`),
  ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`id_destinations`) REFERENCES `destinations` (`id_destinations`);

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

--
-- Ketidakleluasaan untuk tabel `routes`
--
ALTER TABLE `routes`
  ADD CONSTRAINT `routes_ibfk_1` FOREIGN KEY (`buses_id`) REFERENCES `buses` (`buses_id`),
  ADD CONSTRAINT `routes_ibfk_2` FOREIGN KEY (`from_id`) REFERENCES `from_city` (`from_id`),
  ADD CONSTRAINT `routes_ibfk_3` FOREIGN KEY (`to_id`) REFERENCES `to_city` (`to_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
