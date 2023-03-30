<?php

#recebendo a url da imagem
$filename = $_GET['img'];
$extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
//echo $extension;
#Cabeçalho que ira definir a saida da pagina
header("Content-type: image/$extension");
#pegando as dimensoes reais da imagem, largura e altura
list($width, $height) = getimagesize($filename);
#setando as proporcoes desejadas
$largura = isset($_GET['h']) ? $_GET['h'] : 200;
$altura = isset($_GET['w']) ? $_GET['w'] : 150;
#setando a proporcao da miniatura
if ($height > $width) {
    $new_height = $altura;
    $new_width = $width * $altura / $height;
} else {
    $new_width = $largura;
    $new_height = $height * $largura / $width;
}
if ($new_width > $largura) {
    $new_width = $largura;
    $new_height = $height * $largura / $width;
}
if ($new_height > $altura) {
    $new_height = $altura;
    $new_width = $width * $altura / $height;
}
#gerando a a miniatura da imagem
$image_p = imagecreatetruecolor($new_width, $new_height);
switch ($extension) {
    case 'gif':
        $image = imagecreatefromgif($filename);
        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        imagegif($image_p);
        break;
    case 'png':
        $image = imagecreatefrompng($filename);
        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        imagepng($image_p, null, 9);
        break;
    case 'jpeg':
    case 'jpg':
        $image = imagecreatefromjpeg($filename);
        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        imagejpeg($image_p, null, 100);
        break;
}
imagedestroy($image_p);
