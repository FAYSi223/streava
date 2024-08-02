<?php
session_start();
include 'db.php';

// Überprüfen, ob der Benutzer bereits eingeloggt ist
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Verarbeitung des Login-Formulars
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // SQL-Anweisung vorbereiten und ausführen
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id, $hashed_password);
    
    // Überprüfen, ob ein Benutzer gefunden wurde
    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            // Erfolgreiche Anmeldung
            $_SESSION['user_id'] = $user_id;
            header('Location: index.php');
            exit();
        } else {
            $message = "Falsches Passwort.";
        }
    } else {
        $message = "Benutzer nicht gefunden.";
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Streava</title>
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
    input[type="password"] {
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

    p {
      text-align: center;
      color: #fff;
    }

    a {
      color: #e50914;
      text-decoration: none;
    }

    a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Login</h2>
    <?php if (isset($message)) : ?>
      <p class="message"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <form method="post">
      <label for="username">Benutzername:</label>
      <input type="text" id="username" name="username" required>
      <label for="password">Passwort:</label>
      <input type="password" id="password" name="password" required>
      <button type="submit">Login</button>
    </form>
    <p>Noch keinen Account? <a href="register.php">Registrieren</a><br>
       <a href="reset_password.php">Passwort vergessen</a></p>
  </div>
</body>
</html>
