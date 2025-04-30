<?php
// dashboard.php
session_start();
require_once 'database.php';
require_once 'auth_functions.php';

if (!isset($_SESSION['user_id']) || !checkSessionExpiration()) {
    header('Location: index.php');
    exit();
}

// Obtener datos del usuario actual
$stmt = $conn->prepare("SELECT nombre_usuario, rol FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Panel <?php echo htmlspecialchars($user['rol']); ?> – CSM</title>
    <style>
        :root {
            --primary: #0a2463;
            --bg-light: #f5f7fa;
            --sidebar-width: 240px;
            --max-page: 1200px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            background: var(--bg-light);
            color: #333;
        }

        /* ---------- Layout ---------- */
        .wrapper {
            max-width: var(--max-page);
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header {
            background: var(--primary);
            color: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header nav ul {
            list-style: none;
            display: flex;
            gap: 20px;
        }

        header nav a {
            color: #fff;
            text-decoration: none;
            font-size: 14px;
        }

        .main-container {
            flex: 1;
            display: flex;
            min-height: 0;
        }

        /* ---------- Sidebar ---------- */
        aside {
            width: var(--sidebar-width);
            background: #fff;
            border-right: 1px solid #e0e0e0;
            padding-top: 20px;
            display: flex;
            flex-direction: column;
        }

        .logo {
            font-weight: bold;
            margin-bottom: 30px;
            padding-left: 20px;
            color: var(--primary);
        }

        .nav {
            flex: 1;
        }

        .nav ul {
            list-style: none;
        }

        .nav li {
            margin: 8px 0;
        }

        .nav a {
            display: block;
            padding: 10px 20px;
            color: #333;
            text-decoration: none;
            font-size: 14px;
        }

        .nav a:hover {
            background: #eef1f5;
        }

        /* ---------- Content ---------- */
        main {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .card {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }

        .logs {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }

        h1 {
            font-size: 22px;
            margin-bottom: 10px;
            color: var(--primary);
        }

        /* ---------- Responsive ---------- */
        @media (max-width: 768px) {
            aside {
                position: fixed;
                left: -100%;
                top: 0;
                height: 100%;
                transition: left 0.3s ease;
                z-index: 1000;
            }

            aside.open {
                left: 0;
            }

            .main-container {
                flex-direction: column;
            }
        }

        .sidebar {
            width: 200px;
            padding: 1rem;
            transition: left 0.3s;
        }

        .sidebar ul {
            list-style: none;
        }

        .sidebar li {
            margin-bottom: 1rem;
        }

        .sidebar a {
            text-decoration: none;
            color: #333;
            font-size: 1.3rem;
        }

        .content {
            flex: 1;
            padding: 1.5rem;
            overflow-y: auto;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <header>
            <div class="brand">CSM • <?php echo htmlspecialchars($user['rol']); ?></div>
            <nav>
                <ul>
                    <li><a href="#">Mi perfil</a></li>
                    <li><a href="logout.php">Cerrar sesión</a></li>
                </ul>
            </nav>
        </header>

        <div class="main-container">
            <aside>
                <div class="logo">LOGO</div>
                <?php include 'sidebar.php'; ?>
            </aside>

            <main>
                <h1>Bienvenido, <?php echo htmlspecialchars($user['nombre_usuario']); ?></h1>

                <section class="cards">
                    <div class="card">
                        <h2>Proyectos activos</h2>
                        <p>42</p>
                    </div>
                    <div class="card">
                        <h2>Tareas pendientes</h2>
                        <p>87</p>
                    </div>
                    <div class="card">
                        <h2>Nuevos mensajes</h2>
                        <p>5</p>
                    </div>
                </section>

                <section class="logs">
                    <h2>Actividad reciente</h2>
                    <ul>
                        <li>[12:30] Juan subió "Informe final.pdf" al proyecto ACME</li>
                        <li>[11:05] Ana creó el usuario "cliente_xyz"</li>
                        <li>[09:50] Pedro cerró la tarea "Validar contrato"</li>
                    </ul>
                </section>
            </main>
        </div>
    </div>
</body>

</html>