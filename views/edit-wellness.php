<?php
require_once '../config.php';
require_once '../models/Wellness.php';
require_once '../controllers/WellnessController.php';

session_start();
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header("Location: ../login.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();
$controller = new WellnessController($db);

// Step 1: Fetch the wellness entry
$wellness_id = $_GET['id'] ?? null;
$wellness = $wellness_id ? $controller->getWellnessById($wellness_id) : null;

if (!$wellness || $wellness->getUserId() !== $user_id) {
    echo "Invalid entry or unauthorized access.";
    exit;
}

// Step 2: Handle form submission
if (isset($_POST['update_wellness'])) {
    $wellness->setMood($_POST['mood']);
    $wellness->setWaterIntake($_POST['water'] ?? []);
    $wellness->setHabits($_POST['habits'] ?? []);
    $wellness->setReflection($_POST['reflection']);

    $controller->update($wellness);
    header("Location: wellness.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Wellness</title>
  <link rel="stylesheet" href="../public/css/styles.css">
</head>
<body>
  <div class="dashboard-container">
    <h2>Edit Wellness Entry</h2>
    <form method="POST">
      <label for="mood">Mood:</label>
      <select name="mood" id="mood">
        <?php
        $moods = ['😊 Happy', '😐 Okay', '😢 Sad', '😠 Stressed'];
        foreach ($moods as $mood) {
            $selected = $wellness->getMood() === $mood ? 'selected' : '';
            echo "<option value=\"$mood\" $selected>$mood</option>";
        }
        ?>
      </select><br><br>

      <label>Water Intake:</label><br>
      <?php
      $water_intake = $wellness->getWaterIntake(); // array
      for ($i = 0; $i < 8; $i++) {
          $checked = in_array($i, $water_intake) ? 'checked' : '';
          echo "<label><input type='checkbox' name='water[]' value='$i' $checked>💧</label> ";
      }
      ?><br><br>

      <label>Habits:</label><br>
      <?php
      $habits_list = ['Meditated', 'Walked', 'Ate Healthy', 'Took a Break'];
      $user_habits = $wellness->getHabits();
      foreach ($habits_list as $habit) {
          $checked = in_array($habit, $user_habits) ? 'checked' : '';
          echo "<div><label><input type='checkbox' name='habits[]' value=\"$habit\" $checked> $habit</label></div>";
      }
      ?><br>

      <label>Reflection:</label><br>
      <textarea name="reflection"><?= htmlspecialchars($wellness->getReflection()) ?></textarea><br><br>

      <button type="submit" name="update_wellness">Update Wellness</button>
      <a href="wellness.php">Cancel</a>
    </form>
  </div>
</body>
</html>
