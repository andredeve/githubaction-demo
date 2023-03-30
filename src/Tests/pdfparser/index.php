<?php

use Core\Util\PdfParser\Parser;

require_once __DIR__ . "/../../../bootstrap.php";

$files = glob(APP_PATH . "src/Tests/pdf/*.pdf");
$parsed = [];
foreach ($files as $index => $file) {
    try {
        $parsed[$index]['name'] = basename($file);
        $parsed[$index]['text'] = (new Parser())->lxParseFile($file);
    } catch (Exception $e) {
        $text = '<pre>';
        $text .= "O procedimento de extração de texto de PDF falhou: {$e->getMessage()}" . PHP_EOL;
        $text .= $e->getTraceAsString() . PHP_EOL;
        $text .= "</pre>";
        $parsed[$index]['text'] = $text;
    }
}
?>
<html lang="pt-br">
<head>
    <style>
        ul {
            display: none;
        }

        ul.open {
            display: block;
        }

        @media print { .hide, .show { display: none; } }
    </style>
    <script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script>
    <title>Teste de extração de texto de PDF.</title>
</head>
<body>
    <?php foreach ($parsed as $index => $item): ?>
    <div>
        <a href="#" class="hide" data-toggle="#list<?= $index?>">&#164; <?= $index . " => " . $item['name'] ?></a>
        <ul id="list<?= $index?>">
            <?php if (empty($item['text'])): ?>
                <li>Texto não encontrado.</li>
            <?php else: ?>
                <li><?php print_r($item['text']); ?></li>
            <?php endif; ?>
        </ul>
    </div>
    <br>
    <?php endforeach; ?>
    <script defer>
        $(function(){
            $('[data-toggle]').on('click', function(){
                var id = $(this).data("toggle"),
                    $object = $(id),
                    className = "open";
                console.log($object);
                if ($object) {
                    if ($object.hasClass(className)) {
                        $object.removeClass(className)
                    } else {
                        $object.addClass(className)
                    }
                }
            });
        });
    </script>
</body>
</html>