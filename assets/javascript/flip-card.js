/**
 * SYSTEM FOR FLIP CARD PAGE HOME
 */
const cards = document.querySelectorAll('.card-flip');
function transition() {
  if (this.classList.contains('active-flip')) {
    this.classList.remove('active-flip')
  } else {
    this.classList.add('active-flip');
  }
}

cards.forEach(card => card.addEventListener('click', transition));
 