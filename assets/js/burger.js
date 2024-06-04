import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

// Menu burger
const menu = document.querySelector('.menu');
const menuMobile = document.getElementById('menuMobile');
const menuItems = document.querySelectorAll('#menuMobile ul li a');

document.querySelector('.menu svg').addEventListener('click', () => {
    menu.classList.toggle('opened');
    menuMobile.classList.toggle('opened');

    if (menu.classList.contains('opened')) {
        menuItems.forEach((item, index) => {
            gsap.fromTo(item, {
                opacity: 0,
                y: 20
            }, {
                opacity: 1,
                y: 0,
                ease: 'power1.out',
                duration: 0.8,
                delay: index * 0.1
            });
        });
    } else {
        menuItems.forEach(item => {
            gsap.to(item, {
                opacity: 0,
                y: 20,
                ease: 'power1.in',
                duration: 0.3
            });
        });
    }
});

// Ajout des classes 'hide' et 'show' sur le header en fonction du scroll
const header = document.querySelector('header');
let lastScrollValue = 0;

document.addEventListener('scroll', () => {
    let top = document.documentElement.scrollTop || document.body.scrollTop || 0;
    if (lastScrollValue < top) {
        header.classList.add('hide');
        header.classList.remove('show');
    } else {
        header.classList.remove('hide');
        header.classList.add('show');
    }
    lastScrollValue = top;
});

// Bouton retour en haut de page
const toTop = document.createElement('div');
toTop.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FF4B44"><path d="m296-345-56-56 240-240 240 240-56 56-184-184-184 184Z"/></svg>`;
document.body.appendChild(toTop);
toTop.classList.add('totop');
toTop.style.cssText = 'position: fixed; bottom: 5px; right: 1%; cursor: pointer; border-radius: 50% 50%;';

toTop.addEventListener('click', () => {
    window.scrollTo({
        top: 0,
        left: 0,
        behavior: 'smooth'
    });
});

const onScroll = () => {
    const scroll = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
    toTop.style.display = scroll < 50 ? 'none' : 'flex';
};

onScroll();
window.addEventListener('scroll', onScroll);