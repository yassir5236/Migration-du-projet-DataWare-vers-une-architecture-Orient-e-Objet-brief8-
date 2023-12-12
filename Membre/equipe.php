<?php

session_start();
if (!isset($_SESSION['utilisateur']['id'])) {
    header("Location:../Deconnexion.php ");
}

include("../Connexion.php");
$id_utilisateur = $_SESSION['utilisateur']['id'];
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
        where membreequipe.id_user=?
    GROUP BY    
        equipe.nom, utilisateur.nom, projet.nom;
    
    ";

$requete = $conn->prepare($sql);
$requete->bind_param("i", $id_utilisateur);

$requete->execute();

$resultat = $requete->get_result();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>
    <script src="../script.js" defer></script>
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

    <!-- Menu burger pour la version mobile -->
    <div id="burgerOverlay"
        class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden items-center justify-center sm:hidden">
        <nav class="flex flex-col items-center">
            <a href="projet.php" class="text-white  py-2 transition duration-300">Projets</a>
            <a href="equipe.php" class="text-white  py-2  transition duration-300">Équipes</a>
            <a href="../Deconnexion.php" class="text-white  py-2  transition duration-300">Déconnexion</a>
        </nav>
    </div>

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
                            <th scope="col" class="px-6 py-3">Membres</th>
                            <th scope="col" class="px-6 py-3">date de création</th>

                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        while ($row = $resultat->fetch_assoc()) {

                            echo " 
                                <tr class=\"bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50
                                dark:hover:bg-gray-600 \">
                                <td scope=\"row\" class=\" px-6 py-4 text-gray-900 whitespace-nowrap
                                    dark:text-white\">{$row['nom_equipe']}</td>
                                <td class=\"py-2 px-4 border-b\">{$row['nom_projet']}</td>
                                <td class=\"px-6 py-4 border-b\">{$row['scrum_master']}</td>
                                <td class=\"px-6 py-4 border-b\">{$row['membres']}</td>
                                <td class=\"px-6 py-4 border-b\">{$row['date_creation']}</td>
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

    <?php
    $requete->close();
    $conn->close();
    ?>


</body>

</html>