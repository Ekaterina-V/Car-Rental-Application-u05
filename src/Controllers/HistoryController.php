<?php
namespace Main\Controllers;

use Main\Core\Request;
use Main\Models\Model;
use Main\Utils\DependencyInjector;

class HistoryController {
    private $di;
    private $request;

    public function __construct(DependencyInjector $di, Request $request)
    {
        $this->di = $di;
        $this->request = $request;
    }

    public function listHistory() {
        $db = $this->di->get('PDO');
        $twig = $this->di->get('Twig_Environment');
        $model = new Model($db);
        $history = $model->listHistory();

        $total = 0;
        foreach ($history as $h) {
            $total += $h["cost"];
        }
        $properties = ["history" => $history,
                       "totalCost" => $total];
        return $twig->loadTemplate("HistoryView.twig")->render($properties);
    }
}