const cards = document.querySelectorAll('.card-flip');
console.log(cards)
function transition() {
  if (this.classList.contains('active-flip')) {
    this.classList.remove('active-flip')
  } else {
    this.classList.add('active-flip');
  }
}

cards.forEach(card => card.addEventListener('click', transition));
 