<?php
require 'auth.php';   // 1. strażnik
require 'baza.php';

// token CSRF (bonus): jeden na sesję, sprawdzany przy każdym POST
if (empty($_SESSION['csrf'])) {
  $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$blad = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {   // 2. obsługa formularzy
  $token = $_POST['csrf'] ?? '';
  if (!is_string($token) || !hash_equals($_SESSION['csrf'], $token)) {
    http_response_code(403);
    exit('Nieprawidłowy token formularza.');
  }

  $akcja = $_POST['akcja'] ?? '';

  if ($akcja === 'dodaj') {
    $nazwa = trim((string) ($_POST['nazwa'] ?? ''));
    $cena  = (int) ($_POST['cena'] ?? 0);
    if ($nazwa !== '' && strlen($nazwa) <= 100 && $cena > 0) {   // 1. sprawdź
      $z = $pdo->prepare('INSERT INTO uslugi (nazwa, cena) VALUES (:nazwa, :cena)');
      $z->execute(['nazwa' => $nazwa, 'cena' => $cena]);          // 2. zapisz
      header('Location: admin.php?ok=dodano');                     // 3. przekieruj
      exit;
    }
    $blad = 'Podaj nazwę (do 100 znaków) i cenę większą od zera.';
  }

  if ($akcja === 'usun') {
    $z = $pdo->prepare('DELETE FROM uslugi WHERE id = :id');
    $z->execute(['id' => (int) ($_POST['id'] ?? 0)]);
    header('Location: admin.php?ok=usunieto');
    exit;
  }
}

// komunikaty z adresu (tylko znane wartości)
$komunikaty = ['dodano' => 'Usługa dodana.', 'usunieto' => 'Usługa usunięta.'];
$ok = $_GET['ok'] ?? '';
$info = is_string($ok) ? ($komunikaty[$ok] ?? '') : '';

// 3. lista usług do tabeli
$uslugi = $pdo->query('SELECT id, nazwa, cena FROM uslugi ORDER BY nazwa')->fetchAll();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Panel – Szprycha</title>
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
      <a class="btn btn-sm btn-outline-light me-2" href="index.php">Strona</a>
      <a class="btn btn-sm btn-outline-light" href="wyloguj.php">Wyloguj</a>
    </div>
  </nav>

  <main class="container py-4">
    <?php if ($info !== ''): ?>
      <div class="alert alert-success"><?= htmlspecialchars($info) ?></div>
    <?php endif; ?>
    <?php if ($blad !== ''): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($blad) ?></div>
    <?php endif; ?>

    <div class="row g-4">
      <div class="col-lg-8">
        <h2 class="h4">Usługi</h2>
        <table class="table table-striped align-middle">
          <thead class="table-dark">
            <tr><th>Usługa</th><th class="text-end">Cena</th><th></th></tr>
          </thead>
          <tbody>
            <?php foreach ($uslugi as $usluga): ?>
              <tr>
                <td><?= htmlspecialchars($usluga['nazwa']) ?></td>
                <td class="text-end"><?= (int) $usluga['cena'] ?> zł</td>
                <td class="text-end">
                  <form method="post" class="d-inline">
                    <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
                    <input type="hidden" name="akcja" value="usun">
                    <input type="hidden" name="id" value="<?= (int) $usluga['id'] ?>">
                    <button class="btn btn-sm btn-outline-danger">Usuń</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (!$uslugi): ?>
              <tr><td colspan="3" class="text-center text-muted">Brak usług.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <div class="col-lg-4">
        <h2 class="h4">Dodaj usługę</h2>
        <form method="post" class="card card-body shadow-sm">
          <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
          <input type="hidden" name="akcja" value="dodaj">

          <label for="nazwa" class="form-label">Nazwa</label>
          <input id="nazwa" name="nazwa" class="form-control mb-3" required maxlength="100">

          <label for="cena" class="form-label">Cena (zł)</label>
          <input id="cena" name="cena" type="number" min="1" class="form-control mb-3" required>

          <button class="btn btn-primary">Dodaj</button>
        </form>
      </div>
    </div>
  </main>
</body>
</html>
