<?php
// header.php
require_once 'database.php';

// Obtener el logo de la empresa desde la base de datos
$logo_empresa = '';
try {
  $stmt = $conn->query("SELECT valor FROM configuraciones_sistema WHERE clave = 'logo_empresa'");
  $result = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($result) {
    $logo_empresa = $result['valor'];
  }
} catch (PDOException $e) {
  // Si hay error, se mantiene el logo por defecto
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="style.css">
  <!-- Incluir Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Incluir Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

  <!-- Incluir DataTables con Bootstrap 5 -->
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/jszip-2.5.0/dt-1.13.4/b-2.3.6/b-html5-2.3.6/datatables.min.css" />

  <!-- Incluir jQuery y Bootstrap JS Bundle con Popper -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Incluir DataTables y extensiones -->
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/jszip-2.5.0/dt-1.13.4/b-2.3.6/b-html5-2.3.6/datatables.min.js"></script>

  <!-- Incluir nuestro script personalizado -->
  <script src="js/usuarios.js"></script>
  <title>Mi Aplicación</title>

</head>

<body>
  <div class="container">
    <header>
      <div class="branding">
        <a href="/" aria-label="Inicio">
          <?php if (!empty($logo_empresa)): ?>
            <img src="<?= htmlspecialchars($logo_empresa) ?>" alt="Logo de la empresa" style="max-height: 40px;">
          <?php else: ?>
            <svg width="120" height="40" viewBox="0 0 120 40" xmlns="http://www.w3.org/2000/svg">
              <rect width="120" height="40" rx="8" ry="8" fill="#004080" />
              <circle cx="20" cy="20" r="12" fill="#ffd700" />
              <text x="40" y="25" font-family="Arial, sans-serif" font-size="18" fill="#fff">MiApp</text>
            </svg>
          <?php endif; ?>
        </a>
      </div>
      <span class="menu-toggle" id="menuToggle">☰</span>
      <div class="top-actions">
        <a href="#perfil">Mi perfil</a>
        <a href="logout.php">Cerrar cesión</a>
      </div>
    </header>
    <div class="layout">