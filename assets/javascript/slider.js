
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


//test
const coord = document.querySelector('#map');
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