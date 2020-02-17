<?php
namespace Main\Controllers;

use Main\Core\Request;
use Main\Utils\DependencyInjector;

class MainController {
    private $di;
    private $request;

    public function __construct(DependencyInjector $di, Request $request)
    {
        $this->di = $di;
        $this->request = $request;
    }

    public function mainMenu() {
        $twig = $this->di->get('Twig_Environment');
        return $twig->loadTemplate("MainMenuView.twig")->render([]);
    }
}