<?php



class ScrumMaster extends User {
    private $managedTeams = [];

    public function __construct($id, $username, $password) {
        parent::__construct($id, $username, $password);
    }



    
    public function CreeEquipe($team) {
        
        $this->managedTeams[] = $team;
    }



    public function ModifierEquipe() {
        
        return $this->managedTeams;
    }



    public function SupprimerEquipe() {
        
        return $this->managedTeams;
    }
}





















?>
