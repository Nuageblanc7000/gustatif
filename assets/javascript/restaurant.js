import RatingForm from "./component_rating";
const slider_container = document.querySelector(".slider-resto");
const images = slider_container.querySelectorAll(".slider-img");
const controls = slider_container.querySelectorAll(".control-slider");
let n = 0;
let autoplay;
let k;
let time = 3000;
const prev = () => {
  slider((n += 1));
};
const next = () => {
  slider((n -= 1));
};
const slider = (key) => {
  clearInterval(autoplay);
  images.forEach((elem, key) => {
    k = key;
    if (elem.classList.contains("active-slider"))
      elem.classList.remove("active-slider");
    if (controls.item(key).classList.contains("active-control"))
      controls.item(key).classList.remove("active-control");
  });
  if (n < 0) n = n++;
  if (n > images.length - 1) n = 0;
  images.item(n).classList.add("active-slider");
  controls.item(n).classList.add("active-control");
  if (images.length > 1) {
    autoplay = setInterval(prev, time);
  }
};
autoplay = setInterval(prev, time);

controls.forEach((elem, c) => {
  elem.addEventListener("click", () => slider((n = c)));
});

//map
const coord = document.querySelector("#map");
// test rapide 

// REVENIR ICI POUR VOIR SI ON FAIT UN TRUC AVEC LA POS
// navigator.geolocation.getCurrentPosition(elem => console.log(elem));

// function distance(lat1, lon1, lat2, lon2, unit) {
// 	if ((lat1 == lat2) && (lon1 == lon2)) {
// 		return 0;
// 	}
// 	else {
// 		var radlat1 = Math.PI * lat1/180;
// 		var radlat2 = Math.PI * lat2/180;
// 		var theta = lon1-lon2;
// 		var radtheta = Math.PI * theta/180;
// 		var dist = Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);
// 		if (dist > 1) {
// 			dist = 1;
// 		}
// 		dist = Math.acos(dist);
// 		dist = dist * 180/Math.PI;
// 		dist = dist * 60 * 1.1515;
// 		if (unit=="K") { dist = dist * 1.609344 }
// 		if (unit=="N") { dist = dist * 0.8684 }
// 		if (unit=="M") { dist = dist * 1000 }
// 		return dist;
// 	}
// }
// console.log((50.5043242).toFixed(4),(4.36229729389).toFixed(4));
// console.log(distance((50.5043242).toFixed(4),(4.36229729389).toFixed(4),50.5118,4.3617,'M'));
if (coord !== null) {
  const longi = parseFloat(coord.dataset.l).toFixed(3);
  const lati = parseFloat(coord.dataset.la).toFixed(3);

  let map = L.map("map").setView([parseFloat(longi), parseFloat(lati)], 20);


  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    maxZoom: 12,
    minZoom: 12,
    attribution: "© OpenStreetMap",
  }).addTo(map);
  const logiConvert = (parseFloat(longi) - 0.00194).toFixed(3);
  const latiConvert = (parseFloat(lati) - 0.001).toFixed(2);

  var marker = L.marker([logiConvert, latiConvert]).addTo(map);
}

//scroll page restaurant

const nav_resto_lateral = [...document.querySelectorAll(".js-cube-move")];
const nav_resto_lateral_r = [...document.querySelectorAll(".js-cube-move-r")];
const section = [...document.querySelectorAll(".js-move")];
const header_container = document.querySelector(".header-container");
const bounding_header = header_container.getBoundingClientRect();
const { height } = bounding_header;

let sectionPosi;
function calculePos() {
  sectionPosi = section.map((pos) => pos.offsetTop);
}

calculePos();

function addEvent(elem, ev) {
  elem.forEach((each) => {
    each.addEventListener("click", ev);
  });
}
addEvent(nav_resto_lateral, scrollElement);
addEvent(nav_resto_lateral_r, scrollResponsive);

function scrollElement(e) {
  const linkIndex = nav_resto_lateral.indexOf(this);
  window.scrollTo({
    top: sectionPosi[linkIndex] - height,
    behavior: "smooth",
  });
}
function scrollResponsive(e) {
  const linkIndex = nav_resto_lateral_r.indexOf(this);
  window.scrollTo({
    top: sectionPosi[linkIndex] - height,
    behavior: "smooth",
  });
}

//système de rating

// On va chercher toutes les étoiles

// if (
//   document.querySelectorAll(".container-star-js") !== null &&
//   document.querySelectorAll(".js-stars") !== null &&
//   document.querySelector(".js-value-rating") !== null
// ) {
//   const container_stars = document.querySelector(".container-star-js");
//   const stars = container_stars.querySelectorAll(".js-stars");
//   const notation = document.querySelector(".js-value-rating");

//   stars.forEach((star) => {
//     star.addEventListener("mouseover", function () {
//       resetStar();
//       star.classList.remove("fa-regular");
//       star.classList.add("fa-solid");
//       let previous = this.previousElementSibling;

//       while (previous) {
//         previous.classList.remove("fa-regular");
//         previous.classList.add("fa-solid");
//         previous = previous.previousElementSibling;
//       }
//     });
    
//     star.addEventListener("mouseout", function () {
//       resetStar(notation.value);
//     });
//     star.addEventListener("click", function () {
//       if (notation.value == 1) {
//         notation.value = 0;
//       } else {
//         notation.value = this.dataset.val;
//       }
//       resetStar(notation.value);
//     });
//   });

//   function resetStar(rating = 0) {
//     stars.forEach((star) => {
//       if (star.dataset.val <= notation.value) {
//         star.classList.remove("fa-regular");
//         star.classList.add("fa-solid");
//       } else {
//         star.classList.remove("fa-solid");
//         star.classList.add("fa-regular");
//       }
//     });
//   }
// }
// pour la partie cachée
const comments = document.querySelectorAll(".js-box-comment");

const btnViewComment = document.querySelector(".js-comment-views");
let counterComment = 3;
const countComment = 3;

function moreComments(counterComment = 0) {
  if (comments.length > 0) {
    comments.forEach((comment, key) => {
      if (comments.length <= countComment) {
        btnViewComment.style.display = "none";
      }
      if (counterComment >= comments.length) {
        btnViewComment.innerHTML = btnViewComment.dataset.comdown;
      } else {
        btnViewComment.innerHTML =
          btnViewComment.dataset.comup +
          " " +
          `(${(comments.length - counterComment)})`;
      }
      if (key < counterComment) {
        comment.style.display = "block";
        comment.classList.add("up-comment");
      } else {
        comment.style.display = "none";
        comment.classList.remove("up-comment");
      }
    });
  } else {
    btnViewComment.style.display = "none";
  }
}
moreComments(counterComment);

btnViewComment.addEventListener("click", () => {
  moreComments(
    counterComment < comments.length
      ? (counterComment = counterComment + countComment)
      : (counterComment = countComment)
  );
});


//ajout du système de like
if (document.querySelectorAll(".js-like-resto") !== null) {
  const js_like = document.querySelectorAll(".js-like-resto");
  function like(e) {
    const url = this.href;
    e.preventDefault();
    e.stopPropagation();
    const fetching = fetch(url, {
      method: "POST",
    });
    fetching
      .then((response) => response.json())
      .then((data) => {
        if (data.route) {
          window.document.location.href = data.route;
        } else {
            js_like.forEach(elem => {
              const heart = elem.querySelector("i");
          const replace = heart.classList.contains("fa-regular")
            ? heart.classList.replace("fa-regular", "fa-solid")
            : heart.classList.replace("fa-solid", "fa-regular");
            })
        }
      })
      .catch((err) => {
        return;
      });
  }
  js_like.forEach(targetBtnLike => targetBtnLike.addEventListener("click", like))
  
}

customElements.define('rating-tag',RatingForm)