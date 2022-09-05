const slider_container = document.querySelector('.slider-resto')
const images = slider_container.querySelectorAll('.slider-img')
const controls = slider_container.querySelectorAll('.control-slider')

let n = 0
let autoplay
let k
time = 3000
const prev = () => {
  slider((n += 1))
}
const next = () => {
  slider((n -= 1))
}
const slider = (key) => {
  clearInterval(autoplay)
  images.forEach((elem, key) => {
    k = key
    if (elem.classList.contains('active-slider'))
      elem.classList.remove('active-slider')
    if (controls.item(key).classList.contains('active-control'))
      controls.item(key).classList.remove('active-control')
  })
  if (n < 0) n = n++
  if (n > images.length - 1) n = 0
  images.item(n).classList.add('active-slider')
  controls.item(n).classList.add('active-control')
  if (images.length > 1) {
    autoplay = setInterval(prev, time)
  }
}
autoplay = setInterval(prev, time)

controls.forEach((elem, c) => {
  elem.addEventListener('click', () => slider((n = c)))
})

//map
const coord = document.querySelector('#map')

if (coord !== null) {
  const longi = parseFloat(coord.dataset.l).toFixed(3)
  const lati = parseFloat(coord.dataset.la).toFixed(3)
  console.log(parseFloat(longi))
  let map = L.map('map').setView([parseFloat(longi), parseFloat(lati)], 20)

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 12,
    minZoom: 12,
    attribution: '© OpenStreetMap',
  }).addTo(map)
  const logiConvert = (parseFloat(longi) - 0.00194).toFixed(3)
  const latiConvert = (parseFloat(lati) - 0.001).toFixed(2)

  var marker = L.marker([logiConvert, latiConvert]).addTo(map)
}

//scroll page restaurant

const nav_resto_lateral = [...document.querySelectorAll('.resto-cube')]
const nav_resto_lateral_r = [...document.querySelectorAll('.resto-cube-r')]
const section = [...document.querySelectorAll('.js-move')]
const header_container = document.querySelector('.header-container')
const bounding_header = header_container.getBoundingClientRect()
const { height } = bounding_header

let sectionPosi
function calculePos() {
  sectionPosi = section.map((pos) => pos.offsetTop)
}

calculePos()

function addEvent(elem, ev) {
  elem.forEach((each) => {
    each.addEventListener('click', ev)
  })
}
addEvent(nav_resto_lateral, scrollElement)
addEvent(nav_resto_lateral_r, scrollResponsive)

function scrollElement(e) {
  const linkIndex = nav_resto_lateral.indexOf(this)
  window.scrollTo({
    top: sectionPosi[linkIndex] - height,
    behavior: 'smooth',
  })
}
function scrollResponsive(e) {
  console.log(height)
  const linkIndex = nav_resto_lateral_r.indexOf(this)
  window.scrollTo({
    top: sectionPosi[linkIndex] - height,
    behavior: 'smooth',
  })
}

//système de rating

// On va chercher toutes les étoiles

if(document.querySelectorAll('.container-star-js') !== null && document.querySelectorAll('.js-stars') !== null && document.querySelector('.js-value-rating')  !== null){

const container_stars = document.querySelector('.container-star-js')
const stars = container_stars.querySelectorAll('.js-stars')
const notation = document.querySelector('.js-value-rating')

stars.forEach((star) => {
  star.addEventListener('mouseover', function () {
    resetStar()
    star.classList.remove('fa-regular')
    star.classList.add('fa-solid')
    let previous = this.previousElementSibling
    console.log(previous)

    while (previous) {
      previous.classList.remove('fa-regular')
      previous.classList.add('fa-solid')
      previous = previous.previousElementSibling
    }
  })
  star.addEventListener('mouseout', function () {
    resetStar(notation.value)
  })
  star.addEventListener('click', function () {
    if (notation.value == 1) {
      notation.value = 0
    } else {
      notation.value = this.dataset.val
    }
    resetStar(notation.value)
  })
})

function resetStar(rating = 0) {
  stars.forEach((star) => {
    if (star.dataset.val <= notation.value) {
      star.classList.remove('fa-regular')
      star.classList.add('fa-solid')
    } else {
      star.classList.remove('fa-solid')
      star.classList.add('fa-regular')
    }
  })
}
}
// pour la partie cachée
const comments = document.querySelectorAll('.js-box-comment')
console.log(comments)
const btnViewComment = document.querySelector('.js-comment-views')
let counterComment = 3
const  countComment = 3

function moreComments(counterComment = 0){

  comments.forEach((comment, key) => {
    if(comments.length <= countComment){
    
        btnViewComment.style.display = 'none';
      
    }
    if(counterComment >= comments.length){
      btnViewComment.innerHTML = btnViewComment.dataset.comdown
    }else{
      btnViewComment.innerHTML = btnViewComment.dataset.comup + ' ' +  (comments.length - counterComment)
    }
    
      if (key < counterComment) {
        comment.style.display = 'block'
        comment.classList.add('up-comment')
      }
      else {
        comment.style.display = 'none'
        comment.classList.remove('up-comment');
      }
     
  })
}
moreComments(counterComment);

btnViewComment.addEventListener('click',()=>{
console.log(counterComment);
moreComments(  counterComment < comments.length ? counterComment= counterComment + countComment : counterComment = countComment)
})
