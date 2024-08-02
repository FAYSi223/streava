<?php
session_start();
include 'db.php';

// Überprüfen, ob der Benutzer eingeloggt ist
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Funktion zum Abrufen von Filmen und Serien
function fetchMoviesAndSeries($categoryId, $search = '') {
    global $conn;
    $categoryId = intval($categoryId);
    $search = $conn->real_escape_string($search);

    // SQL-Abfrage mit optionalem Suchbegriff
    $sql = "SELECT * FROM movies WHERE category_id = $categoryId";
    if (!empty($search)) {
        $sql .= " AND title LIKE '%$search%'";
    }

    $result = $conn->query($sql);
    $movies = [];
    while ($row = $result->fetch_assoc()) {
        $movies[] = $row;
    }
    return $movies;
}

// Suchbegriff und Filter aus GET-Anfrage abrufen
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category_id']) ? $_GET['category_id'] : '1';
$moviesAndSeries = fetchMoviesAndSeries($category, $search);
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Streava - Ihr Ort für die neuesten und angesagtesten Filme und Serien. Entdecken Sie eine breite Auswahl an Inhalten und genießen Sie beste Unterhaltung.">
  <title>Streava</title>
  <style>
    body {
      background-color: #141414;
      color: #fff;
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
    }

    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px;
    }

    header a {
      color: #fff;
      text-decoration: none;
      margin-right: 20px;
    }

    .container {
      max-width: 800px;
      margin: 0 auto;
      padding: 20px;
    }

    .textbox-container {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 20px;
    }

    .textbox-container input[type="text"] {
      width: 70%;
      padding: 10px;
      border: none;
      border-radius: 5px;
      font-size: 16px;
    }

    .textbox-container button {
      background-color: #e50914;
      color: #fff;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      font-size: 16px;
      cursor: pointer;
    }

    h1 {
      text-align: center;
      margin-top: 50px;
    }

    .sound-streaming {
      text-align: center;
      margin-bottom: 20px;
      font-size: 24px;
      font-weight: bold;
    }

    .video-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: center;
      margin-bottom: 50px;
    }

    .video-item {
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
    }

    .video-item img {
      width: 200px;
      height: 200px;
      object-fit: cover;
      border: 2px solid white;
      border-radius: 10px;
      margin-bottom: 10px;
    }

    .navigation {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      font-size: 18px;
    }

    .navigation a {
      color: #fff;
      text-decoration: none;
      margin-right: 20px;
    }

    .menu-box {
      display: flex;
      justify-content: space-between;
      margin-bottom: 20px;
      padding: 10px;
      background-color: rgba(255, 255, 255, 0.1);
      border-radius: 5px;
    }

    .menu-box a {
      color: #fff;
      text-decoration: none;
      margin-right: 20px;
    }

    .exit-fullscreen-button {
      position: fixed;
      top: 10px;
      left: 10px;
      z-index: 9999;
      color: #fff;
      font-size: 24px;
      cursor: pointer;
    }

    .video-flex {
      display: flex;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="textbox-container">
      <input id="searchInput" type="text" placeholder="Suche" />
      <button onclick="searchMovies()">Suchen</button>
      <a href='logout.php'>
        <button>Logout</button>
      </a>
    </div>

    <h1>Streava</h1>

    <div class="menu-box">
      <a href="?category_id=1">New</a>
      <a href="?category_id=2">Angesagt</a>
      <a href="series.php">Serien</a>
    </div>

    <div class="video-flex">
      <div id="videoGrid" class="video-grid">
        <?php foreach ($moviesAndSeries as $movie): ?>
          <div class="video-item">
            <a href="<?php echo $movie['is_series'] ? 'series_details.php?id=' . $movie['id'] : 'movie_details.php?id=' . $movie['id']; ?>">
              <img src="<?php echo $movie['image']; ?>" alt="<?php echo $movie['title']; ?>">
            </a>
            <p><?php echo $movie['title']; ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="menu-box">
      <a href="profile.php">Profil</a>
      <a href="search.php">Suche</a>
      <a href="series.php">Serien</a>
    </div>
  </div>

  <script>
    function searchMovies() {
      var searchInput = document.getElementById('searchInput').value;
      var category = new URLSearchParams(window.location.search).get('category_id') || '1';

      window.location.href = `index.php?category_id=${category}&search=${encodeURIComponent(searchInput)}`;
    }
  </script>
</body>
</html>
