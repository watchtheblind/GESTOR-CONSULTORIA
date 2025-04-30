<nav class="sidebar" id="sidebar">
  <ul>
    <!-- Elementos comunes a todos los roles -->
    <li><a href="dashboard.php">Dashboard</a></li>

    <?php if ($_SESSION['rol'] === 'Cliente'): ?>
      <!-- Menú exclusivo para Clientes -->
      <li><a href="mis_proyectos.php">Mis Proyectos</a></li>
      <li><a href="mensajes.php">Chat con Consultor</a></li>
      <li><a href="mis_tareas.php">Mis Tareas</a></li>
      <li><a href="archivos.php">Documentos</a></li>

    <?php else: ?>
      <!-- Menú para roles no-cliente -->
      <?php if ($_SESSION['rol'] === 'Consultor Principal' || $_SESSION['rol'] === 'Consultor Colaborador'): ?>
        <li><a href="clientes_asignados.php">Clientes Asignados</a></li>
      <?php endif; ?>

      <?php if ($_SESSION['rol'] !== 'Consultor Colaborador'): ?>
        <li><a href="mensajes.php">Mensajes</a></li>
      <?php endif; ?>

      <li><a href="proyectos.php">Proyectos</a></li>
      <li><a href="tareas.php">Tareas</a></li>
      <li><a href="archivos.php">Archivos</a></li>

      <?php if ($_SESSION['rol'] === 'Consultor Principal'): ?>
        <li><a href="reportes.php">Reportes</a></li>
      <?php endif; ?>

      <?php if ($_SESSION['rol'] === 'Administrador' || $_SESSION['rol'] === 'Subadministrador'): ?>
        <li><a href="usuarios.php">Usuarios</a></li>
        <li><a href="actividad.php">Actividad del Sistema</a></li>
      <?php endif; ?>

      <?php if ($_SESSION['rol'] === 'Administrador'): ?>
        <li><a href="configuracion.php">Configuración</a></li>
      <?php endif; ?>
    <?php endif; ?>
  </ul>
</nav>