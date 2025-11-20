<?php
/**
 * Input Validation Helper
 */

class Validator {
    
    private $errors = [];
    
    public function required($value, $field) {
        if (empty($value) && $value !== '0') {
            $this->errors[$field] = ucfirst($field) . ' is required';
            return false;
        }
        return true;
    }
    
    public function email($value, $field) {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = 'Invalid email format';
            return false;
        }
        return true;
    }
    
    public function minLength($value, $field, $min) {
        if (strlen($value) < $min) {
            $this->errors[$field] = ucfirst($field) . " must be at least {$min} characters";
            return false;
        }
        return true;
    }
    
    public function maxLength($value, $field, $max) {
        if (strlen($value) > $max) {
            $this->errors[$field] = ucfirst($field) . " must not exceed {$max} characters";
            return false;
        }
        return true;
    }
    
    public function date($value, $field) {
        $date = DateTime::createFromFormat('Y-m-d', $value);
        if (!$date || $date->format('Y-m-d') !== $value) {
            $this->errors[$field] = 'Invalid date format. Use YYYY-MM-DD';
            return false;
        }
        return true;
    }
    
    public function phone($value, $field) {
        // Basic phone validation - can be enhanced
        if (!preg_match('/^[+]?[0-9\s\-\(\)]+$/', $value)) {
            $this->errors[$field] = 'Invalid phone number format';
            return false;
        }
        return true;
    }
    
    public function hasErrors() {
        return !empty($this->errors);
    }
    
    public function getErrors() {
        return $this->errors;
    }
    
    public function reset() {
        $this->errors = [];
    }
}
