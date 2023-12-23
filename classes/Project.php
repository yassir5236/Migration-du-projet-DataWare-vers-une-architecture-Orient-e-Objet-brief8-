<?php
class Project
{
    private $projectId;
    private $db;
    public function __construct()
    {
        $db = new Database();
        $this->db = $db->getconnection();
    }


    public function updateProject($projectId)
    {
        // die ($projectId ) ;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_project'])) {
            $sql = "SELECT * FROM projet WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $projectId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $projectIdUpdate = $result['id'];
                $projectName = $result['nom'];
                $deadline = $result['date_limite'];
                $scrumMaster = $result['id_user'];
                $description = $result['description'];


            } else {
                echo "No project found with ID " . $projectId;
            }
        }
    }








    public function handleProjectForm()
    {
        if (
            isset($_POST["projectName"], $_POST["deadline"], $_POST["scrum_master"], $_POST["description"]) ||
            isset($_POST['projectIdUpdate'], $_POST['update_project'], $_POST["projectName"], $_POST["deadline"], $_POST["scrum_master"], $_POST["description"])
        ) {

            $projectName = htmlspecialchars($_POST["projectName"]);
            $deadline = htmlspecialchars($_POST["deadline"]);
            $scrumMaster = htmlspecialchars($_POST["scrum_master"]);
            $description = htmlspecialchars($_POST["description"]);

            if (isset($_POST['update'])) {
                $projectId = $_POST['projectIdUpdate'];
                $sql = "UPDATE projet SET nom=?, description=?, date_limite=?, id_user=? WHERE id= ?";
                $stmt = $this->db->prepare($sql);

                if (!$stmt) {
                    die('Erreur de préparation de la requête : ' . $this->db->errorInfo()[2]);
                }

                $stmt->bindParam(1, $projectName, PDO::PARAM_STR);
                $stmt->bindParam(2, $description, PDO::PARAM_STR);
                $stmt->bindParam(3, $deadline, PDO::PARAM_STR);
                $stmt->bindParam(4, $scrumMaster, PDO::PARAM_STR);
                $stmt->bindParam(5, $projectId, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    echo "<script>
                        const projectModal = document.getElementById('projectModal');
                        const projectForm = document.getElementById('projectForm');

                        projectForm.addEventListener('submit', (event) => {
                            event.preventDefault();
                            projectModal.classList.add('hidden');
                        });
                        </script>";

                    echo "Le projet a été modifié avec succès";
                } else {
                    echo "Erreur lors de l'exécution de la requête : " . $stmt->errorInfo()[2];
                }
            } else {
                $sql = "UPDATE utilisateur SET role='sm' WHERE id=?";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(1, $scrumMaster, PDO::PARAM_STR);
                $stmt->execute();

                $sql = "INSERT INTO projet (nom, description, date_creation, date_limite, statut, id_user) VALUES (?, ?, NOW(), 'En cours', ?)";
                $stmt = $this->db->prepare($sql);

                if ($stmt) {
                    $stmt->bindParam(1, $projectName, PDO::PARAM_STR);
                    $stmt->bindParam(2, $description, PDO::PARAM_STR);
                    $stmt->bindParam(3, $deadline, PDO::PARAM_STR);
                    $stmt->bindParam(4, $scrumMaster, PDO::PARAM_STR);

                    if ($stmt->execute()) {
                        echo "Projet ajouté avec succès.";
                        header("Location: Projet.php");
                    } else {
                        echo "Erreur lors de l'ajout du projet : " . $stmt->errorInfo()[2];
                    }
                    $stmt->closeCursor();
                } else {
                    echo "Erreur de préparation de la requête : " . $this->db->errorInfo()[2];
                }
            }
        }
    }












    public function addProject($projectName, $description, $deadline, $scrumMaster)
    {
        $this->db->beginTransaction();

        try {
            $sql = "UPDATE utilisateur SET role='sm' WHERE id=?";
            $requete = $this->db->prepare($sql);
            $requete->execute([$scrumMaster]);

            $sql = "INSERT INTO projet (nom, description, date_creation, date_limite, statut, id_user) VALUES (?, ?, NOW(), ?,'En cours',?)";
            $requete = $this->db->prepare($sql);
            $requete->execute([$projectName, $description, $deadline, $scrumMaster]);

            $this->db->commit();
            echo "Projet ajouté avec succès.";
            header("Location: Projet.php");

            return $requete->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->db->rollBack();
            echo "Error: " . $e->getMessage();
        }
    }




















    //     public function deleteProject($projectId)    // supprimer projet pour le product owner 
// {
//     $this->db->beginTransaction();

    //     try {
//         // Delete from MembreEquipe
//         $sql = "DELETE FROM MembreEquipe WHERE id_equipe IN (SELECT id FROM equipe WHERE id_projet = :projectId)";
//         $requete = $this->db->prepare($sql);
//         $requete->bindParam(':projectId', $projectId, PDO::PARAM_INT);
//         $requete->execute();

    //         // Delete from equipe
//         $sql = "DELETE FROM equipe WHERE id_projet = :projectId";
//         $requete = $this->db->prepare($sql);
//         $requete->bindParam(':projectId', $projectId, PDO::PARAM_INT);
//         $requete->execute();

    //         // Delete from projet
//         $sql = "DELETE FROM projet WHERE id = :projectId";
//         $requete = $this->db->prepare($sql);
//         $requete->bindParam(':projectId', $projectId, PDO::PARAM_INT);
//         $requete->execute();
//         $this->db->commit();

    //         return $requete->fetchAll(PDO::FETCH_ASSOC);



    //     } catch (PDOException $e) {
//         $this->db->rollBack();
//         echo "Error: " . $e->getMessage();
//     }
// }


    public function deleteProject()
    {

         

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_project'])) {
            $this->projectId = $_POST['projectId'];
            // die("HELLO");

            // $this->db->beginTransaction();

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

            } catch (PDOException $e) {
                $this->db->rollBack();
                echo "Error: " . $e->getMessage();
            }
        }
    }








    public function displayprojects()
    {                                  // affichage des projets pour product owner 
        $sql = "SELECT  projet.id as id_projet,
                        projet.nom as nom_projet,
                        description,
                        projet.date_creation as date_creation,
                        date_limite,
                        projet.statut as statut,
                        utilisateur.nom as nom_utilisateur
                FROM `projet` 
                JOIN utilisateur ON utilisateur.id = projet.id_user";

        $requete = $this->db->prepare($sql);
        $requete->execute();
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }







    public function getProjetsByUserId($userId)   // afficher les  projet d'un membre 
    {


        $sql = "
         SELECT
                projet.id AS projet_id,
                projet.nom AS nom_projet,
                projet.description AS description_projet,
                utilisateur.nom AS scrum_master,
                equipe.nom AS nom_equipe,
                projet.date_creation AS date_creation_projet,
                projet.date_limite AS date_limite_projet,
                projet.statut AS statut_projet
         FROM
                projet
                JOIN
                equipe ON projet.id = equipe.id_projet
                JOIN
                MembreEquipe ON equipe.id = MembreEquipe.id_equipe
                JOIN
                utilisateur ON projet.id_user = utilisateur.id
         WHERE
                MembreEquipe.id_user = ?;

                    ";




        $requete = $this->db->prepare($sql);
        $requete->bindParam(1, $userId, PDO::PARAM_INT);
        $requete->execute();

        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }



    public function getProjects()       // afficher les  projet pour scrum
    {
        if (!isset($_SESSION['utilisateur']['id'])) {
            header("Location:../Deconnexion.php ");
        }

        $id_utilisateur = $_SESSION['utilisateur']['id'];

        $sql = "SELECT projet.nom as nom_projet, description, projet.date_creation as date_creation, date_limite, projet.statut as statut, equipe.nom AS nom_equipe 
                FROM projet 
                JOIN utilisateur ON utilisateur.id = projet.id_user 
                LEFT JOIN equipe ON projet.id = equipe.id_projet 
                WHERE projet.id_user=?";

        $requete = $this->db->prepare($sql);
        $requete->bindParam(1, $id_utilisateur, PDO::PARAM_INT);
        $requete->execute();
        

        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }
}





