//menu-lateral filtre restaurant

const lateral = document.querySelector('.lateral')
const btnFilter = document.querySelector('.btn-filter-responsive')

const backLateral = document.querySelector('.back')
const crossLateral = document.querySelector('.cross')

const lateralOpen =()=>{
    lateral.classList.toggle('active-responive')
    backLateral.classList.toggle('back-lateral')
}
crossLateral.addEventListener('click',lateralOpen )
btnFilter.addEventListener('click',lateralOpen )
backLateral.addEventListener('click',lateralOpen )