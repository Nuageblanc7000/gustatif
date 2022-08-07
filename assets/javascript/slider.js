const slider_container = document.querySelector('.slider-resto')
const images = slider_container.querySelectorAll('.slider-img')
const controls = slider_container.querySelectorAll('.control-slider')

let n = 0;
let autoplay;
let k;
time = 3000;
const prev = () => {
  slider( n += 1);
};
const next = () => {
  slider(n -= 1); 
};
const slider = (key) =>{
    clearInterval(autoplay)
    images.forEach((elem,key) =>{
        k=key
        if(elem.classList.contains('active-slider')) elem.classList.remove('active-slider')
        if(controls.item(key).classList.contains('active-control')) controls.item(key).classList.remove('active-control')
    })
    if (n < 0) n = n++ ;
    if (n > (images.length -1)) n = 0;
    images.item(n).classList.add('active-slider')
    controls.item(n).classList.add('active-control')
    autoplay = setInterval(prev,time)
}
autoplay = setInterval(prev,time)


controls.forEach((elem, c)=>{
    elem.addEventListener('click',()=>slider(n=c))
})
