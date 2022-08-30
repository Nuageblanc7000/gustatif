const addImage = document.querySelector("#add-image");
const imageStamp = document.querySelectorAll(".container-file");
const preview = document.querySelectorAll('.preview-image').length
let lengths = 0
let inputCollection = [];
let inputs = 0;
const limit = 4
let counter = 0
function addImages(e=null) {
  e !== null ? e.stopPropagation() : ''
  inputCollection = document.querySelectorAll(".input-collection");
  inputs = inputCollection.length;
  if (inputs + preview < limit) {
    const widgetCounter = document.querySelector("#widgets-counter");
    const index = +widgetCounter.value;
    const platImages = document.querySelector("#plat_images");
    //recup le prototype des entrées data-prototype
    const prototype = platImages.dataset.prototype.replace(/__name__/g, index);
    console.log(prototype)
    //injecter le code dans la div
    platImages.insertAdjacentHTML("beforeend", prototype);
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
      "#plat_images div.img-collection"
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
      const file_extension_regex = /\.(jpeg|jpg|png|svg)$/i;
      const parent = event.target.parentNode;
      
      if (file) {
          if (file.length === 0 || !file_extension_regex.test(file.name)) {
              currentParent.querySelector(".label-input-file").innerHTML = "";
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
