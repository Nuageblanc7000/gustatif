const addImage = document.querySelector('#add-image')
const imageStamp = document.querySelectorAll('.container-file');
let inputCollection=[];
let inputs = 1;
if(imageStamp.length !== 6){
    addImage.addEventListener('click',()=>{
        inputCollection = document.querySelectorAll('.input-collection');
        inputs = inputCollection.length
        if(inputs + imageStamp.length < 6){
            const widgetCounter = document.querySelector("#widgets-counter")
            const index = +widgetCounter.value  
            const restoImages = document.querySelector('#resto_images')
            //recup le prototype des entrées data-prototype
            const prototype = restoImages.dataset.prototype.replace(/__name__/g, index)
            //injecter le code dans la div
            restoImages.insertAdjacentHTML('beforeend', prototype)
            widgetCounter.value = index+1
            handleDeleteButtons() 
        }
    })
    
    const updateCounter = () => {
        const count = document.querySelectorAll('#resto_images div.img-collection').length
        document.querySelector('#widgets-counter').value = count
    }
    
    const handleDeleteButtons = () => {
        var deletes = document.querySelectorAll("button[data-action='delete']")
        
        deletes.forEach(button => {
            button.addEventListener('click',()=>{
                const target = button.dataset.target
                const elementTarget = document.querySelector(target)
                if(elementTarget){
                    elementTarget.remove() 
                }
            })
        })
        
        
    }
    updateCounter()
    handleDeleteButtons()
}