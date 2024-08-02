<?php
session_start();
include 'db.php';

// Fehlerbericht aktivieren
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Überprüfen, ob der Benutzer eingeloggt ist
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Funktion zum Abrufen von Benutzerinformationen
function fetchUserProfile($userId) {
    global $conn;
    $userId = intval($userId);

    // SQL-Abfrage, um Benutzerinformationen abzurufen
    $sql = "SELECT username, email, created_at FROM users WHERE id = $userId";
    $result = $conn->query($sql);

    // Überprüfen, ob die Abfrage erfolgreich war
    if (!$result) {
        die('Ungültige Abfrage: ' . $conn->error);
    }

    return $result->fetch_assoc();
}

// Benutzerinformationen abrufen
$userId = $_SESSION['user_id'];
$userProfile = fetchUserProfile($userId);

// Überprüfen, ob Benutzerinformationen gefunden wurden
if (!$userProfile) {
    die('Benutzerinformationen konnten nicht abgerufen werden.');
}

// Funktion zum Aktualisieren von Benutzerinformationen
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newUsername = $conn->real_escape_string($_POST['username']);
    $newEmail = $conn->real_escape_string($_POST['email']);

    $updateSql = "UPDATE users SET username='$newUsername', email='$newEmail' WHERE id=$userId";
    if ($conn->query($updateSql) === TRUE) {
        header('Location: profile.php');
        exit();
    } else {
        die('Fehler beim Aktualisieren der Daten: ' . $conn->error);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Profil</title>
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

    .profile-container {
      background-color: #282828;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
    }

    .profile-container h1 {
      margin-top: 0;
    }

    .profile-container p {
      font-size: 18px;
      margin: 10px 0;
    }

    .edit-icon {
      cursor: pointer;
      margin-left: 10px;
      font-size: 18px;
    }

    .edit-form {
      display: none;
      margin-top: 20px;
    }

    .edit-form input {
      padding: 10px;
      margin: 10px 0;
      border: none;
      border-radius: 5px;
      width: calc(100% - 22px); /* Adjust width to account for padding */
    }

    .edit-form button {
      background-color: #e50914;
      color: #fff;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      font-size: 16px;
      cursor: pointer;
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
    <header>
      <h1>Profil</h1>
      <a href="logout.php">Logout</a>
    </header>

    <div class="profile-container">
      <h1>
        <?php echo htmlspecialchars($userProfile['username']); ?>
        <span class="edit-icon" onclick="toggleEditForm('username')">✏️</span>
      </h1>
      <p>
        <strong>Email:</strong> <?php echo htmlspecialchars($userProfile['email']); ?>
        <span class="edit-icon" onclick="toggleEditForm('email')">✏️</span>
      </p>
      <p><strong>Beitrittsdatum:</strong> <?php echo htmlspecialchars($userProfile['created_at']); ?></p>

      <div id="edit-form-username" class="edit-form">
        <form method="post" action="profile.php">
          <input type="text" name="username" value="<?php echo htmlspecialchars($userProfile['username']); ?>" required>
          <button type="submit">Speichern</button>
        </form>
      </div>

      <div id="edit-form-email" class="edit-form">
        <form method="post" action="profile.php">
          <input type="email" name="email" value="<?php echo htmlspecialchars($userProfile['email']); ?>" required>
          <button type="submit">Speichern</button>
        </form>
      </div>
    </div>

    <div class="back-button-container">
      <button onclick="goBack()">Zurück</button>
    </div>
  </div>

  <script>
    function toggleEditForm(field) {
      var formId = 'edit-form-' + field;
      var form = document.getElementById(formId);
      if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
      } else {
        form.style.display = 'none';
      }
    }

    function goBack() {
      window.location.href = 'index.php';
    }
  </script>
</body>
</html>
