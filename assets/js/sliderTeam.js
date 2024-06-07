import Swiper from 'swiper/bundle';

// import styles bundle
import 'swiper/css/bundle';
const swiper = new Swiper('.swiper', {
    // Optional parameters
    direction: 'horizontal',
    loop: true,
    speed:700,
  
  
    // Navigation arrows
    navigation: {
      nextEl: '.arrow_next',
      prevEl: '.arrow_prev',
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

 
  });
  