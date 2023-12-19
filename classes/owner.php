<?php



class ProductOwner extends User {

    private $db;
    public function __construct(Database $db,$id, $username, $password)
    {
        $this->db = $db->getconnection();
        parent::__construct($id, $username, $password);
    }
   




    
   



    public function CreeProjet($project) {
        
    }



    public function ModifierProjet() { // aussi assigner scrum


    }


   

    public function deleteProject($projectId)
{
    $this->db->beginTransaction();

    try {
        // Supprimer les membres de l'équipe associée au projet
        $sql = "DELETE FROM MembreEquipe WHERE id_equipe IN (SELECT id FROM equipe WHERE id_projet = :projectId)";
        $requete = $this->db->prepare($sql);
        $requete->bindParam(':projectId', $projectId, PDO::PARAM_INT);
        $requete->execute();

        // Supprimer les équipes associées au projet
        $sql = "DELETE FROM equipe WHERE id_projet = :projectId";
        $requete = $this->db->prepare($sql);
        $requete->bindParam(':projectId', $projectId, PDO::PARAM_INT);
        $requete->execute();

        // Supprimer le projet lui-même
        $sql = "DELETE FROM projet WHERE id = :projectId";
        $requete = $this->db->prepare($sql);
        $requete->bindParam(':projectId', $projectId, PDO::PARAM_INT);
        $requete->execute();

        $this->db->commit();
    } catch (PDOException $e) {
        $this->db->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
    
}




































