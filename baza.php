<?php
$pdo = new PDO('sqlite:' . __DIR__ . '/szprycha.sqlite');
// MySQL na hostingu: zmienia się tylko ta linijka
// $pdo = new PDO('mysql:host=localhost;dbname=szprycha', 'login', 'haslo');

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
