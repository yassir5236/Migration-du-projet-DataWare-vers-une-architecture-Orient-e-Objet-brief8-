<?php
session_start();
if (!isset($_SESSION['utilisateur']['id'])) {
    header("Location:../Deconnexion.php ");
}

include("../Connexion.php");
$id_utilisateur = $_SESSION['utilisateur']['id'];

//affichage
$sql = "SELECT  projet.id as id_projet ,
projet.nom as nom_projet, description, projet.date_creation as date_creation, date_limite, projet.statut  as statut
, utilisateur.nom as nom_utilisateur FROM `projet` join utilisateur on utilisateur.id= projet.id_user";
$requete = $conn->prepare($sql);
$requete->execute();
$resultat = $requete->get_result();



//Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_project'])) {

        $projectId = $_POST['projectIdUpdate'];


        $sql = "SELECT * FROM projet WHERE id = ?";
        $requete = $conn->prepare($sql);
        $requete->bind_param("i", $projectId);
        $requete->execute();
        $result = $requete->get_result();


        if ($row = $result->fetch_assoc()) {
            $projectIdUpdate = $row['id'];
            $projectName = $row['nom'];
            $deadline = $row['date_limite'];
            $scrumMaster = $row['id_user'];
            $description = $row['description'];
        } else {
            echo "No project found with ID " . $projectId;
        }
    } else if (isset($_POST['projectId'])) {
        $projectId = $_POST['projectId'];

        $sql = "DELETE FROM MembreEquipe WHERE id_equipe IN (SELECT id FROM equipe WHERE id_projet = ?)";
        $requete = $conn->prepare($sql);
        $requete->bind_param("i", $projectId);
        $requete->execute();

        $sql = "DELETE FROM equipe WHERE id_projet = ?";
        $requete = $conn->prepare($sql);
        $requete->bind_param("i", $projectId);
        $requete->execute();

        $sql = "DELETE FROM projet WHERE id = ?";
        $requete = $conn->prepare($sql);
        $requete->bind_param("i", $projectId);
        $requete->execute();

    } elseif (isset($_POST["projectName"], $_POST["deadline"], $_POST["scrum_master"], $_POST["description"]) || isset($_POST['projectIdUpdate'],$_POST['update_project'],$_POST["projectName"], $_POST["deadline"], $_POST["scrum_master"], $_POST["description"]) ) {

        $projectName = htmlspecialchars($_POST["projectName"]);
        $deadline = htmlspecialchars($_POST["deadline"]);
        $scrumMaster = htmlspecialchars($_POST["scrum_master"]);
        $description = htmlspecialchars($_POST["description"]);


        if (isset($_POST['update'])) {

            $projectId = $_POST['projectIdUpdate'];
            $sql = "UPDATE projet SET nom=?, description=?, date_limite=?, id_user=? WHERE id= ?";
            $requete = $conn->prepare($sql);

            // Vérification de la préparation de la requête
            if (!$requete) {
                die('Erreur de préparation de la requête : ' . $conn->error);
            }

            $requete->bind_param("ssssi", $projectName, $description, $deadline, $scrumMaster, $projectId);

            // Vérification de l'exécution de la requête
            if ($requete->execute()) {

                echo "<script>
                    const projectModal = document.getElementById('projectModal');
                    const projectForm = document.getElementById('projectForm');

                    projectForm.addEventListener('submit', (event) => {
                        // Ajoutez le code pour traiter le formulaire ici
                        event.preventDefault();
                        // Fermez le modal après avoir traité le formulaire si nécessaire
                        projectModal.classList.add('hidden');
                    });
                    </script>";

                echo "Le projet a été modifié avec succès";
            } else {
                echo "Erreur lors de l'exécution de la requête : " . $requete->error;
            }

        } else {

            $sql = "Update utilisateur set role='sm' where id=? ;";
            $requete = $conn->prepare($sql);
            $requete->bind_param("s", $scrumMaster);
            $requete->execute();

            // Préparez et exécutez la requête d'insertion
            $sql = "INSERT INTO projet (nom, description, date_creation, date_limite, statut, id_user) VALUES (?, ?, NOW(), ?,'En cours',?);";
            $requete = $conn->prepare($sql);

            if ($requete) {
                $requete->bind_param("ssss", $projectName, $description, $deadline, $scrumMaster);
                if ($requete->execute()) {
                    echo "Projet ajouté avec succès.";
                    header("Location: Projet.php");
                } else {
                    echo "Erreur lors de l'ajout du projet : " . $requete->error;
                }
                $requete->close();
            } else {
                echo "Erreur de préparation de la requête : " . $conn->error;
            }
        }
    }

}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>
    <script src="../script.js" defer></script>
    <title>dataware | projet</title>
</head>

<body class="bg-[#ECECF8]">



    <div class="  h-screen bg-[#ECECF8]">

        <header class="sticky flex  justify-between top-0 bg-[#2F329F] p-4">
            <a href="Dashboard.php" class="flex items-center text-white">
                <img src="../Images/Logo.png" class="h-8 mx-auto" alt="dataware Logo" />
            </a>
            <!-- Bouton burger visible sur les écrans de petite taille -->

            <div class="flex  justify-between items-center">

                <button id="burgerBtn" class="text-white focus:outline-none sm:hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                </button>
                <!-- Menu de navigation pour la version desktop -->
                <nav class="hidden sm:flex space-x-4">
                    <a href="Dashboard.php" class="text-gray-200 hover:bg-[#5355] transition duration-300">Dashboard</a>
                    <a href="projet.php" class="text-gray-200 hover:bg-[#5355] transition duration-300">Projets</a>
                    <a href="equipe.php" class="text-gray-200 hover:bg-[#5355] transition duration-300">Équipes</a>
                    <a href="membre.php" class="text-gray-200 hover:bg-[#5355] transition duration-300">Membres</a>
                    <a href="../Deconnexion.php"
                        class="text-gray-200 hover:bg-[#5355] transition duration-300">Déconnexion</a>
                </nav>
            </div>
        </header>

        <!-- Menu burger pour la version mobile -->
        <div id="burgerOverlay"
            class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center sm:hidden">
            <nav class="flex flex-col items-center">
                <a href="Dashboard.php"
                    class="text-gray-200 py-2 hover:bg-[#5355] transition duration-300">Dashboard</a>
                <a href="projet.php" class="text-gray-200 py-2 hover:bg-[#5355] transition duration-300">Projets</a>
                <a href="equipe.php" class="text-gray-200 py-2 hover:bg-[#5355] transition duration-300">Équipes</a>
                <a href="membre.php" class="text-gray-200 py-2 hover:bg-[#5355] transition duration-300">Membres</a>
                <a href="../Deconnexion.php"
                    class="text-gray-200 py-2 hover:bg-[#5355] transition duration-300">Déconnexion</a>
            </nav>
        </div>

        <div class="flex-1 flex flex-col h-screen">
            <div class="container mx-auto p-6">
                <h1 class="text-3xl text-center font-bold text-gray-800 mb-6">Project Management</h1>
                <div class="mb-6">
                    <button id="openModal" name="addproject" onclick="addProject()" class="inline-flex items-center text-gray-500 bg-white border border-gray-300
                                    hover:bg-gray-100  font-medium
                                    rounded-lg text-sm px-3 py-1.5 ">
                        Ajouter un Projet
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">Nom du Projet</th>
                                <th scope="col" class="px-6 py-3">Description</th>
                                <th scope="col" class="px-6 py-3">Scrum Master</th>
                                <th scope="col" class="px-6 py-3">Statut</th>
                                <th scope="col" class="px-6 py-3">Date de Création</th>
                                <th scope="col" class="px-6 py-3">Deadline</th>
                                <th scope="col" class="px-6 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            while ($row = $resultat->fetch_assoc()) {
                                echo " <tr data-project-id=\"{$row['id_projet']}\" class=\"bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50
                                    dark:hover:bg-gray-600 \">
                                    <td scope=\"row\" class=\" px-6 py-4 text-gray-900 whitespace-nowrap
                                        dark:text-white\">{$row['nom_projet']}</td>
                                    <td class=\"py-2 px-4 border-b\">{$row['description']}</td>
                                    <td class=\"py-2 px-4 border-b\">{$row['nom_utilisateur']}</td>
                                    <td class=\"py-2 px-4 border-b\">{$row['statut']}</td>

                                    <td class=\"px-6 py-4 border-b\">{$row['date_creation']}</td>
                                    <td class=\"px-6 py-4 border-b\">{$row['date_limite']}</td>
                                    <td class=\"px-6 py-4\">
                                                                    <div class=\"flex gap-6\">
                                                                    <form  method=\"post\" id=\"updateform\">
                                                                    <input   type=\"hidden\" name=\"projectIdUpdate\" value=\"{$row['id_projet']}\">
                                                                        <button  type=\"submit\" name=\"update_project\" class=\"openModal2\" style=\"cursor: pointer;\">
                                                                            <svg xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 24 24\" stroke-width=\"1.5\" stroke=\"currentColor\" class=\"w-6 h-6\">
                                                                                    <path stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10\" />
                                                                            </svg>
                                                                        </button>
                                                                    </form>
                                                                            <div id=\"deleteButton\" style=\"cursor: pointer;\" onclick=\"confirmDelete(event)\">
                                                                            <svg   xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 24 24\"
                                                                            stroke-width=\"1.5\" stroke=\"currentColor\" class=\"w-6 h-6\">
                                                                            <path stroke-linecap=\"round\" stroke-linejoin=\"round\"
                                                                                d=\"M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0\" />
                                                                        </svg>
                                                                        </div>
                                                                        
                                                                </div>
                                        </td>
                                        </tr>
                                        ";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div id="projectModal"
                    class="hidden fixed top-0 left-0 w-full h-full bg-black bg-opacity-50 flex items-center justify-center">


                    <div class="bg-white p-8 rounded shadow-lg w-96">
                        <div class="flex justify-end w-full">

                            <button id="closeModal" type="button"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm h-8 w-8  ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                data-modal-toggle="crypto-modal">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                        <form action="Projet.php" method="post">
                            <input type="text" id="projectIdUpdate" name="projectIdUpdate" value="" hidden>
                            <div class="mb-4">
                                <label for="projectName" class="block text-gray-700 text-sm font-bold mb-2">Nom du
                                    Projet</label>
                                <input type="text" id="projectName" name="projectName"
                                    class="px-4 py-2 w-full border rounded focus:outline-none focus:border-blue-500">
                            </div>

                            <div class="mb-4">
                                <label for="deadline"
                                    class="block text-gray-700 text-sm font-bold mb-2">Deadline</label>
                                <input type="date" id="deadline" name="deadline"
                                    class="px-4 py-2 w-full border rounded focus:outline-none focus:border-blue-500">
                            </div>

                            <div class="mb-4">
                                <label for="scrum_master" class="block text-gray-700 text-sm font-bold mb-2">Scrum
                                    Master</label>
                                <select id="scrum_master" name="scrum_master"
                                    class="px-4 py-2 w-full border rounded focus:outline-none focus:border-blue-500">
                                    <?php

                                    $sql = "SELECT id,email from utilisateur where role='sm' OR role='user';";
                                    $requete = $conn->prepare($sql);
                                    $requete->execute();
                                    $resultat = $requete->get_result();
                                    while ($row = $resultat->fetch_assoc()) {
                                        echo "<option value=\"{$row['id']}\">{$row['email']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="description"
                                    class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                                <textarea id="description" name="description" rows="4"
                                    class="px-4 py-2 w-full border rounded focus:outline-none focus:border-blue-500"></textarea>
                            </div>

                            <button type="submit" id="addProjectButton"
                                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 focus:outline-none focus:shadow-outline-blue">Ajouter
                                Projet</button>
                            <button type="submit" id="UpdateProjectButton"
                                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 focus:outline-none focus:shadow-outline-blue"
                                name="update">Modifier
                                Projet</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>



    <script>


        const projectModal = document.getElementById('projectModal');
        const closeModalButton = document.getElementById('closeModal');
        const UpdateProjectButton = document.querySelector('#UpdateProjectButton');
        const addProjectButton = document.querySelector('#addProjectButton');
        const updateform = document.getElementById('updateform');
        const openModalButton = document.getElementById('openModal');

        UpdateProjectButton.style.display = 'flex';
        addProjectButton.style.display = 'none';

        closeModalButton.addEventListener('click', () => {
            projectModal.classList.add('hidden');
        });

        openModalButton.addEventListener('click', () => {
            projectModal.classList.toggle('hidden');

        });
        // Remplir les champs du formulaire avec les données du projet
        document.getElementById('projectIdUpdate').value = '<?php echo $projectIdUpdate; ?>';

        document.getElementById('projectName').value = '<?php echo $projectName; ?>';
        document.getElementById('deadline').value = '<?php echo $deadline; ?>';
        document.getElementById('scrum_master').value = '<?php echo $scrumMaster; ?>';
        document.getElementById('description').value = '<?php echo $description; ?>';




        document.getElementById('projectModal').classList.remove('hidden');

    </script>

    <?php
    $requete->close();
    $conn->close();
    ?>
</body>

</html>