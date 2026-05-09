-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 09, 2026 at 03:00 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `eco-tourism`
--

-- --------------------------------------------------------

--
-- Table structure for table `addon`
--

CREATE TABLE `addon` (
  `AddOnID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Price` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `AdminType` varchar(50) NOT NULL,
  `CreatedAt` datetime NOT NULL,
  `AdminID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auditlog`
--

CREATE TABLE `auditlog` (
  `LogID` int(11) NOT NULL,
  `ExpID` int(11) NOT NULL,
  `FiledType` varchar(100) NOT NULL,
  `TimeStamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `AdminID` int(11) NOT NULL,
  `OldValue` varchar(1000) NOT NULL,
  `NewValue` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `BookingID` int(11) NOT NULL,
  `TravelerID` int(11) NOT NULL,
  `ExpID` int(11) NOT NULL,
  `BookingDate` datetime NOT NULL,
  `Status` enum('Pending','Confirmed','Cancelled') NOT NULL,
  `IsWaitListAccepted` tinyint(1) NOT NULL,
  `GroupSize` int(11) NOT NULL,
  `DiscountID` int(11) NOT NULL,
  `DiscountAmount` decimal(11,0) NOT NULL,
  `BasePrice` decimal(11,0) NOT NULL,
  `FinalPrice` decimal(11,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bookingaddon`
--

CREATE TABLE `bookingaddon` (
  `AddOnID` int(11) NOT NULL,
  `BookingID` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bookingescrow`
--

CREATE TABLE `bookingescrow` (
  `EscrowId` int(11) NOT NULL,
  `BookingId` int(11) NOT NULL,
  `Status` enum('Held','Releasd','Refunded') NOT NULL,
  `AmountHeld` decimal(10,0) NOT NULL,
  `EnteryDate` datetime NOT NULL,
  `ReleasedDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `booking_offset_projects`
--

CREATE TABLE `booking_offset_projects` (
  `ProjectID` int(11) NOT NULL,
  `BookingID` int(11) NOT NULL,
  `offsetAmounts` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `briefingdocument`
--

CREATE TABLE `briefingdocument` (
  `DocID` int(11) NOT NULL,
  `ExpID` int(11) NOT NULL,
  `URL` varchar(900) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cancellation`
--

CREATE TABLE `cancellation` (
  `CancellationId` int(11) NOT NULL,
  `BookingId` int(11) NOT NULL,
  `CancellationDate` datetime NOT NULL,
  `RefundedPercentage` decimal(10,0) NOT NULL,
  `CancellationFee` decimal(10,0) NOT NULL,
  `ReasonOfCancellation` varchar(900) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `carbon_offset_projects`
--

CREATE TABLE `carbon_offset_projects` (
  `ProjectID` int(11) NOT NULL,
  `ProjectName` varchar(200) NOT NULL,
  `Description` varchar(1500) NOT NULL,
  `CostOfCarbonUnit` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `communityconsent`
--

CREATE TABLE `communityconsent` (
  `consentID` int(11) NOT NULL,
  `Status` enum('Approved','Refused') NOT NULL,
  `contractURL` varchar(900) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `complaint`
--

CREATE TABLE `complaint` (
  `ComplaintID` int(11) NOT NULL,
  `BookingID` int(11) NOT NULL,
  `TravelerID` int(11) NOT NULL,
  `GuideID` int(11) NOT NULL,
  `AdminID` int(11) NOT NULL,
  `PhotosURL` varchar(500) NOT NULL,
  `Description` varchar(1000) NOT NULL,
  `Status` enum('Resolved','Rejected') NOT NULL,
  `Resolution` varchar(1000) NOT NULL,
  `CreatedDate` datetime NOT NULL,
  `ResolvedDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ecocertificate`
--

CREATE TABLE `ecocertificate` (
  `CertificateID` int(11) NOT NULL,
  `CertificateURL` varchar(500) NOT NULL,
  `CertificateName` varchar(500) NOT NULL,
  `GuideID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `experience`
--

CREATE TABLE `experience` (
  `ExperienceID` int(11) NOT NULL,
  `ExperienceName` varchar(80) NOT NULL,
  `Description` varchar(700) NOT NULL,
  `EcoLeafRating` decimal(10,0) NOT NULL,
  `Status` varchar(80) NOT NULL,
  `basePrice` decimal(10,0) NOT NULL,
  `locations` varchar(500) NOT NULL,
  `StartTime` datetime NOT NULL,
  `EndTime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `experiencetier`
--

CREATE TABLE `experiencetier` (
  `TierID` int(11) NOT NULL,
  `ExpID` int(11) NOT NULL,
  `TierName` varchar(100) NOT NULL,
  `MaxCapacity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exp_resource`
--

CREATE TABLE `exp_resource` (
  `ExpID` int(11) NOT NULL,
  `ResourceID` int(11) NOT NULL,
  `QuantityPerPerson` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fieldreports`
--

CREATE TABLE `fieldreports` (
  `ReportID` int(11) NOT NULL,
  `MediaURL` varchar(500) NOT NULL,
  `ReportText` varchar(500) NOT NULL,
  `TimeStamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `GuideID` int(11) NOT NULL,
  `ExpID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guide`
--

CREATE TABLE `guide` (
  `GuideID` int(11) NOT NULL,
  `IdentityID` int(11) NOT NULL,
  `localCredScore` int(11) NOT NULL,
  `yearsOfExperience` int(11) NOT NULL,
  `isVerified` tinyint(1) NOT NULL,
  `baseCountry` varchar(100) NOT NULL,
  `status` varchar(100) NOT NULL,
  `yearsOfResidency` int(11) NOT NULL,
  `CancellationRate` int(11) NOT NULL,
  `VisibilityScore` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guideidentity`
--

CREATE TABLE `guideidentity` (
  `IdentityID` int(11) NOT NULL,
  `Status` enum('Expired','Active') NOT NULL,
  `VerificationDate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guidelang`
--

CREATE TABLE `guidelang` (
  `GuideID` int(11) NOT NULL,
  `LangID` int(11) NOT NULL,
  `proficiencyLevel` enum('Advanced','Intermediate','Beginner') NOT NULL,
  `VerificationStatus` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guideschedule`
--

CREATE TABLE `guideschedule` (
  `ScheduleID` int(11) NOT NULL,
  `ExpID` int(11) NOT NULL,
  `GuideID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guidewallet`
--

CREATE TABLE `guidewallet` (
  `WalletID` int(11) NOT NULL,
  `AvailableBalance` decimal(10,0) NOT NULL,
  `PendingBalance` decimal(10,0) NOT NULL,
  `GuideID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `language`
--

CREATE TABLE `language` (
  `LangID` int(11) NOT NULL,
  `LangName` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE `location` (
  `LocationID` int(11) NOT NULL,
  `coordinates` point NOT NULL,
  `LocationName` varchar(500) NOT NULL,
  `IsProtectedLand` tinyint(1) NOT NULL,
  `IsIndigenousLand` tinyint(1) NOT NULL,
  `ConsentID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `locationstops`
--

CREATE TABLE `locationstops` (
  `LocationID` int(11) NOT NULL,
  `ExpID` int(11) NOT NULL,
  `DistanceToNext` decimal(10,0) NOT NULL,
  `TransportMode` enum('Walking','Mountain Bicycles','Camel / Horse','Sailboat / Felucca','Electric Vehicles') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `TransactionId` int(11) NOT NULL,
  `BookingId` int(11) NOT NULL,
  `Amount` decimal(10,0) NOT NULL,
  `Currency` enum('$Dollar','EGP') NOT NULL,
  `ExchangeRate` decimal(10,0) NOT NULL,
  `PaymentDate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `resource`
--

CREATE TABLE `resource` (
  `ResourceID` int(11) NOT NULL,
  `ResourceName` varchar(100) NOT NULL,
  `TotallNum` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `revenuespliting`
--

CREATE TABLE `revenuespliting` (
  `SplitID` int(11) NOT NULL,
  `CommunityTax` decimal(10,0) NOT NULL,
  `TransactionDate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `GuideEarning` decimal(10,0) NOT NULL,
  `PlatFormFee` decimal(10,0) NOT NULL,
  `BookingID` int(11) NOT NULL,
  `WalletID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `ReviewID` int(11) NOT NULL,
  `BookingID` int(11) NOT NULL,
  `TravelerID` int(11) NOT NULL,
  `Rating` decimal(10,0) NOT NULL,
  `CulturalRespectScore` decimal(11,0) NOT NULL,
  `Comment` varchar(900) NOT NULL,
  `Date` datetime NOT NULL,
  `EcoScore` decimal(11,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `seasonalrule`
--

CREATE TABLE `seasonalrule` (
  `RuleID` int(11) NOT NULL,
  `ExpID` int(11) NOT NULL,
  `Reason` varchar(500) NOT NULL,
  `ActionType` varchar(50) NOT NULL,
  `StartDate` datetime NOT NULL,
  `EndDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sentimentflag`
--

CREATE TABLE `sentimentflag` (
  `FlagID` int(11) NOT NULL,
  `ReviewID` int(11) NOT NULL,
  `KeywordDetected` varchar(100) NOT NULL,
  `FlagType` enum('Environmental','Culture','Safety') NOT NULL,
  `Severity` enum('Low','High','Medium') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `translateddoc`
--

CREATE TABLE `translateddoc` (
  `DocID` int(11) NOT NULL,
  `LangID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `traveler`
--

CREATE TABLE `traveler` (
  `TravelerID` int(11) NOT NULL,
  `PreferredCurrency` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `UserName` varchar(100) NOT NULL,
  `PhoneNo.` varchar(50) NOT NULL,
  `UserRole` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `voucher`
--

CREATE TABLE `voucher` (
  `VoucherID` int(11) NOT NULL,
  `Status` enum('Active','Expired') NOT NULL,
  `GeneratedDate` datetime NOT NULL,
  `ScannedDate` datetime NOT NULL,
  `ExpireDate` datetime NOT NULL,
  `QRCode` varchar(500) NOT NULL,
  `BookingID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `waitlist`
--

CREATE TABLE `waitlist` (
  `WaitListID` int(11) NOT NULL,
  `ExpID` int(11) NOT NULL,
  `TravelerID` int(11) NOT NULL,
  `QueuePosition` int(11) NOT NULL,
  `JoinedDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `withdrawalrequest`
--

CREATE TABLE `withdrawalrequest` (
  `RequestID` int(11) NOT NULL,
  `TimeStamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Amount` decimal(10,0) NOT NULL,
  `Status` enum('Rejected','Pending','Approved') NOT NULL,
  `GuideID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addon`
--
ALTER TABLE `addon`
  ADD PRIMARY KEY (`AddOnID`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD KEY `AdminID` (`AdminID`);

--
-- Indexes for table `auditlog`
--
ALTER TABLE `auditlog`
  ADD PRIMARY KEY (`LogID`),
  ADD KEY `AdminID` (`AdminID`),
  ADD KEY `ExpID` (`ExpID`);

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`BookingID`),
  ADD KEY `ExpID` (`ExpID`),
  ADD KEY `TravelerID` (`TravelerID`);

--
-- Indexes for table `bookingaddon`
--
ALTER TABLE `bookingaddon`
  ADD PRIMARY KEY (`AddOnID`,`BookingID`);

--
-- Indexes for table `bookingescrow`
--
ALTER TABLE `bookingescrow`
  ADD PRIMARY KEY (`EscrowId`),
  ADD KEY `BookingId` (`BookingId`);

--
-- Indexes for table `booking_offset_projects`
--
ALTER TABLE `booking_offset_projects`
  ADD PRIMARY KEY (`ProjectID`,`BookingID`);

--
-- Indexes for table `briefingdocument`
--
ALTER TABLE `briefingdocument`
  ADD PRIMARY KEY (`DocID`);

--
-- Indexes for table `cancellation`
--
ALTER TABLE `cancellation`
  ADD PRIMARY KEY (`CancellationId`),
  ADD KEY `BookingId` (`BookingId`);

--
-- Indexes for table `carbon_offset_projects`
--
ALTER TABLE `carbon_offset_projects`
  ADD PRIMARY KEY (`ProjectID`);

--
-- Indexes for table `communityconsent`
--
ALTER TABLE `communityconsent`
  ADD PRIMARY KEY (`consentID`);

--
-- Indexes for table `complaint`
--
ALTER TABLE `complaint`
  ADD PRIMARY KEY (`ComplaintID`),
  ADD KEY `AdminID` (`AdminID`),
  ADD KEY `GuideID` (`GuideID`),
  ADD KEY `TravelerID` (`TravelerID`),
  ADD KEY `BookingID` (`BookingID`);

--
-- Indexes for table `ecocertificate`
--
ALTER TABLE `ecocertificate`
  ADD PRIMARY KEY (`CertificateID`);

--
-- Indexes for table `experience`
--
ALTER TABLE `experience`
  ADD PRIMARY KEY (`ExperienceID`);

--
-- Indexes for table `experiencetier`
--
ALTER TABLE `experiencetier`
  ADD PRIMARY KEY (`TierID`),
  ADD KEY `ExpID` (`ExpID`);

--
-- Indexes for table `exp_resource`
--
ALTER TABLE `exp_resource`
  ADD PRIMARY KEY (`ExpID`,`ResourceID`);

--
-- Indexes for table `fieldreports`
--
ALTER TABLE `fieldreports`
  ADD PRIMARY KEY (`ReportID`),
  ADD KEY `GuideID` (`GuideID`),
  ADD KEY `ExpID` (`ExpID`);

--
-- Indexes for table `guide`
--
ALTER TABLE `guide`
  ADD KEY `GuideID` (`GuideID`),
  ADD KEY `IdentityID` (`IdentityID`);

--
-- Indexes for table `guideidentity`
--
ALTER TABLE `guideidentity`
  ADD PRIMARY KEY (`IdentityID`);

--
-- Indexes for table `guidelang`
--
ALTER TABLE `guidelang`
  ADD PRIMARY KEY (`GuideID`,`LangID`);

--
-- Indexes for table `guideschedule`
--
ALTER TABLE `guideschedule`
  ADD PRIMARY KEY (`ScheduleID`),
  ADD KEY `ExpID` (`ExpID`),
  ADD KEY `GuideID` (`GuideID`);

--
-- Indexes for table `guidewallet`
--
ALTER TABLE `guidewallet`
  ADD PRIMARY KEY (`WalletID`),
  ADD KEY `GuideID` (`GuideID`);

--
-- Indexes for table `language`
--
ALTER TABLE `language`
  ADD PRIMARY KEY (`LangID`);

--
-- Indexes for table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`LocationID`),
  ADD KEY `ConsentID` (`ConsentID`);

--
-- Indexes for table `locationstops`
--
ALTER TABLE `locationstops`
  ADD PRIMARY KEY (`LocationID`,`ExpID`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`TransactionId`),
  ADD KEY `BookingId` (`BookingId`);

--
-- Indexes for table `resource`
--
ALTER TABLE `resource`
  ADD PRIMARY KEY (`ResourceID`);

--
-- Indexes for table `revenuespliting`
--
ALTER TABLE `revenuespliting`
  ADD PRIMARY KEY (`SplitID`),
  ADD KEY `BookingID` (`BookingID`),
  ADD KEY `WalletID` (`WalletID`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`ReviewID`),
  ADD KEY `BookingID` (`BookingID`),
  ADD KEY `TravelerID` (`TravelerID`);

--
-- Indexes for table `seasonalrule`
--
ALTER TABLE `seasonalrule`
  ADD PRIMARY KEY (`RuleID`),
  ADD KEY `ExpID` (`ExpID`);

--
-- Indexes for table `sentimentflag`
--
ALTER TABLE `sentimentflag`
  ADD PRIMARY KEY (`FlagID`),
  ADD KEY `ReviewID` (`ReviewID`);

--
-- Indexes for table `translateddoc`
--
ALTER TABLE `translateddoc`
  ADD PRIMARY KEY (`DocID`,`LangID`);

--
-- Indexes for table `traveler`
--
ALTER TABLE `traveler`
  ADD KEY `TravelerID` (`TravelerID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`);

--
-- Indexes for table `voucher`
--
ALTER TABLE `voucher`
  ADD PRIMARY KEY (`VoucherID`);

--
-- Indexes for table `waitlist`
--
ALTER TABLE `waitlist`
  ADD PRIMARY KEY (`WaitListID`),
  ADD KEY `ExpID` (`ExpID`),
  ADD KEY `TravelerID` (`TravelerID`);

--
-- Indexes for table `withdrawalrequest`
--
ALTER TABLE `withdrawalrequest`
  ADD PRIMARY KEY (`RequestID`),
  ADD KEY `GuideID` (`GuideID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addon`
--
ALTER TABLE `addon`
  MODIFY `AddOnID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `auditlog`
--
ALTER TABLE `auditlog`
  MODIFY `LogID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `BookingID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bookingescrow`
--
ALTER TABLE `bookingescrow`
  MODIFY `EscrowId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `briefingdocument`
--
ALTER TABLE `briefingdocument`
  MODIFY `DocID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cancellation`
--
ALTER TABLE `cancellation`
  MODIFY `CancellationId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `carbon_offset_projects`
--
ALTER TABLE `carbon_offset_projects`
  MODIFY `ProjectID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `communityconsent`
--
ALTER TABLE `communityconsent`
  MODIFY `consentID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `complaint`
--
ALTER TABLE `complaint`
  MODIFY `ComplaintID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ecocertificate`
--
ALTER TABLE `ecocertificate`
  MODIFY `CertificateID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `experience`
--
ALTER TABLE `experience`
  MODIFY `ExperienceID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `experiencetier`
--
ALTER TABLE `experiencetier`
  MODIFY `TierID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fieldreports`
--
ALTER TABLE `fieldreports`
  MODIFY `ReportID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guideidentity`
--
ALTER TABLE `guideidentity`
  MODIFY `IdentityID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guideschedule`
--
ALTER TABLE `guideschedule`
  MODIFY `ScheduleID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guidewallet`
--
ALTER TABLE `guidewallet`
  MODIFY `WalletID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `language`
--
ALTER TABLE `language`
  MODIFY `LangID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `location`
--
ALTER TABLE `location`
  MODIFY `LocationID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `TransactionId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `resource`
--
ALTER TABLE `resource`
  MODIFY `ResourceID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `revenuespliting`
--
ALTER TABLE `revenuespliting`
  MODIFY `SplitID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `ReviewID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `seasonalrule`
--
ALTER TABLE `seasonalrule`
  MODIFY `RuleID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sentimentflag`
--
ALTER TABLE `sentimentflag`
  MODIFY `FlagID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `voucher`
--
ALTER TABLE `voucher`
  MODIFY `VoucherID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `waitlist`
--
ALTER TABLE `waitlist`
  MODIFY `WaitListID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `withdrawalrequest`
--
ALTER TABLE `withdrawalrequest`
  MODIFY `RequestID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`AdminID`) REFERENCES `users` (`UserID`);

--
-- Constraints for table `auditlog`
--
ALTER TABLE `auditlog`
  ADD CONSTRAINT `auditlog_ibfk_1` FOREIGN KEY (`AdminID`) REFERENCES `admin` (`AdminID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `auditlog_ibfk_2` FOREIGN KEY (`ExpID`) REFERENCES `experience` (`ExperienceID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`ExpID`) REFERENCES `experience` (`ExperienceID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `booking_ibfk_2` FOREIGN KEY (`TravelerID`) REFERENCES `traveler` (`TravelerID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bookingaddon`
--
ALTER TABLE `bookingaddon`
  ADD CONSTRAINT `bookingaddon_ibfk_1` FOREIGN KEY (`AddOnID`) REFERENCES `addon` (`AddOnID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bookingaddon_ibfk_2` FOREIGN KEY (`BookingID`) REFERENCES `booking` (`BookingID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bookingescrow`
--
ALTER TABLE `bookingescrow`
  ADD CONSTRAINT `bookingescrow_ibfk_1` FOREIGN KEY (`BookingId`) REFERENCES `booking` (`BookingID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `booking_offset_projects`
--
ALTER TABLE `booking_offset_projects`
  ADD CONSTRAINT `booking_offset_projects_ibfk_1` FOREIGN KEY (`BookingID`) REFERENCES `booking` (`BookingID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `booking_offset_projects_ibfk_2` FOREIGN KEY (`ProjectID`) REFERENCES `carbon_offset_projects` (`ProjectID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `briefingdocument`
--
ALTER TABLE `briefingdocument`
  ADD CONSTRAINT `briefingdocument_ibfk_1` FOREIGN KEY (`ExpID`) REFERENCES `experience` (`ExperienceID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cancellation`
--
ALTER TABLE `cancellation`
  ADD CONSTRAINT `cancellation_ibfk_1` FOREIGN KEY (`BookingId`) REFERENCES `booking` (`BookingID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `complaint`
--
ALTER TABLE `complaint`
  ADD CONSTRAINT `complaint_ibfk_1` FOREIGN KEY (`AdminID`) REFERENCES `admin` (`AdminID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `complaint_ibfk_2` FOREIGN KEY (`GuideID`) REFERENCES `guide` (`GuideID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `complaint_ibfk_3` FOREIGN KEY (`TravelerID`) REFERENCES `traveler` (`TravelerID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `complaint_ibfk_4` FOREIGN KEY (`BookingID`) REFERENCES `booking` (`BookingID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `experiencetier`
--
ALTER TABLE `experiencetier`
  ADD CONSTRAINT `experiencetier_ibfk_1` FOREIGN KEY (`ExpID`) REFERENCES `experience` (`ExperienceID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `exp_resource`
--
ALTER TABLE `exp_resource`
  ADD CONSTRAINT `exp_resource_ibfk_1` FOREIGN KEY (`ExpID`) REFERENCES `experience` (`ExperienceID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `exp_resource_ibfk_2` FOREIGN KEY (`ResourceID`) REFERENCES `resource` (`ResourceID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `fieldreports`
--
ALTER TABLE `fieldreports`
  ADD CONSTRAINT `fieldreports_ibfk_1` FOREIGN KEY (`GuideID`) REFERENCES `guide` (`GuideID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fieldreports_ibfk_2` FOREIGN KEY (`ExpID`) REFERENCES `experience` (`ExperienceID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `guide`
--
ALTER TABLE `guide`
  ADD CONSTRAINT `guide_ibfk_1` FOREIGN KEY (`GuideID`) REFERENCES `users` (`UserID`),
  ADD CONSTRAINT `guide_ibfk_2` FOREIGN KEY (`IdentityID`) REFERENCES `guideidentity` (`IdentityID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `guidelang`
--
ALTER TABLE `guidelang`
  ADD CONSTRAINT `guidelang_ibfk_1` FOREIGN KEY (`GuideID`) REFERENCES `guide` (`GuideID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `guidelang_ibfk_2` FOREIGN KEY (`LangID`) REFERENCES `language` (`LangID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `guideschedule`
--
ALTER TABLE `guideschedule`
  ADD CONSTRAINT `guideschedule_ibfk_1` FOREIGN KEY (`ExpID`) REFERENCES `experience` (`ExperienceID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `guideschedule_ibfk_2` FOREIGN KEY (`GuideID`) REFERENCES `guide` (`GuideID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `guidewallet`
--
ALTER TABLE `guidewallet`
  ADD CONSTRAINT `guidewallet_ibfk_1` FOREIGN KEY (`GuideID`) REFERENCES `guide` (`GuideID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `location`
--
ALTER TABLE `location`
  ADD CONSTRAINT `location_ibfk_1` FOREIGN KEY (`ConsentID`) REFERENCES `communityconsent` (`consentID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`BookingId`) REFERENCES `booking` (`BookingID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `revenuespliting`
--
ALTER TABLE `revenuespliting`
  ADD CONSTRAINT `revenuespliting_ibfk_1` FOREIGN KEY (`BookingID`) REFERENCES `booking` (`BookingID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `revenuespliting_ibfk_2` FOREIGN KEY (`WalletID`) REFERENCES `guidewallet` (`WalletID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `review_ibfk_1` FOREIGN KEY (`BookingID`) REFERENCES `booking` (`BookingID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `review_ibfk_2` FOREIGN KEY (`TravelerID`) REFERENCES `traveler` (`TravelerID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `seasonalrule`
--
ALTER TABLE `seasonalrule`
  ADD CONSTRAINT `seasonalrule_ibfk_1` FOREIGN KEY (`ExpID`) REFERENCES `experience` (`ExperienceID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sentimentflag`
--
ALTER TABLE `sentimentflag`
  ADD CONSTRAINT `sentimentflag_ibfk_1` FOREIGN KEY (`ReviewID`) REFERENCES `review` (`ReviewID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `translateddoc`
--
ALTER TABLE `translateddoc`
  ADD CONSTRAINT `translateddoc_ibfk_1` FOREIGN KEY (`DocID`) REFERENCES `briefingdocument` (`DocID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `translateddoc_ibfk_2` FOREIGN KEY (`LangID`) REFERENCES `language` (`LangID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `traveler`
--
ALTER TABLE `traveler`
  ADD CONSTRAINT `traveler_ibfk_1` FOREIGN KEY (`TravelerID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `voucher`
--
ALTER TABLE `voucher`
  ADD CONSTRAINT `voucher_ibfk_1` FOREIGN KEY (`BookingID`) REFERENCES `booking` (`BookingID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `waitlist`
--
ALTER TABLE `waitlist`
  ADD CONSTRAINT `waitlist_ibfk_1` FOREIGN KEY (`ExpID`) REFERENCES `experiencetier` (`TierID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `waitlist_ibfk_2` FOREIGN KEY (`TravelerID`) REFERENCES `traveler` (`TravelerID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `withdrawalrequest`
--
ALTER TABLE `withdrawalrequest`
  ADD CONSTRAINT `withdrawalrequest_ibfk_1` FOREIGN KEY (`GuideID`) REFERENCES `guide` (`GuideID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
