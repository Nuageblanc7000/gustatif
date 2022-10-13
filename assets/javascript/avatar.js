// permet de donner un aperçu de l'avatar actuel
const avatar = document.querySelector(".js-avatar");
const label_avatar = document.querySelector(".label-avatar");
const avatar_input = document.querySelector(".js-btn-avatar");
const timers = document.querySelector('.js-loader-timer-avatar')
const save_src = avatar.src;
const form = document.querySelector('form[name="user_modify_avatar"]')

avatar_input.addEventListener("change", function (e) {
  e.target.files[0];
  const file_reader = new FileReader();
  const file = avatar_input;
  const fileUp = e.target.files[0]
  const errorMessage = document.createElement('span')
  errorMessage.classList.add('create-error-js')
  if(form.querySelectorAll('.create-error-js') !== null){
      form.querySelectorAll('.create-error-js').forEach(element => {
      element.remove()
    });
  }
  if(form.querySelectorAll('.form-create-error') !== null){
      form.querySelectorAll('.form-create-error').forEach(element => {
      element.remove()
    });
  }
  if(fileUp){
    const file_extension_regex = /\.(jpeg|jpg|png)$/i;
  
    if (!file_extension_regex.test(fileUp.name)) {
        e.target.insertAdjacentElement('afterend',errorMessage); 
            errorMessage.innerHTML = 'Veuillez insérer une image de type jpg|jpeg|png'
        return;
    }
      file_reader.readAsDataURL(fileUp);
     
      
      file_reader.addEventListener("load", (e) => {
          if(fileUp){
              avatar.src=e.target.result
            }
            else{
                return;
            }
            
        });
    }else{
        avatar.src=save_src
    }
  });

