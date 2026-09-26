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
                        if (!array_key_exists($field, $this->data) || is_null($value) || trim((string) $value) === '') {
                            $errors[$field][] = "O campo '$field' é obrigatório.";
                            break 2;
                        }
                        break;

                    case 'string':
                        if (!empty($value) && !is_string($value)) {
                            $errors[$field][] = "O campo '$field' não é uma string.";
                            break 2;
                        }
                        break;

                    case 'email':
                        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field][] = "O campo '$field' precisa ser um e-mail válido.";
                            break 2;
                        }
                        break;

                    case 'numeric':
                        if (!empty($value) && !is_numeric($value)) {
                            $errors[$field][] = "O campo '$field' deve ser um número.";
                            break 2;
                        }
                        break;

                    case 'unique':
                        if (!empty($value)) {
                            $params = explode(',', $ruleValue);
                            $table  = $params[0] ?? '';
                            $column = $params[1] ?? $field;

                            if ($table) {
                                $database = Database::getInstance();
                                $result = $database->getResult(
                                    "SELECT COUNT(*) as total FROM {$table} WHERE {$column} = :val LIMIT 1",
                                    ['val' => [$value, \PDO::PARAM_STR]]
                                );

                                $total = $result[0]['total'] ?? 0;

                                if ($total > 0) {
                                    $errors[$field][] = "O valor informado para '$field' já está em uso.";
                                    break 2;
                                }
                            }
                        }
                        break;
                }
            }
        }

        return $errors;
    }
}