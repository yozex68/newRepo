<?php
namespace App\Helpers;

class Validation {
    private array $errors = [];

    public function validate(array $data, array $rules): bool {
        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            
            foreach ($fieldRules as $rule => $ruleValue) {
                if ($rule === 'required' && $ruleValue && empty($value) && $value !== '0') {
                    $this->addError($field, "The " . str_replace('_', ' ', $field) . " field is required.");
                } elseif (!empty($value)) {
                    if ($rule === 'email' && $ruleValue && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $this->addError($field, "Please enter a valid email address.");
                    }
                    if ($rule === 'min' && strlen($value) < $ruleValue) {
                        $this->addError($field, "The " . str_replace('_', ' ', $field) . " must be at least {$ruleValue} characters.");
                    }
                    if ($rule === 'max' && strlen($value) > $ruleValue) {
                        $this->addError($field, "The " . str_replace('_', ' ', $field) . " must not exceed {$ruleValue} characters.");
                    }
                    if ($rule === 'matches' && $value !== ($data[$ruleValue] ?? null)) {
                        $this->addError($field, "The " . str_replace('_', ' ', $field) . " must match " . str_replace('_', ' ', $ruleValue) . ".");
                    }
                    if ($rule === 'unique' && is_array($ruleValue)) {
                        [$model, $column] = $ruleValue;
                        $existing = $model->findBy($column, $value);
                        if ($existing) {
                            $this->addError($field, "This " . str_replace('_', ' ', $field) . " is already taken.");
                        }
                    }
                }
            }
        }
        return empty($this->errors);
    }

    public function validateFile(array $file, array $allowedExtensions, array $allowedMimeTypes, int $maxSize): bool {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->addError('file', "File upload error occurred. Code: " . $file['error']);
            return false;
        }

        // Validate size
        if ($file['size'] > $maxSize) {
            $this->addError('file', "File size exceeds the limit of " . ($maxSize / 1024 / 1024) . " MB.");
        }

        // Validate extension
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExtensions)) {
            $this->addError('file', "Invalid file extension '{$ext}'. Allowed: " . implode(', ', $allowedExtensions));
        }

        // Validate mime type (finfo)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!array_key_exists($mime, $allowedMimeTypes)) {
            $this->addError('file', "Invalid file type. Mime type detected: {$mime}");
        }

        return empty($this->errors);
    }

    public function addError(string $field, string $message): void {
        $this->errors[$field] = $message;
    }

    public function getErrors(): array {
        return $this->errors;
    }
}
