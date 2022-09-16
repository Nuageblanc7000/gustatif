/**
 * Notifcation created by R.W
 * 2022-15-09
 */

export default class Notyf_flash extends HTMLElement {
  constructor() {
    super();
    this.spanTimer = document.createElement("span");
    this.stopMessage = document.createElement("div");
    this.stopMessage.classList.add("flash-stop-message");
    this.stopMessage.innerHTML = "X";
    this.parentFlash = document.createElement("div");
    this.parentFlash.classList.add("div-flash-message");
    this.append(this.parentFlash);
    this.append(this.stopMessage);
    this.collection = [...this.children];
    this.insertAdjacentElement("beforeend", this.spanTimer);
    this.collection.forEach((child) => {
      if (child !== this.parentFlash && child !== this.spanTimer) {
        this.parentFlash.appendChild(child);
      }
    });
    this.time;
    this.time= this.time == undefined ? 5000 : parseInt(this.getAttribute("time"))
    this.target = this.getBoundingClientRect();
    this.width = this.target.width;
    this.onePourcent = 100 / this.time;
  }
  /**
   * permet dès la création du component d'affecter les fonctionnements que l'on désire
   */
  connectedCallback() {
    this.lineSize = this.onePourcent;
    this.spanTimer.animate(
      [
        {
          width: `${(this.lineSize = this.lineSize + this.onePourcent + "%")}`,
        },
        { width: `${100 + "%"}` },
      ],
      {
        duration: this.time,
        iterations: Infinity,
      }
    );
    this.stopMessage.addEventListener("click",(event)=>
   {
    event.stopPropagation()
    this.remove()
   } 
  );
    this.timeout = setTimeout(() => {
      this.remove();
    }, this.time);
  }
  /**
   * permet de supprimer les comportements du component une fois celui-ci effacé
   */
  disconnectedCallback() {
    clearTimeout(this.timeout);
  }
}


