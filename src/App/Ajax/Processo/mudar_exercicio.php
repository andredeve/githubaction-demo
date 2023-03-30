<?php

include '../../../../bootstrap.php';
$exercicio = $_POST['exercicio'];

if(!empty($exercicio)){
    $_SESSION['exercicio'] =  $exercicio;
}else{
    unset($_SESSION['exercicio']);
}
