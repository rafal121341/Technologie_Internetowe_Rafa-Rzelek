<?php
$firma = 'Szprycha';
$rok = date('Y');

// Bonus: powitanie z adresu, np. index.php?imie=Ola
$imie = $_GET['imie'] ?? '';
if (!is_string($imie) || trim($imie) === '') {
  $imie = 'Gościu';
}

$cennik = [
  ['nazwa' => 'Przegląd',           'cena' => 120],
  ['nazwa' => 'Przegląd generalny', 'cena' => 250],
  ['nazwa' => 'Naprawa dętki',      'cena' => 30],
  ['nazwa' => 'Wymiana opony',      'cena' => 50],
  ['nazwa' => 'Wymiana łańcucha',   'cena' => 70],
];

// Bonus: wymiary wykresu SVG liczone z tej samej tablicy $cennik
$maxCena   = max(array_column($cennik, 'cena'));
$szerEtyk  = 170;  // miejsce na nazwy usług po lewej
$maxSlupek = 330;  // szerokość najdłuższego słupka
$wysSlupek = 28;
$odstep    = 14;
$wysWykres = 10 + count($cennik) * ($wysSlupek + $odstep);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Szprycha – pracownia rowerowa</title>
  <link rel="stylesheet" href="style.css?v=4">
  <script src="app.js" defer></script>
</head>
<body>
  <header class="site-header">
    <a class="logo" href="#">
      <svg viewBox="0 0 120 64" width="72" height="38"
           role="img" aria-labelledby="logo-tytul">
        <title id="logo-tytul">Szprycha</title>
        <!-- koła -->
        <circle cx="28" cy="42" r="18" fill="none" stroke="#8f7bff" stroke-width="5"/>
        <circle cx="92" cy="42" r="18" fill="none" stroke="#8f7bff" stroke-width="5"/>
        <!-- rama -->
        <polyline points="28,42 48,14 80,14 92,42" fill="none" stroke="#8f7bff"
                  stroke-width="5" stroke-linejoin="round" stroke-linecap="round"/>
        <polyline points="48,14 60,42 80,14" fill="none" stroke="#8f7bff"
                  stroke-width="5" stroke-linejoin="round" stroke-linecap="round"/>
      </svg>
      <span>Szprycha</span>
    </a>
    <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="menu">Menu</button>
    <nav class="menu" id="menu">
      <a href="#uslugi">Usługi</a>
      <a href="#cennik">Cennik</a>
      <a href="#kontakt">Kontakt</a>
    </nav>
  </header>

  <main>
    <section id="uslugi">
      <div class="intro">
        <div class="intro__text">
          <p class="greeting">Cześć, <?= htmlspecialchars($imie) ?>!</p>
          <h1>Pracownia Rowerowa Szprycha</h1>
          <p>Szybki serwis, uczciwe ceny i rower gotowy na kolejny sezon. Bydgoszcz.</p>
          <a class="btn" href="#cennik">Zobacz cennik</a>
          <ul class="stats">
            <li><strong>12 lat</strong><span>doświadczenia</span></li>
            <li><strong>2000+</strong><span>naprawionych rowerów</span></li>
            <li><strong>24 h</strong><span>typowy czas naprawy</span></li>
          </ul>
        </div>
        <img class="intro__img" src="rower.svg" alt="Rower na stojaku w warsztacie">
      </div>

      <h2>Usługi</h2>
      <div class="cards">
        <article class="card">
          <h3>Przegląd</h3>
          <p>Hamulce, przerzutki i smarowanie łańcucha.</p>
          <p class="card__price">od 120 zł</p>
        </article>
        <article class="card card--highlight">
          <p class="badge">Najczęściej wybierane</p>
          <h3>Przegląd generalny</h3>
          <p>Pełna regulacja, wymiana smarów, centrowanie kół.</p>
          <p class="card__price">od 250 zł</p>
        </article>
        <article class="card">
          <h3>Naprawa dętki</h3>
          <p>Łatanie lub wymiana dętki z montażem.</p>
          <p class="card__price">od 30 zł</p>
        </article>
      </div>

      <h2>Jak to działa</h2>
      <ol class="steps">
        <li><h3>Przywozisz rower</h3><p>Bez umawiania, w godzinach otwarcia.</p></li>
        <li><h3>Wyceniamy</h3><p>Przed naprawą dostajesz konkretną cenę.</p></li>
        <li><h3>Odbierasz</h3><p>Dzwonimy, gdy rower jest gotowy.</p></li>
      </ol>

      <h2>Opinie klientów</h2>
      <div class="reviews">
        <figure class="review">
          <blockquote><p>Rower jak nowy, a cena zgodna z wyceną. Polecam.</p></blockquote>
          <figcaption>Marta, Bydgoszcz</figcaption>
        </figure>
        <figure class="review">
          <blockquote><p>Szybka naprawa przed wyjazdem, w jeden dzień.</p></blockquote>
          <figcaption>Piotr, Fordon</figcaption>
        </figure>
      </div>
    </section>

    <section id="cennik">
      <h2>Cennik</h2>
      <table class="price-table">
        <thead>
          <tr><th>Usługa</th><th class="num">Cena</th></tr>
        </thead>
        <tbody>
          <?php foreach ($cennik as $usluga): ?>
            <tr>
              <td><?= htmlspecialchars($usluga['nazwa']) ?></td>
              <td class="num"><?= $usluga['cena'] ?> zł</td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <h2>Porównanie cen</h2>
      <svg class="chart" viewBox="0 0 600 <?= $wysWykres ?>" width="100%"
           role="img" aria-labelledby="wykres-tytul"
           style="max-width: 600px; height: auto; display: block; background: #fff; border: 1px solid #e4e4ef; border-radius: 14px;">
        <title id="wykres-tytul">Wykres słupkowy cen usług</title>
        <?php foreach ($cennik as $i => $usluga):
          $y      = 10 + $i * ($wysSlupek + $odstep);
          $szer   = round($usluga['cena'] / $maxCena * $maxSlupek);
          $srodek = $y + $wysSlupek / 2 + 5;
        ?>
          <text x="<?= $szerEtyk - 10 ?>" y="<?= $srodek ?>" text-anchor="end"
                font-size="14" fill="#24243b"><?= htmlspecialchars($usluga['nazwa']) ?></text>
          <rect x="<?= $szerEtyk ?>" y="<?= $y ?>" width="<?= $szer ?>" height="<?= $wysSlupek ?>"
                rx="6" fill="#4426ff"/>
          <text x="<?= $szerEtyk + $szer + 8 ?>" y="<?= $srodek ?>"
                font-size="14" font-weight="700" fill="#14142b"><?= $usluga['cena'] ?> zł</text>
        <?php endforeach; ?>
      </svg>

      <h2>Najczęstsze pytania</h2>
      <div class="faq">
        <details>
          <summary>Czy muszę się umawiać?</summary>
          <p>Nie, wystarczy przyjść w godzinach otwarcia. Przy większych naprawach warto zadzwonić.</p>
        </details>
        <details>
          <summary>Ile trwa przegląd?</summary>
          <p>Standardowy przegląd zwykle robimy w 24 godziny.</p>
        </details>
        <details>
          <summary>Czy wymieniacie części?</summary>
          <p>Tak. Cenę części podajemy przed naprawą, nic nie dokładamy bez Twojej zgody.</p>
        </details>
      </div>
    </section>
  </main>

  <footer id="kontakt">
    <h2>Kontakt</h2>
    <p>ul. Przykładowa 1, Bydgoszcz</p>
    <p>Telefon: <a href="tel:+48500100200">500 100 200</a></p>
    <p>E-mail: <a href="mailto:kontakt@szprycha.pl">kontakt@szprycha.pl</a></p>
    <p>Godziny: pon–pt 10:00–18:00, sob 10:00–14:00</p>
    <p>&copy; <?= $rok ?> <?= $firma ?></p>
  </footer>
</body>
</html>