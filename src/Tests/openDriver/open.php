<?php
/**
 * Created by PhpStorm.
 * User: ander
 * Date: 18/12/2018
 * Time: 10:11
 */
$path = "C:\Program Files (x86)\HP Smart Document Scan Software 3\ScanApp.exe";
$output = exec('ScanApp');

$WshShell = new COM("WScript.Shell");
$oExec = $WshShell->Run("notepad.exe", 7, false);