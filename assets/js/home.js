import { TweenMax, Power1 } from 'gsap';

//menu burger

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
                delay: index * 0.1 
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

//class hide et active sur le header pour le scroll
const header=document.querySelector('header')
let lastScrollValue = 0;

document.addEventListener('scroll',() => {
    let top  = document.documentElement.scrollTop;
if(lastScrollValue < top) {
    header.classList.add("hide");
    header.classList.remove("show");
} else {
    header.classList.remove("hide");
    header.classList.add("show");
}
lastScrollValue = top;
});

const toTop = document.createElement('div');
toTop.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#FF4B44"><path d="m296-345-56-56 240-240 240 240-56 56-184-184-184 184Z"/></svg>`;
document.body.appendChild(toTop);
toTop.classList.add('totop');
toTop.style.cssText = "position: fixed; bottom: 5px; right: 1%; cursor: pointer; border-radius: 50% 50%;";

toTop.addEventListener('click', () => {
window.scrollTo({
    top: 0,
    left: 0,
    behavior: "smooth",
});
});

const onScroll = () => {
const scroll = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
toTop.style.display = scroll < 50 ? "none" : "flex";
};

onScroll();
window.addEventListener('scroll', onScroll);