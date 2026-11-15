<?php
// Uruchamiaj tylko z terminala: php setup.php TwojeHaslo
if (PHP_SAPI !== 'cli') {
  http_response_code(403);
  exit('Tylko z terminala.');
}

require 'baza.php';

$pdo->exec('CREATE TABLE IF NOT EXISTS uslugi (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  nazwa TEXT NOT NULL,
  cena INTEGER NOT NULL,
  popularnosc INTEGER NOT NULL DEFAULT 0
)');

$pdo->exec('CREATE TABLE IF NOT EXISTS uzytkownicy (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  login TEXT NOT NULL UNIQUE,
  haslo_hash TEXT NOT NULL
)');

// 4 usługi, gdy tabela jest pusta
if ((int) $pdo->query('SELECT COUNT(*) FROM uslugi')->fetchColumn() === 0) {
  $z = $pdo->prepare('INSERT INTO uslugi (nazwa, cena, popularnosc) VALUES (:nazwa, :cena, :pop)');
  $dane = [
    ['Przegląd',         120, 45],
    ['Wymiana dętki',     40, 30],
    ['Centrowanie koła',  60, 15],
    ['Serwis pełny',     350, 10],
  ];
  foreach ($dane as [$nazwa, $cena, $pop]) {
    $z->execute(['nazwa' => $nazwa, 'cena' => $cena, 'pop' => $pop]);
  }
}

// konto admina, gdy tabela jest pusta
if ((int) $pdo->query('SELECT COUNT(*) FROM uzytkownicy')->fetchColumn() === 0) {
  $haslo = $argv[1] ?? 'Szprycha!2026';   // zmień: php setup.php TwojeHaslo
  $z = $pdo->prepare('INSERT INTO uzytkownicy (login, haslo_hash) VALUES (:login, :hash)');
  $z->execute([
    'login' => 'admin',
    'hash'  => password_hash($haslo, PASSWORD_DEFAULT),
  ]);
}

echo "Baza gotowa.\n";
