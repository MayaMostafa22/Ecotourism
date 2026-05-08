<?php
require_once '../../models/Payment.php';
require_once '../../models/RevenueSplitting.php';
require_once '../../models/GuideWallet.php';
require_once '../../models/WithdrawalRequest.php';
require_once '../../models/Voucher.php';

class FinanceController
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // ==========================================
    // 1. Automatic Payment & Revenue Splitting
    // ==========================================
    public function processPayment($bookingId, $amount, $currency, $exchangeRate, $guideId)
    {
        if (!isset($_SESSION["UserRole"]) || $_SESSION["UserRole"] !== 'Traveler') {
            $_SESSION["errMsg"] = "Unauthorized.";
            return false;
        }

        // Handle currency string to exactly match the database ENUM
        if (strtoupper($currency) === 'USD' || strtoupper($currency) === 'DOLLAR') {
            $currency = '$Dollar';
        }

        $paymentModel = new Payment();
        if ($paymentModel->getPaymentByBookingId($bookingId)) {
            $_SESSION["errMsg"] = "Payment already exists for this booking.";
            return false;
        }

        // Round the total amount to avoid decimal issues in the database (decimal 10,0)
        $amount = round($amount, 0);

        $isCreated = $paymentModel->createPayment($bookingId, $amount, $currency, $exchangeRate);

        if ($isCreated) {
            
            // Calculate percentages and round to the nearest whole number
            $platformFee = round($amount * 0.10, 0); 
            $communityTax = round($amount * 0.05, 0); 
            $guideEarning = round($amount * 0.85, 0); 

            $walletModel = new GuideWallet();
            $wallet = $walletModel->getWalletByGuideId($guideId);
            
            if (!$wallet) {
                $walletModel->createWallet($guideId, 0.0, 0.0);
                $wallet = $walletModel->getWalletByGuideId($guideId);
            }

            $splitModel = new RevenueSplitting();
            $splitModel->createSplit($communityTax, $guideEarning, $platformFee, $bookingId, $wallet['WalletID'] ?? $guideId);

            // Round the pending balance update
            $newPending = round($wallet['PendingBalance'] + $guideEarning, 0);
            $walletModel->updateBalances($guideId, $wallet['AvailableBalance'], $newPending);

            $voucherModel = new Voucher();
            $expireDate = date('Y-m-d H:i:s', strtotime('+1 month'));
            $qrCode = md5($bookingId . time()); 
            $voucherModel->createVoucher('Active', $expireDate, $qrCode, $bookingId);

            $_SESSION["successMsg"] = "Payment processed successfully. Voucher generated.";
            return true;
        }
        return false;
    }

    // ==========================================
    // 2. Scan Voucher & Release Funds
    // ==========================================
    public function scanAndReleaseFunds($voucherId, $guideId)
    {
        if (!isset($_SESSION["UserRole"]) || ($_SESSION["UserRole"] !== 'Guide' && $_SESSION["UserRole"] !== 'Admin')) {
            return false;
        }

        $voucherModel = new Voucher();
        $voucher = $voucherModel->getVoucherById($voucherId);

        if (!$voucher || $voucher['Status'] === 'Expired') {
            $_SESSION["errMsg"] = "Voucher is invalid or already scanned.";
            return false;
        }

        $isScanned = $voucherModel->scanVoucher($voucherId);

        if ($isScanned) {
            $splitModel = new RevenueSplitting();
            $split = $splitModel->getSplitByBooking($voucher['BookingID']);

            if ($split) {
                $guideEarning = $split['GuideEarning'];
                
                $walletModel = new GuideWallet();
                $walletModel->releasePendingBalance($guideId, $guideEarning);
                
                $_SESSION["successMsg"] = "Voucher scanned. Funds added to your available balance.";
                return true;
            }
        }
        return false;
    }

    // ==========================================
    // 3. Guide Withdrawal Request
    // ==========================================
    public function requestWithdrawal($amount)
    {
        if (!isset($_SESSION["UserID"]) || $_SESSION["UserRole"] !== 'Guide') {
            return false;
        }

        $guideID = $_SESSION["UserID"];
        $walletModel = new GuideWallet();
        $wallet = $walletModel->getWalletByGuideId($guideID);

        if (!$wallet || $wallet['AvailableBalance'] < $amount) {
            $_SESSION["errMsg"] = "Insufficient available balance.";
            return false;
        }

        $requestModel = new WithdrawalRequest();
        $isRequested = $requestModel->createRequest($amount, 'Pending', $guideID);

        if ($isRequested) {
            $newAvailable = $wallet['AvailableBalance'] - $amount;
            $walletModel->updateBalances($guideID, $newAvailable, $wallet['PendingBalance']);

            $_SESSION["successMsg"] = "Withdrawal requested. Amount deducted from available balance.";
            return true;
        }
        return false;
    }

    // ==========================================
    // 4. Process Withdrawal Requests (Admin)
    // ==========================================
    public function processWithdrawalRequest($requestId, $status)
    {
        if (!isset($_SESSION["UserRole"]) || $_SESSION["UserRole"] !== 'Admin') {
            return false;
        }

        $requestModel = new WithdrawalRequest();
        $requests = $requestModel->getAllRequests();
        $currentRequest = null;
        
        foreach($requests as $req) {
            if ($req['RequestID'] == $requestId) {
                $currentRequest = $req;
                break;
            }
        }

        if (!$currentRequest || $currentRequest['Status'] !== 'Pending') {
            $_SESSION["errMsg"] = "Invalid request or already processed.";
            return false;
        }

        $isUpdated = $requestModel->updateRequestStatus($requestId, $status);

        if ($isUpdated && $status === 'Rejected') {
            $walletModel = new GuideWallet();
            $wallet = $walletModel->getWalletByGuideId($currentRequest['GuideID']);
            
            $refundedAvailable = $wallet['AvailableBalance'] + $currentRequest['Amount'];
            $walletModel->updateBalances($currentRequest['GuideID'], $refundedAvailable, $wallet['PendingBalance']);
            
            $_SESSION["successMsg"] = "Request rejected. Funds returned to guide's wallet.";
            return true;
        }

        $_SESSION["successMsg"] = "Withdrawal request status updated to: " . $status;
        return true;
    }

    // ==========================================
    // 5. Getters / View Functions
    // ==========================================
    public function getBookingPayment($bookingId)
    {
        $paymentModel = new Payment();
        return $paymentModel->getPaymentByBookingId($bookingId);
    }

    public function getAllPayments()
    {
        if (!isset($_SESSION["UserRole"]) || $_SESSION["UserRole"] !== 'Admin') {
            return [];
        }
        $paymentModel = new Payment();
        return $paymentModel->getAllPayments();
    }

    public function viewMyWallet()
    {
        if (!isset($_SESSION["UserID"]) || $_SESSION["UserRole"] !== 'Guide') {
            return false;
        }
        $walletModel = new GuideWallet();
        return $walletModel->getWalletByGuideId($_SESSION["UserID"]);
    }

    public function getMyWithdrawalRequests()
    {
        if (!isset($_SESSION["UserID"]) || $_SESSION["UserRole"] !== 'Guide') {
            return [];
        }
        $requestModel = new WithdrawalRequest();
        return $requestModel->getRequestsByGuide($_SESSION["UserID"]);
    }

    public function getAllWithdrawalRequests()
    {
        if (!isset($_SESSION["UserRole"]) || $_SESSION["UserRole"] !== 'Admin') {
            return [];
        }
        $requestModel = new WithdrawalRequest();
        return $requestModel->getAllRequests();
    }
    
    public function getRevenueSplits()
    {
        if (!isset($_SESSION["UserRole"]) || $_SESSION["UserRole"] !== 'Admin') {
            return [];
        }
        $splitModel = new RevenueSplitting();
        return $splitModel->getAllSplits();
    }
}
?>