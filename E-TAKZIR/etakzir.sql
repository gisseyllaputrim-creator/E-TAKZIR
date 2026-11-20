-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 20, 2026 at 12:12 PM
-- Server version: 13.0.2-MariaDB
-- PHP Version: 8.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `etakzir`
--
CREATE DATABASE IF NOT EXISTS `etakzir` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `etakzir`;

-- --------------------------------------------------------

--
-- Table structure for table `orang_tua`
--

CREATE TABLE `orang_tua` (
  `id_orang_tua` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_santri` int(11) NOT NULL,
  `nama_orang_tua` varchar(100) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orang_tua`
--

INSERT INTO `orang_tua` (`id_orang_tua`, `id_user`, `id_santri`, `nama_orang_tua`, `no_hp`) VALUES
(2, 4, 1, 'Bapak Trio', '087812333370');

-- --------------------------------------------------------

--
-- Table structure for table `pelanggaran`
--

CREATE TABLE `pelanggaran` (
  `id_pelanggaran` int(11) NOT NULL,
  `id_santri` int(11) NOT NULL,
  `id_pengurus` int(11) NOT NULL,
  `jenis_pelanggaran` varchar(100) NOT NULL,
  `poin` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pelanggaran`
--

INSERT INTO `pelanggaran` (`id_pelanggaran`, `id_santri`, `id_pengurus`, `jenis_pelanggaran`, `poin`, `tanggal`, `keterangan`) VALUES
(2, 1, 1, 'Merokok', 50, '2026-11-20', 'Peringatan 1');

-- --------------------------------------------------------

--
-- Table structure for table `pengurus`
--

CREATE TABLE `pengurus` (
  `id_pengurus` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nama_pengurus` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengurus`
--

INSERT INTO `pengurus` (`id_pengurus`, `id_user`, `nama_pengurus`) VALUES
(1, 1, 'keamanan');

-- --------------------------------------------------------

--
-- Table structure for table `santri`
--

CREATE TABLE `santri` (
  `id_santri` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nama_santri` varchar(100) NOT NULL,
  `kelas` varchar(30) NOT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `santri`
--

INSERT INTO `santri` (`id_santri`, `id_user`, `nama_santri`, `kelas`, `jenis_kelamin`) VALUES
(1, 2, 'Maulana', '7', 'Laki-laki');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('orang_tua','santri','pengurus') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `role`) VALUES
(1, 'keamanan', '$2y$12$fKtbwaI.PgV9kJ2VX8ayAumkJhHnzg6az/GIm2nT2.WdVDyo8pdeS', 'pengurus'),
(2, 'Maulana', '$2y$12$zViVD4wa.MTuZ1.f52B8RuVAY6bjkgpoF9bIVspyY4IYTuRfvIajS', 'santri'),
(4, 'Bapak Trio', '$2y$12$P1xvxQcZ19WTWHe2y3tf6.6dw9kD8Aq6QSHs3pI4oHLAl1Wl3eEem', 'orang_tua');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orang_tua`
--
ALTER TABLE `orang_tua`
  ADD PRIMARY KEY (`id_orang_tua`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_santri` (`id_santri`);

--
-- Indexes for table `pelanggaran`
--
ALTER TABLE `pelanggaran`
  ADD PRIMARY KEY (`id_pelanggaran`),
  ADD KEY `id_santri` (`id_santri`),
  ADD KEY `id_pengurus` (`id_pengurus`);

--
-- Indexes for table `pengurus`
--
ALTER TABLE `pengurus`
  ADD PRIMARY KEY (`id_pengurus`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `santri`
--
ALTER TABLE `santri`
  ADD PRIMARY KEY (`id_santri`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orang_tua`
--
ALTER TABLE `orang_tua`
  MODIFY `id_orang_tua` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pelanggaran`
--
ALTER TABLE `pelanggaran`
  MODIFY `id_pelanggaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pengurus`
--
ALTER TABLE `pengurus`
  MODIFY `id_pengurus` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `santri`
--
ALTER TABLE `santri`
  MODIFY `id_santri` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orang_tua`
--
ALTER TABLE `orang_tua`
  ADD CONSTRAINT `1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`),
  ADD CONSTRAINT `2` FOREIGN KEY (`id_santri`) REFERENCES `santri` (`id_santri`);

--
-- Constraints for table `pelanggaran`
--
ALTER TABLE `pelanggaran`
  ADD CONSTRAINT `1` FOREIGN KEY (`id_santri`) REFERENCES `santri` (`id_santri`),
  ADD CONSTRAINT `2` FOREIGN KEY (`id_pengurus`) REFERENCES `pengurus` (`id_pengurus`);

--
-- Constraints for table `pengurus`
--
ALTER TABLE `pengurus`
  ADD CONSTRAINT `1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`);

--
-- Constraints for table `santri`
--
ALTER TABLE `santri`
  ADD CONSTRAINT `1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`);
--
-- Database: `mutabaah`
--
CREATE DATABASE IF NOT EXISTS `mutabaah` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `mutabaah`;

-- --------------------------------------------------------

--
-- Table structure for table `log_ibadah`
--

CREATE TABLE `log_ibadah` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `sholat_subuh` tinyint(1) DEFAULT 0,
  `sholat_dzuhur` tinyint(1) DEFAULT 0,
  `sholat_ashar` tinyint(1) DEFAULT 0,
  `sholat_maghrib` tinyint(1) DEFAULT 0,
  `sholat_isya` tinyint(1) DEFAULT 0,
  `tilawah_halaman` int(11) DEFAULT 0,
  `catatan` text DEFAULT NULL,
  `status_validasi` enum('menunggu','disetujui') NOT NULL DEFAULT 'menunggu',
  `catatan_guru` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `log_ibadah`
--

INSERT INTO `log_ibadah` (`id`, `id_user`, `tanggal`, `sholat_subuh`, `sholat_dzuhur`, `sholat_ashar`, `sholat_maghrib`, `sholat_isya`, `tilawah_halaman`, `catatan`, `status_validasi`, `catatan_guru`) VALUES
(1, 1, '2025-09-20', 1, 1, 1, 1, 1, 3, NULL, 'disetujui', NULL),
(2, 1, '2025-09-21', 1, 1, 0, 1, 1, 2, NULL, 'disetujui', 'kenapa ini kok bolong'),
(4, 1, '2026-09-26', 1, 1, 1, 1, 1, 20, 'yes', 'disetujui', 'bagus sekali, semoga lebih konsisten ya'),
(5, 1, '2025-10-12', 1, 1, 1, 1, 1, 20, 'Alhamdulillah', 'disetujui', 'baik sekali'),
(6, 1, '2026-10-12', 1, 1, 1, 1, 1, 20, 'alhamdulilla', 'disetujui', 'oke'),
(8, 1, '2026-11-01', 1, 1, 1, 1, 1, 20, '', 'disetujui', ''),
(10, 1, '2026-11-10', 1, 1, 1, 1, 1, 20, '', 'disetujui', 'oke baik');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `role` enum('siswa','guru','admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `role`) VALUES
(1, 'siswa1', '$2a$12$Wx9ME9LjbPdK2eBa26X8SOxYQzbVbxHUlnVAcNu5tzcn7xmNSMi1O', 'Ahmad Abdullah', 'siswa'),
(2, 'guru1', '$2a$12$ycH/kESZVL7BkOGWSiRSzu.LG0FWRxeTc41x.bmY63vYTkB5IhiHW', 'Guru Pribadi', 'guru');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `log_ibadah`
--
ALTER TABLE `log_ibadah`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_log_to_users` (`id_user`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `log_ibadah`
--
ALTER TABLE `log_ibadah`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `log_ibadah`
--
ALTER TABLE `log_ibadah`
  ADD CONSTRAINT `fk_log_to_users` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
--
-- Database: `test`
--
CREATE DATABASE IF NOT EXISTS `test` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci;
USE `test`;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
