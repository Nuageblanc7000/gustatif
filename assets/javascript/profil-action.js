/**
 * gestion des actions sur la page profil
 */

const step_profil =  document.querySelectorAll('.js-profil-step')
const nav_profil =  document.querySelectorAll('.js-nav-profil')

/**
 * permet de switcher entre les div
 */
class Action_nav{
  /**
   * 
   * @param {array} elementsNav select nav
   * @param {array} steps  select div
   */
  static HtmlElements(elementsNav,steps){
    elementsNav.forEach((elem,key) => {
      elem.addEventListener('click',(e)=>
      {
        this.action(e,key,steps,elementsNav)
      }
      )})
  }
  static action(e,key,steps,elementsNav){
    e.preventDefault(); 
    console.log(elementsNav)
    elementsNav.forEach(elem =>  {
      console.log(elem)
      if(elem.classList.contains('color-up')){
        elem.classList.remove('color-up')
      }
  }  )
    steps.forEach(step => step.classList.remove('up') )
    steps.item(key).classList.add('up')
    elementsNav.item(key).classList.add('color-up')
  }

}
Action_nav.HtmlElements(nav_profil,step_profil);

