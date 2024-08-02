<?php
session_start();
include 'db.php';

// Wenn Benutzer bereits eingeloggt ist, weiterleiten
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $email = $_POST['email'];

    // Überprüfen, ob Benutzer bereits existiert
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param('ss', $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $message = "Benutzername oder E-Mail bereits vergeben.";
    } else {
        // Benutzer registrieren
        $stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $username, $password, $email);
        if ($stmt->execute()) {
            $_SESSION['user_id'] = $conn->insert_id;
            header('Location: pay.php'); // Weiterleiten zur Zahlungsseite
            exit();
        } else {
            $message = "Fehler bei der Registrierung.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Registrierung</title>
  <style>
    body {
      background-color: #141414;
      color: #fff;
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .container {
      background-color: #333;
      padding: 20px;
      border-radius: 5px;
      max-width: 400px;
      width: 100%;
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    label {
      display: block;
      margin-bottom: 10px;
    }

    input[type="text"],
    input[type="password"],
    input[type="email"] {
      width: 100%;
      padding: 10px;
      border: none;
      border-radius: 5px;
      margin-bottom: 20px;
      font-size: 16px;
    }

    button {
      width: 100%;
      background-color: #e50914;
      color: #fff;
      border: none;
      padding: 10px;
      border-radius: 5px;
      font-size: 16px;
      cursor: pointer;
    }

    .message {
      color: #e50914;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Registrieren</h2>
    <?php if (isset($message)) : ?>
      <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="post">
      <label for="username">Benutzername:</label>
      <input type="text" id="username" name="username" required>
      <label for="email">E-Mail:</label>
      <input type="email" id="email" name="email" required>
      <label for="password">Passwort:</label>
      <input type="password" id="password" name="password" required>
      <button type="submit">Registrieren</button>
    </form>
  </div>
</body>
</html>
