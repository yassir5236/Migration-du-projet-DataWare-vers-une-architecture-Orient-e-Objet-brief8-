<?php


class User {
    private $database;
    protected $user_id;
    protected $password;
    private $nom;
    private $equipe;
    private $role;
    private $email;
    private $projet_name;
    
    

    public function __construct() {
        $this->database = Database::getInstance()->getconnection();
    }


  

    public function handleSignUp() {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submitSignUp'])) {
            $nom = $_POST['newname'];
            $email = $_POST['newEmail'];

            $requete = $this->database->prepare("SELECT email FROM utilisateur WHERE email = ?");
            $requete->execute([$email]);

            if ($utilisateur = $requete->fetch()) {
                $erreur = "Cet email existe déjà";
            } else {
                $motDePasse = $_POST['newPassword'];
                $confirmationMotDePasse = $_POST['ConfirmPassword'];

                if ($motDePasse !== $confirmationMotDePasse) {
                    echo "Les mots de passe ne correspondent pas.";
                } else {
                    $motDePasseHash = password_hash($motDePasse, PASSWORD_DEFAULT);

                    $requete = $this->database->prepare("INSERT INTO utilisateur (nom, email, password, statut, role) VALUES (?, ?, ?, 'actif', 'user')");
                    if (!$requete) {
                        die("Erreur de préparation de la requête : " . $this->database->errorInfo()[2]);
                    }

                    if ($requete->execute([$nom, $email, $motDePasseHash])) {
                        echo "Inscription réussie !";
                        header("Location: index.php");
                    } else {
                        echo "Erreur lors de l'inscription : " . $requete->errorInfo()[2];
                    }
                }
            }
        }
    }





    public function handleSignIn() {

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submitSignIn'])) {
            $email = $_POST['email'];
            $motDePasse = $_POST['password'];

            $requete = $this->database->prepare("SELECT * FROM utilisateur WHERE email = ?");
            if (!$requete) {
                die("Erreur de préparation de la requête : " . $this->database->errorInfo()[2]);
            }

            $requete->execute([$email]);
            $utilisateur = $requete->fetch(PDO::FETCH_ASSOC);

            if ($utilisateur && password_verify($motDePasse, $utilisateur['password'])) {
                // session_start();
                $_SESSION['utilisateur'] = ['id' => $utilisateur['id']];

                switch ($utilisateur['role']) {
                    case 'user':
                        header("Location: membre/projet.php");
                        exit();
                    case 'po':
                        header("Location: owner/equipe.php");
                        exit();
                    case 'sm':
                        header("Location: scrum/projet.php");
                        exit();
                }
            } else {
                echo "Mot de passe incorrect.";
            }
        }
    }

  

    public function consulterProjet() {
       
        
    }

    public function consulterEquipe() {
        
        
    }
}
