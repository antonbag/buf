const initBufTopBar = () => {
  // Usamos un objeto vacío por defecto para evitar error si Joomla no está definido
  const config = (typeof Joomla !== 'undefined' && Joomla.getOptions)
    ? Joomla.getOptions('buf.config')
    : { buf_topbar_show_on_scroll: false }; // Valor por defecto

  BufInitFixedBar("buf_topbar", config.buf_topbar_show_on_scroll);
};

if (document.readyState === 'loading') {
  document.addEventListener("DOMContentLoaded", initBufTopBar);
} else {
  initBufTopBar();
}

function BufInitFixedBar(barId, showOnlyMobile) {
  const bar = document.getElementById(barId);
  if (!bar) return;

  // --- Variables de Estado ---
  let lastKnownScrollY = window.scrollY; // Guarda la posición del frame ANTERIOR
  let currentOffset = 0;
  let barHeight = bar.offsetHeight;
  let mobileWidth = parseInt(bar.dataset.mobile || 0);
  let isTicking = false;
  let isActive = false;

  // --- 1. Configurar Estado (Móvil vs Desktop) ---
  const checkActiveState = () => {
    const width = window.innerWidth;
    // Si showOnlyMobile es false, siempre está activo. Si es true, depende del ancho.
    const shouldBeActive = !showOnlyMobile || (width <= mobileWidth);

    // Actualizamos altura por si cambió al redimensionar
    barHeight = bar.offsetHeight;

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
      lastKnownScrollY = window.scrollY;
    }
  }, { passive: true });
}