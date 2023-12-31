<?php
session_start();
include("../config/database.php");
include("../classes/Project.php");



// Create a PDO instance (assuming $conn is your PDO connection)

$userProjects = new Project(null,null,null,null,null);

// Get user projects using the class method
$resultats = $userProjects->getProjects();

// Now you can use $resultat as needed


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>

    <title>dataware | projet</title>
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
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">Nom du Projet</th>
                                <th scope="col" class="px-6 py-3">Description</th>
                                <th scope="col" class="px-6 py-3">Équipe</th>
                                <th scope="col" class="px-6 py-3">Date de Création</th>
                                <th scope="col" class="px-6 py-3">Deadline</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($resultats as $resultat) {

                                echo " 
                                    <tr class=\"bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50
                                    dark:hover:bg-gray-600 \">
                                    <td scope=\"row\" class=\" px-6 py-4 text-gray-900 whitespace-nowrap
                                        dark:text-white\">{$resultat['nom_projet']}</td>
                                    <td class=\"py-2 px-4 border-b\">{$resultat['description']}</td>

                                    <td class=\"py-2 px-4 border-b\">{$resultat['nom_equipe']}</td>


                                    <td class=\"px-6 py-4 border-b\">{$resultat['date_creation']}</td>
                                    <td class=\"px-6 py-4 border-b\">{$resultat['date_limite']}</td>
                             
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