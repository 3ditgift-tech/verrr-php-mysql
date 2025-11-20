<?php
/**
 * Multi-language Support Class
 */

class Language {
    private static $instance = null;
    private $translations = [];
    private $currentLang = 'en';
    private $availableLanguages = ['en', 'fr', 'de', 'es'];
    
    private function __construct() {
        // Get language from session or cookie
        if (isset($_SESSION['language'])) {
            $this->currentLang = $_SESSION['language'];
        } elseif (isset($_COOKIE['language'])) {
            $this->currentLang = $_COOKIE['language'];
        } else {
            // Auto-detect from browser
            $this->currentLang = $this->detectBrowserLanguage();
        }
        
        // Load translations
        $this->loadTranslations($this->currentLang);
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Load translations from JSON file
     */
    private function loadTranslations($lang) {
        $file = __DIR__ . '/../languages/' . $lang . '.json';
        
        if (file_exists($file)) {
            $content = file_get_contents($file);
            $this->translations = json_decode($content, true);
        } else {
            // Fallback to English
            $file = __DIR__ . '/../languages/en.json';
            if (file_exists($file)) {
                $content = file_get_contents($file);
                $this->translations = json_decode($content, true);
            }
        }
    }
    
    /**
     * Get translation for a key
     */
    public function get($key, $default = null) {
        if (isset($this->translations[$key])) {
            return $this->translations[$key];
        }
        return $default ?? $key;
    }
    
    /**
     * Set current language
     */
    public function setLanguage($lang) {
        if (in_array($lang, $this->availableLanguages)) {
            $this->currentLang = $lang;
            $_SESSION['language'] = $lang;
            setcookie('language', $lang, time() + (86400 * 365), '/');
            $this->loadTranslations($lang);
            return true;
        }
        return false;
    }
    
    /**
     * Get current language
     */
    public function getCurrentLanguage() {
        return $this->currentLang;
    }
    
    /**
     * Get available languages
     */
    public function getAvailableLanguages() {
        return $this->availableLanguages;
    }
    
    /**
     * Detect browser language
     */
    private function detectBrowserLanguage() {
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            foreach ($langs as $lang) {
                $lang = substr($lang, 0, 2);
                if (in_array($lang, $this->availableLanguages)) {
                    return $lang;
                }
            }
        }
        return 'en';
    }
}

/**
 * Helper function to get translation
 */
function __($key, $default = null) {
    return Language::getInstance()->get($key, $default);
}
