<?php
session_start();

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'];
    $new_password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Verbindung zur Datenbank herstellen
    include 'db.php';

    if ($conn->connect_error) {
        $error_message = "Verbindung fehlgeschlagen: " . $conn->connect_error;
    } else {
        $sql = "SELECT id, reset_expiry FROM users WHERE reset_token=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id, $reset_expiry);
        $stmt->fetch();

        if ($stmt->num_rows > 0 && new DateTime() < new DateTime($reset_expiry)) {
            $sql = "UPDATE users SET password=?, reset_token=NULL, reset_expiry=NULL WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $new_password, $id);
            if ($stmt->execute()) {
                $success_message = "Dein Passwort wurde erfolgreich zurückgesetzt.";
            } else {
                $error_message = "Passwort konnte nicht zurückgesetzt werden. Bitte versuche es erneut.";
            }
        } else {
            $error_message = "Der Link zum Zurücksetzen deines Passworts ist ungültig oder abgelaufen.";
        }

        $stmt->close();
        $conn->close();
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['token'])) {
    $token = $_GET['token'];
} else {
    header("Location: forgot_password.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passwort zurücksetzen</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            background-color: #141414;
            color: #fff;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #282828;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            text-align: center;
            width: 300px;
        }
        h2 {
            margin-top: 0;
        }
        .error {
            color: #e50914;
        }
        .success {
            color: #4CAF50;
        }
        label, input, button {
            display: block;
            width: 100%;
            margin: 10px 0;
        }
        input {
            padding: 10px;
            border: none;
            border-radius: 5px;
        }
        button {
            background-color: #e50914;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Passwort zurücksetzen</h2>

        <?php if (!empty($error_message)) : ?>
            <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>

        <?php if (!empty($success_message)) : ?>
            <p class="success"><?php echo htmlspecialchars($success_message); ?></p>
        <?php endif; ?>

        <?php if (empty($success_message)) : ?>
            <form action="reset_password.php" method="POST">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <label for="password">Neues Passwort:</label>
                <input type="password" id="password" name="password" required>
                <button type="submit">Passwort zurücksetzen</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
