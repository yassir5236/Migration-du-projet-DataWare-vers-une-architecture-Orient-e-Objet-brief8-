<?php
session_start();
if (!isset($_SESSION['utilisateur']['id'])) {
    header("Location:../Deconnexion.php ");
}

include("../config/database.php");
include("../classes/Team.php");
include("../classes/Project.php");

// Create a PDO instance (assuming $conn is your PDO connection)

$utilisateur = $_SESSION['utilisateur']['id'];
$EquipeScrums = new Team();
$resultats = $EquipeScrums->getInformationsEquipe($utilisateur);

$projectScrum = new Project(null, null, null, null, null);
$projects = $projectScrum->getProjects();

$MembersN = new Team();
$members = $MembersN->getEquipesScrumByUserId();

$createquipeee = new Team();
$createquipeee->CreateTeam();

$deleteEquipe = new Team();
$deleteEquipe->DeleteTeam();

$UpdateEquipe = new Team();
$UpdateEquipe->updateTeam();












?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>
    <!-- <script src="../public/script.js" defer></script> -->

    <script defer>
        document.addEventListener("DOMContentLoaded", function () {

            document.getElementById('openModal').addEventListener('click', function () {
                document.getElementById('createForm').classList.toggle('hidden');
            });
        });

        function confirmDelete() {
            var result = confirm("Êtes-vous sûr de vouloir supprimer cet equipe?");

            if (result) {
                document.getElementById("deleteteamForm").submit();
            }
            else {
                event.preventDefault();
            }
        }

        document.querySelectorAll('.editProjectButton').forEach(button => {
            button.addEventListener('click', function () {
                showEditProjectForm(button);
            });
        });


        function showEditProjectForm(button) {
    var editProjectForm = document.getElementById('editProjectForm');

    // Utilisez la classe hidden pour afficher/cacher le formulaire
    editProjectForm.classList.toggle('hidden');

    if (!editProjectForm.classList.contains('hidden')) {
        // Remplir les champs du formulaire avec les données de l'équipe
        editProjectForm.querySelector('#team_id').value = button.getAttribute('data-equipe-id');
        editProjectForm.querySelector('#team_name').value = button.getAttribute('data-equipe-name');

        // Assurez-vous que le nom de l'attribut est correct ici
        var equipeProjet = button.getAttribute('data-equipe-projet');
        editProjectForm.querySelector('#projet').value = equipeProjet;

        // Remplir les membres et sélectionner les bonnes options dans le menu déroulant
        var membresSelect = editProjectForm.querySelector('#membresEquipe');
        var equipeMembres = button.getAttribute('data-equipe-membres');
        var membresArray = equipeMembres.split(',');

        for (var i = 0; i < membresSelect.options.length; i++) {
            membresSelect.options[i].selected = membresArray.includes(membresSelect.options[i].value);
        }

        // Sélectionnez le projet dans le menu déroulant
        var projetSelect = editProjectForm.querySelector('#projet');
        for (var i = 0; i < projetSelect.options.length; i++) {
            if (projetSelect.options[i].value === equipeProjet) {
                projetSelect.options[i].selected = true;
                break;
            }
        }
    }
}





    </script>

    <title>dataware | equipe</title>
</head>

<body class="bg-[#ECECF8]">


    <header class="sticky flex  justify-between top-0 bg-[#2F329F] p-4">
        <a href="Dashboard.php" class="flex items-center text-white">
            <img src="../Images/Logo.png" class="h-8 mx-auto" alt="dataware Logo" />
        </a>
        <!-- Bouton burger visible sur les écrans de petite taille -->

        <div class="flex  justify-between items-center">

            <button id="burgerBtn" class="text-white focus:outline-none sm:hidden">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7">
                    </path>
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
            <a href="Dashboard.php" class="text-gray-200 py-2 hover:bg-[#5355] transition duration-300">Dashboard</a>
            <a href="projet.php" class="text-gray-200 py-2 hover:bg-[#5355] transition duration-300">Projets</a>
            <a href="equipe.php" class="text-gray-200 py-2 hover:bg-[#5355] transition duration-300">Équipes</a>
            <a href="membre.php" class="text-gray-200 py-2 hover:bg-[#5355] transition duration-300">Membres</a>
            <a href="../Deconnexion.php"
                class="text-gray-200 py-2 hover:bg-[#5355] transition duration-300">Déconnexion</a>
        </nav>
    </div>

    <div class="flex-1 flex flex-col h-screen">
        <div class="container mx-auto p-6">

            <h1 class="text-3xl text-center font-bold text-gray-800 mb-6">teams Management</h1>
            <div id="editProjectForm" class="hidden mx-auto mt-3 bg-white p-8 rounded w-96 shadow-md max-w-md rounded-2xl">
        <h2 class="text-2xl text-center mb-6">Update Team</h2>

        <form method="POST" action="" id="editProjectForm" class="space-y-4">
            <input hidden type="text" id="team_id" name="team_id" value="">

            <div>
                <label for="team_name" class="block text-sm font-medium text-gray-700">Team Name:</label>
                <input type="text" id="team_name" name="team_name"
                    class="mt-1 p-2 w-full border rounded-md focus:outline-none focus:border-blue-500" value=""
                    required>
            </div>

            <div class="mb-4">
                <label for="projet" class="block text-gray-700 text-sm font-bold mb-2">Project for the Team:</label>
                <select id="projet" name="projet"
                    class="w-full px-4 py-2 border rounded focus:outline-none focus:border-blue-500">
                    <?php
                    foreach ($projects as $project) {
                        // $selected = ($project['Id_Project'] == $teamInfo['Id_Project']) ? 'selected' : '';
                        // echo "<option value=\"{$project['Id_Project']}\" $selected>{$project['project_name']}</option>";
                    
                        echo "<option value='{$project["id_projet"]}'>{$project["nom_projet"]}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="mb-4">
                <label for="membresEquipe" class="block text-gray-700 text-sm font-bold mb-2">Team Members:</label>
                <select id="membresEquipe" name="membresEquipe[]" multiple class="w-full px-1 py-2 border rounded">
                    <?php
                    foreach ($members as $member) {
                        // $selected = in_array($member['email'], explode(',', $teamInfo['team_members'])) ? 'selected' : '';
                        // echo "<option value=\"{$member['id_user']}\" $selected>{$member['email']}</option>";
                        echo "<option value='{$member["id"]}'>{$member["email"]}</option>";
                    }
                    ?>
                </select>
            </div>

            <button type="submit" name="update_team"
                class="w-full text-white bg-red-700 hover:bg-yellow-500 focus:outline-none focus:ring-4 focus:ring-yellow-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:focus:ring-yellow-900">Update
                Team</button>
        </form>
    </div>

            <div class="mb-6">
                <button id="openModal" class="inline-flex items-center text-gray-500 bg-white border border-gray-300
                            hover:bg-gray-100 font-medium rounded-lg text-sm px-3 py-1.5">
                    Ajouter une équipe
                </button>
            </div>


            <div id="createForm" class="hidden mx-auto mt-3 bg-white p-8 rounded w-96 shadow-md max-w-md rounded-2xl">
                <h2 class="text-2xl text-center mb-6">Create Team</h2>
                <form action="" method="POST" class="space-y-4">
                    <div>
                        <label for="team_name" class="block text-sm font-medium text-gray-700">Team Name:</label>
                        <input type="text" id="team_name" name="team_name"
                            class="mt-1 p-2 w-full border rounded-md focus:outline-none focus:border-blue-500" required>
                    </div>
                    <div class="mb-4">
                        <label for="projet" class="block text-gray-700 text-sm font-bold mb-2">Project for the
                            Team:</label>
                        <select id="projet" name="projet"
                            class="w-full px-4 py-2 border rounded focus:outline-none focus:border-blue-500">
                            <?php
                            foreach ($projects as $project) {
                                echo "<option value='{$project["id_projet"]}'>{$project["nom_projet"]}</option>";

                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="membresEquipe" class="block text-gray-700 text-sm font-bold mb-2">Team
                            Members:</label>
                        <select id="membresEquipe" name="membresEquipe[]" multiple
                            class="w-full px-1 py-2 border rounded">
                            <?php
                            foreach ($members as $member) {
                                echo "<option value='{$member["id"]}'>{$member["email"]}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" name="create_team"
                        class="w-full text-white bg-red-700 hover:bg-yellow-500 focus:outline-none focus:ring-4 focus:ring-yellow-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:focus:ring-yellow-900">Create
                        Team</button>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Nom d'équipes</th>
                            <th scope="col" class="px-6 py-3">Projet</th>

                            <th scope="col" class="px-6 py-3">Membres</th>
                            <th scope="col" class="px-6 py-3">date de création</th>

                            <th scope="col" class="px-6 py-3">Actions</th>

                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        foreach ($resultats as $resultat) {
                            echo " 
                                    <tr data-equipe-id=\"{$resultat['id_equipe']}\" class=\"bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50
                                    dark:hover:bg-gray-600 \">
                                    <td scope=\"row\" class=\" px-6 py-4 text-gray-900 whitespace-nowrap
                                        dark:text-white\">{$resultat['nom_equipe']}</td>
                                    <td class=\"py-2 px-4 border-b\">{$resultat['nom_projet']}</td>
                                    <td class=\"px-6 py-4 border-b\">{$resultat['membres']}</td>
                                    <td class=\"px-6 py-4 border-b\">{$resultat['date_creation']}</td>
                                    <td class=\"px-6 py-4\">
                                    <div class=\" flex gap-6\"> 
                                    
                                    
                                
                                    <button type='button' class='editProjectButton text-gray-900' onclick='showEditProjectForm(this)'
                                    data-equipe-id=' {$resultat['id_equipe']}'                        
                                        data-equipe-name='{$resultat['nom_equipe']}'
                                        data-equipe-membres='<?php echo {$resultat['membres']}; ?>'

                                        
                                        data-equipe-projet='<?php echo {$resultat['nom_projet']}; ?>'>
                                        <svg width='24' height='24' viewBox='0 0 24 24' fill='none'
                                        xmlns='http://www.w3.org/2000/svg'>
                                        <path
                                            d='M3 17V21H7L17.59 10.41L13.17 6L3 16.17V17ZM21.41 5.59L18.83 3L20.41 1.41C20.59 1.23 20.8 1.09 21 1.03C21.2 0.97 21.41 0.99 21.59 1.07L23.59 3.07C23.77 3.15 23.91 3.36 23.97 3.57C24.03 3.78 24.01 3.99 23.93 4.17L22.34 6.76L21.41 5.59Z'
                                            fill='white'/>
                                      </svg>
                                    </button>



                                
                                 
                                    <form id='deleteteamForm' action='' method='POST'>
                                    <input  type='hidden' name='team_id' value=\"{$resultat['id_equipe']}\">
                                    <button type=\"submit\" name='deletebtnteam' id=\"deleteButton\" style=\"cursor: pointer;\" onclick=\"confirmDelete()\">
                                    <svg   xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 24 24\"
                                    stroke-width=\"1.5\" stroke=\"currentColor\" class=\"w-6 h-6\">
                                    <path stroke-linecap=\"round\" stroke-linejoin=\"round\"
                                        d=\"M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0\" />
                                    </svg>
                                    </button>
                                    
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
        </div>
    </div>
    </div>







    









</body>

</html>