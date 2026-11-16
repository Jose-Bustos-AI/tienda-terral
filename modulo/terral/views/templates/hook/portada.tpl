{**
 * Portada de la tienda de ejemplo Terral.
 *}
<div class="terral-portada">

  <section class="ter-hero" aria-labelledby="ter-hero-titulo">
    <img class="ter-hero__img" src="{$terral_ruta}views/img/terraza.jpg" alt="Mesa puesta en una terraza encalada al atardecer, con vistas al mar" width="1920" height="1080" fetchpriority="high">
    <div class="ter-hero__velo" aria-hidden="true"></div>
    <div class="ter-hero__in">
      <p class="ter-ceja">Hogar y decoración mediterránea</p>
      <h1 id="ter-hero-titulo" class="ter-hero__titulo">La casa, al ritmo <em>del Mediterráneo.</em></h1>
      <p class="ter-hero__texto">Lino, barro, palma y madera de olivo hechos a mano por talleres de la costa. Piezas sencillas para vivir la casa como se vive el verano.</p>
      <div class="ter-botones">
        <a class="ter-btn" href="{$terral_tienda}">Ver la colección</a>
        <a class="ter-btn ter-btn--claro" href="#compra-el-look">Compra el look</a>
      </div>
    </div>
  </section>

  <section class="ter-ventajas">
    <div><b>Hecho a mano</b><span>En talleres de Elche, Crevillente, Valencia y La Bisbal</span></div>
    <div><b>Envío gratis desde 60 €</b><span>Estándar en 48-72 h o exprés en 24 h</span></div>
    <div><b>Materiales naturales</b><span>Lino, yute, barro, palma y olivo</span></div>
    <div><b>30 días para cambiarlo</b><span>Si no encaja en tu casa, lo recogemos</span></div>
  </section>

  {if $terral_categorias}
  <section class="ter-bloque">
    <div class="ter-bloque__cab">
      <div><p class="ter-ceja ter-ceja--oscura">Por estancias</p><h2 class="ter-titulo">Elige por dónde <em>empezar</em></h2></div>
    </div>
    <div class="ter-categorias">
      {foreach $terral_categorias as $cat}
        <a class="ter-categoria ter-revela" href="{$cat.url}">
          <img src="{$cat.img}" alt="" loading="lazy" width="1000" height="1250">
          <span class="ter-categoria__txt"><b>{$cat.nombre}</b><small>{$cat.texto}</small></span>
        </a>
      {/foreach}
    </div>
  </section>
  {/if}

  <section class="ter-bloque ter-bloque--productos">
    <div class="ter-bloque__cab">
      <div><p class="ter-ceja ter-ceja--oscura">Recién llegados del taller</p><h2 class="ter-titulo">Piezas para <em>esta temporada</em></h2></div>
      <a class="ter-enlace" href="{$terral_tienda}">Ver todo →</a>
    </div>
    {$terral_destacados nofilter}
  </section>

  {if $terral_look}
  <section class="ter-look" id="compra-el-look" aria-labelledby="ter-look-titulo">
    <div class="ter-look__cab">
      <p class="ter-ceja">Compra el look</p>
      <h2 class="ter-titulo" id="ter-look-titulo">Este salón, <em>pieza a pieza.</em></h2>
      <p>Toca los puntos de la foto para ver cada pieza, su precio y llevártela.</p>
    </div>
    <div class="ter-look__escena">
      <img src="{$terral_ruta}views/img/salon.jpg" alt="Salón mediterráneo con sofá de lino, cojines, manta, alfombra de yute, lámpara de ratán y espejo de mimbre" loading="lazy" width="1600" height="893">
      {foreach $terral_look as $i => $p}
        <button type="button" class="ter-punto" style="left:{$p.x}%;top:{$p.y}%" aria-expanded="false" aria-controls="ter-tarjeta-{$i}" aria-label="Ver {$p.nombre}"><span></span></button>
        <div class="ter-tarjeta" id="ter-tarjeta-{$i}" role="dialog" aria-label="{$p.nombre}" hidden style="--x:{$p.x};--y:{$p.y}">
          {if $p.img}<img src="{$p.img}" alt="" width="250" height="250" loading="lazy">{/if}
          <div>
            <b>{$p.nombre}</b>
            <small>{$p.nota}</small>
            <em>{$p.precio}</em>
            <a href="{$p.url}">Ver la pieza →</a>
          </div>
        </div>
      {/foreach}
    </div>
    <ol class="ter-look__lista">
      {foreach $terral_look as $p}
        <li><a href="{$p.url}"><span>{$p.nombre}</span><em>{$p.precio}</em></a></li>
      {/foreach}
    </ol>
  </section>
  {/if}

  <section class="ter-historia">
    <div class="ter-historia__texto ter-revela">
      <p class="ter-ceja ter-ceja--oscura">Quiénes hacen Terral</p>
      <h2 class="ter-titulo">Talleres pequeños, <em>oficios de siempre.</em></h2>
      <p>Trabajamos con una palmera de Elche, un telar familiar de Crevillente, un soplador de vidrio de Valencia y alfareros de La Bisbal. Les compramos en pequeñas tandas y les pagamos lo que piden.</p>
      <p>Por eso a veces un color se agota o un jarrón sale un poco distinto: así es lo hecho a mano.</p>
    </div>
    <div class="ter-historia__cupon ter-revela">
      <p class="ter-ceja">Tu primera compra</p>
      <p class="ter-cupon__num">10 %</p>
      <p>de descuento con el código</p>
      <p class="ter-cupon__codigo">BIENVENIDA10</p>
      <a class="ter-btn" href="{$terral_tienda}">Empezar a comprar</a>
    </div>
  </section>

  <section class="ter-faq" aria-labelledby="ter-faq-titulo">
    <div class="ter-faq__cab">
      <p class="ter-ceja ter-ceja--oscura">Antes de pedir</p>
      <h2 class="ter-titulo" id="ter-faq-titulo">Preguntas <em>frecuentes</em></h2>
      <p>Lo que más nos preguntan quienes compran piezas hechas a mano por primera vez.</p>
    </div>
    <div class="ter-faq__lista">
      <details class="ter-revela" open><summary>¿Las piezas son todas iguales?</summary><p>No del todo. Están hechas a mano en tandas pequeñas, así que el tono de un lino o la forma de un jarrón pueden variar un poco de una pieza a otra.</p></details>
      <details class="ter-revela"><summary>¿Cuánto tarda y cuánto cuesta el envío?</summary><p>El envío estándar llega en 48-72 h y cuesta 4,95 €; es gratis a partir de 60 €. El exprés llega en 24 h por 9,95 €.</p></details>
      <details class="ter-revela"><summary>¿Y si no queda bien en casa?</summary><p>Tienes 30 días para cambiarlo o devolverlo. Nos escribes, lo recogemos y te devolvemos el importe cuando llega al taller.</p></details>
      <details class="ter-revela"><summary>¿Cómo se cuidan el lino y el yute?</summary><p>El lino se lava a 30 grados y no hace falta plancharlo. El yute se sacude o se aspira y se mantiene lejos de la humedad. Cada producto lleva sus cuidados en la ficha técnica.</p></details>
      <details class="ter-revela"><summary>¿Tengo que crear una cuenta para comprar?</summary><p>No. Puedes comprar como invitado, solo con tu correo y la dirección de entrega.</p></details>
    </div>
  </section>
</div>
