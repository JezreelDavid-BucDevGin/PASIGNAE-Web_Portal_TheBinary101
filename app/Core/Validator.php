<?php

declare(strict_types=1);

namespace App\Core;

class Validator
{
    private array $errors = [];

    public function __construct(private array $data, private array $rules) {}

    public static function make(array $data, array $rules): self
    {
        return new self($data, $rules);
    }

    public function fails(): bool
    {
        return !$this->passes();
    }

    public function passes(): bool
    {
        $this->errors = [];

        foreach ($this->rules as $field => $ruleString) {
            $rules = explode('|', $ruleString);
            $value = $this->data[$field] ?? null;

            foreach ($rules as $rule) {
                $this->applyRule($field, $value, $rule);
            }
        }

        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): ?string
    {
        return $this->errors[array_key_first($this->errors)] ?? null;
    }

    private function applyRule(string $field, mixed $value, string $rule): void
    {
        $label = ucwords(str_replace('_', ' ', $field));

        if ($rule === 'required' && ($value === null || $value === '')) {
            $this->errors[$field] = "{$label} is required.";
            return;
        }

        if ($value === null || $value === '') {
            return;
        }

        if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "{$label} must be a valid email address.";
        }

        if (str_starts_with($rule, 'min:')) {
            $min = (int) substr($rule, 4);
            if (strlen((string) $value) < $min) {
                $this->errors[$field] = "{$label} must be at least {$min} characters.";
            }
        }

        if (str_starts_with($rule, 'max:')) {
            $max = (int) substr($rule, 4);
            if (strlen((string) $value) > $max) {
                $this->errors[$field] = "{$label} must not exceed {$max} characters.";
            }
        }

        if ($rule === 'confirmed' && $value !== ($this->data[$field . '_confirmation'] ?? null)) {
            $this->errors[$field] = "{$label} confirmation does not match.";
        }

        if ($rule === 'numeric' && !is_numeric($value)) {
            $this->errors[$field] = "{$label} must be a number.";
        }

        if ($rule === 'date' && !strtotime((string) $value)) {
            $this->errors[$field] = "{$label} must be a valid date.";
        }
    }
}
