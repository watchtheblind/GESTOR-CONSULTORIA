<?php
// login.php - Solo markup (la lógica está en index.php)
if (isset($error)) echo "<div class='error'>$error</div>";

// Incluir conexión a la base de datos
require_once 'database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $usernameOrEmail = $_POST['username']; // Cambiamos el nombre de la variable
  $password = $_POST['password'];

  try {
    // Consulta preparada para evitar SQL injection
    $stmt = $conn->prepare("SELECT id, contrasena, rol FROM usuarios WHERE nombre_usuario = ? OR correo_electronico = ? LIMIT 1");
    $stmt->execute([$usernameOrEmail, $usernameOrEmail]); // Ejecutamos con el mismo valor
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['contrasena'])) {
      // Credenciales válidas
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['username'] = $usernameOrEmail; // Cambiamos a la nueva variable
      $_SESSION['rol'] = $user['rol'];
      $_SESSION['last_activity'] = time();

      header('Location: dashboard.php');
      exit();
    } else {
      $error = "Usuario o contraseña incorrectos";
    }
  } catch (PDOException $e) {
    $error = "Error al conectar con la base de datos: " . $e->getMessage();
  }
}
?>

<!-- Wrapper para centrado -->
<div class="login-wrapper">
  <div class="login-container">
    <h1>Acceso a clientes</h1>
    <?php if (isset($error)) echo "<p>$error</p>"; ?>
    <form action="#" method="post">
      <input type="text" name="username" placeholder="Usuario o correo electrónico" required />
      <input type="password" name="password" placeholder="Contraseña" required />
      <button type="submit">Ingresar</button>
    </form>
    <div class="recover">
      <button type="button" onclick="location.href='#'">Recuperar contraseña</button>
    </div>
    <div class="signup-link">
      <a href="#">Si no eres cliente, adquiere nuestros servicios aquí</a>
    </div>
  </div>
</div>