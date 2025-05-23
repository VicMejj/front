<?php
require_once '../models/Agenda.php';

class AgendaController {
    private $db;
    private $agendaModel;

    public function __construct($db) {
        $this->db = $db;
        $this->agendaModel = new Agenda($db);
    }

    public function create(Agenda $agenda) {
        return $agenda->create();
    }

    public function update(Agenda $agenda) {
        return $agenda->update();
    }

    public function delete($id) {
        $agenda = new Agenda($this->db);
        $agenda->setId($id);
        return $agenda->delete();
    }

    public function getAgendasByUser($user_id) {
        return $this->agendaModel->getByUser($user_id);
    }
}
?>