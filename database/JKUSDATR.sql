-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 03, 2017 at 11:26 AM
-- Server version: 5.7.17-0ubuntu0.16.04.1
-- PHP Version: 7.0.15-0ubuntu0.16.04.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `JKUSDATR`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `AdminID` int(10) NOT NULL,
  `Username` varchar(100) NOT NULL,
  `Password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `AUTH`
--

CREATE TABLE `AUTH` (
  `USER` char(64) NOT NULL,
  `PASS` char(64) NOT NULL,
  `NAME` char(64) NOT NULL,
  `GROUPtr` int(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `AUTH`
--

INSERT INTO `AUTH` (`USER`, `PASS`, `NAME`, `GROUPtr`) VALUES
('0410a4812f90103487093896454282c1', '9fdc1e5f5194470dd93711e6d09e7ff2', 'Treasurer', 0);

-- --------------------------------------------------------

--
-- Table structure for table `authsession`
--

CREATE TABLE `authsession` (
  `Ind` int(11) NOT NULL,
  `SID` char(64) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `churchdeaconry`
--

CREATE TABLE `churchdeaconry` (
  `uid` char(32) NOT NULL,
  `churchid` int(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `churches`
--

CREATE TABLE `churches` (
  `Ind` int(11) NOT NULL,
  `Church` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `churchgroups`
--

CREATE TABLE `churchgroups` (
  `Gid` char(32) NOT NULL,
  `Gname` char(32) NOT NULL,
  `churchid` int(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `churchtreasurers`
--

CREATE TABLE `churchtreasurers` (
  `uid` char(32) NOT NULL,
  `churchid` int(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `churchtreasury`
--

CREATE TABLE `churchtreasury` (
  `uid` char(32) NOT NULL,
  `churchid` int(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ci_sessions`
--

CREATE TABLE `ci_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(16) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) DEFAULT NULL,
  `last_activity` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `user_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `downloadedreceipts`
--

CREATE TABLE `downloadedreceipts` (
  `Ind` bigint(20) NOT NULL,
  `CollectionDate` date NOT NULL,
  `Name` varchar(128) NOT NULL,
  `Tithe` decimal(11,2) NOT NULL,
  `Combined` decimal(11,2) NOT NULL,
  `CampOffering` decimal(11,2) NOT NULL,
  `Building` decimal(11,2) NOT NULL,
  `Uns1` char(64) NOT NULL,
  `Amt1` decimal(11,2) NOT NULL,
  `Uns2` char(64) NOT NULL,
  `Amt2` decimal(11,2) NOT NULL,
  `Uns3` char(64) NOT NULL,
  `Amt3` decimal(11,2) NOT NULL,
  `Uns4` char(64) NOT NULL,
  `Amt4` decimal(11,2) NOT NULL,
  `Uns5` char(64) NOT NULL,
  `Amt5` decimal(11,2) NOT NULL,
  `Uns6` char(64) NOT NULL,
  `Amt6` decimal(11,2) NOT NULL,
  `Uns7` char(64) NOT NULL,
  `Amt7` decimal(11,2) NOT NULL,
  `Uploaded` char(2) NOT NULL DEFAULT 'F',
  `Email` varchar(100) NOT NULL DEFAULT '',
  `EmailSent` enum('T','F') NOT NULL DEFAULT 'F'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `Gid` int(12) NOT NULL,
  `GroupName` char(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`Gid`, `GroupName`) VALUES
(1, 'AMO'),
(2, 'ALO');

-- --------------------------------------------------------

--
-- Table structure for table `grouptreasurers`
--

CREATE TABLE `grouptreasurers` (
  `induid` char(32) NOT NULL,
  `uid` char(32) NOT NULL,
  `groupid` char(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `grouptreasurers`
--

INSERT INTO `grouptreasurers` (`induid`, `uid`, `groupid`) VALUES
('10dc86ae7b742985a14d5db70151ae4e', 'dade7cebc320e73bbf1798355f9a6c66', '29aec2ea61b91233edad04882c6c2d5e'),
('d2a2978fd41258f65a137d2f66110565', 'dade7cebc320e73bbf1798355f9a6c66', 'e1ffd6cf2145da960ef6da23aabbc9bb');

-- --------------------------------------------------------

--
-- Table structure for table `receipts`
--

CREATE TABLE `receipts` (
  `Ind` bigint(20) NOT NULL,
  `CollectionDate` date NOT NULL,
  `Name` char(128) NOT NULL,
  `Tithe` decimal(11,2) NOT NULL,
  `Combined` decimal(11,2) NOT NULL,
  `CampOffering` decimal(11,2) NOT NULL,
  `Building` decimal(11,2) NOT NULL,
  `Uns1` char(64) NOT NULL,
  `Amt1` decimal(11,2) NOT NULL,
  `Uns2` char(64) NOT NULL,
  `Amt2` decimal(11,2) NOT NULL,
  `Uns3` char(64) NOT NULL,
  `Amt3` decimal(11,2) NOT NULL,
  `Uns4` char(64) NOT NULL,
  `Amt4` decimal(11,2) NOT NULL,
  `Uns5` char(64) NOT NULL,
  `Amt5` decimal(11,2) NOT NULL,
  `Uns6` char(64) NOT NULL,
  `Amt6` decimal(11,2) NOT NULL,
  `Uns7` char(64) NOT NULL,
  `Amt7` decimal(11,2) NOT NULL,
  `Uploaded` char(2) NOT NULL DEFAULT 'F',
  `Email` varchar(64) NOT NULL DEFAULT '',
  `EmailSent` enum('T','F') NOT NULL DEFAULT 'F'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`AdminID`);

--
-- Indexes for table `AUTH`
--
ALTER TABLE `AUTH`
  ADD PRIMARY KEY (`USER`);

--
-- Indexes for table `authsession`
--
ALTER TABLE `authsession`
  ADD PRIMARY KEY (`Ind`);

--
-- Indexes for table `churchdeaconry`
--
ALTER TABLE `churchdeaconry`
  ADD PRIMARY KEY (`uid`);

--
-- Indexes for table `churches`
--
ALTER TABLE `churches`
  ADD PRIMARY KEY (`Ind`),
  ADD UNIQUE KEY `Church` (`Church`);

--
-- Indexes for table `churchgroups`
--
ALTER TABLE `churchgroups`
  ADD PRIMARY KEY (`Gid`);

--
-- Indexes for table `churchtreasurers`
--
ALTER TABLE `churchtreasurers`
  ADD PRIMARY KEY (`uid`);

--
-- Indexes for table `churchtreasury`
--
ALTER TABLE `churchtreasury`
  ADD PRIMARY KEY (`uid`);

--
-- Indexes for table `ci_sessions`
--
ALTER TABLE `ci_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `last_activity` (`last_activity`);

--
-- Indexes for table `downloadedreceipts`
--
ALTER TABLE `downloadedreceipts`
  ADD PRIMARY KEY (`Ind`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`Gid`);

--
-- Indexes for table `grouptreasurers`
--
ALTER TABLE `grouptreasurers`
  ADD PRIMARY KEY (`induid`);

--
-- Indexes for table `receipts`
--
ALTER TABLE `receipts`
  ADD PRIMARY KEY (`Ind`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `AdminID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `authsession`
--
ALTER TABLE `authsession`
  MODIFY `Ind` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `churches`
--
ALTER TABLE `churches`
  MODIFY `Ind` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `Gid` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `receipts`
--
ALTER TABLE `receipts`
  MODIFY `Ind` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13278;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
