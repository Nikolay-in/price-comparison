-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 27, 2022 at 08:57 AM
-- Server version: 10.3.34-MariaDB-0+deb10u1
-- PHP Version: 7.3.31-1~deb10u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db1`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(255) CHARACTER SET latin1 NOT NULL,
  `password` varchar(255) CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3');

-- --------------------------------------------------------

--
-- Table structure for table `brand`
--

CREATE TABLE `brand` (
  `id` int(11) UNSIGNED NOT NULL,
  `brandName` varchar(255) NOT NULL,
  `products` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) UNSIGNED NOT NULL,
  `ix` int(11) UNSIGNED DEFAULT NULL,
  `vis` tinyint(1) NOT NULL DEFAULT 0,
  `catName` varchar(255) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `url` varchar(2083) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `crawler`
--

CREATE TABLE `crawler` (
  `id` int(11) NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `onoff` tinyint(4) NOT NULL DEFAULT 0,
  `workFrom` time DEFAULT '00:00:00',
  `workTo` time DEFAULT '00:00:00',
  `catFreq` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `prodFreq` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `secPage` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `uaList` text DEFAULT NULL,
  `uaSet` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `delTempDays` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `deactiveOffersDays` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `delOffersDays` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `lastRun` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `crawler`
--

INSERT INTO `crawler` (`id`, `status`, `onoff`, `workFrom`, `workTo`, `catFreq`, `prodFreq`, `secPage`, `uaList`, `uaSet`, `delTempDays`, `deactiveOffersDays`, `delOffersDays`, `lastRun`) VALUES
(0, 1, 1, '02:00:00', '03:00:00', 0, 0, 5, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36\r\nMozilla/5.0 (Windows NT 10.0; Win64; x64; rv:94.0) Gecko/20100101 Firefox/94.0\r\nMozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.55 Safari/537.36\r\nMozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.93 Safari/537.36\r\nMozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/95.0.4638.69 Safari/537.36\r\nMozilla/5.0 (Windows NT 10.0; rv:91.0) Gecko/20100101 Firefox/91.0\r\nMozilla/5.0 (Windows NT 10.0; Win64; x64; rv:95.0) Gecko/20100101 Firefox/95.0\r\nMozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/15.1 Safari/605.1.15\r\nMozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/95.0.4638.69 Safari/537.36\r\nMozilla/5.0 (X11; Linux x86_64; rv:94.0) Gecko/20100101 Firefox/94.0\r\nMozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:94.0) Gecko/20100101 Firefox/94.0\r\nMozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:94.0) Gecko/20100101 Firefox/94.0\r\nMozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.93 Safari/537.36\r\nMozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36\r\nMozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36\r\nMozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.55 Safari/537.36 Edg/96.0.1054.34\r\nMozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.55 Safari/537.36 Edg/96.0.1054.43\r\nMozilla/5.0 (X11; Linux x86_64; rv:95.0) Gecko/20100101 Firefox/95.0\r\nMozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:95.0) Gecko/20100101 Firefox/95.0\r\nMozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36\r\nMozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/15.0 Safari/605.1.15\r\nMozilla/5.0 (X11; Linux x86_64; rv:78.0) Gecko/20100101 Firefox/78.0\r\nMozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/95.0.4638.69 Safari/537.36 Edg/95.0.1020.53\r\nMozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/95.0.4638.69 Safari/537.36\r\nMozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36\r\nMozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:95.0) Gecko/20100101 Firefox/95.0\r\nMozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/95.0.4638.69 Safari/537.36 OPR/81.0.4196.60\r\nMozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.2 Safari/605.1.15\r\nMozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36 Edg/96.0.1054.29\r\nMozilla/5.0 (Windows NT 10.0; Win64; x64; rv:91.0) Gecko/20100101 Firefox/91.0\r\nMozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.93 Safari/537.36 Edg/96.0.1054.53\r\nMozilla/5.0 (X11; Fedora; Linux x86_64; rv:94.0) Gecko/20100101 Firefox/94.0\r\nMozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.93 Safari/537.36\r\nMozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/95.0.4638.54 Safari/537.36\r\nMozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.55 Safari/537.36 Edg/96.0.1054.41\r\nMozilla/5.0 (Windows NT 6.1; Win64; x64; rv:94.0) Gecko/20100101 Firefox/94.0\r\nMozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36\r\nMozilla/5.0 (Windows NT 10.0; Win64; x64; rv:93.0) Gecko/20100101 Firefox/93.0\r\nMozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36\r\nMozilla/5.0 (Windows NT 10.0; rv:78.0) Gecko/20100101 Firefox/78.0\r\nMozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36\r\nMozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/95.0.4638.69 Safari/537.36 OPR/81.0.4196.61\r\nMozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/95.0.4638.54 Safari/537.36\r\nMozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36 OPR/82.0.4227.23\r\nMozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.81 Safari/537.36 OPR/80.0.4170.63\r\nMozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.81 Safari/537.36\r\nMozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.1 Safari/605.1.15\r\nMozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.81 Safari/537.36\r\nMozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.93 Safari/537.36\r\nMozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.55 Safari/537.36\r\nMozilla/5.0 (Macintosh; Intel Mac OS X 10_15_6) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/15.1 Safari/605.1.15\r\nMozilla/5.0 (Windows NT 10.0; Win64; x64; rv:96.0) Gecko/20100101 Firefox/96.0\r\nMozilla/5.0 (X11; CrOS x86_64 14150.87.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.124 Safari/537.36\r\nMozilla/5.0 (X11; Linux x86_64; rv:93.0) Gecko/20100101 Firefox/93.0\r\nMozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.71 Safari/537.36\r\nMozilla/5.0 (X11; Linux x86_64; rv:91.0) Gecko/20100101 Firefox/91.0\r\nMozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:93.0) Gecko/20100101 Firefox/93.0\r\nMozilla/5.0 (Windows NT 6.3; Win64; x64; rv:94.0) Gecko/20100101 Firefox/94.0\r\nMozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/95.0.4638.54 Safari/537.36', 0, 7, 3, 30, '2022-03-08 06:22:37');

-- --------------------------------------------------------

--
-- Table structure for table `discounts`
--

CREATE TABLE `discounts` (
  `id` int(11) UNSIGNED NOT NULL,
  `pid` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `rn` int(11) NOT NULL,
  `discount` int(11) NOT NULL,
  `minPrice` decimal(10,2) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `latest`
--

CREATE TABLE `latest` (
  `id` int(11) UNSIGNED NOT NULL,
  `pid` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `dr` int(11) NOT NULL,
  `date` date NOT NULL,
  `minPrice` decimal(10,2) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `offers`
--

CREATE TABLE `offers` (
  `id` int(11) UNSIGNED NOT NULL,
  `productID` int(11) UNSIGNED NOT NULL,
  `vendorID` int(11) UNSIGNED NOT NULL,
  `vendorCode` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) UNSIGNED NOT NULL,
  `oldprice` decimal(10,2) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `link` varchar(2083) NOT NULL,
  `dateAdded` timestamp NOT NULL DEFAULT current_timestamp(),
  `dateUpdated` timestamp NULL DEFAULT NULL,
  `priceUpdated` timestamp NULL DEFAULT NULL,
  `dateApproved` timestamp NULL DEFAULT NULL,
  `lastAlive` timestamp NOT NULL DEFAULT current_timestamp(),
  `currency` varchar(10) NOT NULL DEFAULT 'лв.',
  `active` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `pricehistory`
--

CREATE TABLE `pricehistory` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `offerID` int(11) UNSIGNED DEFAULT NULL,
  `tempProdId` int(11) UNSIGNED DEFAULT NULL,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `pricesupdated`
--

CREATE TABLE `pricesupdated` (
  `id` int(11) UNSIGNED NOT NULL,
  `pid` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `rn` int(11) NOT NULL,
  `date` date NOT NULL,
  `minPrice` decimal(10,2) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) UNSIGNED NOT NULL,
  `ean` bigint(20) DEFAULT NULL,
  `image` text CHARACTER SET utf8 DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `brandID` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `model` varchar(255) CHARACTER SET utf8 NOT NULL,
  `modelStrip` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `description` longtext CHARACTER SET utf8 DEFAULT NULL,
  `specs` longtext CHARACTER SET utf8 DEFAULT NULL,
  `subCatID` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `dateadded` timestamp NOT NULL DEFAULT current_timestamp(),
  `dateupdated` timestamp NULL DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `url` varchar(2083) CHARACTER SET utf8 DEFAULT NULL,
  `discount` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `subcategories`
--

CREATE TABLE `subcategories` (
  `id` int(11) UNSIGNED NOT NULL,
  `ix` int(11) DEFAULT NULL,
  `categoryid` int(11) UNSIGNED NOT NULL,
  `vis` int(11) NOT NULL DEFAULT 0,
  `subCatName` varchar(255) NOT NULL,
  `url` varchar(2083) DEFAULT NULL,
  `aliExId` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `tempproducts`
--

CREATE TABLE `tempproducts` (
  `id` int(11) UNSIGNED NOT NULL,
  `vendorId` int(11) UNSIGNED NOT NULL,
  `ean` bigint(20) DEFAULT NULL,
  `productCode` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `subCatID` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `brandID` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `model` varchar(255) CHARACTER SET utf8 NOT NULL,
  `modelStrip` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `description` longtext CHARACTER SET utf8 DEFAULT NULL,
  `specs` longtext CHARACTER SET utf8 DEFAULT NULL,
  `image` text CHARACTER SET utf8 DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `oldPrice` decimal(10,2) DEFAULT NULL,
  `dateadded` timestamp NOT NULL DEFAULT current_timestamp(),
  `lastAlive` timestamp NOT NULL DEFAULT current_timestamp(),
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `url` varchar(2083) CHARACTER SET utf8 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fname` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `lname` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `email` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `password` varchar(300) CHARACTER SET latin1 DEFAULT NULL,
  `contactno` varchar(11) CHARACTER SET latin1 DEFAULT NULL,
  `posting_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `website` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `vis` int(11) NOT NULL DEFAULT 0,
  `crawl` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `catdir` varchar(2083) DEFAULT NULL,
  `crawlName` varchar(255) DEFAULT NULL,
  `lastCatsCrawl` datetime DEFAULT NULL,
  `lastCrawl` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `vendorscats`
--

CREATE TABLE `vendorscats` (
  `id` int(11) UNSIGNED NOT NULL,
  `vendorid` int(11) UNSIGNED DEFAULT NULL,
  `active` tinyint(1) DEFAULT 0,
  `catName` varchar(255) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `vendorssubcats`
--

CREATE TABLE `vendorssubcats` (
  `id` int(11) UNSIGNED NOT NULL,
  `categoryid` int(11) UNSIGNED DEFAULT NULL,
  `assign` int(11) DEFAULT 0,
  `active` int(11) DEFAULT 0,
  `subCatName` varchar(255) CHARACTER SET utf8 NOT NULL,
  `url` varchar(2083) CHARACTER SET utf8 NOT NULL,
  `lastCrawl` datetime DEFAULT NULL,
  `delString` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `crawl` tinyint(3) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `brand`
--
ALTER TABLE `brand`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `crawler`
--
ALTER TABLE `crawler`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `discounts`
--
ALTER TABLE `discounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pid` (`pid`);

--
-- Indexes for table `latest`
--
ALTER TABLE `latest`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pid` (`pid`);

--
-- Indexes for table `offers`
--
ALTER TABLE `offers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `productID` (`productID`),
  ADD KEY `vendorID` (`vendorID`);

--
-- Indexes for table `pricehistory`
--
ALTER TABLE `pricehistory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `offerID` (`offerID`),
  ADD KEY `tempProdId` (`tempProdId`);

--
-- Indexes for table `pricesupdated`
--
ALTER TABLE `pricesupdated`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pid` (`pid`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subcategories`
--
ALTER TABLE `subcategories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoryid` (`categoryid`);

--
-- Indexes for table `tempproducts`
--
ALTER TABLE `tempproducts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendorId` (`vendorId`),
  ADD KEY `modelStrip` (`modelStrip`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vendorscats`
--
ALTER TABLE `vendorscats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendorid` (`vendorid`);

--
-- Indexes for table `vendorssubcats`
--
ALTER TABLE `vendorssubcats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoryid` (`categoryid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `brand`
--
ALTER TABLE `brand`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `discounts`
--
ALTER TABLE `discounts`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `latest`
--
ALTER TABLE `latest`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `offers`
--
ALTER TABLE `offers`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pricehistory`
--
ALTER TABLE `pricehistory`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pricesupdated`
--
ALTER TABLE `pricesupdated`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subcategories`
--
ALTER TABLE `subcategories`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tempproducts`
--
ALTER TABLE `tempproducts`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vendorscats`
--
ALTER TABLE `vendorscats`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vendorssubcats`
--
ALTER TABLE `vendorssubcats`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `discounts`
--
ALTER TABLE `discounts`
  ADD CONSTRAINT `discounts_ibfk_1` FOREIGN KEY (`pid`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `latest`
--
ALTER TABLE `latest`
  ADD CONSTRAINT `latest_ibfk_1` FOREIGN KEY (`pid`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `offers`
--
ALTER TABLE `offers`
  ADD CONSTRAINT `offers_ibfk_1` FOREIGN KEY (`productID`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `offers_ibfk_2` FOREIGN KEY (`vendorID`) REFERENCES `vendors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pricehistory`
--
ALTER TABLE `pricehistory`
  ADD CONSTRAINT `pricehistory_ibfk_3` FOREIGN KEY (`offerID`) REFERENCES `offers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pricehistory_ibfk_4` FOREIGN KEY (`tempProdId`) REFERENCES `tempproducts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pricesupdated`
--
ALTER TABLE `pricesupdated`
  ADD CONSTRAINT `pricesupdated_ibfk_1` FOREIGN KEY (`pid`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `subcategories`
--
ALTER TABLE `subcategories`
  ADD CONSTRAINT `subcategories_ibfk_1` FOREIGN KEY (`categoryid`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tempproducts`
--
ALTER TABLE `tempproducts`
  ADD CONSTRAINT `tempproducts_ibfk_1` FOREIGN KEY (`vendorId`) REFERENCES `vendors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `vendorscats`
--
ALTER TABLE `vendorscats`
  ADD CONSTRAINT `vendorscats_ibfk_1` FOREIGN KEY (`vendorid`) REFERENCES `vendors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `vendorssubcats`
--
ALTER TABLE `vendorssubcats`
  ADD CONSTRAINT `vendorssubcats_ibfk_1` FOREIGN KEY (`categoryid`) REFERENCES `vendorscats` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
