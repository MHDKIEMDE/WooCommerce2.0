// public/js/search.js

// Attend que le DOM soit prêt
document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('search-form');

    searchForm.addEventListener('submit', function(event) {
        event.preventDefault();

        // Récupérer les valeurs des champs de sélection
        const ville = document.querySelector('[name="ville"]').value;
        const typeMaison = document.querySelector('[name="type_maison"]').value;
        const quartier = document.querySelector('[name="quartier"]').value;
        // Récupérer les autres champs de sélection ici
        // ...

        // Appeler la fonction de recherche via Ajax
        $.ajax({
            type: 'GET',
            url: '/rechercher', // Mettez ici le chemin vers votre route de recherche
            data: {
                ville: ville,
                type_maison: typeMaison,
                quartier: quartier,
                // Autres valeurs de recherche
                // ...
            },
            success: function(data) {
                const searchResults = document.getElementById('search-results');
                searchResults.innerHTML = data;
            }
        });
    });
});