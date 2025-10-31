//topbar
//script to show topbar only on scroll up


function BufInitFixedBar(barId, showOnlyMobile) {

    let lastScrollY = window.scrollY;
    let bar = document.getElementById(barId);
    if (!bar) return; // Si no se encuentra el elemento, salimos.
    let barHeight = bar.offsetHeight;
    let currentOffset = 0;
    let mobile_width = bar.dataset.mobile; // Asume que cada elemento tiene su propio data-mobile
  
    // Función para detectar si el dispositivo es móvil
    function isMobile() {

      if(showOnlyMobile){
        return window.innerWidth <= mobile_width;
      }else{
        return true;
      }
     
    }
  
    // Si es móvil, se aplican estilos fijos
    if (isMobile()) {
      bar.style.position = 'absolute';
      bar.style.width = '100%';
    }
  
    // Función que actualiza la posición en función del scroll
    function updateBarPosition() {
      if (!isMobile()) {
        bar.style.transform = ""; // Restablece en pantallas grandes
        return;
      }
  
      let currentScrollY = window.scrollY;
      let scrollDifference = lastScrollY - currentScrollY;
  
      // Scroll hacia abajo: oculta progresivamente la barra
      if (scrollDifference < 0) {
        currentOffset = Math.max(-barHeight, currentOffset + scrollDifference);
      } 
      // Scroll hacia arriba: muestra progresivamente la barra
      else {
        currentOffset = Math.min(0, currentOffset + scrollDifference);
      }
  
      bar.style.transform = `translateY(${currentOffset}px)`;
      lastScrollY = currentScrollY;
    }
  
    window.addEventListener("scroll", updateBarPosition);
  }





/*
document.addEventListener("DOMContentLoaded", function () {
    let lastScrollY = window.scrollY;
    let topbar = document.getElementById("buf_topbar");
    let topbarHeight = topbar.offsetHeight;
    let currentOffset = 0; // Controla la posición actual de la barra
    let mobile_width = document.getElementById('buf_topbar').dataset.mobile;


    if (topbar && isMobile()) {
        topbar.style.position = 'fixed';
        topbar.style.width = '100%';
    }

    function isMobile() {
        return window.innerWidth <= mobile_width; // Define qué consideras móvil
    }

    function updateTopbarPosition() {
        if (!isMobile()) {
            topbar.style.transform = ""; // Restablece en pantallas grandes
            return;
        }

        let currentScrollY = window.scrollY;
        let scrollDifference = lastScrollY - currentScrollY;

        // Scroll hacia abajo → Ocultar progresivamente la barra
        if (scrollDifference < 0) {
            currentOffset = Math.max(-topbarHeight, currentOffset + scrollDifference);
        } 
        // Scroll hacia arriba → Mostrar progresivamente la barra
        else {
            currentOffset = Math.min(0, currentOffset + scrollDifference);
        }

        topbar.style.transform = `translateY(${currentOffset}px)`;

        lastScrollY = currentScrollY;
    }

    window.addEventListener("scroll", updateTopbarPosition);
});
*/
