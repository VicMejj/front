<?php
require_once '../models/Project.php';

class ProjectController {
    private $db;
    private $projectModel;

    public function __construct($db) {
        $this->db = $db;
        $this->projectModel = new Project($db);
    }

    public function create(Project $project) {
        return $project->create();
    }

    public function update(Project $project) {
        return $project->update();
    }

    public function delete(int $project_id) {
        $project = new Project($this->db);
        $project->setProjectId($project_id);
        return $project->delete();
    }

    public function getProjectById(int $project_id) {
        return $this->projectModel->getById($project_id);
    }

    public function getProjectsByUser(int $user_id) {
        return $this->projectModel->getProjectsByUser($user_id);
    }
}
