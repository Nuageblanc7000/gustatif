/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';
import Notyf_flash from './javascript/notyf';   
// start the Stimulus application
import './bootstrap';
import 'tom-select/dist/css/tom-select.default.css';


// menu slider-nav
const menu_slider_nav = document.querySelector('.container-anim-burger')
const menu_slider_responsive = document.querySelector('.navigation-responsive')
const menu_slider_close = document.querySelector('.navigation-close')
menu_slider_nav.addEventListener('click',function(){
    menu_slider_responsive.classList.toggle('navigation-up')
})
menu_slider_close.addEventListener('click',function(){
    menu_slider_responsive.classList.toggle('navigation-up')
})

if(document.querySelector('.header-btn-profil') !== null && document.querySelector('.menu-down-profil') !== null ){

    
    const profilButton = document.querySelector('.header-btn-profil');
    const menuDownProfil = document.querySelector('.menu-down-profil');
    const chevron = profilButton.querySelector('i')
    
    const activeMenuProfil = () =>  
    {
        menuDownProfil.classList.toggle('active-menu-header-profil')
        if(chevron.classList.contains('fa-chevron-down'))
        {
            chevron.classList.toggle('fa-chevron-up')
        }else
        {
            chevron.classList.toggle('fa-chevron-down')
            
        }
        
        
    }
    profilButton.addEventListener('click',activeMenuProfil)
}


if(document.querySelector('flash-message') !== null)
{
  customElements.define('flash-message',Notyf_flash);

}