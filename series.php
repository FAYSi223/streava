<?php
session_start();
include 'db.php';

// Überprüfen, ob der Benutzer eingeloggt ist
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Funktion zum Abrufen aller Serien
function fetchAllSeries() {
    global $conn;
    $sql = "SELECT * FROM movies WHERE is_series = 1";
    $result = $conn->query($sql);
    $series = [];
    while ($row = $result->fetch_assoc()) {
        $series[] = $row;
    }
    return $series;
}

// Alle Serien abrufen
$seriesList = fetchAllSeries();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Serien - Streava</title>
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
    .series-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: center;
      margin-bottom: 50px;
    }
    .series-item {
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      width: 200px;
    }
    .series-item img {
      width: 200px;
      height: 200px;
      object-fit: cover;
      border: 2px solid white;
      border-radius: 15px;
    }
    .series-item a {
      color: #fff;
      text-decoration: none;
      margin-top: 10px;
      font-size: 16px;
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
  </style>
</head>
<body>
  <div class="container">
    <div class="header-buttons">
      <a href='logout.php'>Logout</a>
      <a href="index.php" class="home-button">Home</a>
    </div>
    <h1>Serien</h1>
    <div class="series-grid">
      <?php foreach ($seriesList as $series): ?>
        <div class="series-item">
          <a href="series_detail.php?id=<?php echo $series['id']; ?>">
            <img src="<?php echo htmlspecialchars($series['image']); ?>" alt="<?php echo htmlspecialchars($series['title']); ?>">
            <p><?php echo htmlspecialchars($series['title']); ?></p>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</body>
</html>
