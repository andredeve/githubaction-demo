<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Util;

/**
 * Description of Converter
 *
 * @author victorcanario
 */
class Converter extends Process{
    //put your code here
    
    public function __construct( $path, $file, $output ){
        $command = "gs -dPDFA  -dMaxBitmap=100000000 -dBufferSpace=400000000 -dDetectDuplicateImages=true -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile=\"{$output}\" \"{$path}{$file}\"";
//        die($command);
        parent::__construct($command);
    }
    
    
    
}
