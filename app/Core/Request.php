<?php

namespace app\Core;

class Request
{
    private array $data;

    public function __construct()
    {
        $this->data = array_merge($_GET, $_POST);
    }

    public function all(): Array
    {
        return $this->data;
    }

    public function validate(array $validations): array
    {
        $errors = [];

        foreach ($validations as $field => $validation) {
            $rules = explode('|', $validation);

            foreach ($rules as $rule) {
                $ruleNameValue = [];
                if (str_contains(':', $rule)) {
                    $ruleNameValue = explode(':', $rule);
                }

                $ruleName  = $ruleNameValue[0] ?? $rule ?? '';
                $ruleValue = $ruleNameValue[1] ?? 0;

                $value = $this->data[$field] ?? null;

                switch ($ruleName) {
                    case 'required':
                        if (!array_key_exists($field, $this->data)) {
                            $errors[$field][] = "O campo '$field' é obrigatório.";
                        }
                        break 2;

                    case 'string':
                        if (!is_string($value)) {
                            $errors[$field][] = "O campo '$field' não é uma string.";
                        }
                        break 2;

                    case 'email':
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field][] = "O campo '$field' precisa ser um e-mail válido.";
                        }
                        break 2;
                }
            }
        }

        return $errors;
    }
}