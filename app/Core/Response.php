<?php

namespace app\Core;

use app\Core\Contracts\Returns;

class Response implements Returns
{
    private int $statusCode = 200;
    private array $headers = [];

    public function setStatusCode(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    public function redirect(string $url = ''): self
    {
        $this->setStatusCode(302);
        if ($url) {
            $this->headers['Location'] = $url;
        }
        return $this;
    }

    public function route(string $routeName): self
    {
        $this->headers['Location'] = route($routeName)->getFullPath();
        return $this;
    }

    public function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header("Content-Type: application/json;");
        echo json_encode($data);
        exit;
    }

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