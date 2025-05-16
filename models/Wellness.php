<?php

class Wellness {
    private $entry_id;
    private $user_id;
    private $date;
    private $mood_level;
    private $sleep_hours;
    private $notes;

    // Entry ID
    public function getEntryId() {
        return $this->entry_id;
    }

    public function setEntryId($entry_id) {
        $this->entry_id = $entry_id;
    }

    // User ID
    public function getUserId() {
        return $this->user_id;
    }

    public function setUserId($user_id) {
        $this->user_id = $user_id;
    }

    // Date
    public function getDate() {
        return $this->date;
    }

    public function setDate($date) {
        $this->date = $date;
    }

    // Mood Level
    public function getMoodLevel() {
        return $this->mood_level;
    }

    public function setMoodLevel($mood_level) {
        $this->mood_level = $mood_level;
    }

    // Sleep Hours
    public function getSleepHours() {
        return $this->sleep_hours;
    }

    public function setSleepHours($sleep_hours) {
        $this->sleep_hours = $sleep_hours;
    }

    // Notes
    public function getNotes() {
        return $this->notes;
    }

    public function setNotes($notes) {
        $this->notes = $notes;
    }
}