<?php
namespace App\Core;

class Validator
{
    private array $errors = [];

    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];

        foreach ($rules as $field => $ruleSet) {
            $ruleList = is_string($ruleSet) ? explode('|', $ruleSet) : $ruleSet;
            $value = $data[$field] ?? null;

            foreach ($ruleList as $rule) {
                $params = [];
                if (str_contains($rule, ':')) {
                    [$rule, $paramStr] = explode(':', $rule, 2);
                    $params = explode(',', $paramStr);
                }

                $methodName = 'validate' . ucfirst($rule);
                if (method_exists($this, $methodName)) {
                    $this->$methodName($field, $value, $params, $data);
                }
            }
        }

        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    private function validateRequired(string $field, mixed $value, array $params, array $data): void
    {
        if ($value === null || $value === '' || $value === []) {
            $this->addError($field, "Trường {$field} là bắt buộc.");
        }
    }

    private function validateEmail(string $field, mixed $value, array $params, array $data): void
    {
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, "Trường {$field} phải là email hợp lệ.");
        }
    }

    private function validateMin(string $field, mixed $value, array $params, array $data): void
    {
        $min = (int)($params[0] ?? 0);
        if (is_string($value) && mb_strlen($value) < $min) {
            $this->addError($field, "Trường {$field} phải có ít nhất {$min} ký tự.");
        } elseif (is_numeric($value) && !is_string($value) && $value < $min) {
            $this->addError($field, "Trường {$field} phải lớn hơn hoặc bằng {$min}.");
        }
    }

    private function validateMax(string $field, mixed $value, array $params, array $data): void
    {
        $max = (int)($params[0] ?? 0);
        if (is_string($value) && mb_strlen($value) > $max) {
            $this->addError($field, "Trường {$field} không được vượt quá {$max} ký tự.");
        } elseif (is_numeric($value) && !is_string($value) && $value > $max) {
            $this->addError($field, "Trường {$field} phải nhỏ hơn hoặc bằng {$max}.");
        }
    }

    private function validateNumeric(string $field, mixed $value, array $params, array $data): void
    {
        if (!empty($value) && !is_numeric($value)) {
            $this->addError($field, "Trường {$field} phải là số.");
        }
    }

    private function validateInteger(string $field, mixed $value, array $params, array $data): void
    {
        if (!empty($value) && !ctype_digit((string)$value)) {
            $this->addError($field, "Trường {$field} phải là số nguyên.");
        }
    }

    private function validateDatetime(string $field, mixed $value, array $params, array $data): void
    {
        if (!empty($value)) {
            $d = \DateTime::createFromFormat('Y-m-d\TH:i', $value);
            if (!$d) {
                $d = \DateTime::createFromFormat('Y-m-d H:i:s', $value);
            }
            if (!$d) {
                $this->addError($field, "Trường {$field} phải là ngày giờ hợp lệ.");
            }
        }
    }

    private function validateNtuEmail(string $field, mixed $value, array $params, array $data): void
    {
        if (!empty($value) && !str_ends_with(strtolower($value), '@ntu.edu.vn')) {
            $this->addError($field, "Email phải có đuôi @ntu.edu.vn.");
        }
    }
}
