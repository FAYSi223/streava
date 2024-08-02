<?php
session_start();
include 'db.php';

// Überprüfen, ob der Benutzer eingeloggt ist
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Funktion zum Abrufen der Serie und ihrer Episoden
function fetchSeriesAndEpisodes($seriesId) {
    global $conn;
    $seriesId = intval($seriesId);

    // Serie abrufen
    $seriesSql = "SELECT * FROM movies WHERE id = $seriesId AND is_series = 1";
    $seriesResult = $conn->query($seriesSql);
    if ($seriesResult->num_rows === 0) {
        // Serie nicht gefunden
        header('HTTP/1.0 404 Not Found');
        exit();
    }
    $series = $seriesResult->fetch_assoc();

    // Episoden abrufen
    $episodesSql = "SELECT * FROM episodes WHERE series_id = $seriesId";
    $episodesResult = $conn->query($episodesSql);
    $episodes = [];
    while ($row = $episodesResult->fetch_assoc()) {
        $episodes[] = $row;
    }

    return [$series, $episodes];
}

// Serien-ID aus GET-Anfrage abrufen
$seriesId = isset($_GET['id']) ? $_GET['id'] : 0;
list($series, $episodes) = fetchSeriesAndEpisodes($seriesId);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($series['title']); ?> - Streava</title>
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
    .header-buttons {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }
    .header-buttons a {
      background-color: #e50914;
      color: #fff;
      text-decoration: none;
      padding: 10px 20px;
      border-radius: 5px;
      font-size: 16px;
    }
    .header-buttons a:first-child {
      margin-right: auto;
    }
    .series-header {
      text-align: center;
      margin-top: 50px;
      margin-bottom: 50px;
    }
    .episodes-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: center;
      margin-bottom: 50px;
    }
    .episode-item {
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      width: 200px;
    }
    .episode-item img {
      width: 200px;
      height: 200px;
      object-fit: cover;
      border: 2px solid white;
      border-radius: 15px;
    }
    .episode-item a {
      color: #fff;
      text-decoration: none;
    }
    .episode-item a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header-buttons">
      <a href='logout.php'>Logout</a>
      <a href="index.php">Home</a>
    </div>
    <div class="series-header">
      <h1><?php echo htmlspecialchars($series['title']); ?></h1>
    </div>
    <div class="episodes-grid">
      <?php foreach ($episodes as $episode): ?>
        <div class="episode-item">
          <a href="episode.php?id=<?php echo $episode['id']; ?>">
            <img src="<?php echo htmlspecialchars($episode['image']); ?>" alt="<?php echo htmlspecialchars($episode['title']); ?>">
            <p><?php echo htmlspecialchars($episode['title']); ?></p>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</body>
</html>
