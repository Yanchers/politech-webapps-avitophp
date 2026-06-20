<?php

namespace App\Core;

class Validator
{
    private array $errors = [];

    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];

        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            $ruleList = is_array($fieldRules) ? $fieldRules : explode('|', $fieldRules);

            foreach ($ruleList as $rule) {
                $params = [];
                if (str_contains($rule, ':')) {
                    [$rule, $paramStr] = explode(':', $rule, 2);
                    $params = explode(',', $paramStr);
                }

                $methodName = 'rule' . ucfirst($rule);
                if (method_exists($this, $methodName)) {
                    $this->$methodName($field, $value, $params);
                }
            }
        }

        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function getFirstError(): ?string
    {
        $first = reset($this->errors);
        return is_array($first) ? $first[0] : ($first ?: null);
    }

    public function getErrorsFor(string $field): array
    {
        return $this->errors[$field] ?? [];
    }

    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    private function ruleRequired(string $field, mixed $value, array $params): void
    {
        if ($value === null || $value === '' || (is_array($value) && empty($value))) {
            $this->addError($field, 'Поле обязательно для заполнения');
        }
    }

    private function ruleEmail(string $field, mixed $value, array $params): void
    {
        if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, 'Введите корректный email адрес');
        }
    }

    private function ruleMin(string $field, mixed $value, array $params): void
    {
        $min = (int) ($params[0] ?? 0);
        if (is_string($value) && strlen($value) < $min) {
            $this->addError($field, "Минимальная длина — {$min} символов");
        }
    }

    private function ruleMax(string $field, mixed $value, array $params): void
    {
        $max = (int) ($params[0] ?? 0);
        if (is_string($value) && strlen($value) > $max) {
            $this->addError($field, "Максимальная длина — {$max} символов");
        }
    }

    private function ruleNumeric(string $field, mixed $value, array $params): void
    {
        if ($value !== null && $value !== '' && !is_numeric($value)) {
            $this->addError($field, 'Поле должно быть числом');
        }
    }

    private function ruleConfirmed(string $field, mixed $value, array $params): void
    {
        $confirmationField = $field . '_confirmation';
        $confirmation = $_POST[$confirmationField] ?? null;
        if ($value !== $confirmation) {
            $this->addError($field, 'Значения не совпадают');
        }
    }

    private function ruleUnique(string $field, mixed $value, array $params): void
    {
        if ($value === null || $value === '') return;
        $table = $params[0] ?? null;
        $column = $params[1] ?? $field;
        $excludeId = $params[2] ?? null;
        if (!$table) return;

        $db = \App\Core\Database::getInstance();
        $sql = "SELECT COUNT(*) as cnt FROM {$table} WHERE {$column} = ?";
        $bindings = [$value];

        if ($excludeId) {
            $pk = $params[3] ?? $table . '_id';
            $sql .= " AND {$pk} != ?";
            $bindings[] = $excludeId;
        }

        $result = $db->fetch($sql, $bindings);
        if ($result && (int)$result['cnt'] > 0) {
            $this->addError($field, 'Такое значение уже используется');
        }
    }
}
