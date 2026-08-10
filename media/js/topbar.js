const initBufTopBar = () => {
  // El efecto mostrar/ocultar al desplazar se aplica siempre que la barra esté
  // visible. La opción "sólo móvil" ya no interviene aquí: controla la
  // visibilidad de la topbar por CSS (media query del breakpoint móvil).
  BufInitFixedBar("buf_topbar");
};

if (document.readyState === 'loading') {
  document.addEventListener("DOMContentLoaded", initBufTopBar);
} else {
  initBufTopBar();
}

function BufInitFixedBar(barId) {
  const bar = document.getElementById(barId);
  if (!bar) return;

  // Botón offcanvas: se oculta/muestra junto a la barra sólo si está anclado
  // arriba (oc_button_vpos_top), que es donde se solapa con la topbar.
  const ocButton = document.querySelector('#bufoc_button.oc_button_vpos_top');

  // --- Variables de Estado ---
  let lastKnownScrollY = window.scrollY; // Guarda la posición del frame ANTERIOR
  let currentOffset = 0;
  let barHeight = bar.offsetHeight;
  let isTicking = false;
  let isActive = false;

  // --- 1. Configurar Estado (barra visible o no) ---
  const checkActiveState = () => {
    // Actualizamos altura por si cambió al redimensionar
    barHeight = bar.offsetHeight;

    // Si el CSS la oculta (p. ej. "sólo móvil" en desktop) no hay nada que fijar.
    // (offsetParent no vale: siempre es null con position:fixed)
    const shouldBeActive = bar.getClientRects().length > 0 && barHeight > 0;

    if (shouldBeActive !== isActive) {
      isActive = shouldBeActive;
      if (isActive) {
        // Aplicar estilos fijos necesarios
        bar.style.position = 'fixed';
        bar.style.top = '0';
        bar.style.left = '0';
        bar.style.width = '100%';
        bar.style.zIndex = '1030'; // Bootstrap default navbar z-index
        bar.style.willChange = 'transform';
        if (ocButton) {
          ocButton.style.willChange = 'transform';
        }
      } else {
        // Limpiar estilos para volver al flujo normal
        bar.style.position = '';
        bar.style.top = '';
        bar.style.left = '';
        bar.style.width = '';
        bar.style.zIndex = '';
        bar.style.transform = '';
        bar.style.willChange = '';
        currentOffset = 0;
        if (ocButton) {
          ocButton.style.transform = '';
          ocButton.style.willChange = '';
        }
      }
    }
  };

  // --- 2. Función de Animación (Render) ---
  const update = () => {
    const currentScrollY = window.scrollY;

    // Protección contra rebote elástico en iOS (scroll negativo)
    if (currentScrollY < 0) {
      isTicking = false;
      return;
    }

    const scrollDifference = lastKnownScrollY - currentScrollY;

    // Lógica de movimiento:
    // scrollDifference es POSITIVO si subimos (mostramos barra)
    // scrollDifference es NEGATIVO si bajamos (ocultamos barra)

    if (scrollDifference < 0) {
      // Bajando: Restamos offset hasta -barHeight
      currentOffset = Math.max(-barHeight, currentOffset + scrollDifference);
    } else {
      // Subiendo: Sumamos offset hasta 0
      currentOffset = Math.min(0, currentOffset + scrollDifference);
    }

    // Aplicar transformación
    bar.style.transform = `translate3d(0, ${currentOffset}px, 0)`;

    // El botón se oculta en proporción a su propia altura, para que
    // desaparezca por completo aunque sea más alto que la barra.
    if (ocButton) {
      const ratio = barHeight ? currentOffset / barHeight : 0;
      const buttonOffset = ratio * (ocButton.offsetHeight || barHeight);
      ocButton.style.transform = `translate3d(0, ${buttonOffset}px, 0)`;
    }

    // IMPORTANTE: Actualizamos la posición "anterior" AHORA, para el siguiente frame
    lastKnownScrollY = currentScrollY;
    isTicking = false;
  };

  // --- 3. Listener del Scroll ---
  const onScroll = () => {
    if (!isActive) return;

    if (!isTicking) {
      window.requestAnimationFrame(update);
      isTicking = true;
    }
  };

  // --- Inicialización ---
  checkActiveState(); // Configurar estado inicial

  window.addEventListener("scroll", onScroll, { passive: true });

  window.addEventListener("resize", () => {
    checkActiveState();
    // Reseteamos offset al cambiar tamaño para evitar que se quede oculta
    if (isActive) {
      currentOffset = 0;
      bar.style.transform = `translate3d(0, 0, 0)`;
      if (ocButton) {
        ocButton.style.transform = `translate3d(0, 0, 0)`;
      }
      lastKnownScrollY = window.scrollY;
    }
  }, { passive: true });
}