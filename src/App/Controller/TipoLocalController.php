<?php
/**
 * Created by PhpStorm.
 * User: ander
 * Date: 11/12/2018
 * Time: 16:05
 */

namespace App\Controller;


use Core\Controller\AppController;

class TipoLocalController extends AppController
{
    function __construct()
    {
        parent::__construct(get_class());
        $this->breadcrumb = "Tipos de Locais de Arquivamento";
    }
}