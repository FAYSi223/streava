<?php
session_start();
include 'db.php';

// Überprüfen, ob der Benutzer eingeloggt ist
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Funktion zum Abrufen von Filmen basierend auf dem Suchbegriff
function searchMovies($search) {
    global $conn;
    $search = $conn->real_escape_string($search); // Sicherheitsmaßnahme gegen SQL-Injektionen

    // SQL-Abfrage mit Suchbegriff
    $sql = "SELECT * FROM movies WHERE title LIKE '%$search%'";
    $result = $conn->query($sql);
    $movies = [];
    while ($row = $result->fetch_assoc()) {
        $movies[] = $row;
    }
    return $movies;
}

// Suchbegriff aus GET-Anfrage abrufen
$search = isset($_GET['search']) ? $_GET['search'] : '';
$movies = searchMovies($search);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Filme suchen</title>
  <style>
    body {
      background-color: #141414;
      color: #fff;
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
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

    .video-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: center;
    }

    .video-item {
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
    }

    .video-item img {
      width: 100%;
      max-width: 200px;
      height: auto;
      margin-bottom: 10px;
    }

    .video-item p {
      margin: 0;
    }

    .back-button-container {
      margin-top: 20px;
      text-align: center;
    }

    .back-button-container button {
      background-color: #e50914;
      color: #fff;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      font-size: 16px;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="textbox-container">
      <input id="searchInput" type="text" placeholder="Suche nach Filmen" value="<?php echo htmlspecialchars($search); ?>" />
      <button onclick="searchMovies()">Suchen</button>
    </div>

    <div class="video-grid">
      <?php if (!empty($movies)): ?>
        <?php foreach ($movies as $movie): ?>
          <div class="video-item">
            <a href="#">
              <img src="<?php echo $movie['image']; ?>" alt="<?php echo $movie['title']; ?>">
            </a>
            <p><?php echo $movie['title']; ?></p>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>Keine Filme gefunden</p>
      <?php endif; ?>
    </div>

    <div class="back-button-container">
      <button onclick="goBack()">Zurück</button>
    </div>
  </div>

  <script>
    function searchMovies() {
      var searchInput = document.getElementById('searchInput').value;
      window.location.href = `search.php?search=${encodeURIComponent(searchInput)}`;
    }

    function goBack() {
      window.history.back();
    }
  </script>
</body>
</html>
