<?php

class Team {
    Private $db;
    public function __construct() {
        $this->db = Database::getInstance()->getconnection();
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


        $sql = "SELECT id, nom, email, statut FROM utilisateur WHERE role='user' ";
        $requete = $this->db->prepare($sql);
        // Bind the parameter using named placeholder
        
        $requete->execute();

    return $requete->fetchAll(PDO::FETCH_ASSOC);
    }




    

    


    public function getInformationsEquipe($utilisateur) {
        try {
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
                        equipe.id_user = :id_user
                    GROUP BY
                        equipe.id;";
    
            $requete = $this->db->prepare($sql);
            $requete->bindParam(":id_user", $utilisateur);
            $requete->execute();
    
            // ... rest of your code ...
    
             
            return $requete->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    



  

    public function CreateTeam()
    {
        // session_start();
        // $userId = $_SESSION['user_id'];

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_team'])) {
            // Récupérer les données du formulaire
            $userId = $_SESSION['utilisateur']['id'];
            $teamName = htmlspecialchars($_POST['team_name']);
            $projectId = intval($_POST['projet']);
            $teamMembers = isset($_POST['membresEquipe']) ? $_POST['membresEquipe'] : [];

            // Appeler la méthode pour créer une équipe
       
            $insertTeamQuery = "INSERT INTO equipe (nom, date_creation, id_user, id_projet) VALUES (?, NOW(), ?, ?)";
            $stmt = $this->db->prepare($insertTeamQuery);
            $stmt->bindParam(1, $teamName, PDO::PARAM_STR);
            $stmt->bindParam(2, $userId, PDO::PARAM_INT);
            $stmt->bindParam(3, $projectId, PDO::PARAM_INT);
    
            if (!$stmt->execute()) {
                // Gérer l'erreur si nécessaire
                return false;
            }
    
            $teamId = $this->db->lastInsertId();
    
            // Insérer les membres dans la table in_team
            $insertMembersQuery = "INSERT INTO membreequipe (id_user, id_equipe) VALUES (?, ?)";
            $stmtMembers = $this->db->prepare($insertMembersQuery);
    
            foreach ($teamMembers as $memberId) {
                $stmtMembers->bindParam(1, $memberId, PDO::PARAM_INT);
                $stmtMembers->bindParam(2, $teamId, PDO::PARAM_INT);
    
                if (!$stmtMembers->execute()) {
                    // Gérer l'erreur si nécessaire
                    return false;
                }
            }
            header("Location: ../scrum/equipe.php");
    
            // L'équipe a été créée avec succès
            // return true;
        }
    }



    


    public function updateTeam()
    {
        // Vérifiez si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST' &&  isset($_POST['team_id']) ) {
            // Récupérez les informations du formulaire POST
            $newTeamName = htmlspecialchars($_POST['team_name']);
            $newProjectId = htmlspecialchars($_POST['projet']);
            $selectedMembers = isset($_POST['membresEquipe']) ? $_POST['membresEquipe'] : [];
            $teamId = isset($_POST['team_id']) ? $_POST['team_id'] : null;
            // Mettre à jour les informations de l'équipe dans le modèle
              // Mettez à jour le nom de l'équipe et le projet dans la table Team
              $updateTeamQuery = "UPDATE equipe SET nom = :newTeamName, id_projet = :newProjectId WHERE id = :teamId";
              $stmtUpdateTeam = $this->db->prepare($updateTeamQuery);
              $stmtUpdateTeam->bindParam(':newTeamName', $newTeamName);
              $stmtUpdateTeam->bindParam(':newProjectId', $newProjectId);
              $stmtUpdateTeam->bindParam(':teamId', $teamId);
              $stmtUpdateTeam->execute();
  
              // Supprimez d'abord tous les membres de l'équipe de la table in_team
              $deleteMembersQuery = "DELETE FROM membreequipe WHERE id_equipe = :teamId";
              $stmtDeleteMembers = $this->db->prepare($deleteMembersQuery);
              $stmtDeleteMembers->bindParam(':teamId', $teamId);
              $stmtDeleteMembers->execute();
  
              // Ensuite, insérez les membres sélectionnés dans la table in_team
              $insertMembersQuery = "INSERT INTO membreequipe (id_user, id_equipe) VALUES (:idMember, :teamId)";
              $stmtInsertMembers = $this->db->prepare($insertMembersQuery);
  
              foreach ($selectedMembers as $idMember) {
                  $stmtInsertMembers->bindParam(':idMember', $idMember);
                  $stmtInsertMembers->bindParam(':teamId', $teamId);
                  $stmtInsertMembers->execute();
              }
  
              

            
                echo "Team details updated successfully!";
                die("yaaaaaaaassssssssssssiiiiiiiiiiiir");
                // header("Location: index.php?action=teams"); // Rediriger vers la page d'accueil ou une autre page après la mise à jour
                exit();
           
        }
          
        
    }


  




    public function DeleteTeam()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["deletebtnteam"])) {
            $teamId = $_POST["team_id"];
            // die($teamId);
            try {
               
                    $sql = "DELETE FROM equipe WHERE id= :id";
                    $stmt = $this->db->prepare($sql);
                    
                    // die($this->idProject);
                    $stmt->bindParam(':id', $teamId, PDO::PARAM_INT);
                    $stmt->execute();
                                
                    $stmt->closeCursor();
               
                $message = "Equipe a été supprime avec succès";
                header("Location: ../scrum/equipe.php");
                exit;
            } catch (Exception $e) {
                $message = "Erreur lors de la suppression de l'equipe. Veuillez réessayer.";
            }
        }
    }



  
}
