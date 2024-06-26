import './bootstrap.js';
import AOS from 'aos';
import 'aos/dist/aos.css';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.scss';
import './styles/bootstrap.min.css';
import './styles/pages/home.scss';
import './styles/pages/teams.scss';
import './styles/pages/_sliderTeam.scss';
// import './styles/partials/_cardNews.scss';
import './styles/partials/header.scss';
import './styles/partials/footer.scss';
import './styles/partials/_cardTeam.scss';
import 'bootstrap/dist/js/bootstrap.bundle';
// import './js/sliderTeam.js'
import './js/home.js'
import './js/_sliderNews.js'

import 'gsap'

AOS.init();


console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
