import Swiper from 'swiper/bundle';

// import styles bundle
import 'swiper/css/bundle';
const swiper = new Swiper('.swiper-games', {
    // Optional parameters
    direction: 'horizontal',
    loop: true,
  
  
    // Navigation arrows
    navigation: {
      nextEl: '.arrow_next_games',
      prevEl: '.arrow_prev_games',
    },
    breakpoints: {
        // Lorsque la largeur de l'écran est inférieure à 768px
        767: {
            slidesPerView: 1,
    
        },
        768: {
            slidesPerView: 2,
            spaceBetween: 20
        },
      
        1024: {
            slidesPerView: 3,
            spaceBetween: 30
        }
    
    },
    autoplay:true,
    autoplay: {
        delay: 3000, // Durée entre chaque changement de slide en millisecondes
        disableOnInteraction: true, // Empêche l'autoplay de s'arrêter lors d'une interaction utilisateur (par défaut true)
    }
  
    // And if we need scrollbar
  
  });
  