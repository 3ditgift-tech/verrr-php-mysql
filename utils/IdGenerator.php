<?php
/**
 * Unique ID Generator
 */

class IdGenerator {
    
    /**
     * Generate unique application ID
     * Format: VC-BIZ-XXXXXX
     */
    public static function generateApplicationId() {
        $random = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6));
        return 'VC-BIZ-' . $random;
    }
    
    /**
     * Generate unique ID (alternative method)
     */
    public static function generateUniqueId($prefix = '') {
        return $prefix . uniqid() . bin2hex(random_bytes(4));
    }
}
