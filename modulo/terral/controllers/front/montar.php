<?php
/**
 * Montaje de la tienda Terral por pasos.
 * index.php?fc=module&module=terral&controller=montar&k=CLAVE&paso=N
 * Solo funciona mientras exista el archivo de claves de la instalación.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class TerralMontarModuleFrontController extends ModuleFrontController
{
    const POR_PASO = 3;

    public function initContent()
    {
        $archivo = _PS_ROOT_DIR_ . '/terral-claves.php';
        $clave = is_file($archivo) ? (include $archivo)['clave'] : null;
        if (!$clave || !hash_equals($clave, (string) Tools::getValue('k'))) {
            Tools::redirect('index.php');
        }
        @set_time_limit(300);
        if ('diag' === Tools::getValue('paso')) {
            // Solo lectura: transportistas y precios por tramo, para comprobar el envío.
            $pre = _DB_PREFIX_;
            $this->responder([
                'transportistas' => Db::getInstance()->executeS("SELECT id_carrier, id_reference, name, active, deleted, is_free, shipping_method, range_behavior FROM {$pre}carrier WHERE deleted = 0"),
                'tramos' => Db::getInstance()->executeS("SELECT d.id_carrier, d.id_zone, d.price, r.delimiter1, r.delimiter2 FROM {$pre}delivery d LEFT JOIN {$pre}range_price r ON r.id_range_price = d.id_range_price"),
                'zona_es' => (int) Country::getIdZone((int) Country::getByIso('ES')),
                'coste_estandar_30' => (new Carrier((int) Configuration::get('TERRAL_TRANS_EST')))->getDeliveryPriceByPrice(30, (int) Country::getIdZone((int) Country::getByIso('ES'))),
                'gratis_desde' => Configuration::get('PS_SHIPPING_FREE_PRICE'),
            ]);
        }
        $paso = (int) Tools::getValue('paso');
        $hecho = '';
        try {
            if (0 === $paso) {
                $this->ajustes();
                $this->transportistas();
                $this->cupon();
                $hecho = 'ajustes, categorías, módulos, transportistas y cupón';
            } else {
                $lote = array_slice(TerralDatos::productos(), ($paso - 1) * self::POR_PASO, self::POR_PASO);
                if (!$lote) {
                    $this->rematar();
                    $this->responder(['listo' => true, 'hecho' => 'índice de búsqueda y cachés']);
                }
                foreach ($lote as $d) {
                    $this->producto($d);
                    $hecho .= $d['nombre'] . '; ';
                }
            }
        } catch (Throwable $e) {
            $this->responder(['error' => $e->getMessage(), 'donde' => basename($e->getFile()) . ':' . $e->getLine()]);
        }
        $this->responder(['listo' => false, 'paso' => $paso, 'hecho' => $hecho]);
    }

    private function responder($datos)
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function idioma()
    {
        return (int) Configuration::get('PS_LANG_DEFAULT');
    }

    private function texto($valor)
    {
        $t = [];
        foreach (Language::getLanguages(false) as $l) {
            $t[(int) $l['id_lang']] = $valor;
        }
        return $t;
    }

    private function ajustes()
    {
        $c = [
            'PS_SHOP_NAME' => 'Terral',
            'PS_SHOP_EMAIL' => 'hola@dania360.com',
            'PS_SHOP_CITY' => 'Murcia',
            'PS_SHOP_COUNTRY_ID' => (int) Country::getByIso('ES'),
            'PS_TAX' => 0,
            'PS_TAX_DISPLAY' => 0,
            'PS_STOCK_MANAGEMENT' => 1,
            'PS_ORDER_OUT_OF_STOCK' => 1,
            'PS_CONDITIONS' => 0,
            'PS_GUEST_CHECKOUT_ENABLED' => 1,
            'PS_CART_FOLLOWING' => 0,
            'PS_DISPLAY_QTIES' => 0,
            'PS_PRODUCTS_PER_PAGE' => 12,
            'PS_PRODUCTS_ORDER_BY' => 4,
            'HOME_FEATURED_NBR' => 8,
            'HOME_FEATURED_CAT' => (int) Configuration::get('PS_HOME_CATEGORY'),
            'HOME_FEATURED_RANDOMIZE' => 0,
            'PS_MAIL_METHOD' => 3,
            'PS_NB_DAYS_NEW_PRODUCT' => 0,
            'PSR_ICON_COLOR' => '#c0603a',
            'PSR_TEXT_COLOR' => '#1d1b18',
        ];
        foreach ($c as $k => $v) {
            Configuration::updateValue($k, $v);
        }
        Configuration::updateValue('PS_SHOP_DETAILS', $this->texto(''));
        Configuration::updateValue('PS_SHOP_ADDR1', '');

        // Logo y favicon.
        $img = _PS_MODULE_DIR_ . 'terral/views/img/';
        @copy($img . 'terral-logo.png', _PS_IMG_DIR_ . 'terral-logo.png');
        Configuration::updateValue('PS_LOGO', 'terral-logo.png');
        Configuration::updateValue('PS_LOGO_MAIL', 'terral-logo.png');
        Configuration::updateValue('PS_LOGO_INVOICE', 'terral-logo.png');
        @copy($img . 'favicon.ico', _PS_IMG_DIR_ . 'favicon.ico');
        Configuration::updateValue('PS_FAVICON', 'favicon.ico');

        // Categorías.
        $home = (int) Configuration::get('PS_HOME_CATEGORY');
        $menu = [];
        foreach (TerralDatos::categorias() as $clave => $d) {
            $id = (int) Configuration::get('TERRAL_CAT_' . strtoupper($clave));
            $cat = $id ? new Category($id) : new Category();
            $cat->name = $this->texto($d['nombre']);
            $cat->link_rewrite = $this->texto($d['url']);
            $cat->description = $this->texto('<p>' . $d['desc'] . '</p>');
            $cat->id_parent = $home;
            $cat->active = 1;
            $cat->save();
            Configuration::updateValue('TERRAL_CAT_' . strtoupper($clave), (int) $cat->id);
            $menu[] = 'CAT' . (int) $cat->id;
        }
        $inicio = new Category($home);
        $inicio->name = $this->texto('Inicio');
        $inicio->save();

        // Menú principal con nuestras categorías.
        Configuration::updateValue('MOD_BLOCKTOPMENU_ITEMS', implode(',', $menu));

        // Módulos de la demo que no queremos en la portada.
        foreach (['ps_imageslider', 'ps_customtext', 'ps_banner', 'ps_wirepayment', 'ps_checkpayment', 'ps_socialfollow', 'ps_newproducts', 'ps_bestsellers', 'ps_specials', 'productcomments'] as $nombre) {
            $m = Module::getInstanceByName($nombre);
            if ($m && Module::isInstalled($nombre)) {
                $m->disable();
            }
        }
        // Nuestros ganchos, registrados ya con la tienda en marcha (desde la línea de comandos no se asocian a la tienda).
        foreach (['displayHeader', 'displayHome', 'displayAfterBodyOpeningTag', 'displayProductAdditionalInfo', 'displayFooterAfter', 'paymentOptions', 'displayPaymentReturn'] as $gancho) {
            if (!$this->module->isRegisteredInHook($gancho)) {
                $this->module->registerHook($gancho);
            }
        }
        // Que el pago de prueba valga para el euro, España y todos los grupos de clientes.
        $idMod = (int) $this->module->id;
        $pre = _DB_PREFIX_;
        $idShop = (int) $this->context->shop->id;
        Db::getInstance()->execute("INSERT IGNORE INTO {$pre}module_currency (id_module, id_shop, id_currency) SELECT {$idMod}, {$idShop}, id_currency FROM {$pre}currency");
        Db::getInstance()->execute("INSERT IGNORE INTO {$pre}module_country (id_module, id_shop, id_country) SELECT {$idMod}, {$idShop}, id_country FROM {$pre}country WHERE active = 1");
        Db::getInstance()->execute("INSERT IGNORE INTO {$pre}module_group (id_module, id_shop, id_group) SELECT {$idMod}, {$idShop}, id_group FROM {$pre}group");
        Db::getInstance()->execute("INSERT IGNORE INTO {$pre}module_carrier (id_module, id_shop, id_reference) SELECT {$idMod}, {$idShop}, id_reference FROM {$pre}carrier WHERE deleted = 0");

        // Los destacados se pintan dentro de nuestra portada; fuera los bloques de la demo.
        foreach (['ps_featuredproducts', 'ps_specials', 'ps_newproducts', 'ps_bestsellers', 'ps_imageslider', 'ps_customtext', 'ps_banner'] as $nombre) {
            $m = Module::getInstanceByName($nombre);
            if ($m && Module::isInstalled($nombre)) {
                $m->unregisterHook('displayHome');
            }
        }

        // Envío solo a España; el DNI no es obligatorio en la dirección; provincias activas para España.
        $pre = _DB_PREFIX_;
        Db::getInstance()->execute("UPDATE {$pre}country SET need_identification_number = 0, need_zip_code = 1 WHERE iso_code = 'ES'");
        Db::getInstance()->execute("UPDATE {$pre}country SET active = IF(iso_code = 'ES', 1, 0)");
        Configuration::updateValue('PS_SHIPPING_FREE_PRICE', 0);
        Configuration::updateValue('PS_SHIPPING_FREE_WEIGHT', 0);
        Configuration::updateValue('PS_SHIPPING_HANDLING', 0);

        // Nombres de los bloques de enlaces del pie en español.
        Db::getInstance()->execute("UPDATE {$pre}link_block_lang SET name='Productos' WHERE name='Products'");
        Db::getInstance()->execute("UPDATE {$pre}link_block_lang SET name='Nuestra empresa' WHERE name='Our company'");

        // Textos de reaseguro (blockreassurance) en español y con nuestras condiciones.
        $textos = ['Envío gratis desde 60 €', 'Cambios y devoluciones en 30 días', 'Pago de prueba: no se cobra nada'];
        try {
            $filas = Db::getInstance()->executeS("SELECT id_psreassurance FROM {$pre}psreassurance ORDER BY position ASC");
        } catch (Throwable $e) {
            $filas = [];
        }
        if (is_array($filas)) {
            foreach ($filas as $i => $f) {
                if (!isset($textos[$i])) {
                    break;
                }
                Db::getInstance()->update('psreassurance_lang', ['title' => pSQL($textos[$i]), 'description' => ''], 'id_psreassurance=' . (int) $f['id_psreassurance']);
            }
        }
    }

    private function transportistas()
    {
        if (Configuration::get('TERRAL_TRANS_EST')) {
            return;
        }
        // Retirar los transportistas de la instalación.
        foreach (Carrier::getCarriers($this->idioma(), false, false, false, null, Carrier::ALL_CARRIERS) as $c) {
            $obj = new Carrier((int) $c['id_carrier']);
            $obj->deleted = 1;
            $obj->active = 0;
            $obj->update();
        }
        $zona = (int) Country::getIdZone((int) Country::getByIso('ES'));
        $estandar = $this->transportista('Envío estándar', '48-72 h laborables', [[0, 60, 4.95], [60, 1000000, 0]], $zona);
        $expres = $this->transportista('Envío exprés', '24 h laborables', [[0, 1000000, 9.95]], $zona);
        Configuration::updateValue('PS_CARRIER_DEFAULT', $estandar);
        Configuration::updateValue('TERRAL_TRANS_EST', $estandar);
        Configuration::updateValue('TERRAL_TRANS_EXP', $expres);
    }

    private function transportista($nombre, $plazo, $tramos, $zona)
    {
        $c = new Carrier();
        $c->name = $nombre;
        $c->delay = $this->texto($plazo);
        $c->active = 1;
        $c->is_free = 0;
        $c->shipping_handling = 0;
        $c->range_behavior = 0;
        $c->shipping_method = Carrier::SHIPPING_METHOD_PRICE;
        $c->id_tax_rules_group = 0;
        $c->add();
        $c->addZone($zona);
        $grupos = [];
        foreach (Group::getGroups($this->idioma()) as $g) {
            $grupos[] = (int) $g['id_group'];
        }
        $c->setGroups($grupos);
        $precios = [];
        foreach ($tramos as $t) {
            $r = new RangePrice();
            $r->id_carrier = (int) $c->id;
            $r->delimiter1 = $t[0];
            $r->delimiter2 = $t[1];
            $r->add();
            $precios[] = ['id_shop' => null, 'id_shop_group' => null, 'id_carrier' => (int) $c->id, 'id_range_price' => (int) $r->id, 'id_range_weight' => null, 'id_zone' => $zona, 'price' => $t[2]];
        }
        // Al crear cada tramo PrestaShop mete un precio 0 por zona: se borran antes de poner los buenos.
        Db::getInstance()->delete('delivery', 'id_carrier = ' . (int) $c->id);
        $c->addDeliveryPrice($precios);
        return (int) $c->id;
    }

    private function cupon()
    {
        if (CartRule::getIdByCode('BIENVENIDA10')) {
            return;
        }
        $r = new CartRule();
        $r->name = $this->texto('Bienvenida 10 %');
        $r->code = 'BIENVENIDA10';
        $r->description = 'Cupón de bienvenida de la tienda de ejemplo.';
        $r->reduction_percent = 10;
        $r->quantity = 10000;
        $r->quantity_per_user = 10000;
        $r->date_from = date('Y-m-d H:i:s', time() - 86400);
        $r->date_to = date('Y-m-d H:i:s', time() + 5 * 365 * 86400);
        $r->active = 1;
        $r->highlight = 0;
        $r->add();
    }

    private function grupoAtributos($nombre)
    {
        $idLang = $this->idioma();
        foreach (AttributeGroup::getAttributesGroups($idLang) as $g) {
            if ($g['name'] === $nombre) {
                return (int) $g['id_attribute_group'];
            }
        }
        $g = new AttributeGroup();
        $g->name = $this->texto($nombre);
        $g->public_name = $this->texto($nombre);
        $g->group_type = 'Color' === $nombre ? 'color' : 'radio';
        $g->is_color_group = 'Color' === $nombre;
        $g->add();
        return (int) $g->id;
    }

    private function valorAtributo($idGrupo, $valor)
    {
        foreach (AttributeGroup::getAttributes($this->idioma(), $idGrupo) as $a) {
            if ($a['name'] === $valor) {
                return (int) $a['id_attribute'];
            }
        }
        $colores = ['Terracota' => '#c0603a', 'Oliva' => '#6b7449', 'Arena' => '#d9c7a7', 'Crudo' => '#efe6d6', 'Teja' => '#b5532f'];
        $a = new ProductAttribute();
        $a->id_attribute_group = $idGrupo;
        $a->name = $this->texto($valor);
        $a->color = $colores[$valor] ?? '';
        $a->add();
        return (int) $a->id;
    }

    private function caracteristica($nombre, $valor, $idProducto)
    {
        $idF = (int) Feature::addFeatureImport($nombre);
        $idV = (int) FeatureValue::addFeatureValueImport($idF, $valor, $idProducto, $this->idioma(), false);
        Product::addFeatureProductImport($idProducto, $idF, $idV);
    }

    private function producto($d)
    {
        if (Product::getIdByReference($d['ref'])) {
            return;
        }
        $idCat = (int) Configuration::get('TERRAL_CAT_' . strtoupper($d['cat']));
        $home = (int) Configuration::get('PS_HOME_CATEGORY');
        $p = new Product();
        $p->name = $this->texto($d['nombre']);
        $p->link_rewrite = $this->texto(Tools::str2url($d['nombre']));
        $p->description_short = $this->texto('<p>' . $d['corta'] . '</p>');
        $p->description = $this->texto('<p>' . $d['larga'] . '</p>');
        $p->reference = $d['ref'];
        $p->price = $d['precio'];
        $p->id_tax_rules_group = 0;
        $p->id_category_default = $idCat;
        $p->active = 1;
        $p->visibility = 'both';
        $p->condition = 'new';
        $p->show_price = 1;
        $p->available_for_order = 1;
        $p->on_sale = !empty($d['oferta']) ? 1 : 0;
        $p->state = Product::STATE_SAVED;
        $p->add();
        $p->addToCategories([$idCat, $home]);

        // Foto de portada.
        $this->imagen((int) $p->id, $d['foto'], $d['nombre']);

        // Ficha técnica.
        foreach ($d['ficha'] as $nombre => $valor) {
            $this->caracteristica($nombre, $valor, (int) $p->id);
        }

        // Combinaciones.
        if (!empty($d['variantes'])) {
            $grupo = $this->grupoAtributos($d['grupo']);
            $primera = true;
            foreach ($d['variantes'] as $valor => $impacto) {
                $comb = new Combination();
                $comb->id_product = (int) $p->id;
                $comb->price = $impacto;
                $comb->reference = $d['ref'] . '-' . Tools::str2url($valor);
                $comb->default_on = $primera ? 1 : null;
                $comb->minimal_quantity = 1;
                $comb->add();
                $comb->setAttributes([$this->valorAtributo($grupo, $valor)]);
                StockAvailable::setQuantity((int) $p->id, (int) $comb->id, 50);
                $primera = false;
            }
            Product::updateDefaultAttribute((int) $p->id);
            $p->checkDefaultAttributes();
        }
        StockAvailable::setQuantity((int) $p->id, 0, 50);
        if (!empty($d['variantes'])) {
            StockAvailable::synchronize((int) $p->id);
        }

        // Ofertas: descuento en euros.
        if (!empty($d['oferta'])) {
            $sp = new SpecificPrice();
            $sp->id_product = (int) $p->id;
            $sp->id_product_attribute = 0;
            $sp->id_shop = 0;
            $sp->id_currency = 0;
            $sp->id_country = 0;
            $sp->id_group = 0;
            $sp->id_customer = 0;
            $sp->from_quantity = 1;
            $sp->price = -1;
            $sp->reduction = $d['oferta'];
            $sp->reduction_tax = 1;
            $sp->reduction_type = 'amount';
            $sp->from = '0000-00-00 00:00:00';
            $sp->to = '0000-00-00 00:00:00';
            $sp->add();
        }
        Search::indexation(false, (int) $p->id);
    }

    private function imagen($idProducto, $archivo, $alt)
    {
        $origen = _PS_MODULE_DIR_ . 'terral/views/img/productos/' . $archivo;
        $img = new Image();
        $img->id_product = $idProducto;
        $img->position = Image::getHighestPosition($idProducto) + 1;
        $img->cover = 1;
        $img->legend = $this->texto($alt);
        $img->add();
        $ruta = $img->getPathForCreation();
        ImageManager::resize($origen, $ruta . '.jpg');
        foreach (ImageType::getImagesTypes('products') as $tipo) {
            ImageManager::resize($origen, $ruta . '-' . stripslashes($tipo['name']) . '.jpg', (int) $tipo['width'], (int) $tipo['height'], 'jpg');
        }
        Hook::exec('actionWatermark', ['id_image' => $img->id, 'id_product' => $idProducto]);
    }

    private function rematar()
    {
        Search::indexation(true);
        // Ajustes de la versión: textos para buscadores, nombre de la colección y fotos recortadas.
        $this->module->actualizar();
        Tools::clearSmartyCache();
        Tools::clearXMLCache();
        Media::clearCache();
        // Ahora que existen los transportistas, el pago de prueba también vale para ellos.
        $idMod = (int) $this->module->id;
        $pre = _DB_PREFIX_;
        $idShop = (int) $this->context->shop->id;
        Db::getInstance()->execute("INSERT IGNORE INTO {$pre}module_carrier (id_module, id_shop, id_reference) SELECT {$idMod}, {$idShop}, id_reference FROM {$pre}carrier WHERE deleted = 0");
    }
}
