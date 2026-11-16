# Terral · tienda PrestaShop de demostración

Tienda de hogar y decoración mediterránea: textil de lino y yute, cerámica, vidrio reciclado, lámparas y piezas de palma hechas a mano, con una portada a pantalla completa y un "Compra el look" en el que tocas cada pieza de un salón para ver su precio y comprarla.

**Verla publicada:** https://terral.dania360.com/

## Qué hay en este repositorio
Solo el módulo hecho a medida para esta tienda; PrestaShop no se incluye.

```
modulo/terral/terral.php                 Módulo principal: ganchos de la portada, estilos y scripts
modulo/terral/classes/                  Datos del catálogo
modulo/terral/controllers/front/        Montaje de la tienda y pago de demostración
modulo/terral/views/                    Plantilla de la portada, CSS sobre el tema Hummingbird, JavaScript y tipografías
```

## Cómo está hecha
- PrestaShop 9.2 con el tema oficial Hummingbird, en el hosting de Hostinger, con su propio subdominio y certificado de seguridad
- Módulo propio para PrestaShop que añade la portada, los estilos, el aviso de tienda de prueba y el pago de prueba
- Paleta mediterránea (cal, terracota y añil) y tipografías Instrument Serif y Figtree servidas desde la propia tienda
- Portada con foto a pantalla completa, categorías con foto, novedades, "Compra el look" e historia de los talleres, sin animaciones si el visitante pide menos movimiento
- Tienda, carrito y pago en español

## Catálogo y compra
- 12 productos en 4 categorías: textil, mesa y cerámica, iluminación y decoración
- Combinaciones con su precio: colores del cojín y la manta con muestra de color, medidas de la alfombra y tamaños del jarrón y del capazo
- Ficha técnica en cada producto (material, medidas, cuidados y taller de origen)
- Ofertas con precio tachado y etiqueta de descuento
- "Compra el look": una foto de un salón con un punto en cada pieza que abre su tarjeta con precio y enlace; en el móvil la tarjeta sale abajo
- Fotos de producto creadas para la marca
- Dos transportistas: envío estándar a 4,95 € y gratis desde 60 €, y exprés en 24 h a 9,95 €
- Cupón de bienvenida BIENVENIDA10 con un 10 % de descuento
- Compra como invitado, sin tener que crear cuenta
- Pago de prueba propio que no pide tarjeta y deja el pedido como pagado, con su factura
- Dos pedidos de prueba completos: con combinaciones, cupón y envío gratis, y desde el móvil con envío exprés

## Detalles
- Sin módulos de pago: lo propio está en un módulo hecho a medida
- Montaje automático del catálogo, las imágenes, los transportistas, el cupón y el pago desde el propio módulo
- Módulos de la demo retirados y textos del pie y de las garantías adaptados a la tienda
- Lista para conectar Redsys, Bizum o Stripe cuando sea una tienda real

## Cómo instalarlo
1. PrestaShop 9 con el tema Hummingbird.
2. Copia `modulo/terral` en la carpeta `modules/` e instálalo desde el panel.
3. El controlador de montaje solo funciona mientras exista un archivo de claves en la instalación, y se elimina al terminar.

Tipografías de Google Fonts con licencia SIL Open Font License, incluidas en `modulo/terral/views/fonts`.

## Sobre esta tienda
Diseño y desarrollo de [Dania360](https://dania360.com). Es una tienda de ejemplo: la marca y los productos son inventados, las fotos se crearon para la marca y el pago es de prueba, no cobra nada. El código se publica para enseñar cómo está hecha; no tiene licencia de uso.

Más tiendas en el portafolio: https://tiendas.dania360.com
