<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h2>🔥 POST RECEIVED</h2><pre>";
    print_r($_POST);
    echo "</pre>";
}
?>

<form method="POST" action="" style="margin-top: 40px;">
    <input name="title" placeholder="Title"><br>
    <input name="description" placeholder="Description"><br>
    <input type="date" name="due_date"><br>
    <button type="submit" name="add_task">Submit</button>
</form>