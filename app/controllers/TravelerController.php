<?php
require_once __DIR__ . '/../models/Traveler.php';
require_once __DIR__ . '/../models/Language.php';
require_once __DIR__ . '/../models/BriefingDocument.php';
require_once __DIR__ . '/../models/TranslatedDoc.php';

class TravelerController
{
    private $travelerModel;
    private $languageModel;
    private $briefingModel;
    private $translatedDocModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Profile: Displays the dashboard for the traveler.
     * Fetches personal data and supported system languages.
     */
    public function profile()
    {
        if (!isset($_SESSION['UserID']) || $_SESSION['UserRole'] !== 'traveler') {
            $_SESSION['errMsg'] = "Unauthorized access. Please login.";
            return false;
        }

        $this->travelerModel = new Traveler();
        $this->languageModel = new Language();

        $id        = $_SESSION['UserID'];
        $traveler  = $this->travelerModel->getTravelerById($id);
        $languages = $this->languageModel->getAllLanguages();

        return [
            'traveler'  => $traveler,
            'languages' => $languages
        ];
    }

    /**
     * Update Currency: Implements Requirement #23 (Multi-Currency Engine).
     * Saves the traveler's preferred currency for price localization.
     */
    public function updateCurrency()
    {
        if (!isset($_SESSION['UserID']) || $_SESSION['UserRole'] !== 'traveler') {
            $_SESSION['errMsg'] = "Unauthorized access. Please login.";
            return false;
        }

        $id                = $_SESSION['UserID'];
        $preferredCurrency = $_POST['preferred_currency'];

        if (empty($preferredCurrency)) {
            $_SESSION['errMsg'] = "Currency is required.";
            return false;
        }

        $this->travelerModel = new Traveler();

        if ($this->travelerModel->updateTraveler($id, $preferredCurrency)) {
            $_SESSION['successMsg'] = "Currency updated successfully.";
            return true;
        }

        $_SESSION['errMsg'] = "Failed to update currency.";
        return false;
    }

    /**
     * Documents: Implements Requirement #6 ("Leave No Trace" Briefing).
     * Retrieves briefing documents related to a specific experience.
     */
    public function documents($expID)
    {
        if (!isset($_SESSION['UserID']) || $_SESSION['UserRole'] !== 'traveler') {
            $_SESSION['errMsg'] = "Unauthorized access. Please login.";
            return false;
        }

        if (empty($expID)) {
            $_SESSION['errMsg'] = "Experience ID is required.";
            return false;
        }

        $this->briefingModel = new BriefingDocument();

        $allDocs = $this->briefingModel->getAllBriefingDocuments();
        $docs    = array_filter($allDocs, fn($d) => $d['ExpID'] == $expID);

        return array_values($docs);
    }

    /**
     * View Document: Displays details of a specific briefing document.
     * Links the original document with its available translations.
     */
    public function viewDocument($docId)
    {
        if (!isset($_SESSION['UserID']) || $_SESSION['UserRole'] !== 'traveler') {
            $_SESSION['errMsg'] = "Unauthorized access. Please login.";
            return false;
        }

        if (empty($docId)) {
            $_SESSION['errMsg'] = "Document ID is required.";
            return false;
        }

        $this->briefingModel      = new BriefingDocument();
        $this->translatedDocModel = new TranslatedDoc();

        $document        = $this->briefingModel->getBriefingDocumentById($docId);
        $allTranslations = $this->translatedDocModel->getAllTranslatedDocs();
        $translations    = array_filter(
            $allTranslations,
            fn($t) => $t['DocID'] == $docId
        );

        return [
            'document'     => $document,
            'translations' => array_values($translations)
        ];
    }

    /**
     * Set Language: Updates the Application UI language.
     * Stores the selection in the session for immediate UI adaptation.
     */
    public function setLanguage()
    {
        if (!isset($_SESSION['UserID']) || $_SESSION['UserRole'] !== 'traveler') {
            $_SESSION['errMsg'] = "Unauthorized access. Please login.";
            return false;
        }

        if (!isset($_POST['lang_id'])) {
            $_SESSION['errMsg'] = "Language ID is required.";
            return false;
        }

        $this->languageModel = new Language();

        $langId   = $_POST['lang_id'];
        $language = $this->languageModel->getLanguageById($langId);

        if ($language) {
            $_SESSION['ui_lang']    = $language['LangName'];
            $_SESSION['successMsg'] = "Language updated successfully.";
            return true;
        }

        $_SESSION['errMsg'] = "Language not found.";
        return false;
    }
}