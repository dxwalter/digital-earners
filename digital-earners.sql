-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 02, 2019 at 06:20 PM
-- Server version: 10.1.36-MariaDB
-- PHP Version: 7.2.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `digital-earners`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `fullname` varchar(50) NOT NULL,
  `date_created` date NOT NULL,
  `time_created` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `fullname`, `date_created`, `time_created`) VALUES
(3, 'dxwalter', '328c3b3a16ca6f652bcf8a1faaae4ba986491cd3', 'Daniel Walter', '2019-07-15', '11:05:04'),
(4, 'harry99', '328c3b3a16ca6f652bcf8a1faaae4ba986491cd3', 'Daniel', '2019-07-15', '11:13:11');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `id` int(11) NOT NULL,
  `userid` varchar(50) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(50) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `password` varchar(50) NOT NULL,
  `profile_picture` varchar(100) DEFAULT NULL,
  `last_seen` varchar(50) NOT NULL,
  `active_status` int(1) NOT NULL DEFAULT '1' COMMENT 'An active investor is equal to 1 and a suspended investor is equal to 0',
  `first_time_access` int(1) NOT NULL DEFAULT '0' COMMENT 'After a customer account has been created, we have to check if the account has been accessed. This is done to be sure that the customer has changed the login info provided by the admin. By default it is set to 0 which signifies that the customer has not changed their profile detai;s',
  `date_added` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`id`, `userid`, `fullname`, `email`, `phone_number`, `password`, `profile_picture`, `last_seen`, `active_status`, `first_time_access`, `date_added`) VALUES
(1, '356a192b7913b04c54574d18c28d46e6395428ab', 'Daniel Walter udoh', 'realdanielwalter@gmail.com', '08037478690', '328c3b3a16ca6f652bcf8a1faaae4ba986491cd3', '29a1ee793f9383eae811a22de193ccc89afab5e4.jpg', '1564760421', 1, 1, '2019-07-29'),
(2, 'da4b9237bacccdf19c0760cab7aec4a8359010b0', '', 'walter@gmail.com', '08104686729', '7c4a8d09ca3762af61e59520943dc26494f8941b', NULL, '', 1, 0, '2019-07-29'),
(3, '77de68daecd823babbb58edb1c8e14d7106e83bb', '', 'daniel@gmail.com', '081046867291', '7c4a8d09ca3762af61e59520943dc26494f8941b', NULL, '', 1, 0, '2019-07-29'),
(4, '1b6453892473a467d07372d45eb05abc2031647a', '', 'daniel1@gmail.com', '0810468672911', '7c4a8d09ca3762af61e59520943dc26494f8941b', NULL, '', 1, 0, '2019-07-29'),
(5, 'ac3478d69a3c81fa62e60f5c3696165a4e5e6ac4', '', 'daniel11@gmail.com', '08104686729111', '7c4a8d09ca3762af61e59520943dc26494f8941b', NULL, '', 1, 0, '2019-07-29'),
(6, 'c1dfd96eea8cc2b62785275bca38ac261256e278', '', 'daniel111@gmail.com', '081046867291111', '7c4a8d09ca3762af61e59520943dc26494f8941b', NULL, '', 1, 0, '2019-07-29'),
(7, '902ba3cda1883801594b6e1b452790cc53948fda', '', 'daniel1111@gmail.com', '0810468672911111', '7c4a8d09ca3762af61e59520943dc26494f8941b', NULL, '', 1, 0, '2019-07-29'),
(8, 'fe5dbbcea5ce7e2988b8c69bcfdfde8904aabc1f', 'Daniel Greatman udoh', 'realdanielwalter@gmail.com', '08104686729', '7c4a8d09ca3762af61e59520943dc26494f8941b', NULL, '1564362228', 1, 1, '2019-07-29');

-- --------------------------------------------------------

--
-- Table structure for table `investment`
--

CREATE TABLE `investment` (
  `id` int(11) NOT NULL,
  `userid` varchar(50) NOT NULL,
  `capital` bigint(20) NOT NULL,
  `roi` bigint(20) NOT NULL,
  `upload_date` varchar(30) NOT NULL,
  `upload_time` varchar(10) NOT NULL,
  `binary_date` varchar(100) DEFAULT NULL,
  `proof_of_payment` varchar(70) NOT NULL,
  `receipt_status` int(11) NOT NULL DEFAULT '0' COMMENT 'This is a confirmation of an investment. 1 means confirmed, 0 means pending',
  `roi_payment_status` int(11) NOT NULL DEFAULT '0' COMMENT 'This is a confirmation that the admin has paid an investor. 0 means pending and 1 means cleared'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `investment`
--

INSERT INTO `investment` (`id`, `userid`, `capital`, `roi`, `upload_date`, `upload_time`, `binary_date`, `proof_of_payment`, `receipt_status`, `roi_payment_status`) VALUES
(1, 'fe5dbbcea5ce7e2988b8c69bcfdfde8904aabc1f', 100000, 30000, 'Tuesday 2 July, 2019', '12:56 PM', '1564095600', 'b1f950eaa401e5ddcd16a4aa9a90c91379ffb5ea.jpg', 1, 1),
(2, 'fe5dbbcea5ce7e2988b8c69bcfdfde8904aabc1f', 200000, 60000, 'Wednesday 3 July, 2019', '10:11 AM', '1564095600', '7e5c553da9dc2668c10c02d8f64765ad24fd04bb.png', 1, 1),
(14, 'fe5dbbcea5ce7e2988b8c69bcfdfde8904aabc1f', 95400000, 28620000, 'Friday 26 July, 2019', '03:01 PM', '1564095600', '180c48c92c910a5f00e70fc42b6a7074b95e611f5.jpg', 1, 1),
(17, 'fe5dbbcea5ce7e2988b8c69bcfdfde8904aabc1f', 3943589548, 5126666412, 'Tuesday 9 July, 2019', '02:29 AM', '1564095600', '', 1, 1),
(18, 'fe5dbbcea5ce7e2988b8c69bcfdfde8904aabc1f', 200000, 260000, 'Sunday 30 June, 2019', '03:00 AM', '1563318000', '', 1, 1),
(20, 'fe5dbbcea5ce7e2988b8c69bcfdfde8904aabc1f', 2000000, 600000, 'Sunday 30 June, 2019', '03:04 AM', '1563318000', '', 1, 1),
(21, 'fe5dbbcea5ce7e2988b8c69bcfdfde8904aabc1f', 3943589548, 1183076864, 'Tuesday 9 July, 2019', '03:26 AM', '1564095600', '', 1, 1),
(22, 'fe5dbbcea5ce7e2988b8c69bcfdfde8904aabc1f', 30000, 9000, 'Sunday 30 June, 2019', '03:28 AM', '1563318000', '', 1, 1),
(23, '902ba3cda1883801594b6e1b452790cc53948fda', 20000, 6000, 'Sunday 30 June, 2019', '03:37 AM', '1563318000', '', 1, 1),
(24, '902ba3cda1883801594b6e1b452790cc53948fda', 20000, 6000, 'Monday 1 July, 2019', '03:37 AM', '1563404400', '', 1, 1),
(25, '902ba3cda1883801594b6e1b452790cc53948fda', 30000, 9000, 'Sunday 30 June, 2019', '03:37 AM', '1563318000', '', 1, 1),
(26, 'da4b9237bacccdf19c0760cab7aec4a8359010b0', 95400000, 28620000, 'Sunday 28 July, 2019', '05:42 PM', '1565737200', 'ce3f005489cb2be106ab256f5dd0fe710a014274.jpg', 1, 0),
(27, 'fe5dbbcea5ce7e2988b8c69bcfdfde8904aabc1f', 50000, 15000, 'Monday 29 July, 2019', '02:30 AM', '1565823600', '1180c48c92c910a5f00e70fc42b6a7074b95e611f5.jpg', 1, 0),
(28, 'fe5dbbcea5ce7e2988b8c69bcfdfde8904aabc1f', 500000, 150000, 'Monday 29 July, 2019', '02:35 AM', '1565823600', 'a799bf45fe3a49770586a05499be053a419e109a.jpg', 1, 0),
(29, '356a192b7913b04c54574d18c28d46e6395428ab', 10000, 3000, 'Thursday 1 August, 2019', '08:39 PM', '1566082800', '211021a529a973edffdc19551687364e6d4fde82.jpg', 1, 0),
(30, '356a192b7913b04c54574d18c28d46e6395428ab', 200000, 60000, 'Thursday 1 August, 2019', '08:49 PM', '1566082800', 'dpimage1a235659d9558b1553995a7b1fb7295341fe7e48.jpg', 1, 0),
(31, '356a192b7913b04c54574d18c28d46e6395428ab', 2000000, 600000, 'Thursday 1 August, 2019', '09:10 PM', '1566082800', '3c45700976f8789bae77197400f85930a3493b14.jpg', 1, 0),
(32, '356a192b7913b04c54574d18c28d46e6395428ab', 200000, 60000, 'Thursday 1 August, 2019', '09:27 PM', '1566082800', '18960f9d6104157144acbc0256e6f86c79749fb7.jpg', 1, 0),
(33, '356a192b7913b04c54574d18c28d46e6395428ab', 200000, 60000, 'Thursday 1 August, 2019', '09:34 PM', '1566082800', 'dpimage1a235659d9558b1553995a7b1fb7295341fe7e48.jpg', 1, 0),
(34, '356a192b7913b04c54574d18c28d46e6395428ab', 300000, 90000, 'Thursday 1 August, 2019', '09:35 PM', '1566082800', '5a210d23c0d02c73feb47b2434848ac44364022a.jpg', 1, 0),
(35, '356a192b7913b04c54574d18c28d46e6395428ab', 500000000, 150000000, 'Thursday 1 August, 2019', '09:41 PM', '1566082800', '6118fb4098b5b13baa6180c8edee2bca414032b1.jpg', 1, 0),
(36, '356a192b7913b04c54574d18c28d46e6395428ab', 20000, 6000, 'Thursday 1 August, 2019', '09:49 PM', '1566082800', '2d48d14bf635b9e01681d89813d3b9b7c8062416.jpg', 1, 0),
(37, '356a192b7913b04c54574d18c28d46e6395428ab', 10000, 3000, 'Friday 2 August, 2019', '04:53 PM', '0', '13c45700976f8789bae77197400f85930a3493b14.jpg', 0, 0),
(38, '356a192b7913b04c54574d18c28d46e6395428ab', 30000, 9000, 'Friday 2 August, 2019', '05:00 PM', '0', '4cac27952a29d2ecb93ef40c3a3e8794c5ef0566.jpg', 0, 0),
(39, '356a192b7913b04c54574d18c28d46e6395428ab', 30000, 9000, 'Friday 2 August, 2019', '05:01 PM', '0', 'dpimage1a235659d9558b1553995a7b1fb7295341fe7e48.jpg', 0, 0),
(40, '356a192b7913b04c54574d18c28d46e6395428ab', 50000, 15000, 'Friday 2 August, 2019', '05:02 PM', '0', '14cac27952a29d2ecb93ef40c3a3e8794c5ef0566.jpg', 0, 0),
(41, '356a192b7913b04c54574d18c28d46e6395428ab', 4999900000, 1499970000, 'Friday 2 August, 2019', '05:06 PM', '0', '113c45700976f8789bae77197400f85930a3493b14.jpg', 0, 0),
(42, '356a192b7913b04c54574d18c28d46e6395428ab', 500000, 150000, 'Friday 2 August, 2019', '05:08 PM', '0', 'dpimage1a235659d9558b1553995a7b1fb7295341fe7e48.jpg', 0, 0),
(43, '356a192b7913b04c54574d18c28d46e6395428ab', 400000, 120000, 'Friday 2 August, 2019', '05:08 PM', '0', 'a1f36021df77e4cda774223c2658d925153f9e4a.jpg', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `id` int(11) NOT NULL,
  `userid` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `type` text,
  `typeid` varchar(100) NOT NULL,
  `read_status` int(11) NOT NULL DEFAULT '0' COMMENT 'This is to check if a user has read a notification. 1 means read 0 means not read',
  `date_created` date NOT NULL,
  `time_created` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `notification`
--

INSERT INTO `notification` (`id`, `userid`, `message`, `type`, `typeid`, `read_status`, `date_created`, `time_created`) VALUES
(1, 'fe5dbbcea5ce7e2988b8c69bcfdfde8904aabc1f', 'A sum of ?130000 has been paid into your bank account for investing ?100000 on Tuesday 2 July, 2019', 'investment', '1', 1, '2019-07-26', '19:48:10'),
(2, 'fe5dbbcea5ce7e2988b8c69bcfdfde8904aabc1f', 'A sum of ?260000 has been paid into your bank account for investing ?200000 on Wednesday 3 July, 2019', 'investment', '2', 1, '2019-07-26', '19:48:15'),
(3, 'fe5dbbcea5ce7e2988b8c69bcfdfde8904aabc1f', 'A sum of ?124020000 has been paid into your bank account for investing ?95400000 on Friday 26 July, 2019', 'investment', '14', 1, '2019-07-26', '19:48:18'),
(4, 'fe5dbbcea5ce7e2988b8c69bcfdfde8904aabc1f', 'A sum of ?9070255960 has been paid into your bank account for investing ?3943589548 on Tuesday 9 July, 2019', 'investment', '17', 1, '2019-07-27', '02:57:30'),
(5, 'fe5dbbcea5ce7e2988b8c69bcfdfde8904aabc1f', 'A sum of ?460000 has been paid into your bank account for investing ?200000 on Sunday 30 June, 2019', 'investment', '18', 1, '2019-07-27', '03:02:25'),
(6, 'fe5dbbcea5ce7e2988b8c69bcfdfde8904aabc1f', 'A sum of ?5126666412 has been paid into your bank account for investing ?3943589548 on Tuesday 9 July, 2019', 'investment', '21', 1, '2019-07-27', '03:29:00'),
(7, 'fe5dbbcea5ce7e2988b8c69bcfdfde8904aabc1f', 'A sum of ?2600000 has been paid into your bank account for investing ?2000000 on Sunday 30 June, 2019', 'investment', '20', 1, '2019-07-27', '03:29:03'),
(8, 'fe5dbbcea5ce7e2988b8c69bcfdfde8904aabc1f', 'A sum of ?260000 has been paid into your bank account for investing ?200000 on Sunday 30 June, 2019', 'investment', '19', 1, '2019-07-27', '03:29:19'),
(9, 'da4b9237bacccdf19c0760cab7aec4a8359010b0', 'Your investment of 95400000 naira has been confirmed', 'investment', '26', 0, '2019-07-28', '17:45:08'),
(10, 'fe5dbbcea5ce7e2988b8c69bcfdfde8904aabc1f', 'Your investment of 50000 naira has been confirmed', 'investment', '27', 1, '2019-07-29', '02:30:48'),
(11, 'fe5dbbcea5ce7e2988b8c69bcfdfde8904aabc1f', 'Your investment of ?500000 naira has been confirmed', 'investment', '28', 1, '2019-07-29', '02:35:21'),
(12, '356a192b7913b04c54574d18c28d46e6395428ab', 'Your investment of 500000000 naira has been confirmed', 'investment', '35', 1, '2019-08-01', '23:16:20'),
(13, '356a192b7913b04c54574d18c28d46e6395428ab', 'Your investment of 200000 naira has been confirmed', 'investment', '33', 1, '2019-08-01', '23:16:22'),
(14, '356a192b7913b04c54574d18c28d46e6395428ab', 'Your investment of 2000000 naira has been confirmed', 'investment', '31', 1, '2019-08-01', '23:16:25'),
(15, '356a192b7913b04c54574d18c28d46e6395428ab', 'Your investment of 10000 naira has been confirmed', 'investment', '29', 1, '2019-08-01', '23:16:27'),
(16, '356a192b7913b04c54574d18c28d46e6395428ab', 'Your investment of 200000 naira has been confirmed', 'investment', '30', 1, '2019-08-01', '23:16:30'),
(17, '356a192b7913b04c54574d18c28d46e6395428ab', 'Your investment of 200000 naira has been confirmed', 'investment', '32', 1, '2019-08-01', '23:16:34'),
(18, '356a192b7913b04c54574d18c28d46e6395428ab', 'Your investment of 300000 naira has been confirmed', 'investment', '34', 1, '2019-08-01', '23:16:36'),
(19, '356a192b7913b04c54574d18c28d46e6395428ab', 'Your investment of 20000 naira has been confirmed', 'investment', '36', 1, '2019-08-01', '23:19:11');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `investment`
--
ALTER TABLE `investment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `investment`
--
ALTER TABLE `investment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
