import { TweenMax, Power1 } from 'gsap';

document.querySelector('.menu svg').addEventListener('click', () => {
    const menu = document.querySelector('.menu');
    const menuMobile = document.getElementById('menuMobile');
    menu.classList.toggle('opened');
    menuMobile.classList.toggle('opened');
    const menuItems = document.querySelectorAll('#menuMobile ul li a');

    // Si le menu est ouvert
    if (menu.classList.contains('opened')) {
        menuItems.forEach((item, index) => {
            // Animation d'apparition progressive des éléments du menu
            TweenMax.fromTo(item, 0.8, {
                opacity: 0,
                y: 20
            }, {
                opacity: 1,
                y: 0,
                ease: Power1.easeOut,
                delay: index * 0.1 // Réglez le délai entre chaque élément
            });
        });
    } else {
        // Si le menu est fermé
        menuItems.forEach(item => {
            // Animation de disparition des éléments du menu
            TweenMax.to(item, 0.3, {
                opacity: 0,
                y: 20,
                ease: Power1.easeIn
            });
        });
    }
});
