//système de rating

// On va chercher toutes les étoiles

export default class Rating extends HTMLElement {
  static get observedAttributes() {
    return ["value"];
  }
  constructor() {
    super();
    this.input = this.querySelector("input");
    this.input.style.display = "none";
    this.container_stars = document.createElement("div");
    this.container_stars.classList.add("container-star-js");
    this.appendChild(this.container_stars);
    for (let index = 1; index <= 5; index++) {
      this.container_stars.innerHTML += `<i class="fa-regular fa-star js-stars" data-value=${index}>`;
    }
    this.stars = this.querySelectorAll(".js-stars");
    this.resetStar(this.input.value, this.stars);
  }
  connectedCallback() {
    this.stars.forEach((star) => {
      star.addEventListener("click", (e) => this.actionClick(e, this.input));
      star.addEventListener("mouseover", (e) => {
        this.actionMouseOver(star, e);
      });
      star.addEventListener("mouseout", () =>
        this.actionMouseOut(this.input.value)
      );
    });
  }
  disconnectedCallback() {}
  attributeChangedCallback(name, oldValue, newValue) {}
  actionClick(e, input) {
    e.stopPropagation();
    if (input.getAttribute("value") == 1) {
      input.setAttribute("value", 0);
    } else {
      input.setAttribute("value", e.target.dataset.value);
    }
    this.resetStar(input.value);
  }
  actionMouseOut(input) {
    this.resetStar(input, this.stars);
  }
  actionMouseOver(star, e) {
    e.stopPropagation();
    this.resetStar(0, this.stars);
    star.classList.remove("fa-regular");
    star.classList.add("fa-solid");
    let previous = e.target.previousElementSibling;


    while (previous) {
      previous.classList.remove("fa-regular");
      previous.classList.add("fa-solid");
      previous = previous.previousElementSibling;
    }
  }
  resetStar(rating = 0, stars = []) {
    stars.forEach((star) => {
      if (star.dataset.value <= rating) {
        star.classList.remove("fa-regular");
        star.classList.add("fa-solid");
      } else {
        star.classList.remove("fa-solid");
        star.classList.add("fa-regular");
      }
    });
  }
}

