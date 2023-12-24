<?php
class Project
{

    private $projectId;
    private $projectName;
    private $deadline;
    private $scrumMaster;
    private $description;
    private $db;

    public function __construct($projectId, $projectName, $deadline, $scrumMaster, $description)
    {
        $this->projectId = $projectId;
        $this->projectName = $projectName;
        $this->deadline = $deadline;
        $this->scrumMaster = $scrumMaster;
        $this->description = $description;
        $this->db = Database::getInstance()->getconnection();
    }

    // Getters
    public function getProjectId()
    {
        return $this->projectId;
    }

    public function getProjectName()
    {
        return $this->projectName;
    }

    public function getDeadline()
    {
        return $this->deadline;
    }

    public function getScrumMaster()
    {
        return $this->scrumMaster;
    }

    public function getDescription()
    {
        return $this->description;
    }

    // Setters
    public function setProjectId($projectId)
    {
        $this->projectId = $projectId;
    }

    public function setProjectName($projectName)
    {
        $this->projectName = $projectName;
    }

    public function setDeadline($deadline)
    {
        $this->deadline = $deadline;
    }

    public function setScrumMaster($scrumMaster)
    {
        $this->scrumMaster = $scrumMaster;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }


    public function getScrumMasters()
    {
        $sql = "SELECT id, email FROM utilisateur";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function requestupdateProject($projectId)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_project'])) {
            $sql = "SELECT  projet.id as id_projet ,
            projet.nom as nom_projet, description, projet.date_creation as date_creation, date_limite, projet.statut  as statut
            , utilisateur.email as email_utilisateur FROM `projet` join utilisateur on utilisateur.id= projet.id_user WHERE projet.id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $projectId);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $this->setProjectId($result['id_projet']);
                $this->setProjectName($result['nom_projet']);
                $this->setDeadline($result['date_limite']);
                $this->setScrumMaster($result['email_utilisateur']);
                $this->setDescription($result['description']);
            } else {
                echo "No project found with ID " . $projectId;
            }
        }

        return $this;

    }
    public function updateProject(Project $project)
    {
        try {
            // Prepare the SQL statement
            $sql = "UPDATE projet 
                    SET nom = :projectName, 
                        date_limite = :deadline, 
                        id_user = :scrumMaster, 
                        description = :description
                    WHERE id = :projectId";

            // Prepare the PDO statement
            $stmt = $this->db->prepare($sql);
            // Get project properties
            $projectId = $project->getProjectId();
            $projectName = $project->getProjectName();
            $deadline = $project->getDeadline();
            $scrumMaster = $project->getScrumMaster();
            $description = $project->getDescription();

            // Bind parameters
            $stmt->bindParam(':projectName', $projectName, PDO::PARAM_STR);
            $stmt->bindParam(':deadline', $deadline, PDO::PARAM_STR);
            $stmt->bindParam(':scrumMaster', $scrumMaster, PDO::PARAM_INT);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->bindParam(':projectId', $projectId, PDO::PARAM_INT);

            // Execute the statement
            $stmt->execute();

            // Check if the update was successful
            if ($stmt->rowCount() > 0) {
                return true;
            }
        } catch (PDOException $e) {
            echo "Error updating project: " . $e->getMessage();
        }
        return false;
    }


    public function deleteProject($projectId)
    {
        $sql = "DELETE FROM projet WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(1, $projectId);

        try {
            $stmt->execute();
            echo "Project deleted successfully!";
        } catch (PDOException $e) {
            echo "Error deleting project: " . $e->getMessage();
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

    public function addProject($project)
    {
        try {
            // Utilisez des paramètres liés pour éviter les injections SQL
            $stmt = $this->db->prepare("INSERT INTO `projet`(`nom`, `description`, `date_limite`, `statut`, `id_user`)VALUES (:projectName,:description, :deadline,'en cours', :scrumMaster)");
            $projectName = $project->getProjectName();
            $deadline = $project->getDeadline();
            $scrumMaster = $project->getScrumMaster();
            $description = $project->getDescription();

            $stmt->bindParam(':projectName', $projectName);
            $stmt->bindParam(':deadline', $deadline);
            $stmt->bindParam(':scrumMaster', $scrumMaster);
            $stmt->bindParam(':description', $description);

            return $stmt->execute();
        } catch (PDOException $e) {
            // Gérez les erreurs ici
            echo "Error: " . $e->getMessage();
            return false;
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





