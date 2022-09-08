/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';

// start the Stimulus application
import './bootstrap';
import 'tom-select/dist/css/tom-select.default.css';





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
