<?php

require __DIR__ . '/vendor/autoload.php';

use App\LeitorGabarito\Leitor;

if (isset($_FILES['image'])) {
    $obImage = new Leitor($_FILES['image']);
    echo "<pre>";
        var_dump($obImage);
    echo "</pre>";
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="image" accept="image/*" required>
        <button type="submit">Enviar</button>
    </form>
</body>
</html>