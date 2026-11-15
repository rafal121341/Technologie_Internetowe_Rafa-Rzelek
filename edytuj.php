<?php
require 'auth.php';   // strażnik: bez logowania nie wejdziesz
require 'baza.php';

if (empty($_SESSION['csrf'])) {
  $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

$z = $pdo->prepare('SELECT id, nazwa, cena, popularnosc FROM uslugi WHERE id = :id');
$z->execute(['id' => $id]);
$usluga = $z->fetch();

if (!$usluga) {   // nie ma takiej usługi
  header('Location: admin.php');
  exit;
}

$blad = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $token = $_POST['csrf'] ?? '';
  if (!is_string($token) || !hash_equals($_SESSION['csrf'], $token)) {
    http_response_code(403);
    exit('Nieprawidłowy token formularza.');
  }

  $nazwa = trim((string) ($_POST['nazwa'] ?? ''));
  $cena  = (int) ($_POST['cena'] ?? 0);
  $pop   = max(0, min(100, (int) ($_POST['popularnosc'] ?? 0)));

  if ($nazwa !== '' && strlen($nazwa) <= 100 && $cena > 0) {
    $z = $pdo->prepare(
      'UPDATE uslugi SET nazwa = :nazwa, cena = :cena, popularnosc = :pop WHERE id = :id');
    $z->execute(['nazwa' => $nazwa, 'cena' => $cena, 'pop' => $pop, 'id' => $id]);
    header('Location: admin.php?ok=zapisano');
    exit;
  }

  $blad = 'Podaj nazwę (do 100 znaków) i cenę większą od zera.';
  // zostaw w formularzu to, co użytkownik wpisał
  $usluga['nazwa'] = $nazwa;
  $usluga['cena'] = $cena;
  $usluga['popularnosc'] = $pop;
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edycja usługi – Szprycha</title>
  <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
    crossorigin="anonymous">
</head>
<body class="bg-light">
  <nav class="navbar navbar-dark bg-dark">
    <div class="container">
      <span class="navbar-brand">Szprycha · panel</span>
      <span class="navbar-text me-auto ms-3">
        Witaj, <?= htmlspecialchars($_SESSION['uzytkownik']) ?>
      </span>
      <a class="btn btn-sm btn-outline-light me-2" href="admin.php">Panel</a>
      <a class="btn btn-sm btn-outline-light" href="wyloguj.php">Wyloguj</a>
    </div>
  </nav>

  <main class="container py-4" style="max-width: 520px;">
    <h1 class="h4 mb-3">Edycja usługi</h1>

    <?php if ($blad !== ''): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($blad) ?></div>
    <?php endif; ?>

    <form method="post" class="card card-body shadow-sm">
      <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
      <input type="hidden" name="id" value="<?= (int) $usluga['id'] ?>">

      <label for="nazwa" class="form-label">Nazwa</label>
      <input id="nazwa" name="nazwa" class="form-control mb-3" required maxlength="100"
             value="<?= htmlspecialchars($usluga['nazwa']) ?>">

      <label for="cena" class="form-label">Cena (zł)</label>
      <input id="cena" name="cena" type="number" min="1" class="form-control mb-3" required
             value="<?= (int) $usluga['cena'] ?>">

      <label for="popularnosc" class="form-label">Popularność (0–100)</label>
      <input id="popularnosc" name="popularnosc" type="number" min="0" max="100"
             class="form-control mb-3" value="<?= (int) $usluga['popularnosc'] ?>">

      <div class="d-flex gap-2">
        <button class="btn btn-primary">Zapisz</button>
        <a class="btn btn-outline-secondary" href="admin.php">Anuluj</a>
      </div>
    </form>
  </main>
</body>
</html>
