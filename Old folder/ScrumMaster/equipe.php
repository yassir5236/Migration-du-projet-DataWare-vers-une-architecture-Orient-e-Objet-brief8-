<?php
session_start();
if (!isset($_SESSION['utilisateur']['id'])) {
    header("Location:../Deconnexion.php ");
}

include("../Connexion.php");
$utilisateur = $_SESSION['utilisateur']['id'];
$sql = "SELECT 
equipe.id AS id_equipe,
equipe.nom AS nom_equipe,
equipe.date_creation AS date_creation,
projet.nom AS nom_projet,
GROUP_CONCAT(DISTINCT membre.nom SEPARATOR ', ') AS membres
FROM
equipe
JOIN projet ON projet.id = equipe.id_projet
LEFT JOIN MembreEquipe AS membre_equipe ON equipe.id = membre_equipe.id_equipe
LEFT JOIN utilisateur AS membre ON membre_equipe.id_user = membre.id
WHERE
equipe.id_user = ?
GROUP BY
equipe.id;";
$requete = $conn->prepare($sql);
$requete->bind_param("i", $utilisateur);
$requete->execute();
$resultat = $requete->get_result();



if ($_SERVER["REQUEST_METHOD"] == "POST") {


    //delete team
    if (isset($_POST['id_equipe'])) {
        $id_equipe = $_POST['id_equipe'];

        $sqlDeleteMembers = "DELETE FROM MembreEquipe WHERE id_equipe =?";
        $stmtDeleteMembers = $conn->prepare($sqlDeleteMembers);
        $stmtDeleteMembers->bind_param("i", $id_equipe);
        $stmtDeleteMembers->execute();

        $sqlDeleteTeams = "DELETE FROM equipe WHERE id = ?";
        $sqlDeleteTeams = $conn->prepare($sqlDeleteTeams);
        $sqlDeleteTeams->bind_param("i", $id_equipe);
        $sqlDeleteTeams->execute();
    }


    //updattee team 

    if (isset($_POST['update_team'])) {
        $equipeId = $_POST['TeamUpdate'];

        // Select team information from the equipe table
        $sql = "SELECT * FROM equipe WHERE id = ?";
        $requete = $conn->prepare($sql);
        $requete->bind_param("i", $equipeId);
        $requete->execute();
        $result = $requete->get_result();

        if ($row = $result->fetch_assoc()) {
            $teamIdUpdate = $row['id'];
            $equipeName = $row['nom'];
            $projet = $row['id_projet'];

            // Fetch team members from the MembreEquipe table
            $sqlMembers = "SELECT id_user FROM MembreEquipe WHERE id_equipe = ?";
            $requeteMembers = $conn->prepare($sqlMembers);
            $requeteMembers->bind_param("i", $equipeId);
            $requeteMembers->execute();
            $resultMembers = $requeteMembers->get_result();

            // Store team member IDs in an array
            $membresEquipe = [];
            while ($rowMember = $resultMembers->fetch_assoc()) {
                $membresEquipe[] = $rowMember['id_user'];
            }

        } else {
            echo "No team found with ID " . $equipeId;
        }
    }
    //add team 
    if (isset($_POST["nomEquipe"], $_POST["projet"], $_POST["membresEquipe"]) || isset($_POST["nomEquipe"], $_POST["projet"], $_POST["membresEquipe"], $_POST['update_team'])) {

        if (isset($_POST["updateeq"])) {
            $equipeId = $_POST['teamIdUpdate'];
            // Retrieve updated values from the form
            $nomEquipe = htmlspecialchars($_POST["nomEquipe"]);
            $idProjet = htmlspecialchars($_POST["projet"]);
            $membresEquipe = $_POST["membresEquipe"];

            // Update team information in the equipe table
            $sqlUpdateEquipe = "UPDATE equipe SET nom=?, id_projet=? WHERE id=?";
            $requeteUpdateEquipe = $conn->prepare($sqlUpdateEquipe);
            $requeteUpdateEquipe->bind_param("sii", $nomEquipe, $idProjet, $equipeId);
            $requeteUpdateEquipe->execute();

            // Delete existing team members from MembreEquipe table
            $sqlDeleteMembers = "DELETE FROM MembreEquipe WHERE id_equipe = ?";
            $stmtDeleteMembers = $conn->prepare($sqlDeleteMembers);
            $stmtDeleteMembers->bind_param("i", $equipeId);
            $stmtDeleteMembers->execute();

            // Insert updated team members into MembreEquipe table
            $sqlInsertMembres = "INSERT INTO MembreEquipe (id_user, id_equipe) VALUES (?, ?)";
            $requeteInsertMembres = $conn->prepare($sqlInsertMembres);

            foreach ($membresEquipe as $idMembre) {
                $requeteInsertMembres->bind_param("ii", $idMembre, $equipeId);
                $requeteInsertMembres->execute();
            }

            echo "Équipe mise à jour avec succès.";
            // Vérification de l'exécution de la requête
            if ($requete->execute()) {

                echo "<script>
                    const equipeModal = document.getElementById('equipeModal');
                    const equipeForm = document.getElementById('equipeForm');

                    equipeForm.addEventListener('submit', (event) => {
                        // Ajoutez le code pour traiter le formulaire ici
                        event.preventDefault();
                        // Fermez le modal après avoir traité le formulaire si nécessaire
                        equipeModal.classList.add('hidden');
                    });
                    </script>";
                    header("Location:equipe.php");

            } else {
                echo "Erreur lors de l'exécution de la requête : " . $requete->error;
            }
        } else {
            $nomEquipe = htmlspecialchars($_POST["nomEquipe"]);
            $idProjet = htmlspecialchars($_POST["projet"]);
            $membresEquipe = $_POST["membresEquipe"];

            $sqlInsertEquipe = "INSERT INTO equipe (nom, id_user, id_projet) VALUES (?,? , ?)";
            $requeteInsertEquipe = $conn->prepare($sqlInsertEquipe);
            $requeteInsertEquipe->bind_param("sii", $nomEquipe, $utilisateur, $idProjet);

            if ($requeteInsertEquipe->execute()) {
                $idEquipe = $requeteInsertEquipe->insert_id;

                $sqlInsertMembres = "INSERT INTO MembreEquipe (id_user, id_equipe) VALUES (?, ?)";
                $requeteInsertMembres = $conn->prepare($sqlInsertMembres);

                foreach ($membresEquipe as $idMembre) {
                    $requeteInsertMembres->bind_param("ii", $idMembre, $idEquipe);
                    $requeteInsertMembres->execute();
                }

                echo "Équipe ajoutée avec succès.";
                header("Location: equipe.php");
            } else {
                echo "Erreur lors de l'ajout de l'équipe : " . $requeteInsertEquipe->error;
            }

            $requeteInsertEquipe->close();
            $requeteInsertMembres->close();
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
            <div class="mb-6">
                <button id="openModal" onclick="addTeam()" class="inline-flex items-center text-gray-500 bg-white border border-gray-300
                                hover:bg-gray-100  font-medium
                                rounded-lg text-sm px-3 py-1.5 ">
                    Ajouter une équipe
                </button>
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
                        while ($row = $resultat->fetch_assoc()) {
                            echo " 
                                    <tr data-equipe-id=\"{$row['id_equipe']}\" class=\"bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50
                                    dark:hover:bg-gray-600 \">
                                    <td scope=\"row\" class=\" px-6 py-4 text-gray-900 whitespace-nowrap
                                        dark:text-white\">{$row['nom_equipe']}</td>
                                    <td class=\"py-2 px-4 border-b\">{$row['nom_projet']}</td>
                                    <td class=\"px-6 py-4 border-b\">{$row['membres']}</td>
                                    <td class=\"px-6 py-4 border-b\">{$row['date_creation']}</td>
                                    <td class=\"px-6 py-4\">
                                    <div class=\" flex gap-6\">                                       
                                    <form  method=\"POST\" id=\"updateform\">
                                    <input   type=\"hidden\" name=\"TeamUpdate\" value=\"{$row['id_equipe']}\">
                                        <button  type=\"submit\" name=\"update_team\" class=\"openModal\" style=\"cursor: pointer;\">
                                            <svg xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 24 24\" stroke-width=\"1.5\" stroke=\"currentColor\" class=\"w-6 h-6\">
                                                    <path stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10\" />
                                            </svg>
                                        </button>
                                    </form>
                                    <div id=\"deleteButton\" style=\"cursor: pointer;\" onclick=\"confirmDeleteTeam(event)\">
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
        </div>
    </div>
    </div>



    <div id="equipeModal"
        class="hidden fixed top-0 left-0 w-full h-full bg-black bg-opacity-50 flex items-center justify-center">


        <div class="bg-white p-8 rounded shadow-lg w-96">
            <div class="flex justify-end w-full">

                <button id="closeModal" type="button"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm h-8 w-8  ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-toggle="crypto-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <form action="equipe.php" method="post" name="equipeForm" class="max-w-md mx-auto ">
                <input type="text" id="teamIdUpdate" name="teamIdUpdate" value="" hidden>

                <div class="mb-4">
                    <label for="nomEquipe" class="block text-gray-700 text-sm font-bold mb-2">Nom de l'Équipe</label>
                    <input type="text" id="nomEquipe" name="nomEquipe" placeholder="Entrez le nom de l'équipe"
                        class="w-full px-4 py-2 border rounded focus:outline-none focus:border-blue-500">
                </div>

                <div class="mb-4">
                    <label for="projet" class="block text-gray-700 text-sm font-bold mb-2">Projet de l'Équipe</label>
                    <select id="projet" name="projet"
                        class="w-full px-4 py-2 border rounded focus:outline-none focus:border-blue-500">
                        <?php
                        $sqlProjet = "SELECT id, nom FROM projet ";
                        $requeteProjet = $conn->prepare($sqlProjet);
                        $requeteProjet->execute();
                        $resultatProjet = $requeteProjet->get_result();
                        while ($rowProjet = $resultatProjet->fetch_assoc()) {
                            echo "<option value=\"{$rowProjet['id']}\">{$rowProjet['nom']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="membresEquipe" class="block text-gray-700 text-sm font-bold mb-2">Membres de
                        l'Équipe</label>
                    <select id="membresEquipe" name="membresEquipe[]" multiple class="w-full px-1 py-2 border rounded">
                        <?php
                        $sqlMembres = "SELECT id, email FROM utilisateur WHERE role='user'";
                        $requeteMembres = $conn->prepare($sqlMembres);
                        $requeteMembres->execute();
                        $resultatMembres = $requeteMembres->get_result();
                        while ($rowMembre = $resultatMembres->fetch_assoc()) {
                            echo "<option value=\"{$rowMembre['id']}\">{$rowMembre['email']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" id="addTeamButton"
                    class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 focus:outline-none focus:shadow-outline-blue">Ajouter
                    equipe</button>
                <button type="submit" id="UpdateTeamButton" name="updateeq"
                    class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 focus:outline-none focus:shadow-outline-blue">Modifier
                    equipe</button>
            </form>

        </div>
    </div>



    <script>


        const equipeModal = document.getElementById('equipeModal');
        const closeModalButton = document.getElementById('closeModal');
        const UpdateTeamButton = document.querySelector('#UpdateTeamButton');
        const addTeamButton = document.querySelector('#addTeamButton');
        const updateform = document.getElementById('updateform');
        const openModalButton = document.getElementById('openModal');

        UpdateTeamButton.style.display = 'flex';
        addTeamButton.style.display = 'none';

        closeModalButton.addEventListener('click', () => {
            equipeModal.classList.add('hidden');
        });

        openModalButton.addEventListener('click', () => {
            // Add logic to fill in form values with obtained information
            // Assuming you have PHP variables $equipeName, $projet, and $membresEquipe


            equipeModal.classList.toggle('hidden');
        });
        // Remplir les champs du formulaire avec les données du projet
        document.getElementById('teamIdUpdate').value = '<?php echo $teamIdUpdate; ?>';
        document.getElementById('nomEquipe').value = '<?php echo $equipeName; ?>';
        document.getElementById('projet').value = '<?php echo $projet; ?>';

        // Convert PHP array $membresEquipe to a JavaScript array
        const membresEquipe = <?php echo json_encode($membresEquipe); ?>;
        const selectMembresEquipe = document.getElementById('membresEquipe');

        // Set selected property based on the obtained values
        for (let i = 0; i < selectMembresEquipe.options.length; i++) {
            if (membresEquipe.includes(parseInt(selectMembresEquipe.options[i].value))) {
                selectMembresEquipe.options[i].selected = true;
            }
        }
        document.getElementById('equipeModal').classList.remove('hidden');

    </script>
    <?php
    $requete->close();
    $conn->close();
    ?>
</body>

</html>