/**
 * BUF Template Logic
 * Modernized structure
 */

// Definimos valores por defecto seguros para evitar errores de "undefined"
// Usamos window explícitamente si necesitamos que sean globales para otros scripts.
window.buf_vars = {}; // CORREGIDO: Inicializado como Objeto, no Array.
window.buf_params = window.buf_params || { debug: false }; // Fallback por defecto

// Función de Debug unificada
const buf_debug = (msg) => {
	// Usamos Optional Chaining (?.) para seguridad
	if (window.buf_params?.debug) {
		console.log(`BUF |-*-| ${msg}`);
	}
};

// Alias si es estrictamente necesario por compatibilidad con otros archivos antiguos
const buf_debug_n = buf_debug;

const buf_js_init = () => {

	// 1. Carga de Configuración (Optimizada)
	const loadJoomlaParams = () => {
		if (typeof Joomla !== 'undefined' && Joomla.getOptions) {
			const config = Joomla.getOptions('buf.config');
			if (config && config.params) {
				window.buf_params = config.params;
				return true;
			}
		}
		return false;
	};

	// Intentamos cargar inmediatamente
	if (!loadJoomlaParams()) {
		// Si falla, hacemos polling rápido (cada 100ms) durante máximo 2 segundos
		// 3 segundos de espera inicial es demasiado bloqueo.
		let attempts = 0;
		const interval = setInterval(() => {
			attempts++;
			if (loadJoomlaParams() || attempts > 20) {
				clearInterval(interval);
				if (attempts > 20) console.warn('BUF: Joomla params could not be loaded.');
			}
		}, 100);
	}

	// 2. Inicialización del DOM
	const onReady = () => {

		// Lazy CSS - Sintaxis Moderna
		document.querySelectorAll('link[rel="lazy-stylesheet"]').forEach(link => {
			link.rel = "stylesheet";
		});

		// Asignación de variable
		window.buf_vars.currentDevice = 'mobile';

		buf_debug("buf_js_init initialized");

		// Dev Mode - Listeners
		const devModeDivs = document.querySelectorAll('div.buf_dev_mode');

		if (devModeDivs.length > 0) {
			document.querySelectorAll('a.buf_dev_mode_close').forEach(button => {
				button.addEventListener('click', (e) => {
					e.preventDefault(); // Prevenir comportamiento de enlace
					e.stopPropagation();
					devModeDivs.forEach(div => div.remove()); // .remove() es mejor que display:none para limpiar el DOM
				});
			});
		}
	};

	// Ejecutar cuando el DOM esté listo
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', onReady);
	} else {
		onReady();
	}
};

// Ejecutar inicialización
buf_js_init();