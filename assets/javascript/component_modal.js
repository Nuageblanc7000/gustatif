
/**
 * MODAL CREATED BY RW 
 * VERSION 1.0
 */
export default class Modal extends HTMLElement{
    static get observedAttributes() {
        return ["data-path","data-pseudo"];
      }
    constructor(){
        super()
       
    }
    connectedCallback() {
        
        this.path = this.dataset.path
        this.pseudo = this.dataset.pseudo
        this.classList.add('modal-admin')
        this.a = document.createElement('a')
        this.a.href=this.path
        this.a.innerHTML='Confirmer'
        this.h = document.createElement("h3")
        this.h.innerHTML =`Voulez vous supprimer? <span style="display:block; text-align:center;">${this.pseudo}</span>` 
        this.close = document.createElement('div')
        this.close.innerHTML='x'
        this.append(this.h,this.close,this.a)
    
        this.setAttribute("style",
        `
            position:fixed;
            z-index:9880;
            max-width:400px;
            width:100%;
            top:50%;
            left:50%;
            border-radius:10px;
            transform:translate(-50%,-50%);
            min-height:200px;
            background-color:var(--white);
            display:flex;
            flex-direction:column;
            align-items:center;
            justify-content:center;
            box-shadow:var(--box-shadow);
        `);
        this.h.setAttribute("style",
        `width:fit-content;`
        )
        this.close.setAttribute("style",`
            position:absolute;
            top:0;
            right:0;
            color:white;
            min-width:25px;
            min-height:25px;
            display:flex;
            justify-content:center;
            align-items:center;
            cursor:pointer;
            background-color:var(--error2);
        `)
        this.a.classList.add('btn')
        this.a.setAttribute('style',`
            max-width:200px;
            width:100%;
        `)
        this.close.addEventListener('click',()=>{
            this.remove()
        })
    }
    attributeChangedCallback(name, oldValue, newValue) {

        
    }
    updateStyle(elem)
    {
        console.log(elem)
    }
    disconnectedCallback() {
        this.remove()
    }
}
  