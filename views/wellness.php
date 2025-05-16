<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once '../config.php';
require_once '../models/Wellness.php';
require_once '../controllers/WellnessController.php';

$isLoggedIn = isset($_SESSION['user_id']);
$user_id = $_SESSION['user_id'] ?? null;

$database = new Database();
$db = $database->getConnection();
$controller = new WellnessController($db);

// Handle Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_entry'])) {
    $entry = new Wellness();
    $entry->setUserId($user_id);
    $entry->setDate($_POST['date']);
    $entry->setMoodLevel($_POST['mood_level']);
    $entry->setSleepHours($_POST['sleep_hours']);
    $entry->setNotes($_POST['notes']);
    $controller->create($entry);
    header("Location: wellness.php");
    exit;
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_entry_id'])) {
    $entry = new Wellness();
    $entry->setEntryId($_POST['update_entry_id']);
    $entry->setDate($_POST['date']);
    $entry->setMoodLevel($_POST['mood_level']);
    $entry->setSleepHours($_POST['sleep_hours']);
    $entry->setNotes($_POST['notes']);
    $controller->update($entry);
    header("Location: wellness.php");
    exit;
}

// Handle Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_entry_id'])) {
    $controller->delete($_POST['delete_entry_id']);
    header("Location: wellness.php");
    exit;
}

$entries = $isLoggedIn ? $controller->getByUser($user_id) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Wellness Tracker</title>
    <link rel="stylesheet" href="../public/css/styles.css">
</head>
<body>
    <nav class="navbar">
        <div class="brand">MyDashboard</div>
        <ul class="nav-links">
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="tasks.php">Tasks</a></li>
            <li><a href="wellness.php" class="active">Wellness</a></li>
            <li><a href="budget.html">Budget</a></li>
            <li><a href="wishlist.html">Wishlist</a></li>
            <li><a href="agenda.html">Agenda</a></li>
            <?php if ($isLoggedIn): ?>
                <li><a href="logout.php" class="login-btn">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php" class="login-btn">Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <main class="dashboard-container">
        <style>
            /* Styles are the same as before, no major change required */
        </style>

        <header class="dashboard-header">
            <h1>Wellness Tracker</h1>
            <p>Track your mood, sleep, and notes daily</p>
        </header>

        <section class="dashboard-card">
            <h2>Add New Entry</h2>
            <form method="POST" class="task-form">
                <input type="date" name="date" required>
                <input type="number" name="mood_level" placeholder="Mood (1-10)" min="1" max="10" required>
                <input type="number" step="0.1" name="sleep_hours" placeholder="Sleep (hours)" required>
                <textarea name="notes" placeholder="Notes"></textarea>
                <button type="submit" name="add_entry">➕ Add Entry</button>
            </form>
        </section>

        <section class="dashboard-card">
            <h2>My Wellness Entries</h2>
            <?php if (empty($entries)): ?>
                <p style="text-align: center;">No entries yet.</p>
            <?php else: ?>
                <table class="task-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Mood</th>
                            <th>Sleep (hrs)</th>
                            <th>Notes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($entries as $entry): ?>
                        <tr>
                            <form method="POST">
                                <td><input type="date" name="date" value="<?= htmlspecialchars($entry->getDate()) ?>" required></td>
                                <td><input type="number" name="mood_level" value="<?= htmlspecialchars($entry->getMoodLevel()) ?>" min="1" max="10" required></td>
                                <td><input type="number" step="0.1" name="sleep_hours" value="<?= htmlspecialchars($entry->getSleepHours()) ?>" required></td>
                                <td><textarea name="notes"><?= htmlspecialchars($entry->getNotes()) ?></textarea></td>
                                <td>
                                    <input type="hidden" name="update_entry_id" value="<?= $entry->getEntryId() ?>">
                                    <button type="submit">💾 Save</button>
                            </form>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="delete_entry_id" value="<?= $entry->getEntryId() ?>">
                                <button type="submit" onclick="return confirm('Delete this entry?')" style="color:red;">🗑️ Delete</button>
                            </form>
                                </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
