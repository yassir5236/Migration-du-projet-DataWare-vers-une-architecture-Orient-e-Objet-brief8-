<?php

class Team {
    Private $db;
    public function __construct(Database $db) {
        $this->db = $db->getConnection();
    }
  
    
    public function getAllEquipes()          // afficher toutes les equipes avec le scrum master de chaque equipe  pour le productOwner 
        {
            $sql = "SELECT
                        equipe.nom AS nom_equipe,
                        projet.nom AS nom_projet,
                        utilisateur.nom AS scrum_master,
                        equipe.date_creation as date_creation
                    FROM
                        equipe
                    JOIN
                        utilisateur ON equipe.id_user = utilisateur.id
                    JOIN
                        projet ON equipe.id_projet = projet.id
                    GROUP BY
                        equipe.nom, utilisateur.nom, projet.nom;";
    
            $requete = $this->db->prepare($sql);
            $requete->execute();
    
            return $requete->fetchAll(PDO::FETCH_ASSOC);
        }
   



    public function getEquipesByUserIdowner($userId){            // afficher equipe pour product owner

        $sql = "SELECT
                    equipe.id AS id_equipe,
                    equipe.nom AS nom_equipe,
                    equipe.date_creation AS date_creation,
                    projet.nom AS nom_projet,
                    GROUP_CONCAT(DISTINCT membre.nom SEPARATOR ', ') AS membres
                FROM
                    equipe
                JOIN projet ON projet.id = equipe.id_projet
                LEFT JOIN MembreEquipe AS membre_equipe ON equipe.id = membre_equipe.id_equipe
                LEFT JOIN utilisateur AS membre ON membre_equipe.id_user = membre.id
                WHERE
                    equipe.id_user = :id_utilisateur
                GROUP BY
                    equipe.id";

    
                $requete = $this->db->prepare($sql);
                $requete->bindParam(':id_utilisateur', $userId, PDO::PARAM_INT);
                $requete->execute();

                return $requete->fetchAll(PDO::FETCH_ASSOC);
}


  

    public function getEquipesByUserId($userId) {          // afficher equipe pour membre 

        $sql = "SELECT
            equipe.nom AS nom_equipe,
            projet.nom AS nom_projet,
            utilisateur.nom AS scrum_master,
            GROUP_CONCAT(DISTINCT membre.nom SEPARATOR ', ') AS membres,
            equipe.date_creation as date_creation
        FROM
            equipe
        JOIN
            MembreEquipe ON equipe.id = MembreEquipe.id_equipe
        JOIN
            utilisateur ON equipe.id_user = utilisateur.id
        JOIN
            projet ON equipe.id_projet = projet.id
        LEFT JOIN
            MembreEquipe AS membre_equipe ON equipe.id = membre_equipe.id_equipe
        LEFT JOIN
            utilisateur AS membre ON membre_equipe.id_user = membre.id
        WHERE membre_equipe.id_user = :id_utilisateur
        GROUP BY    
            equipe.nom, utilisateur.nom, projet.nom;
        ";
    
        $requete = $this->db->prepare($sql);
        $requete->bindParam(':id_utilisateur', $userId, PDO::PARAM_INT);
        $requete->execute();
    
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }
    



    public function getEquipesScrumByUserId(){        // afficher membre equipe pour scrum
    if(!isset($_SESSION['utilisateur']['id'])){
        header("Location:../Deconnexion.php ");
        $id_utilisateur = $_SESSION['utilisateur']['id'];
    }
    
    
    
    // $sql = "select nom,email,statut from utilisateur where role='user' ";
    // $requete = $this->db->prepare($sql);
    //     $requete->bindParam(1, $id_utilisateur, PDO::PARAM_INT);
    //     $requete->execute();


        $sql = "SELECT nom, email, statut FROM utilisateur WHERE role='user' ";
        $requete = $this->db->prepare($sql);
        // Bind the parameter using named placeholder
        
        $requete->execute();

    return $requete->fetchAll(PDO::FETCH_ASSOC);
    }






    public function createTeam($name) {
        // Logique de création d'équipe
    }

    public function updateTeam($teamId, $name) {
        // Logique de mise à jour d'équipe
    }

    public function deleteTeam($teamId) {
        // Logique de suppression d'équipe
    }
}
