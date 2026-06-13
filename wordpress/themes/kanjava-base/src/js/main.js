// Kanjava Base — JS entry point.
// Imports drive the CSS pipeline: Font Awesome first, then the themed Bulma
// build. Vite emits hashed CSS + webfont assets into dist/ for production.
import '@fortawesome/fontawesome-free/css/all.min.css'
import '../scss/main.scss'

// Bulma navbar is responsive but needs JS to toggle the mobile burger menu.
function initNavbarBurgers() {
  const burgers = document.querySelectorAll('.navbar-burger')
  burgers.forEach((burger) => {
    burger.addEventListener('click', () => {
      const target = document.getElementById(burger.dataset.target)
      burger.classList.toggle('is-active')
      target?.classList.toggle('is-active')
      const expanded = burger.classList.contains('is-active')
      burger.setAttribute('aria-expanded', String(expanded))
    })
  })
}

if (document.readyState !== 'loading') {
  initNavbarBurgers()
} else {
  document.addEventListener('DOMContentLoaded', initNavbarBurgers)
}
