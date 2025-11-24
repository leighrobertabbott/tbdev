<?php

namespace App\Services;

use App\Core\Security;

class ValidationService
{
    private array $errors = [];

    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];

        foreach ($rules as $field => $ruleSet) {
            $value = $data[$field] ?? null;
            $rulesArray = is_array($ruleSet) ? $ruleSet : explode('|', $ruleSet);

            foreach ($rulesArray as $rule) {
                $this->applyRule($field, $value, $rule, $data);
            }
        }

        return empty($this->errors);
    }

    private function applyRule(string $field, $value, string $rule, array $data): void
    {
        if (strpos($rule, ':') !== false) {
            [$ruleName, $param] = explode(':', $rule, 2);
        } else {
            $ruleName = $rule;
            $param = null;
        }

        switch ($ruleName) {
            case 'required':
                if (empty($value)) {
                    $this->errors[$field][] = "The {$field} field is required.";
                }
                break;

            case 'email':
                if (!empty($value) && !Security::validateEmail($value)) {
                    $this->errors[$field][] = "The {$field} must be a valid email address.";
                }
                break;

            case 'username':
                if (!empty($value) && !Security::validateUsername($value)) {
                    $this->errors[$field][] = "The {$field} must be 3-20 alphanumeric characters, underscores, or hyphens.";
                }
                break;

            case 'min':
                if (!empty($value) && strlen($value) < (int)$param) {
                    $this->errors[$field][] = "The {$field} must be at least {$param} characters.";
                }
                break;

            case 'max':
                if (!empty($value) && strlen($value) > (int)$param) {
                    $this->errors[$field][] = "The {$field} must not exceed {$param} characters.";
                }
                break;

            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->errors[$field][] = "The {$field} must be numeric.";
                }
                break;

            case 'integer':
                if (!empty($value) && !is_int($value) && !ctype_digit($value)) {
                    $this->errors[$field][] = "The {$field} must be an integer.";
                }
                break;

            case 'confirmed':
                $confirmField = $field . '_confirmation';
                if (!isset($data[$confirmField]) || $value !== $data[$confirmField]) {
                    $this->errors[$field][] = "The {$field} confirmation does not match.";
                }
                break;

            case 'password':
                if (!empty($value)) {
                    $passwordErrors = Security::validatePassword($value);
                    if (!empty($passwordErrors)) {
                        $this->errors[$field] = array_merge($this->errors[$field] ?? [], $passwordErrors);
                    }
                }
                break;
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getFirstError(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }
}


