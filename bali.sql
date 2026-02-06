-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 26 Jun 2024 pada 04.47
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
-- Struktur dari tabel `bookings_hotel`
--

CREATE TABLE `bookings_hotel` (
  `id_bookings` int(11) NOT NULL,
  `id_destinations` int(11) NOT NULL,
  `check_in_date` date NOT NULL,
  `check_out_date` date DEFAULT NULL,
  `rooms` int(11) NOT NULL,
  `id_hotel` int(11) NOT NULL,
  `detail` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `bookings_hotel`
--

INSERT INTO `bookings_hotel` (`id_bookings`, `id_destinations`, `check_in_date`, `check_out_date`, `rooms`, `id_hotel`, `detail`) VALUES
(1, 1, '2024-07-15', '2024-07-17', 1, 1, 'https://www.tiket.com/hotel/indonesia/dprimahotel-jemursari-surabaya-609001695177630477?room=1&adult=1&checkin=2024-07-15&checkout=2024-07-17&referrer=https%3A%2F%2Fwww.tiket.com%2Fhotel%2Fsearch%3Froom%3D1%26adult%3D1%26id%3Dsurabaya-108001534490276270%26type%3DCITY%26q%3DSurabaya%26checkin%3D2024-07-15%26checkout%3D2024-07-17&utm_page=searchResultPage'),
(2, 1, '2024-07-16', '2024-07-18', 2, 2, 'https://www.tiket.com/hotel/indonesia/the-life-styles-hotel-city-center-surabaya-701001704880465439?room=2&adult=2&checkin=2024-07-16&checkout=2024-07-18&referrer=https%3A%2F%2Fwww.tiket.com%2Fhotel%2Fsearch%3Froom%3D2%26adult%3D2%26checkin%3D2024-07-16%26checkout%3D2024-07-18%26type%3DCITY%26q%3DSurabaya%26id%3Dsurabaya-108001534490276270&utm_page=searchResultPage'),
(3, 1, '2024-07-17', '2024-07-19', 1, 4, 'https://www.tiket.com/hotel/indonesia/varna-culture-hotel-tunjungan-surabaya-108001534490321068?room=1&adult=1&checkin=2024-07-17&checkout=2024-07-19&referrer=https%3A%2F%2Fwww.tiket.com%2Fhotel%2Fsearch%3Froom%3D1%26adult%3D1%26checkin%3D2024-07-17%26checkout%3D2024-07-19%26type%3DCITY%26q%3DSurabaya%26id%3Dsurabaya-108001534490276270&utm_page=searchResultPage'),
(4, 1, '2024-07-18', '2024-07-20', 1, 3, 'https://www.tiket.com/hotel/indonesia/quest-hotel-surabaya-108001534490311070?room=1&adult=1&checkin=2024-07-18&checkout=2024-07-20&referrer=https%3A%2F%2Fwww.tiket.com%2Fhotel%2Fsearch%3Froom%3D1%26adult%3D1%26checkin%3D2024-07-18%26checkout%3D2024-07-20%26type%3DCITY%26q%3DSurabaya%26id%3Dsurabaya-108001534490276270&utm_page=searchResultPage'),
(11, 2, '2024-07-15', '2024-07-17', 1, 6, 'https://www.tiket.com/hotel/indonesia/neo-denpasar-108001534490316188?room=1&adult=1&checkin=2024-07-15&checkout=2024-07-17&referrer=https%3A%2F%2Fwww.tiket.com%2Fhotel%2Fsearch%3Froom%3D1%26adult%3D1%26checkin%3D2024-07-15%26checkout%3D2024-07-17%26type%3DCITY%26q%3DDenpasar%26id%3Ddenpasar-108001534490277507%26label%3DCITY&utm_page=searchResultPage'),
(12, 2, '2024-07-16', '2024-07-18', 1, 7, 'https://www.tiket.com/hotel/indonesia/prime-plaza-hotel-sanur-bali-formerly-sanur-paradise-plaza-hotel-108001534490296281?room=1&adult=1&checkin=2024-07-16&checkout=2024-07-18&referrer=https%3A%2F%2Fwww.tiket.com%2Fhotel%2Fsearch%3Froom%3D1%26adult%3D1%26checkin%3D2024-07-16%26checkout%3D2024-07-18%26type%3DCITY%26q%3DDenpasar%26id%3Ddenpasar-108001534490277507%26label%3DCITY&utm_page=searchResultPage'),
(13, 2, '2024-07-17', '2024-07-19', 1, 8, 'https://www.tiket.com/hotel/indonesia/aston-denpasar-hotel-and-convention-center-108001534490295182?room=1&adult=1&checkin=2024-07-17&checkout=2024-07-19&referrer=https%3A%2F%2Fwww.tiket.com%2Fhotel%2Fsearch%3Froom%3D1%26adult%3D1%26checkin%3D2024-07-17%26checkout%3D2024-07-19%26type%3DCITY%26q%3DDenpasar%26id%3Ddenpasar-108001534490277507%26label%3DCITY&utm_page=searchResultPage'),
(14, 2, '2024-07-18', '2024-07-20', 1, 9, 'https://www.tiket.com/hotel/indonesia/the-cakra-hotel-108001534490302960?room=1&adult=1&checkin=2024-07-18&checkout=2024-07-20&referrer=https%3A%2F%2Fwww.tiket.com%2Fhotel%2Fsearch%3Froom%3D1%26adult%3D1%26checkin%3D2024-07-18%26checkout%3D2024-07-20%26type%3DCITY%26q%3DDenpasar%26id%3Ddenpasar-108001534490277507%26label%3DCITY&utm_page=searchResultPage');

-- --------------------------------------------------------

--
-- Struktur dari tabel `bookings_mobil`
--

CREATE TABLE `bookings_mobil` (
  `id_bm` int(11) NOT NULL,
  `id_car` int(11) DEFAULT NULL,
  `id_destinations` int(11) DEFAULT NULL,
  `check_in_date` date DEFAULT NULL,
  `check_out_date` date DEFAULT NULL,
  `rooms` int(11) DEFAULT NULL,
  `detail` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `bookings_mobil`
--

INSERT INTO `bookings_mobil` (`id_bm`, `id_car`, `id_destinations`, `check_in_date`, `check_out_date`, `rooms`, `detail`) VALUES
(1, 1, 2, '2024-07-15', '2024-07-17', 1, 'https://www.tiket.com/sewa-mobil/review?placeId=ChIJ24BeDptA0i0RSje5zOg0c-I&from=Denpasar&date=2024-07-15&totalDays=2&hour=06&minute=00&minPickupDate=2024-07-15&minPickupTime=00%3A00&maxPickupTime=23%3A59&pickupOffsetMinute=180&timeZone=Asia%2FBangkok&driverOption=withoutDriver&latitude=-8.670458199999999&longitude=115.2126293&carRegionalId=8d0bba80-0163-4047-b10d-64ca3ca0ef27&type=AUTOMATIC'),
(2, 2, 2, '2024-07-16', '2024-07-18', 2, 'https://www.tiket.com/sewa-mobil/review?placeId=ChIJ24BeDptA0i0RSje5zOg0c-I&from=Denpasar&date=2024-07-16&totalDays=2&hour=06&minute=00&minPickupDate=2024-07-16&minPickupTime=00%3A00&maxPickupTime=23%3A59&pickupOffsetMinute=180&timeZone=Asia%2FBangkok&driverOption=withoutDriver&latitude=-8.670458199999999&longitude=115.2126293&carRegionalId=48830f5c-f272-4a76-998d-f288e1df790f&type=AUTOMATIC'),
(3, 3, 2, '2024-07-17', '2024-07-19', 1, 'https://www.tiket.com/sewa-mobil/review?placeId=ChIJ24BeDptA0i0RSje5zOg0c-I&from=Denpasar&date=2024-07-17&totalDays=2&hour=06&minute=00&minPickupDate=2024-07-17&minPickupTime=00%3A00&maxPickupTime=23%3A59&pickupOffsetMinute=180&timeZone=Asia%2FBangkok&driverOption=withoutDriver&latitude=-8.670458199999999&longitude=115.2126293&carRegionalId=c4f82640-446d-4b0e-9fe6-47ecdf266778&type=AUTOMATIC'),
(4, 4, 2, '2024-07-18', '2024-07-20', 2, 'https://www.tiket.com/sewa-mobil/review?placeId=ChIJ24BeDptA0i0RSje5zOg0c-I&from=Denpasar&date=2024-07-18&totalDays=2&hour=06&minute=00&minPickupDate=2024-07-18&minPickupTime=00%3A00&maxPickupTime=23%3A59&pickupOffsetMinute=180&timeZone=Asia%2FBangkok&driverOption=withoutDriver&latitude=-8.670458199999999&longitude=115.2126293&carRegionalId=1c2399f6-19d2-4dc9-948e-4d5099eef14b&type=AUTOMATIC'),
(6, 6, 1, '2024-07-15', '2024-07-17', 1, 'https://www.tiket.com/sewa-mobil/review?placeId=ChIJf8QaOPj71y0RQL5S43Z6AgM&from=Surabaya&date=2024-07-15&totalDays=2&hour=06&minute=00&minPickupDate=2024-07-15&minPickupTime=00%3A00&maxPickupTime=23%3A59&pickupOffsetMinute=180&timeZone=Asia%2FBangkok&driverOption=withoutDriver&latitude=-7.2574719&longitude=112.7520883&carRegionalId=fb4b26e0-b854-43f4-b0b4-e81f342470ef&type=AUTOMATIC'),
(7, 7, 1, '2024-07-16', '2024-07-18', 1, 'https://www.tiket.com/sewa-mobil/review?placeId=ChIJf8QaOPj71y0RQL5S43Z6AgM&from=Surabaya&date=2024-07-16&totalDays=2&hour=06&minute=00&minPickupDate=2024-07-16&minPickupTime=00%3A00&maxPickupTime=23%3A59&pickupOffsetMinute=180&timeZone=Asia%2FBangkok&driverOption=withoutDriver&latitude=-7.2574719&longitude=112.7520883&carRegionalId=fed189f7-21ea-4215-a868-f85a99232499&type=AUTOMATIC'),
(8, 8, 1, '2024-07-17', '2024-07-19', 1, 'https://www.tiket.com/sewa-mobil/review?placeId=ChIJf8QaOPj71y0RQL5S43Z6AgM&from=Surabaya&date=2024-07-17&totalDays=2&hour=06&minute=00&minPickupDate=2024-07-17&minPickupTime=00%3A00&maxPickupTime=23%3A59&pickupOffsetMinute=180&timeZone=Asia%2FBangkok&driverOption=withoutDriver&latitude=-7.2574719&longitude=112.7520883&carRegionalId=ae3aeb47-9293-40b1-a357-369d8ec45f78&type=AUTOMATIC'),
(9, 9, 1, '2024-07-18', '2024-07-20', 1, 'https://www.tiket.com/sewa-mobil/review?placeId=ChIJf8QaOPj71y0RQL5S43Z6AgM&from=Surabaya&date=2024-07-18&totalDays=2&hour=06&minute=00&minPickupDate=2024-07-18&minPickupTime=00%3A00&maxPickupTime=23%3A59&pickupOffsetMinute=180&timeZone=Asia%2FBangkok&driverOption=withoutDriver&latitude=-7.2574719&longitude=112.7520883&carRegionalId=c5b41607-a405-4d3c-b868-6920f842684c&type=AUTOMATIC');

-- --------------------------------------------------------

--
-- Struktur dari tabel `bookings_pesawat`
--

CREATE TABLE `bookings_pesawat` (
  `id_bp` int(11) NOT NULL,
  `from_id` int(11) DEFAULT NULL,
  `to_id` int(11) DEFAULT NULL,
  `id_pesawat` int(11) DEFAULT NULL,
  `departure_date` date DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `jumlah_kursi` int(11) DEFAULT NULL,
  `detail` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `bookings_pesawat`
--

INSERT INTO `bookings_pesawat` (`id_bp`, `from_id`, `to_id`, `id_pesawat`, `departure_date`, `return_date`, `jumlah_kursi`, `detail`) VALUES
(1, 1, 2, 1, '2024-07-15', '2024-07-17', 1, 'https://www.tiket.com/cart/flight/6679081dcdb1ce14bc54e2a5?d=SUB&a=DPS&date=2024-07-15&adult=1&child=0&infant=0&class=economy&dType=AIRPORT&aType=AIRPORT&dLabel=Surabaya&aLabel=Denpasar-Bali&type=return&flexiFare=true&ret_date=2024-07-17&fid=pKOhqmuAte5toQcdaRrde277HdX5CyKv_KyNLZQkcTOpwwmdk0cXscJ_kvRQDAc11OIYPDhSItr48-NE9Gn_CIjReM3hdiUZ1Fva0kXgTTdMI7CwsnOe9u6kmlTTa9mo'),
(2, 1, 2, 1, '2024-07-16', '2024-07-18', 1, 'https://www.tiket.com/cart/flight/66790896cdb1ce14bc54e36b?d=SUB&a=DPS&date=2024-07-16&adult=1&child=0&infant=0&class=economy&dType=AIRPORT&aType=AIRPORT&dLabel=Surabaya&aLabel=Denpasar-Bali&type=return&flexiFare=true&ret_date=2024-07-18&fid=pKOhqmuAte5toQcdaRrde0XrFF8Poo2g802mFGkOFSTgnY0J2XAvs_j8CCh07ORw8jh2zp2XPTg640X1H5Pkcek4_tgxE3YElP5rOUQtn2Euox7mtyQcxUKdnHPxdO1g'),
(3, 1, 2, 1, '2024-07-17', '2024-07-19', 2, 'https://www.tiket.com/cart/flight/66790993261e255ee04eaa44?d=SUB&a=DPS&date=2024-07-17&adult=2&child=0&infant=0&class=economy&dType=AIRPORT&aType=AIRPORT&dLabel=Surabaya&aLabel=Denpasar-Bali&type=return&flexiFare=true&ret_date=2024-07-19&fid=pKOhqmuAte5toQcdaRrde3gKcwJr10hdOZ7cCQV8GNvTA6Pp133m89oZ9IimL94EeX-p-242pWYyWkPV_FF-V5gBdpe3kby2fF-3BzOLBl-odClVzgLTyDUGtHAxmei-'),
(4, 1, 2, 1, '2024-07-18', '2024-07-20', 2, 'https://www.tiket.com/cart/flight/667909d4cdb1ce14bc54e5a4?d=SUB&a=DPS&date=2024-07-18&adult=2&child=0&infant=0&class=economy&dType=AIRPORT&aType=AIRPORT&dLabel=Surabaya&aLabel=Denpasar-Bali&type=return&flexiFare=true&ret_date=2024-07-20&fid=pKOhqmuAte5toQcdaRrde0T7rQGa9TsjtyUX87Y3ndO3RwxnRw8zLRy4KwoZ1UHBRw9OSRrwivrsGm4-1q-ti9ovzB-QFaBIRCouJv2zvocsZg7JzU7wTuDoyHjWfvFd'),
(5, 2, 1, 2, '2024-07-15', '2024-07-17', 1, 'https://www.tiket.com/cart/flight/66790a87cdb1ce14bc54e6d9?d=DPS&a=SUB&date=2024-07-15&adult=1&child=0&infant=0&class=economy&dType=AIRPORT&aType=AIRPORT&dLabel=Denpasar-Bali&aLabel=Surabaya&type=return&flexiFare=true&ret_date=2024-07-17&fid=RZU3l-sKpQK16PEGQXAcMXdACNFxE1W_8ZIdj597YdOluJxV1g-WdU1Si-1HXpekbq5xhCnMKOzeKiCg6Gh1aWFxAOQy9leCIx0BxQ92Aaa86tEfTW-mIEZGQUX0H-U9Ts3eTTh7Msg3nZhDghGTyg'),
(6, 2, 1, 2, '2024-07-16', '2024-07-18', 2, 'https://www.tiket.com/cart/flight/66790b67cdb1ce14bc54e841?d=DPS&a=SUB&date=2024-07-16&adult=2&child=0&infant=0&class=economy&dType=AIRPORT&aType=AIRPORT&dLabel=Denpasar-Bali&aLabel=Surabaya&type=return&flexiFare=true&ret_date=2024-07-18&fid=RZU3l-sKpQK16PEGQXAcMVSOIWO_t5MH-VRQ53yZZ_-90f_kGan03ZUGQih9cWUSzbKSrCMru3awXhV0bq_gKCQYxg_TiKNN8NijVddCrbDHG3hcUfQ-_rUEbamG1POVC-QSmFwp2uYeLKVWGKTejQ'),
(7, 2, 1, 2, '2024-07-17', '2024-07-19', 1, 'https://www.tiket.com/cart/flight/66790badcdb1ce14bc54e8d5?d=DPS&a=SUB&date=2024-07-17&adult=1&child=0&infant=0&class=economy&dType=AIRPORT&aType=AIRPORT&dLabel=Denpasar-Bali&aLabel=Surabaya&type=return&flexiFare=true&ret_date=2024-07-19&fid=RZU3l-sKpQK16PEGQXAcMT0vCmWB096xdGpt4EIsAVwtQRFqUlJl7Xy-lqctfapk_TrP4jWvn8Fy5rQc0g3JtdHQcAZ93J05Wg1XV2JcnstMCecZ9AotHeFRGI0BsxUMuReIT5EbNHnam1OLUlGDOg'),
(8, 2, 1, 2, '2024-07-18', '2024-07-20', 2, 'https://www.tiket.com/cart/flight/66790bee261e255ee04ead8d?d=DPS&a=SUB&date=2024-07-18&adult=2&child=0&infant=0&class=economy&dType=AIRPORT&aType=AIRPORT&dLabel=Denpasar-Bali&aLabel=Surabaya&type=return&flexiFare=true&ret_date=2024-07-20&fid=RZU3l-sKpQK16PEGQXAcMZIpwDLEniWoVmIyeKcif30nobzsc2g127b1qsfr7GsA7A7J0dQsdPLaucSmlJt8SPFZxeW80Qs_zaxDzwcNERcxCZVVMV2CHOSWMr30PniumEmduOlF-fumWV10XpEgJw');

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
-- Struktur dari tabel `car`
--

CREATE TABLE `car` (
  `id_car` int(11) NOT NULL,
  `vendor` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `car`
--

INSERT INTO `car` (`id_car`, `vendor`, `name`) VALUES
(1, 'KHAI TRANS', 'Toyota Agya'),
(2, 'BALI MUTIA RENTAL', 'Toyota Grand New Avanza'),
(3, 'BUKIT SARI TRANS', 'Daihatsu Ayla'),
(4, 'ULUN DANU TRANS', 'Daihatsu All New Xenia 2022'),
(5, 'SINGA BALI TRANSPORT', 'Mitsubishi Xpander'),
(6, 'TRAC ASTRA', 'Toyota All New Innova Zenix'),
(7, 'D-TRANS', 'Suzuki Ertiga'),
(8, 'SMART RENT CAR', 'Mitsubishi Xpander Facelift 2021'),
(9, 'TUNJUNG TRANSPORT', 'Toyota All New Avanza 2022'),
(10, 'ERA TRANS', 'Daihatsu Ayla');

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
(1, 1, '<div>\n          <p>\n            Pantai Kuta adalah sebuah tempat pariwisata yang terletak di kecamatan Kuta sebelah selatan Kota Denpasar, Bali, Indonesia. Daerah ini merupakan sebuah tujuan wisata turis mancanegara dan telah menjadi objek wisata andalan Pulau Bali sejak awal tahun 1970-an. Pantai Kuta sering pula disebut sebagai pantai matahari terbenam sebagai lawan dari pantai Sanur. Selain itu, Lapangan Udara I Gusti Ngurah Rai terletak tidak jauh dari Kuta. Sebelum menjadi objek wisata, Kuta merupakan sebuah pelabuhan dagang tempat produk lokal diperdagangkan kepada pembeli dari luar Bali. Pada abad ke-19, Mads Lange, seorang pedagang Denmark, datang ke Bali dan mendirikan basis perdagangan di Kuta. Ia ahli bernegosiasi sehingga dirinya terkenal di antara raja-raja Bali dan Belanda.\n          </p>\n        </div>\n        <div>\n          <h2>Akses</h2>\n          <p>\n           Pantai Kuta Bali terletak di Desa Kuta, Kecamatan Kuta, Kabupaten Badung dan Provinsi Bali, Indonesia. Lokasinya hanya berjarak 5 menit dari Bandara Denpasar bila mengendarai mobil.\nAda banyak taksi yang dapat membawa pengunjung ke Pantai Kuta. Anda dapat menyewa taksi secara meteran atau per jam. Membawa kendaraan pribadi merupakan suatu hal yang tidak dianjurkan karena parkiran yang terbatas dan ketat.\n          </p>\n        </div>\n        <div>\n          <h2>Fasilitas</h2>\n          <p>\n         Wisatawan yang datang ke pantai ini tidak perlu khawatir masalah fasilitas umum karena sudah sangat lengkap. Wisatawan bisa menemukan banyak kantong parkir yang luas jika membawa kendaraan pribadi, Jika ingin menginap atau mencari hotel, wisatawan justru akan dibuat pusing untuk memiliki karena memang ada banyak penginapan di kawasan pantai ini. Mulai dari hotel bintang 2 hingga bintang 5. Beberapa di antaranya, seperti Favehotel dan Mercure Kuta Bali, Jika sudah puas berkeliling dan menikmati pemandangan pantai, wisatawan juga bisa mampir ke kafe atau restoran yang ada di sekitar pantai ini. Selain menyediakan berbagai menu makanan enak dan lezat, pantai juga menyediakan berbagai hiburan menarik, Pantai kuta memiliki pemandangan yang memanjakan mata dengan hamparan pasir putih yang halus dan luas. Di pinggir pantai, wisatawan bisa menikmati pemandangan sambil bersantai di banyak tempat yang disediakan.\n          </p>\n        </div>'),
(2, 2, '<div>\n          <p>\n            Pura Tanah Lot adalah salah satu Pura (Tempat Ibadah Umat Hindu) yang sangat disucikan di Bali, Indonesia. Di sini ada dua Pura yang terletak di atas batu besar. Satu terletak di atas bongkahan batu dan satunya terletak di atas tebing mirip dengan Pura Uluwatu. Pura Tanah Lot ini merupakan bagian dari Pura Dang Kahyangan. Pura Tanah Lot merupakan Pura laut tempat pemujaan dewa-dewa penjaga laut. Tanah Lot terkenal sebagai tempat yang indah untuk melihat matahari terbenam. Sejarah Pura Tanah Lot Bali Indonesia berdasarkan legenda, dikisahkan pada abad ke -15, Bhagawan Dang Hyang Nirartha atau dikenal dengan nama Dang Hyang Dwijendra melakukan misi penyebaran agama Hindu dari pulau Jawa ke pulau Bali.\n          </p>\n        </div>\n        <div>\n          <h2>Akses</h2>\n          <p>\n            Tanah Lot terletak sekitar 30 km dari Bandara Ngurah Rai di Denpasar. Apabila Sobat Pesona berangkat dari Denpasar, di persimpangan utama jalan raya Kota Kediri, terdapat papan petunjuk yang mengarah ke barat daya yang akan membawa Sobat Pesona ke Tanah Lot. Untuk menuju ke Pura Tanah Lot, Sobat Pesona akan melewati dataran bertanah kering dan naik ke Bukit.\n          </p>\n        </div>\n        <div>\n          <h2>Fasilitas</h2>\n          <p>\n            Wisata Tanah Lot menjadi salah satu objek wisata yang paling terkenal di Bali. Menampilkan daya tarik yang dapat memanjakan para pengunjung yang sedang menikmati momen indah mereka.  Sehingga fasilitas wisata yang ada di Tanah Lot sangat memadai. Fasilitas-fasilitaas tersebut diantaranya yaitu\nLahan parkir yang luas, Restoran, Toilet yang berkelas internasional, Penginapan/hotel, Pusat informasi, dan\nRestorant art shop di sekitarnya., Selain fasilitas umum tersebut di atas, terdapat juga pedagang makanan dan minuman seperti pedagang klepon dan kerami serta pedagang tattoo dan gambar. Tidak hanya itu, ada juga puluhan pedagang post card dan tukang foto.\n          </p>\n        </div>'),
(3, 3, '<div>\n          <p>\n            Nusa Penida adalah sebuah pulau (=nusa) bagian dari Kabupaten Klungkung, Bali, Indonesia yang terletak di sebelah tenggara Bali yang dipisahkan oleh Selat Badung. Di dekat pulau ini terdapat juga pulau-pulau kecil lainnya yaitu Nusa Ceningan dan Nusa Lembongan. Perairan pulau Nusa Penida terkenal dengan kawasan selamnya di antaranya terdapat di Crystal Bay, Manta Point, Batu Meling, Batu Lumbung, Batu Abah, Toyapakeh dan Malibu Point. Sejarah pulau Nusa Penida di Bali dimulai pada abad ke-10. Tulisan-tulisan paling awal tentang Nusa Penida memang telah ditemukan di Pilar Belanjong, yang berasal dari tahun 914 M. Pilar ini memuat prasasti yang menyebutkan ekspedisi militer Raja Bali pertama, Sri Kesari Warmadewa, menaklukan Nusa Penida.\n          </p>\n        </div>\n        <div>\n          <h2>Akses</h2>\n          <p>\n           Secara umum ada empat lokasi untuk menyeberang dari Bali ke pulau Nusa Penida.\nDari Pelabuhan Sanur yang lokasinya berada di pantai Matahari Terbit Sanur ke Nusa Penida.\nPelabuhan Serangan ke pulau Nusa Penida.\nDari pelabuhan Tribuana dan pelabuhan Banjar Bias pantai Kusamba Klungkung ke Nusa Penida.\nPelabuhan Padang Bai ke Nusa Penida.\nJadi lokasi penyeberangan dari pulau Bali ke Nusa Penida ada banyak pilihan. Karena sebagian besar wisatawan Indonesia saat liburan di pulau Bali memilih menginap di kawasan tempat wisata Bali bagian selatan. Maka lokasi menyeberang dari Bali ke Nusa Penida yang paling dikenal adalah pantai Sanur Bali.\n          </p>\n        </div>\n        <div>\n          <h2>Fasilitas</h2>\n          <p>\n           Pelabuhan \nTerdapat 4 pelabuhan (Buyuk, Toya Pakeh, Banjar Nyuh, Sampalan) di Nusa Penida dimana pelabuhan utama sebagai pintu masuk ke pulau dengan menggunakan jalur laut. \nTransportasi \nDisediakan transportasi umum berupa pick up yang sudah dimodifikasi dan dapat disewa jika kamu ingin berkeliling bersama rombongan.\nPenginapan \nBackpacker tidak perlu berkecil hati apabila ingin menginap di Nusa Penida, karena ada berbagai jenis penginapan bagi kamu pembawa ransel. Mulai dari hotel bintang 2 hingga 5 bisa kamu pilih, seperti Krisna Guest House, The Mel Huts, Linas Villas, Manta Cottage Sea View plus, Ramwan Guest House hingga Nova Homestay. \nRestoran dan Cafe \nPoh Manis Restaurant dapat menjadi salah satu tempat makan alternatif di Nusa Penida yang menyuguhkan hidangan makanan laut dan steak.\n          </p>\n        </div>'),
(4, 4, '<div>\n          <p>\n           Gunung Batur adalah sebuah gunung berapi aktif di Kecamatan Kintamani, Kabupaten Bangli, Bali, Indonesia. Sisi tenggara dari kaldera yang berukuran 10×13 km ini sebagian besar berisi danau kaldera. Baik kaldera yang lebih besar, dan kaldera yang lebih kecil 7,5 km dibentuk oleh runtuhnya ruang magma gunung , keruntuhan lebih besar pertama terjadi sekitar 29.300 tahun yang lalu, dan kaldera bagian dalam runtuh kedua kalinya sekitar 20.150 tahun yang lalu. Perkiraan lain dari tanggal pembentukan kaldera bagian dalam, terbentuk selama letusan ignimbrit Bali, sekitar 23.670 dan 28.500 tahun yang lalu.\n          </p>\n        </div>\n        <div>\n          <h2>Akses</h2>\n          <p>\n          Jalur pendakian dari Pasar Agung adalah jalur tersingkat yang dapat ditempuh para pendaki. Kamu dapat menggunakan kendaraan pribadi untuk sampai di perut Gunung Batur. Untuk sampai di jalur pendakian ini, kamu akan melewati Pura Jati, menuju ke Pura Pasar Agung Batur.\nSetelah sampai di sana, kamu bisa memulai mendaki, melewati bebatuan yang terjal untuk sampai di puncak kedua Gunung Batur, atau biasa disebut dengan Puncak Kanginan.\nSetelah sampai di Puncak Kanginan, masih membutuhkan waktu sekitar 30 menit lagi untuk sampai di puncak Gunung Batur. Ikuti jalur yang sudah ada dan jangan membuat jalur baru atau merusak petunjuk arah.\n          </p>\n        </div>\n        <div>\n          <h2>Fasilitas</h2>\n          <p>\n             Fasilitas utama yang paling diinginkan pengunjung selama liburan di pantai adalah ketersediaan toilet, ruang ganti, kamar mandi dan air bersih.\n          </p>\n        </div>'),
(5, 5, '<div>\r\n          <p>\r\n            Pantai Pandawa adalah salah satu tempat wisata di area Kuta selatan, Kabupaten Badung, Bali. Pantai ini terletak di balik perbukitan dan sering disebut sebagai Pantai Rahasia (Secret Beach). Di sekitar pantai ini terdapat dua tebing yang sangat besar yang pada salah satu sisinya dipahat lima patung Pandawa[1] dan Kunti. Keenam patung tersebut secarara berurutan (dari posisi tertinggi) diberi penejasan nama Dewi Kunti, Dharma Wangsa, Bima, Arjuna, Nakula, dan Sadewa.\r\n          </p>\r\n        </div>\r\n        <div>\r\n          <h2>Akses</h2>\r\n          <p>\r\n           Dari Seminyak, Kuta, dan Canggu menuju Pantai Pandawa berjarak kurang lebih 35 km dengan waktu tempuh 1 jam lebih perjalanan. Anda bisa melewati Benoa Toll Road atau Bypass Ngurah Rai dengan jarak yang tak jauh berbeda.\r\nUmumnya, wisatawan yang berkunjung ke Pantai Pandawa menggunakan kendaraan roda dua atau roda empat yang disewa selama seharian dari jasa sewa mobil-motor. Mengingat, tidak ada transportasi umum di sana.\r\n          </p>\r\n        </div>\r\n        <div>\r\n          <h2>Fasilitas</h2>\r\n          <p>\r\n            Pijat refleksi tersedia di pantai ini, nama area pijatnya adalah Pandawa SPA. Di bawah ini adalah tabel harga untuk Pandawa Beachfront SPA.\r\nPijat Pandawa – 90 menit Rp 130.000,  Pandawa Beach Bali juga menawarkan persewaan kano untuk rekreasi air.\r\nKano tunggal domestik: Rp 25.000 / 1 orang, durasi 1 jam.\r\nDomestik Double Canoe: Rp 50.000 / 2 orang, Durasi 1 jam., Dan Warung makan ini menawarkan berbagai macam kuliner seperti nasi goreng, mie goreng, ayam bakar, seafood bakar dan berbagai jenis western food.\r\n          </p>\r\n        </div>'),
(6, 6, '<div>\n          <p>\n            Taman Budaya Garuda Wisnu Kencana (GWK) merupakan tempat wisata di Unggasan Jimbaran Bali dengan objek wisata Patung GWK (Garuda Wisnu Kencana). [1] Patung GWK ini merupakan replika Dewa Wisnu yang mengendarai kendaraan bernama Garuda setinggi 12 meter yang dibuat oleh pematung terkenal asal Bali, I Nyoman Nuarta. Sejarah patung GWK dimulai oleh seorang maestro Bali bernama I Nyoman Nuarta pada tahun 1989. Ide pembangunan patung ini awalnya tidak langsung diterima oleh masyarakat, bahkan membutuhkan waktu delapan tahun untuk memperkenalkan ide tersebut.\n          </p>\n        </div>\n        <div>\n          <h2>Akses</h2>\n          <p>\n           Untuk menuju lokasi GWK Cultural Park, wisatawan bisa menggunakan layanan shuttle. GWK Loop bisa diakses secara gratis, sedangkan GWK Buggy bayar Rp30.000 per orang. Layanan antar jemput beroperasi mulai pukul 10.00 Wita - 18.00 Wita.\n\nSebagai informasi, layanan antar jemput ini tersedia di area parkir 4 HA. GWK Loop akan mengantar wisatawan dari area parkir ke Plaza Bhagawan.\n\nGWK Loop juga akan mengantar wisatawan dari area parkir ke Jendela Bali Resto/Beranda Restaurant. Sementara Buggy GWK akan mengantar wisatawan dari Plaza Bhagawan menuju Pedestal, kemudian ke Festival Park.\n          </p>\n        </div>\n        <div>\n          <h2>Fasilitas</h2>\n          <p>\n            Restoran Jendela Bali : Restoran ini selain menyajikan sajian yang lezat, juga menawarkan pemandangan yang indah. Pemandangan indah mulai dari pantai barat (Pantai Kuta, Pantai Jimbaran, Pantai Seminyak) hingga pantai timur (Tanjung Benoa dan Pantai Sanur) serta gunung Agung dan gunung Batur dapat dilihat dari restoran Jendela Bali ini, Toko Souvenir  Otentik :Sepertinya berbelanja adalah kegiatan yang tidak boleh ketinggalan saat berlibur, bukan? Itulah sebabnya dihadirkan Kencana Souvenir Shop di dalam GWK. Tempat belanja di Bali memang banyak tetapi tempat yang menyediakan suvenir dengan identitas GWK hanya ada di sini,Halaman Parkir :Tersedia halaman parkir yang sangat luas di GWK Bali yang terbagi menjadi parkiran sepeda motor, parkiran mobil pribadi serta parkiran bus pariwisata, Toilet :Toilet di tempat wisata GWK Bali ini termasuk bersih dan cukup banyak tersebar di lokasi-lokasi yang strategis dekat berbagai lokasi pertunjukan seperti Street Theater dan Amphitheater, Musholla: Mushollah di objek wisata GWK Bali ini berada di Lotus Pond.          </p>\n        </div>');

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
(1, 1, 'kuta1.png'),
(2, 1, 'kuta2.png'),
(3, 2, 'lot1.png'),
(4, 2, 'lot2.png'),
(5, 3, 'pnd1.jpg'),
(6, 3, 'pnd2.jpg'),
(7, 4, 'gng1.jpg'),
(8, 4, 'gng2.png'),
(11, 5, 'pnd1.png'),
(12, 5, 'pnd2.png'),
(13, 6, 'grd1.png'),
(14, 6, 'grd2.png');

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
(4, 'Varna Culture Hotel Tunjungan Surabaya'),
(5, 'MaxOneHotels at Tidar Surabaya'),
(6, 'Neo Denpasar Hotel by ASTON'),
(7, 'Prime Plaza Hotel Sanur - Bali'),
(8, 'ASTON Denpasar Hotel and Convention Center'),
(9, 'The Cakra Hotel'),
(10, 'Sanur Resort Watujimbar');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pesawat`
--

CREATE TABLE `pesawat` (
  `id_pesawat` int(11) NOT NULL,
  `nama_pesawat` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `pesawat`
--

INSERT INTO `pesawat` (`id_pesawat`, `nama_pesawat`) VALUES
(1, 'Lion Air'),
(2, 'Garuda Indonesia'),
(3, 'Lion Air'),
(4, 'Garuda Indonesia');

-- --------------------------------------------------------

--
-- Struktur dari tabel `routes_bus`
--

CREATE TABLE `routes_bus` (
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
-- Dumping data untuk tabel `routes_bus`
--

INSERT INTO `routes_bus` (`routes_id`, `from_id`, `to_id`, `departure_date`, `return_date`, `jumlah_kursi`, `buses_id`, `detail`) VALUES
(1, 1, 2, '2024-07-15', NULL, 1, 3, 'https://www.tiket.com/bus-travel/detail?origin=63d0b5bb2cbd5656536b9f28&destination=63d0b5be2cbd5656536ba1ac&departureDate=2024-07-15&returnDate=2024-06-23&adult=1&tripType=oneway&journeyType=depart&scheduleId=redbus-1076765449440150883-27542-269822-27558-1102001-15df811c7892b7ba8d15942a365bc1a3&scheduleHash=5ca1d7fbabd7b2734d99a6307155cc56fe3d0c35'),
(2, 2, 1, '2024-07-16', '2024-07-18', 2, 1, 'https://www.tiket.com/bus-travel/detail?origin=63d0b5be2cbd5656536ba1ac&destination=63d0b5bb2cbd5656536b9f28&departureDate=2024-07-16&returnDate=2024-07-18&adult=1&tripType=roundtrip&journeyType=depart&scheduleId=redbus-1076763749450581728-27558-8686279-27542-3647358-15df811c7892b7ba8d15942a365bc1a3&scheduleHash=66d2b8744bdadcae09e0fe3bbb9a7119f8b4ad09&compressedDepartureDetail=N4IgJgpgDghgTgFwK5wgZQMYAsJiQGwhAC5QB7KCOGBMuAORgFsjiQBBAOwHMBrGAM4wABABVqnAcIBSMAJ4wQAGnDR4yVCVBgarEACYADPoAsAWkMB2MwEYAbMpAIAlixIhDATmIBmQyABfFSg4ZwxWfTsAVksfEwCAoA'),
(5, 1, 2, '2024-07-16', NULL, 2, 2, 'https://www.tiket.com/bus-travel/detail?origin=63d0b5bb2cbd5656536b9f28&destination=63d0b5be2cbd5656536ba1ac&departureDate=2024-07-16&returnDate=2024-06-23&adult=1&tripType=oneway&journeyType=depart&scheduleId=redbus-1076765449450723755-27542-8686469-27558-2870301-15df811c7892b7ba8d15942a365bc1a3&scheduleHash=5ffe4131983674c8c1901dd0099613c03a53aa6c'),
(6, 1, 2, '2024-07-17', NULL, 1, 3, 'https://www.tiket.com/bus-travel/detail?origin=63d0b5bb2cbd5656536b9f28&destination=63d0b5be2cbd5656536ba1ac&departureDate=2024-07-17&returnDate=2024-06-23&adult=1&tripType=oneway&journeyType=depart&scheduleId=redbus-1076765449460150883-27542-4635520-27558-1102001-15df811c7892b7ba8d15942a365bc1a3&scheduleHash=4951b97f3cb256ed78d9ec2b15d8d8d2e7fe91dd'),
(7, 1, 2, '2024-07-18', NULL, 1, 5, 'https://www.tiket.com/bus-travel/detail?origin=63d0b5bb2cbd5656536b9f28&destination=63d0b5be2cbd5656536ba1ac&departureDate=2024-07-18&returnDate=2024-06-23&adult=1&tripType=oneway&journeyType=depart&scheduleId=redbus-1076765449470410058-27542-1104344-27558-1455028-15df811c7892b7ba8d15942a365bc1a3&scheduleHash=ae60e1aebb39b54d1f2b1664caf2ab91203604c6'),
(36, 2, 1, '2024-07-15', NULL, 1, 2, 'https://www.tiket.com/bus-travel/detail?origin=63d0b5be2cbd5656536ba1ac&destination=63d0b5bb2cbd5656536b9f28&departureDate=2024-07-15&returnDate=2024-06-23&adult=1&tripType=oneway&journeyType=depart&scheduleId=redbus-1076763749440723758-27558-8686217-27542-2870422-15df811c7892b7ba8d15942a365bc1a3&scheduleHash=f880cab1fd6ba5e51962aea06a329a6dc8c505c9'),
(37, 2, 1, '2024-07-16', NULL, 1, 1, 'https://www.tiket.com/bus-travel/detail?origin=63d0b5be2cbd5656536ba1ac&destination=63d0b5bb2cbd5656536b9f28&departureDate=2024-07-16&returnDate=2024-06-23&adult=1&tripType=oneway&journeyType=depart&scheduleId=redbus-1076763749450581728-27558-8686279-27542-3647358-15df811c7892b7ba8d15942a365bc1a3&scheduleHash=66d2b8744bdadcae09e0fe3bbb9a7119f8b4ad09'),
(38, 2, 1, '2024-07-17', NULL, 1, 5, 'https://www.tiket.com/bus-travel/detail?origin=63d0b5be2cbd5656536ba1ac&destination=63d0b5bb2cbd5656536b9f28&departureDate=2024-07-17&returnDate=2024-06-23&adult=1&tripType=oneway&journeyType=depart&scheduleId=redbus-1076763749460409823-27558-1104333-27542-3292406-15df811c7892b7ba8d15942a365bc1a3&scheduleHash=f6c320671f5df807637b8d6bbb414e5f388adda1'),
(39, 2, 1, '2024-07-18', NULL, 2, 3, 'https://www.tiket.com/bus-travel/detail?origin=63d0b5be2cbd5656536ba1ac&destination=63d0b5bb2cbd5656536b9f28&departureDate=2024-07-18&returnDate=2024-06-23&adult=1&tripType=oneway&journeyType=depart&scheduleId=redbus-1076763749470151056-27558-1102001-27542-269822-15df811c7892b7ba8d15942a365bc1a3&scheduleHash=d1807fe8dd267bad4c1d92c3e3c4ff35be9a9e44');

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
-- Indeks untuk tabel `bookings_hotel`
--
ALTER TABLE `bookings_hotel`
  ADD PRIMARY KEY (`id_bookings`),
  ADD KEY `id_hotel` (`id_hotel`),
  ADD KEY `id_destinations` (`id_destinations`);

--
-- Indeks untuk tabel `bookings_mobil`
--
ALTER TABLE `bookings_mobil`
  ADD PRIMARY KEY (`id_bm`),
  ADD KEY `id_car` (`id_car`),
  ADD KEY `id_destinations` (`id_destinations`);

--
-- Indeks untuk tabel `bookings_pesawat`
--
ALTER TABLE `bookings_pesawat`
  ADD PRIMARY KEY (`id_bp`),
  ADD KEY `from_id` (`from_id`),
  ADD KEY `to_id` (`to_id`),
  ADD KEY `id_pesawat` (`id_pesawat`);

--
-- Indeks untuk tabel `buses`
--
ALTER TABLE `buses`
  ADD PRIMARY KEY (`buses_id`);

--
-- Indeks untuk tabel `car`
--
ALTER TABLE `car`
  ADD PRIMARY KEY (`id_car`);

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
-- Indeks untuk tabel `pesawat`
--
ALTER TABLE `pesawat`
  ADD PRIMARY KEY (`id_pesawat`);

--
-- Indeks untuk tabel `routes_bus`
--
ALTER TABLE `routes_bus`
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
-- AUTO_INCREMENT untuk tabel `bookings_hotel`
--
ALTER TABLE `bookings_hotel`
  MODIFY `id_bookings` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `bookings_mobil`
--
ALTER TABLE `bookings_mobil`
  MODIFY `id_bm` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `buses`
--
ALTER TABLE `buses`
  MODIFY `buses_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `car`
--
ALTER TABLE `car`
  MODIFY `id_car` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `detail_image`
--
ALTER TABLE `detail_image`
  MODIFY `id_img` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

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
-- AUTO_INCREMENT untuk tabel `pesawat`
--
ALTER TABLE `pesawat`
  MODIFY `id_pesawat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `routes_bus`
--
ALTER TABLE `routes_bus`
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
-- Ketidakleluasaan untuk tabel `bookings_hotel`
--
ALTER TABLE `bookings_hotel`
  ADD CONSTRAINT `bookings_hotel_ibfk_2` FOREIGN KEY (`id_hotel`) REFERENCES `hotel` (`id_hotel`),
  ADD CONSTRAINT `bookings_hotel_ibfk_3` FOREIGN KEY (`id_destinations`) REFERENCES `destinations` (`id_destinations`);

--
-- Ketidakleluasaan untuk tabel `bookings_mobil`
--
ALTER TABLE `bookings_mobil`
  ADD CONSTRAINT `bookings_mobil_ibfk_1` FOREIGN KEY (`id_car`) REFERENCES `car` (`id_car`),
  ADD CONSTRAINT `bookings_mobil_ibfk_2` FOREIGN KEY (`id_destinations`) REFERENCES `destinations` (`id_destinations`);

--
-- Ketidakleluasaan untuk tabel `bookings_pesawat`
--
ALTER TABLE `bookings_pesawat`
  ADD CONSTRAINT `bookings_pesawat_ibfk_1` FOREIGN KEY (`from_id`) REFERENCES `from_city` (`from_id`),
  ADD CONSTRAINT `bookings_pesawat_ibfk_2` FOREIGN KEY (`to_id`) REFERENCES `to_city` (`to_id`),
  ADD CONSTRAINT `bookings_pesawat_ibfk_3` FOREIGN KEY (`id_pesawat`) REFERENCES `pesawat` (`id_pesawat`);

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
-- Ketidakleluasaan untuk tabel `routes_bus`
--
ALTER TABLE `routes_bus`
  ADD CONSTRAINT `routes_bus_ibfk_1` FOREIGN KEY (`buses_id`) REFERENCES `buses` (`buses_id`),
  ADD CONSTRAINT `routes_bus_ibfk_2` FOREIGN KEY (`from_id`) REFERENCES `from_city` (`from_id`),
  ADD CONSTRAINT `routes_bus_ibfk_3` FOREIGN KEY (`to_id`) REFERENCES `to_city` (`to_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
