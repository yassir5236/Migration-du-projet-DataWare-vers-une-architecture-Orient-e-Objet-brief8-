<?php


session_start();
if (!isset($_SESSION['utilisateur']['id'])) {
    header("Location:../Deconnexion.php");
}
$id_utilisateur = $_SESSION['utilisateur']['id'];

include("../config/database.php");
include("../classes/Project.php");

// Initialisation de la base de données



$projetManager = new Project(null, null, null, null, null);              // affichage des des projet pour product owner
$projets = $projetManager->displayprojects();


// Appeler la méthode deleteProject de la classe Project

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addProjectButton'])) {
    // Récupérer les valeurs du formulaire
    $projectName = isset($_POST['projectName']) ? $_POST['projectName'] : '';
    $deadline = isset($_POST['deadline']) ? $_POST['deadline'] : '';
    $scrumMaster = isset($_POST['scrum_master']) ? $_POST['scrum_master'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';

    // Créer un objet Project avec les valeurs du formulaire
    $newProject = new Project(null, $projectName, $deadline, $scrumMaster, $description);

    // Appeler la fonction pour ajouter le projet à la base de données
    if ($projetManager->addProject($newProject)) {
        echo 'Project added successfully!';
    } else {
        echo 'Error adding project.';
    }
}
// Check if the form has been submitted and projectId is set
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_project'])) {
    // Get the project ID from the form submission
    $projectId = isset($_POST['projectId']) ? $_POST['projectId'] : null;

    // Call the updateProject method with the project ID
    $project = $projetManager->requestupdateProject($projectId);

    // Fetch the project data from the Project instance
    $projectIdUpdate = $project->getProjectId();
    $projectName = $project->getProjectName();
    $deadline = $project->getDeadline();
    $description = $project->getDescription(); ?>



    <div id="myModel" class="fixed top-0 left-0 w-full h-full bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-8 rounded shadow-lg w-96">
            <div class="flex justify-end w-full">
                <button id="closeModal" type="button" onclick="toggleModal()"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm h-8 w-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-toggle="crypto-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <form action="Projet.php" method="post">
                <input type="text" id="projectIdUpdate" name="projectIdUpdate" value="<?php echo $projectIdUpdate; ?>"
                    hidden>
                <div class="mb-4">
                    <label for="projectName" class="block text-gray-700 text-sm font-bold mb-2">Nom du
                        Projet</label>
                    <input type="text" id="projectName" name="projectName" value="<?php echo $projectName; ?>"
                        class="px-4 py-2 w-full border rounded focus:outline-none focus:border-blue-500">
                </div>

                <div class="mb-4">
                    <label for="deadline" class="block text-gray-700 text-sm font-bold mb-2">Deadline</label>
                    <input type="date" id="deadline" name="deadline" value="<?php echo $deadline; ?>"
                        class="px-4 py-2 w-full border rounded focus:outline-none focus:border-blue-500">
                </div>

                <div class="mb-4">
                    <label for="scrum_master" class="block text-gray-700 text-sm font-bold mb-2">Scrum
                        Master</label>
                    <select id="scrum_master" name="scrum_master"
                        class="px-4 py-2 w-full border rounded focus:outline-none focus:border-blue-500">
                        <?php
                        // Assuming you have a function to get the list of Scrum Masters
                        $scrumMasters = $projetManager->getScrumMasters(); // Implement this function
                        foreach ($scrumMasters as $scrumMaster) {
                            $selected = ($scrumMaster['email'] === $project->getScrumMaster()) ? 'selected' : '';

                            echo '<option value="' . $scrumMaster['id'] . '" ' . $selected . '>' . $scrumMaster['email'] . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                    <textarea id="description" name="description" rows="4"
                        class="px-4 py-2 w-full border rounded focus:outline-none focus:border-blue-500"><?php echo $description; ?></textarea>
                </div>

                <button type="submit" id="UpdateProjectButton"
                    class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 focus:outline-none focus:shadow-outline-blue"
                    name="UpdateProjectButton">Modifier Projet</button>
            </form>
        </div>
    </div>


    <?php

}




if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['UpdateProjectButton'])) {
    // Get the project ID from the form submission
    $projectId = isset($_POST['projectIdUpdate']) ? $_POST['projectIdUpdate'] : null;

    // Get the updated values from the form
    $updatedProjectName = isset($_POST['projectName']) ? $_POST['projectName'] : '';
    $updatedDeadline = isset($_POST['deadline']) ? $_POST['deadline'] : '';
    $updatedScrumMaster = isset($_POST['scrum_master']) ? $_POST['scrum_master'] : '';
    $updatedDescription = isset($_POST['description']) ? $_POST['description'] : '';

    // Create an instance of the Project class
    $updatedProject = new Project($projectId, $updatedProjectName, $updatedDeadline, $updatedScrumMaster, $updatedDescription);

    // Call the updateProject method with the Project instance
    if ($projetManager->updateProject($updatedProject)) {

        echo 'Project updated successfully!';
    } else {
        echo "erreeuuuuuur";
    }

}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteproject'])) {
    // Get the project ID from the form submission
    $projectId = isset($_POST['projectId']) ? $_POST['projectId'] : null;

    // Log for debugging
    echo "Form submitted for project deletion. Project ID: $projectId";

    // Call the deleteProject method with the project ID
    $project->deleteProject($projectId);
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>
    <script src="../public/script.js" defer></script>
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
                    <button id="openModal" name="addproject" onclick="toggleElement()" class="inline-flex items-center text-gray-500 bg-white border border-gray-300
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
                            foreach ($projets as $projet) {
                                echo " <tr data-project-id=\"{$projet['id_projet']}\" class=\"bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50
                                    dark:hover:bg-gray-600 \">
                                    <td scope=\"row\" class=\" px-6 py-4 text-gray-900 whitespace-nowrap
                                        dark:text-white\">{$projet['nom_projet']}</td>
                                    <td class=\"py-2 px-4 border-b\">{$projet['description']}</td>
                                    <td class=\"py-2 px-4 border-b\">{$projet['nom_utilisateur']}</td>
                                    <td class=\"py-2 px-4 border-b\">{$projet['statut']}</td>

                                    <td class=\"px-6 py-4 border-b\">{$projet['date_creation']}</td>
                                    <td class=\"px-6 py-4 border-b\">{$projet['date_limite']}</td>
                                    <td class=\"px-6 py-4\">
                                                                    <div class=\"flex gap-6\">
                                                                    <form  method=\"post\">
                                                                    <input   type=\"hidden\" name=\"projectId\" value=\"{$projet['id_projet']}\">
                                                                        <button  type=\"submit\" name=\"update_project\" class=\"openModal2\" style=\"cursor: pointer;\">
                                                                            <svg xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 24 24\" stroke-width=\"1.5\" stroke=\"currentColor\" class=\"w-6 h-6\">
                                                                                    <path stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10\" />
                                                                            </svg>
                                                                        </button>
                                                            
                                                                        </form>

                                                                        <form method='post' id='deleteForm'>
                                                                        <input type=\"hidden\" name=\"projectId\" value=\"{$projet['id_projet']}\">
                                                                        <div id=\"deleteButton\" style=\"cursor: pointer;\" onclick=\"confirmDelete(event)\">
                                                                            <svg xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 24 24\" stroke-width=\"1.5\" stroke=\"currentColor\" class=\"w-6 h-6\">
                                                                                <path stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0\" />
                                                                            </svg>
                                                                        </div>
                                                                        <button type='submit' name='deleteproject' hidden></button>

                                                                    </form>
                                                                    
                                                                </div>
                                        </td>
                                        </tr>
                                        ";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div id="myModel2"
                    class="hidden fixed top-0 left-0 w-full h-full bg-black bg-opacity-50 flex items-center justify-center">


                    <div class="bg-white p-8 rounded shadow-lg w-96">
                        <div class="flex justify-end w-full">

                            <button id="closeModal" type="button" onclick="toggleModal()"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm h-8 w-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
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
                                    // Assuming you have a function to get the list of Scrum Masters
                                    $scrumMasters = $projetManager->getScrumMasters(); // Implement this function
                                    foreach ($scrumMasters as $scrumMaster) {

                                        echo '<option value="' . $scrumMaster['id'] . '" >' . $scrumMaster['email'] . '</option>';
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

                            <button type="submit" id="addProjectButton"name="addProjectButton"
                                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 focus:outline-none focus:shadow-outline-blue">Ajouter
                                Projet</button>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>



    <script>
        function confirmDelete(event) {
            console.log('Confirm delete function executed.');
            event.preventDefault();
            var result = confirm('Are you sure you want to delete this project?');

            if (result) {
                // User clicked 'OK', submit the form
                document.getElementById('deleteForm').submit();
            } else {
                // User clicked 'Cancel', do nothing
            }
        }
        function toggleElement() {
            document.getElementById('myModel2').classList.toggle('hidden');
        }
        function toggleModal() {
            var modal = document.getElementById("myModel");
            var modal2 = document.getElementById("myModel2");
            modal2.style.display = "none";

            modal.style.display = "none";
        }
    </script>

    <?php
    
    ?>
</body>

</html>