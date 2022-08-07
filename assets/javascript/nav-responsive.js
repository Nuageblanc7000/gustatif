const profilDown = document.querySelector('.nav-profile');
const profilButton = profilDown.querySelector('button');
const menuDownProfil = profilDown.querySelector('.menu-down-profil');
const chevron = profilDown.querySelector('i')

const activeMenuProfil = () =>  
{
    menuDownProfil.classList.toggle('active-menu-pop')
    if(chevron.classList.contains('bx-chevron-down'))
    {
        chevron.classList.toggle('bx-chevron-up')
    }else
    {
        chevron.classList.toggle('bx-chevron-down')

    }

    
}
profilButton.addEventListener('click',activeMenuProfil)