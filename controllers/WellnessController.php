<?php
require_once '../models/Wellness.php';

class WellnessController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // ✅ Create a new wellness entry
    public function create(Wellness $entry) {
        $sql = "INSERT INTO wellness_tracker (user_id, date, mood_level, sleep_hours, notes)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "isids",
            $entry->getUserId(),
            $entry->getDate(),
            $entry->getMoodLevel(),
            $entry->getSleepHours(),
            $entry->getNotes()
        );

        return $stmt->execute();
    }

    // ✅ Get all entries for a user
public function getByUser($user_id) {
    $sql = "SELECT * FROM wellness_tracker WHERE user_id = ? ORDER BY date DESC";
    $stmt = $this->conn->prepare($sql);

    if (!$stmt) {
        die("❌ Prepare failed in getByUser(): " . $this->conn->error);
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $entries = [];
    while ($row = $result->fetch_assoc()) {
        $entry = new Wellness();
        $entry->setEntryId($row['entry_id']);
        $entry->setUserId($row['user_id']);
        $entry->setDate($row['date']);
        $entry->setMoodLevel($row['mood_level']);
        $entry->setSleepHours($row['sleep_hours']);
        $entry->setNotes($row['notes']);
        $entries[] = $entry;
    }

    return $entries;
}


    // ✅ Get one entry by ID
    public function getById($entry_id) {
        $sql = "SELECT * FROM wellness_tracker WHERE entry_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $entry_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row) {
            $entry = new Wellness();
            $entry->setEntryId($row['entry_id']);
            $entry->setUserId($row['user_id']);
            $entry->setDate($row['date']);
            $entry->setMoodLevel($row['mood_level']);
            $entry->setSleepHours($row['sleep_hours']);
            $entry->setNotes($row['notes']);
            return $entry;
        }

        return null;
    }

    // ✅ Update an existing entry
    public function update(Wellness $entry) {
        $sql = "UPDATE wellness_tracker 
                SET date = ?, mood_level = ?, sleep_hours = ?, notes = ?
                WHERE entry_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "ssdsi",
            $entry->getDate(),
            $entry->getMoodLevel(),
            $entry->getSleepHours(),
            $entry->getNotes(),
            $entry->getEntryId()
        );

        return $stmt->execute();
    }

    

    // ✅ Delete an entry
    public function delete($entry_id) {
        $sql = "DELETE FROM wellness_tracker WHERE entry_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $entry_id);
        return $stmt->execute();
    }
    // ✅ HELPER: handle POST logic
public function handlePostRequest($user_id) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // ✅ ADD ENTRY
        if (isset($_POST['add_wellness'])) {
            $entry = new Wellness();
            $entry->setUserId($user_id);
            $entry->setDate($_POST['date']);
            $entry->setMoodLevel($_POST['mood_level']);
            $entry->setSleepHours($_POST['sleep_hours']);
            $entry->setNotes(trim($_POST['notes']));
            return $this->create($entry);
        }

        // ✅ UPDATE ENTRY
        if (isset($_POST['update_wellness'])) {
            $entry = new Wellness();
            $entry->setEntryId($_POST['entry_id']);
            $entry->setDate($_POST['date']);
            $entry->setMoodLevel($_POST['mood_level']);
            $entry->setSleepHours($_POST['sleep_hours']);
            $entry->setNotes(trim($_POST['notes']));
            return $this->update($entry);
        }

        // ✅ DELETE ENTRY
        if (isset($_POST['delete_entry_id'])) {
            return $this->delete((int)$_POST['delete_entry_id']);
        }
    }

    return false;
}

}