<?php

use Core\Util\Functions;

include '../../../bootstrap.php';
Functions::escreverLogErro(filter_input(INPUT_POST, 'log'));
