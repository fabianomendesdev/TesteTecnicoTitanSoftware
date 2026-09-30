<?php

namespace app\Core;

class Request
{
    private array $data;

    public function __construct()
    {
        $inputData = [];

        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if (in_array($method, ['PUT', 'PATCH'])) {
            parse_str(file_get_contents('php://input'), $inputData);
        }
        $this->data = array_merge($_GET, $_POST, $inputData);
    }

    /**
     * Retorna todos os dados GET, POST, PUT e etc enviados por requisições
     * 
     * @return array
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * Recebe parametros de campos para validação e faz a validação desses campos
     * 
     * @param array $validations
     * @return array
     */
    public function validate(array $validations): array
    {
        $errors = [];

        // Percorre todos os campos de validações
        foreach ($validations as $field => $validation) {
            // Separa as rules por '|'
            $rules = explode('|', $validation);

            // Percorre todas as rules
            foreach ($rules as $rule) {
                $ruleNameValue = [];

                // Caso contenha ':' o que seria uma valição complexa então faz o explode
                if (str_contains($rule, ':')) {
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
                                // Conta a quantidade de linhas pelo valor e coluna passados
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