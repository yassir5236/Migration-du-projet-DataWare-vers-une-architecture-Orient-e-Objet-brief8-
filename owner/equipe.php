<?php
session_start();
if (!isset($_SESSION['utilisateur']['id'])) {
    header("Location:../Deconnexion.php ");
}






include("../config/database.php");
include("../classes/Team.php");


// Initialisation de la classe Equipe
$equipeManager = new Team();

$equipes = $equipeManager->getAllEquipes();


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>

    <title>dataware | equipe</title>
</head>

<body class="bg-[#ECECF8]">

   

    <header class="sticky flex  justify-between top-0 bg-[#7393B3] p-4">
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
                <a href="projet.php" class="text-white  py-2 transition duration-300">Projets</a>
                <a href="equipe.php" class="text-white  py-2  transition duration-300">Équipes</a>
                <a href="../Deconnexion.php" class="text-white  py-2  transition duration-300">Déconnexion</a>
            </nav>
        </div>
    </header>

    <div class="flex-1 flex flex-col h-screen">
        <div class="container mx-auto p-6">
            <h1 class="text-3xl text-center font-bold text-gray-800 mb-6">teams Management</h1>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Nom d'équipes</th>
                            <th scope="col" class="px-6 py-3">Projet</th>
                            <th scope="col" class="px-6 py-3">Scrum Master</th>
                            <th scope="col" class="px-6 py-3">date de création</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($equipes as $equipe) {
                            echo "
                                <tr class=\"bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 \">
                                    <td scope=\"row\" class=\" px-6 py-4 text-gray-900 whitespace-nowrap dark:text-white\">{$equipe['nom_equipe']}</td>
                                    <td class=\"py-2 px-4 border-b\">{$equipe['nom_projet']}</td>
                                    <td class=\"px-6 py-4 border-b\">{$equipe['scrum_master']}</td>
                                    <td class=\"px-6 py-4 border-b\">{$equipe['date_creation']}</td>
                                </tr>
                            ";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php
    $requete->close();
    ?>

</body>

</html>
