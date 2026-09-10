<?php

namespace app\Core;

use app\Core\Contracts\Returns;
use app\Core\Exceptions\ViewNotFoundException;

class ViewResponse implements Returns
{
    private $viewName = '';
    private $data = [];

    public function __construct(string $name, array $data = [])
    {
        $this->viewName = $name;
        $this->data     = $data;
    }

    public function getFileName(): string
    {
        $viewNameNomalized = str_replace('.', '/', strtolower($this->viewName));
        return VIEW_PATH . "/$viewNameNomalized.view.php";
    }

    public function execute(): void
    {
        $fileName = $this->getFileName();
        if (!file_exists($fileName)) {
            throw new ViewNotFoundException("View '$viewName' não encontada!");
        }
            
        extract($this->data);

        require $fileName;

        return;
    }
}