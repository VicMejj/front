<?php
session_start();

require_once '../config.php';
require_once '../models/Wishlist.php';
require_once '../controllers/WishlistController.php';

$isLoggedIn = isset($_SESSION['user_id']);
$user_id = $_SESSION['user_id'] ?? null;

$db = (new Database())->getConnection();
$wishlistController = new WishlistController($db);

// Handle Wishlist Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add Wishlist Item
    if (isset($_POST['add_wishlist'])) {
        $wishlist = new Wishlist($db);
        $wishlist->setUserId($user_id);
        $wishlist->setItemName($_POST['item_name']);
        $wishlist->setItemUrl($_POST['item_url']);
        $wishlist->setPriority($_POST['priority']);
        $wishlist->setNotes($_POST['notes']);
        $wishlistController->create($wishlist);
        header("Location: wishlist.php");
        exit;
    }

    // Delete Wishlist
    if (isset($_POST['delete_wishlist'])) {
        $wishlistController->delete((int)$_POST['item_id']);
        header("Location: wishlist.php");
        exit;
    }

    // Update Wishlist
    if (isset($_POST['update_wishlist_id'])) {
        $wishlist = new Wishlist($db);
        $wishlist->setItemId((int)$_POST['update_wishlist_id']);
        $wishlist->setItemName($_POST['item_name']);
        $wishlist->setItemUrl($_POST['item_url']);
        $wishlist->setPriority($_POST['priority']);
        $wishlist->setNotes($_POST['notes']);
        $wishlistController->update($wishlist);
        header("Location: wishlist.php");
        exit;
    }
}

// Fetch wishlist items
$wishlists = $user_id ? $wishlistController->getWishlistsByUser($user_id) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>My Wishlist</title>
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
        <li><a href="wishlist.php" class="active">Wishlist</a></li>
        <li><a href="agenda.php">Agenda</a></li>
        <?php if ($isLoggedIn): ?>
            <li><a href="logout.php" class="login-btn">Logout</a></li>
        <?php else: ?>
            <li><a href="login.php" class="login-btn">Login</a></li>
        <?php endif; ?>
    </ul>
</nav>

<main class="dashboard-container">
    
    <h1>My Wishlist</h1>

    <?php if ($isLoggedIn): ?>
        <form method="POST" class="project-form">
            <h3>Add Wishlist Item</h3>
            <input type="text" name="item_name" placeholder="Item Name" required />
            <input type="url" name="item_url" placeholder="Item URL" />
            <input type="number" name="priority" placeholder="Priority (1-5)" min="1" max="5" />
            <textarea name="notes" placeholder="Notes"></textarea>
            <button type="submit" name="add_wishlist">➕ Add Item</button>
        </form>
    <?php endif; ?>

    <?php if (!empty($wishlists)): ?>
        <?php foreach ($wishlists as $item): ?>
            <section class="dashboard-card project-card" style="margin-bottom: 2rem;">
                <form method="POST">
                    <input type="hidden" name="update_wishlist_id" value="<?= (int)$item['item_id'] ?>" />
                    <input type="text" name="item_name" value="<?= htmlspecialchars($item['item_name']) ?>" required />
                    <input type="url" name="item_url" value="<?= htmlspecialchars($item['item_url']) ?>" />
                    <input type="number" name="priority" value="<?= htmlspecialchars($item['priority']) ?>" min="1" max="5" />
                    <textarea name="notes"><?= htmlspecialchars($item['notes']) ?></textarea>
                    
                    <button type="submit" style="color: green;">💾 Save</button>
                    <input type="hidden" name="item_id" value="<?= (int)$item['item_id'] ?>" />
                    <button type="submit" name="delete_wishlist" style="color: red;" onclick="return confirm('Are you sure?')">🗑️ Delete</button>
                </form>
            </section>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="text-align:center; color: #888;">No wishlist items yet</p>
    <?php endif; ?>
</main>

<script src="../public/js/main.js"></script>
</body>
</html>