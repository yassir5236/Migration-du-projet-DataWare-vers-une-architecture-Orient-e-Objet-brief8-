<?php
session_start();
include("../config/database.php");
include("../classes/Team.php");



// Create a PDO instance (assuming $conn is your PDO connection)


$userEquipe = new Team();

// Get user projects using the class method

$resultats = $userEquipe->getEquipesScrumByUserId();

// Now you can use $resultat as needed





?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>

    <title>dataware | membre</title>
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
                <h1 class="text-3xl text-center font-bold text-gray-800 mb-6">Membres Management</h1>


                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">

                    <?php
                    foreach ($resultats as $resultat ) {
                        echo "
                            <div class=\"bg-white p-6 rounded-lg shadow-md text-center\">
                    <img class=\"w-28 h-28 mb-4 rounded-full shadow-lg mx-auto\" src=\"../Images/user.png\"
                        alt=\"Bonnie image\" />
                            <h2 class=\"text-xl font-bold mb-2\">{$resultat['nom']}</h2>
                            <p class=\"text-gray-600 mb-2\">{$resultat['email']}</p>
                            <p class=\"text-gray-600 mb-4\">{$resultat['statut']}</p>
                            <button class=\"bg-[#2F329F] text-white py-2 px-4 rounded-md \">
                            Voir le Profil
                        </button>
                        </div>
                            ";
                    }
                    ?>

                </div>



            </div>
        </div>
    </div>




</body>

</html>