<?php
session_start();

require_once '../config.php';
require_once '../models/Agenda.php';
require_once '../controllers/AgendaController.php';

$isLoggedIn = isset($_SESSION['user_id']);
$user_id = $_SESSION['user_id'] ?? null;

$db = (new Database())->getConnection();
$agendaController = new AgendaController($db);

// Handle Agenda Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add Agenda Event
    if (isset($_POST['add_agenda'])) {
        $agenda = new Agenda($db);
        $agenda->setUserId($user_id);
        $agenda->setTitle($_POST['title']);
        $agenda->setDate($_POST['date']);
        $agenda->setStartTime($_POST['start_time']);
        $agenda->setEndTime($_POST['end_time']);
        $agenda->setNotes($_POST['notes']);
        $agendaController->create($agenda);
        header("Location: agenda.php");
        exit;
    }

    // Delete Agenda
    if (isset($_POST['delete_agenda'])) {
        $agendaController->delete((int)$_POST['agenda_id']);
        header("Location: agenda.php");
        exit;
    }

    // Update Agenda
    if (isset($_POST['update_agenda_id'])) {
        $agenda = new Agenda($db);
        $agenda->setId((int)$_POST['update_agenda_id']);
        $agenda->setTitle($_POST['title']);
        $agenda->setDate($_POST['date']);
        $agenda->setStartTime($_POST['start_time']);
        $agenda->setEndTime($_POST['end_time']);
        $agenda->setNotes($_POST['notes']);
        $agendaController->update($agenda);
        header("Location: agenda.php");
        exit;
    }
}

// Fetch agenda items
$agendas = $user_id ? $agendaController->getAgendasByUser($user_id) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>My Agenda</title>
    <link rel="stylesheet" href="../public/css/styles.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>
<body>
<nav class="navbar">
    <div class="brand">MyDashboard</div>
    <button class="navbar-toggle"><i class="fas fa-bars"></i></button>
    <ul class="nav-links">
        <li><a href="index.php">Dashboard</a></li>
        <li><a href="tasks.php">Tasks</a></li>
        <li><a href="projects.php" >Projects</a></li>
        <li><a href="wellness.php">Wellness</a></li>
        <li><a href="budget.php">Budget</a></li>
        <li><a href="wishlist.php">Wishlist</a></li>
        <li><a href="agenda.php" class="active">Agenda</a></li>
        <?php if ($isLoggedIn): ?>
            <li><a href="logout.php" class="login-btn">Logout</a></li>
        <?php else: ?>
            <li><a href="login.php" class="login-btn">Login</a></li>
        <?php endif; ?>
    </ul>
</nav>

<main class="dashboard-container">

    <h1>My Agenda</h1>

    <?php if ($isLoggedIn): ?>
        <form method="POST" class="project-form">
            <h3>Add Event</h3>
            <input type="text" name="title" placeholder="Event Title" required />
            <input type="date" name="date" required />
            <input type="time" name="start_time" required />
            <input type="time" name="end_time" required />
            <textarea name="notes" placeholder="Notes"></textarea>
            <button type="submit" name="add_agenda">➕ Add Event</button>
        </form>
    <?php endif; ?>

    <?php if (!empty($agendas)): ?>
        <?php foreach ($agendas as $agenda): ?>
            <section class="dashboard-card project-card" style="margin-bottom: 2rem;">
                <form method="POST">
                    <input type="hidden" name="update_agenda_id" value="<?= (int)$agenda['id'] ?>" />
                    <input type="text" name="title" value="<?= htmlspecialchars($agenda['title']) ?>" required />
                    <input type="date" name="date" value="<?= htmlspecialchars($agenda['date']) ?>" required />
                    <input type="time" name="start_time" value="<?= htmlspecialchars($agenda['start_time']) ?>" required />
                    <input type="time" name="end_time" value="<?= htmlspecialchars($agenda['end_time']) ?>" required />
                    <textarea name="notes"><?= htmlspecialchars($agenda['notes']) ?></textarea>
                    
                    <button type="submit" style="color: green;">💾 Save</button>
                    <input type="hidden" name="agenda_id" value="<?= (int)$agenda['id'] ?>" />
                    <button type="submit" name="delete_agenda" style="color: red;" onclick="return confirm('Are you sure?')">🗑️ Delete</button>
                </form>
            </section>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="text-align:center; color: #888;">No agenda events yet</p>
    <?php endif; ?>
</main>

<script src="../public/js/main.js"></script>
</body>
</html>