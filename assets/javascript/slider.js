
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
    if(images.length > 1){

      autoplay = setInterval(prev,time)
    }
}
autoplay = setInterval(prev,time)


controls.forEach((elem, c)=>{
    elem.addEventListener('click',()=>slider(n=c))
})


//map
const coord = document.querySelector('#map');

if(coord !== null){

  const longi = parseFloat(coord.dataset.l).toFixed(3)
  const lati = parseFloat(coord.dataset.la).toFixed(3)
  console.log(parseFloat(longi))
  let map = L.map('map').setView([parseFloat(longi),parseFloat(lati)], 20);
  
  
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 12,
    minZoom:12,
    attribution: '© OpenStreetMap'
  }).addTo(map);
  const logiConvert = (parseFloat(longi) - 0.00194).toFixed(3)
  const latiConvert = (parseFloat(lati) - 0.001).toFixed(2)
console.log(logiConvert)
var marker = L.marker([logiConvert , latiConvert]).addTo(map);
}



//scroll page restaurant

const nav_resto_lateral = [...document.querySelectorAll('.resto-cube')]
const nav_resto_lateral_r = [...document.querySelectorAll('.resto-cube-r')]
const section = [...document.querySelectorAll('.js-move')] 
const header_container = document.querySelector('.header-container')
const bounding_header =  header_container.getBoundingClientRect()
const {height} = bounding_header 


let sectionPosi;
function calculePos(){
 sectionPosi = section.map(pos => pos.offsetTop)
}

calculePos()

function addEvent(elem,ev){
  elem.forEach(each => {
    each.addEventListener('click',ev)
  });

}
addEvent(nav_resto_lateral,scrollElement)
addEvent(nav_resto_lateral_r,scrollResponsive)

function scrollElement (e) {
  const linkIndex = nav_resto_lateral.indexOf(this)
  console.log(linkIndex)
  window.scrollTo({
    top:sectionPosi[linkIndex] - (height),
    behavior:'smooth'
  })

}
function scrollResponsive (e) {
  console.log(height)
  const linkIndex = nav_resto_lateral_r.indexOf(this)
  window.scrollTo({
    top:(sectionPosi[linkIndex] - (height)) ,
    behavior:'smooth'
  })

}