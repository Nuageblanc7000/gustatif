
/**
 * MANAGE COLLECTION IMAGE (ADD AND REMOVE) + LIMIT
 * VERSION 1.0 RW
 *
 */
const addImage = document.querySelector("#add-image");
const preview = document.querySelectorAll('.preview-image').length
let lengths = 0
let inputCollection = [];
let inputs = 0;
const limit = 4
let counter = 0
let messageError = '';
function addImages(e=null) {

  e !== null ? e.stopPropagation() : ''
  inputCollection = document.querySelectorAll(".input-collection");
  inputs = inputCollection.length;
  if (inputs + preview < limit) {
    const widgetCounter = document.querySelector("#widgets-counter");
    const index = +widgetCounter.value;
    const restoImages = document.querySelector("#resto_images");
    //recup le prototype des entrées data-prototype
    const prototype = restoImages.dataset.prototype.replace(/__name__/g, index);
    //injecter le code dans la div
    restoImages.insertAdjacentHTML("beforeend", prototype);
    widgetCounter.value = index + 1;
    handleDeleteButtons()
    initialVue()
  }
}
  addImage.addEventListener("click",(e)=> {
    if (inputs + preview < limit) {
      e.stopPropagation()
      addImages(e)
    }else{
      return;
    }
  });

  const updateCounter = () => {
    const count = document.querySelectorAll(
      "#resto_images div.img-collection"
    ).length;
    document.querySelector("#widgets-counter").value = count;
  };

  const handleDeleteButtons = () => {
    var deletes = document.querySelectorAll("button[data-action='delete']");
    deletes.forEach((button) => {
      button.addEventListener("click", () => {
        const target = button.dataset.target;
        const elementTarget = document.querySelector(target);
        if (elementTarget) {
          inputs--
          elementTarget.remove();
        }
      });
    });
  };

  
  function viewImage(event) {
      const file = event.target.files[0];
      const file_extension_regex = /\.(jpeg|jpg|png)$/i;
      const parent = event.target.parentNode;
      const errorMessage = document.createElement('span')
            errorMessage.classList.add('create-error-js')
            if(parent.querySelectorAll('.create-error-js') !== null){
              parent.querySelectorAll('.create-error-js').forEach(element => {
                element.remove()
              });
            }
            if(parent.querySelectorAll('.form-create-error') !== null){
              parent.querySelectorAll('.form-create-error').forEach(element => {
                element.remove()
              });
            }
            
      if (file) {
          if (file.length === 0 || !file_extension_regex.test(file.name)) {
            let label = parent.querySelector(".label-input-file");
            label.innerHTML = "";
            label.insertAdjacentElement('afterend',errorMessage); 
            errorMessage.innerHTML = 'Veuillez insérer une image de type jpg|jpeg|png'
              return;
            }
            const file_reader = new FileReader();
            file_reader.readAsDataURL(file);
            file_reader.addEventListener("load", (e) => {
        e.stopPropagation();
        display(e, parent);
    });
} else {
    display(null, parent);
}
}

function display(e = null, parent) {
    const currentParent = parent.querySelector(".label-input-file");
    currentParent.innerHTML = "";
    if (e !== null) currentParent.innerHTML = `<img  src=${e.target.result} />`;
}

function input(collection) {
    collection.forEach((element) => {
    });
}


function initialVue()
{
  document.querySelectorAll(".input-collection").forEach((file) => {
    file.addEventListener('load',(e)=>{
      e.stopPropagation();
      viewImage(e);   
    })
    file.addEventListener("change", (e) => {
      e.stopPropagation();
      viewImage(e);
    });
  })
  
}
updateCounter();
handleDeleteButtons();
initialVue()
