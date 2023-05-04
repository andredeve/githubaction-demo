<?php
include '../../../../bootstrap.php';

$codigoCaptcha = substr(md5(time()) ,1, 4);
$_SESSION['captcha'] = $codigoCaptcha;
$fundoCaptcha = imagecreatefrompng("../../../../assets/img/fundocaptcha.png");
$fundo = imagecolorallocate($fundoCaptcha, 255, 255, 255);
imagecolortransparent($fundoCaptcha, $fundo);
$fonteCaptcha = imageloadfont("../../../../assets/fonts/fonte_captcha.gdf");
$corCaptcha = imagecolorallocate($fundoCaptcha, 23,162,184);
imagestring($fundoCaptcha, $fonteCaptcha, 140, 15, $codigoCaptcha, $corCaptcha);
header('Content-type: image/png');
imagepng($fundoCaptcha);
imagedestroy($fundoCaptcha);
