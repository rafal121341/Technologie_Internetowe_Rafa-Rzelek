<?php
require 'baza.php';

header('Content-Type: application/json; charset=utf-8');   // to jest JSON, nie HTML

function odpowiedz(int $kod, array $dane): void {
  http_response_code($kod);                                  // kod statusu
  echo json_encode($dane, JSON_UNESCAPED_UNICODE);           // "dętki", nie "d\u0119tki"
  exit;
}

// GET: lista usług z bazy
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $uslugi = $pdo->query(
    'SELECT id, nazwa, cena, popularnosc FROM uslugi ORDER BY cena')->fetchAll();
  odpowiedz(200, $uslugi);
}

// każda inna metoda: 405
header('Allow: GET');
odpowiedz(405, ['blad' => 'Metoda niedozwolona.']);