/* Terral: compra el look y animaciones de la portada. */
(function () {
	var reducir = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	// Aparición suave al hacer scroll.
	var revela = document.querySelectorAll('.ter-revela');
	if (reducir || !('IntersectionObserver' in window)) {
		revela.forEach(function (el) { el.classList.add('visto'); });
	} else {
		var obs = new IntersectionObserver(function (es) {
			es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('visto'); obs.unobserve(e.target); } });
		}, { rootMargin: '0px 0px -8% 0px' });
		revela.forEach(function (el, i) { el.style.transitionDelay = (i % 4) * 90 + 'ms'; obs.observe(el); });
	}

	// Compra el look: un punto abre su tarjeta y cierra las demás.
	var puntos = Array.prototype.slice.call(document.querySelectorAll('.ter-punto'));
	function cerrar(excepto) {
		puntos.forEach(function (p) {
			if (p === excepto) return;
			p.setAttribute('aria-expanded', 'false');
			var t = document.getElementById(p.getAttribute('aria-controls'));
			if (t) t.hidden = true;
		});
	}
	puntos.forEach(function (p) {
		p.addEventListener('click', function (e) {
			e.stopPropagation();
			var t = document.getElementById(p.getAttribute('aria-controls'));
			var abrir = p.getAttribute('aria-expanded') !== 'true';
			cerrar(p);
			p.setAttribute('aria-expanded', abrir ? 'true' : 'false');
			if (!t) return;
			t.hidden = !abrir;
			if (abrir) {
				var x = parseFloat(t.style.getPropertyValue('--x')), y = parseFloat(t.style.getPropertyValue('--y'));
				t.classList.toggle('abajo', y < 35);
				t.classList.toggle('izq', x > 75);
				t.classList.toggle('der', x < 25);
			}
		});
	});
	document.addEventListener('click', function (e) {
		if (!e.target.closest('.ter-tarjeta')) cerrar(null);
	});
	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape') cerrar(null);
	});

	// Carrito: cuánto falta para el envío gratis.
	var rejilla = document.querySelector('body#cart .cart-grid');
	if (rejilla && window.prestashop) {
		var GRATIS = 60;
		var euros = function (n) { return n.toFixed(2).replace('.', ',') + ' €'; };
		var barra = document.createElement('div');
		barra.className = 'ter-envio';
		barra.hidden = true;
		barra.innerHTML = '<p class="ter-envio__texto" aria-live="polite"></p><div class="ter-envio__pista" aria-hidden="true"><span></span></div>';
		rejilla.parentNode.insertBefore(barra, rejilla);
		var textoEnvio = barra.querySelector('.ter-envio__texto'), relleno = barra.querySelector('.ter-envio__pista span');
		var pintarEnvio = function (carrito) {
			var s = carrito && carrito.subtotals;
			if (!s || !s.products || !carrito.products_count) { barra.hidden = true; return; }
			var total = s.products.amount - (s.discounts ? Math.abs(s.discounts.amount) : 0);
			var falta = GRATIS - total;
			barra.hidden = false;
			barra.classList.toggle('ter-envio--hecho', falta <= 0);
			textoEnvio.textContent = falta > 0 ? 'Te faltan ' + euros(falta) + ' para el envío gratis.' : 'Ya tienes el envío estándar gratis.';
			relleno.style.width = Math.max(4, Math.min(100, total / GRATIS * 100)) + '%';
		};
		pintarEnvio(prestashop.cart);
		if (prestashop.on) {
			prestashop.on('updatedCart', function (e) {
				if (e && e.resp && e.resp.cart) pintarEnvio(e.resp.cart);
			});
		}
	}

	// Botones del pago que la traducción de PrestaShop deja en inglés.
	function traducir(raiz) {
		var it = document.createTreeWalker(raiz || document.body, NodeFilter.SHOW_TEXT);
		var n;
		while ((n = it.nextNode())) {
			var t = n.nodeValue;
			if (t.indexOf('Back to ') !== -1 || t.indexOf('Continue to ') !== -1) {
				n.nodeValue = t.replace('Back to ', 'Volver a ').replace('Continue to ', 'Continuar a ');
			}
		}
	}
	if (document.body.classList.contains('page-order') || document.getElementById('checkout')) {
		traducir();
		new MutationObserver(function (cambios) {
			cambios.forEach(function (c) { c.addedNodes.forEach(function (nodo) { if (nodo.nodeType === 1) traducir(nodo); }); });
		}).observe(document.body, { childList: true, subtree: true });
	}
})();
