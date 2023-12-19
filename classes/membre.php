<?php


class Member extends User {
    private $assignedTeams = [];

    public function __construct($id, $username, $password) {
        parent::__construct($id, $username, $password);
    }

    public function addAssignedTeam($team) {
        // Ajoute une équipe à la liste des équipes assignées au Team Member
        $this->assignedTeams[] = $team;
    }

    public function getAssignedTeams() {
        // Récupère la liste des équipes assignées au Team Member
        return $this->assignedTeams;
    }

    // Vous pouvez ajouter d'autres méthodes spécifiques au Team Member
}


