import Modal from "./component_modal";

/**
 * ACTIVATE COMPONENT MODAL 
 */
const lists_button_delete = document.querySelectorAll('.action-btn-admin-delete')
const body = document.querySelector('body')
customElements.define('modal-admin',Modal)

lists_button_delete.forEach(selector =>{
    selector.addEventListener('click',function(e){
        e.preventDefault();
        const modals = document.querySelectorAll('modal-admin')
        modals.forEach(elem => {
            elem.remove()
        });
        let modal = document.createElement('modal-admin')
        modal.dataset.path = this.dataset.path
        modal.dataset.pseudo = this.dataset.pseudo
        body.append(modal)
    })
})

