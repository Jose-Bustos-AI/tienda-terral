<?php
/**
 * Catálogo y textos de la tienda de ejemplo Terral.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class TerralDatos
{
    public static function categorias()
    {
        return [
            'textil' => ['nombre' => 'Textil', 'url' => 'textil', 'foto' => 'cojin.jpg', 'corta' => 'Cojines, mantas y alfombras', 'desc' => 'Lino lavado, algodón y yute: tejidos naturales que envejecen bien.'],
            'mesa' => ['nombre' => 'Mesa y cerámica', 'url' => 'mesa-y-ceramica', 'foto' => 'platos.jpg', 'corta' => 'Platos, vasos y bandejas', 'desc' => 'Gres hecho a mano, vidrio reciclado y madera de olivo para la mesa de cada día.'],
            'luz' => ['nombre' => 'Iluminación', 'url' => 'iluminacion', 'foto' => 'techo.jpg', 'corta' => 'Lámparas de techo y de mesa', 'desc' => 'Ratán, cerámica y lino: luz cálida para las noches largas de verano.'],
            'deco' => ['nombre' => 'Decoración', 'url' => 'decoracion', 'foto' => 'espejo.jpg', 'corta' => 'Jarrones, espejos y cestas', 'desc' => 'Piezas de taller que cuentan algo: fibras naturales, barro y velas.'],
        ];
    }

    /**
     * Productos. "variantes": grupo => [valor => impacto en precio]. Precios con IVA incluido.
     */
    public static function productos()
    {
        return [
            ['ref' => 'TER-COJ', 'nombre' => 'Cojín de lino lavado', 'cat' => 'textil', 'foto' => 'cojin.jpg', 'precio' => 24.90,
                'grupo' => 'Color', 'variantes' => ['Terracota' => 0, 'Oliva' => 0, 'Arena' => 0],
                'corta' => 'Lino 100 % lavado a la piedra, suave desde el primer día. Funda con cremallera oculta y relleno de fibra incluidos.',
                'larga' => 'Tejido en Portugal con lino europeo y teñido en pequeños lotes, por eso cada tanda tiene un tono ligeramente distinto. Se lava a 30 grados y no hace falta plancharlo: las arrugas son parte de su encanto.',
                'ficha' => ['Material' => 'Lino 100 %', 'Medidas' => '45 x 45 cm', 'Cuidados' => 'Lavar a 30 °C, no usar secadora']],
            ['ref' => 'TER-MAN', 'nombre' => 'Manta de algodón con flecos', 'cat' => 'textil', 'foto' => 'manta.jpg', 'precio' => 49.90,
                'grupo' => 'Color', 'variantes' => ['Crudo' => 0, 'Teja' => 0],
                'corta' => 'Algodón grueso tejido en telar con flecos anudados a mano. Para el sofá en invierno y la terraza en las noches de septiembre.',
                'larga' => 'Tejida en un telar familiar de Crevillente con algodón reciclado. Pesa lo justo para abrigar sin agobiar y se lava en la lavadora.',
                'ficha' => ['Material' => 'Algodón reciclado', 'Medidas' => '130 x 170 cm', 'Hecho en' => 'Crevillente (Alicante)']],
            ['ref' => 'TER-ALF', 'nombre' => 'Alfombra redonda de yute', 'cat' => 'textil', 'foto' => 'alfombra.jpg', 'precio' => 89.00,
                'grupo' => 'Medida', 'variantes' => ['Ø 120 cm' => 0, 'Ø 160 cm' => 50, 'Ø 200 cm' => 110],
                'corta' => 'Yute trenzado a mano en espiral. Da calidez a cualquier suelo de barro o de madera.',
                'larga' => 'Fibra de yute natural, sin tintes. Es resistente al paso diario y se limpia con aspiradora. Recomendamos una base antideslizante en suelos pulidos.',
                'ficha' => ['Material' => 'Yute natural', 'Grosor' => '1 cm', 'Cuidados' => 'Aspirar, no mojar']],
            ['ref' => 'TER-JAR', 'nombre' => 'Jarrón de cerámica blanca', 'cat' => 'deco', 'foto' => 'jarron.jpg', 'precio' => 29.90,
                'grupo' => 'Tamaño', 'variantes' => ['Mediano (22 cm)' => 0, 'Grande (32 cm)' => 10],
                'corta' => 'Torneado a mano y esmaltado en blanco mate. Perfecto con ramas de olivo o flores secas.',
                'larga' => 'Cada jarrón sale de un torno distinto, así que su forma cambia un poco de una pieza a otra. Esmalte apto para agua.',
                'ficha' => ['Material' => 'Cerámica esmaltada', 'Hecho en' => 'La Bisbal (Girona)']],
            ['ref' => 'TER-PLA', 'nombre' => 'Juego de 4 platos de gres', 'cat' => 'mesa', 'foto' => 'platos.jpg', 'precio' => 54.90, 'oferta' => 10.00,
                'corta' => 'Cuatro platos llanos de gres con esmalte arena moteado y borde terracota. Aptos para lavavajillas.',
                'larga' => 'Gres de alta temperatura, mucho más resistente que la loza. Cada plato está esmaltado a mano y las motas son únicas.',
                'ficha' => ['Material' => 'Gres', 'Medidas' => 'Ø 27 cm', 'Cuidados' => 'Apto para lavavajillas y microondas']],
            ['ref' => 'TER-VAS', 'nombre' => 'Vasos de vidrio reciclado (4)', 'cat' => 'mesa', 'foto' => 'vasos.jpg', 'precio' => 22.90,
                'corta' => 'Cuatro vasos soplados con vidrio 100 % reciclado, con sus burbujas y su tono agua.',
                'larga' => 'Soplados a mano en un taller de vidrio de Valencia. Las pequeñas burbujas son señal de que el vidrio es reciclado.',
                'ficha' => ['Material' => 'Vidrio reciclado', 'Capacidad' => '300 ml', 'Hecho en' => 'Valencia']],
            ['ref' => 'TER-BAN', 'nombre' => 'Bandeja de madera de olivo', 'cat' => 'mesa', 'foto' => 'bandeja.jpg', 'precio' => 34.90,
                'corta' => 'Tallada en una sola pieza de olivo, con su veta única. Para servir el aperitivo o el pan.',
                'larga' => 'Madera de olivos que ya no dan fruto, tratada con aceite alimentario. Lavar a mano y aceitar de vez en cuando.',
                'ficha' => ['Material' => 'Madera de olivo', 'Medidas' => '35 x 18 cm', 'Cuidados' => 'Lavar a mano']],
            ['ref' => 'TER-LMS', 'nombre' => 'Lámpara de mesa de cerámica', 'cat' => 'luz', 'foto' => 'lampara.jpg', 'precio' => 89.00,
                'corta' => 'Pie de cerámica arena y pantalla plisada de lino. Luz cálida y suave para la mesilla o el aparador.',
                'larga' => 'Incluye cable textil de 1,8 m con interruptor. Admite bombillas E27 de hasta 40 W (recomendamos LED cálida).',
                'ficha' => ['Material' => 'Cerámica y lino', 'Alto' => '42 cm', 'Bombilla' => 'E27, no incluida']],
            ['ref' => 'TER-RAT', 'nombre' => 'Lámpara de techo de ratán', 'cat' => 'luz', 'foto' => 'techo.jpg', 'precio' => 119.00, 'oferta' => 20.00,
                'corta' => 'Campana de ratán tejida a mano. Llena de sombras bonitas el salón o el comedor.',
                'larga' => 'Ratán natural tejido sobre estructura metálica. Se entrega con florón, cable de 1 m y portalámparas E27.',
                'ficha' => ['Material' => 'Ratán natural', 'Medidas' => 'Ø 45 x 38 cm', 'Bombilla' => 'E27, no incluida']],
            ['ref' => 'TER-ESP', 'nombre' => 'Espejo redondo de mimbre', 'cat' => 'deco', 'foto' => 'espejo.jpg', 'precio' => 69.90,
                'corta' => 'Marco grueso de mimbre trenzado alrededor de un espejo de 40 cm. Ilumina y da profundidad a la pared.',
                'larga' => 'Tejido a mano alrededor de un aro de madera. Incluye colgador trasero.',
                'ficha' => ['Material' => 'Mimbre y espejo', 'Medidas' => 'Ø 70 cm (espejo de 40 cm)']],
            ['ref' => 'TER-CES', 'nombre' => 'Capazo de palma', 'cat' => 'deco', 'foto' => 'cesta.jpg', 'precio' => 19.90,
                'grupo' => 'Tamaño', 'variantes' => ['Pequeño' => 0, 'Grande' => 10],
                'corta' => 'Palma natural trenzada en Elche. Para las mantas, la leña o ir al mercado.',
                'larga' => 'Trenzado a mano por artesanas de Elche con palma de la zona. Asas reforzadas.',
                'ficha' => ['Material' => 'Palma natural', 'Hecho en' => 'Elche (Alicante)']],
            ['ref' => 'TER-VEL', 'nombre' => 'Vela perfumada Higuera', 'cat' => 'deco', 'foto' => 'vela.jpg', 'precio' => 18.90,
                'corta' => 'Cera vegetal con aroma a hoja de higuera y leche de higo, en un vaso de barro reutilizable.',
                'larga' => 'Unas 40 horas de combustión. Cuando se acabe, el vaso de barro sirve para una planta pequeña.',
                'ficha' => ['Material' => 'Cera de soja y barro', 'Duración' => '40 horas aprox.']],
        ];
    }

    /** Puntos de "Compra el look" sobre la foto del salón (en %). */
    public static function look()
    {
        return [
            ['ref' => 'TER-RAT', 'x' => 43.6, 'y' => 17, 'nota' => 'Sobre la mesa baja'],
            ['ref' => 'TER-ESP', 'x' => 85, 'y' => 32, 'nota' => 'En la pared del sofá'],
            ['ref' => 'TER-COJ', 'x' => 72, 'y' => 62, 'nota' => 'Color terracota'],
            ['ref' => 'TER-JAR', 'x' => 39, 'y' => 72, 'nota' => 'Tamaño mediano'],
            ['ref' => 'TER-MAN', 'x' => 76, 'y' => 81, 'nota' => 'Color crudo'],
            ['ref' => 'TER-CES', 'x' => 89.4, 'y' => 86, 'nota' => 'Tamaño grande'],
            ['ref' => 'TER-ALF', 'x' => 26, 'y' => 92, 'nota' => 'Ø 200 cm'],
        ];
    }
}
