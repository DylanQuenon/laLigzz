

// Fonction pour basculer la classe 'active' sur la carte
const toggleCardDetail = (event) => {
    const card = event.currentTarget;
    card.classList.toggle('active');
    console.log('click');
};

// Récupérer toutes les cartes
const cards = document.querySelectorAll('.cardNews');

// Ajouter un écouteur d'événement de clic à chaque carte
cards.forEach(card => {
    card.addEventListener('click', toggleCardDetail);
});


