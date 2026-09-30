<?php

namespace app\Core;

use app\Core\Contracts\Returns;

class Response implements Returns
{
    private int $statusCode = 200;
    private array $headers = [];

    /**
     * Faz o set do código http de status que vai ser retornado via JSON
     * 
     * @param int $code
     * @return self
     */
    public function setStatusCode(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    /**
     * Faz o redirect para uma nova url
     * 
     * @param string $url
     * @return self
     */
    public function redirect(string $url = ''): self
    {
        $this->setStatusCode(302);
        if ($url) {
            $this->headers['Location'] = $url;
        }
        return $this;
    }

    /**
     * Faz o redirect para uma nova rota
     * 
     * @param string #routeName
     * @return self
    */
    public function redirectToRoute(string $routeName): self
    {
        $this->setStatusCode(302);
        $this->headers['Location'] = route($routeName)->getFullPath();
        return $this;
    }

    /**
     * Faz o return de um JSON
     * 
     * @param int $status
     * @param mixed $data
     * @return void
     */
    public function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header("Content-Type: application/json;");
        echo json_encode($data);
        exit;
    }

    /**
     * Executa o response
     * Faz um retorno JSON ou um redirect
     * 
     * @return void
     */
    public function execute(): void
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }

        if (in_array($this->statusCode, [301, 302, 303, 307, 308])) {
            exit;
        }

        return;
    }
}