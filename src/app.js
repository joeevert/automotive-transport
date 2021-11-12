import '@fortawesome/fontawesome-free/js/all.js';
import 'jquery';
import {homepageSwiperInit} from "./js/homepage-swiper";

/* Mobile Menu */
const mobileMenuBtn = document.getElementById('mobile-menu-btn'); //hamburger button
const mobileMenu = document.getElementById('mobile-menu'); //mobile menu container

mobileMenuBtn.addEventListener('click', function () {
    mobileMenuBtn.classList.toggle('is-active');
    mobileMenu.classList.toggle('mobile-menu-active');
    document.body.classList.toggle('no-scroll');
});

const homePage = document.querySelector('body.home');
// const environmentalImpactScene = document.getElementById('environmentalImpactScene');

// if (homePage) {
//     homepageSwiperInit();
// }


// (function( $ ) {
//     'use strict';
//
//     $(function() {
//
//         $(document).ready(function() {
//             console.log('doc ready');
//             let retrieveOrder = localStorage.getItem('order');
//
//             console.log('retrieveOrder: ', JSON.parse(retrieveOrder));
//         })
//     })
// })( jQuery );