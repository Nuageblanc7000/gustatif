
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