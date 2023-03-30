<?php

namespace App\Controller;

use Core\Controller\AppController;

class TesteController extends AppController
{
    function index() {
        include APP_PATH . 'teste.php';
    }
}