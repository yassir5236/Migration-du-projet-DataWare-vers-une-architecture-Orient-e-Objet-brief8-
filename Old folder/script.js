    document.getElementById('burgerBtn').addEventListener('click', function () {
        document.getElementById('burgerOverlay').classList.toggle('hidden');
    });

function confirmDelete(event) {
    event.stopPropagation();

    var confirmation = window.confirm('Are you sure you want to delete this project?');

    if (confirmation) {
        var projectId = event.target.closest('tr').dataset.projectId;

        // Modification ici : utilisez FormData pour envoyer les données
        var formData = new FormData();
        formData.append('projectId', projectId);

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'Projet.php', true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                console.log(xhr.responseText);
                window.location.reload(); // Décommentez cette ligne pour recharger la page si nécessaire
            }
        };

        // Modification ici : envoyez formData au lieu de la chaîne de requête
        xhr.send(formData);
    }
}
function confirmDeleteTeam(event) {
    var confirmation = window.confirm('Are you sure you want to delete this team?');

    if (confirmation) {
        // If confirmed, proceed with the deletion
        var equipeId = event.target.closest('tr').dataset.equipeId; // Get the team ID
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'equipe.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // Handle the response from the server
                console.log(xhr.responseText);
                // Optional: Reload or update the team list after deletion
                window.location.reload(); // Uncomment this line to reload the page
            }
        };
        xhr.send('id_equipe=' + equipeId);
    }
}


function addTeam(){
    const openModalButton = document.getElementById('openModal');
const closeModalButton = document.getElementById('closeModal');
const equipeModal = document.getElementById('equipeModal');
// const equipeForm = document.getElementById('equipeForm');
const UpdateTeamButton = document.querySelector('#UpdateTeamButton');
const addTeamButton = document.querySelector('#addTeamButton');

UpdateTeamButton.style.display = 'none';
addTeamButton.style.display = 'flex';

openModalButton.addEventListener('click', () => {
    equipeModal.classList.remove('hidden');

});

closeModalButton.addEventListener('click', () => {
    equipeModal.classList.add('hidden');

});



// equipeForm.addEventListener('submit', (event) => {
//     // Ajoutez le code pour traiter le formulaire ici
//     event.preventDefault();
//     // Fermez le modal après avoir traité le formulaire si nécessaire
//     equipeModal.classList.add('hidden');
// });

}

function addProject(){
    document.getElementById('projectName').value = '';
    document.getElementById('deadline').value = '';
    document.getElementById('scrum_master').value = '';
    document.getElementById('description').value = '';

    document.getElementById('projectModal').classList.remove('hidden');

    const openModalButton = document.getElementById('openModal');
    const projectModal = document.getElementById('projectModal');
    const closeModalButton = document.getElementById('closeModal');
    const projectForm = document.getElementById('projectForm');
    const UpdateProjectButton = document.querySelector('#UpdateProjectButton');
    const addProjectButton = document.querySelector('#addProjectButton');


    UpdateProjectButton.style.display = 'none';
    addProjectButton.style.display = 'flex';


    openModalButton.addEventListener('click', () => {
        projectModal.classList.toggle('hidden');
    
    });
    
    closeModalButton.addEventListener('click', () => {
        projectModal.classList.add('hidden');
    
    });
    
    projectForm.addEventListener('submit', (event) => {
        // Ajoutez le code pour traiter le formulaire ici
        event.preventDefault();
        // Fermez le modal après avoir traité le formulaire si nécessaire
        projectModal.classList.add('hidden');
    });
}

