<?php

namespace Main\Core;

class Request {
    private $path, $form;

    public function __construct() {
        $pathArray = explode("?", $_SERVER["REQUEST_URI"]);
        $this->path = substr($pathArray[0], 1);
        $this->form = array_merge($_POST, $_GET); // ["index" => 123]
    }

    public function getPath() {
        return $this->path;
    }

    public function getForm() {
        return $this->form;
    }
}