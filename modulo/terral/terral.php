<?php
/**
 * Terral: módulo a medida de la tienda de ejemplo Terral (hogar y decoración mediterránea).
 * Portada propia, "Compra el look", aviso de tienda de prueba, pago de prueba y estilos.
 * Hecho por Dania360.
 */

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/classes/TerralDatos.php';

class Terral extends PaymentModule
{
    public function __construct()
    {
        $this->name = 'terral';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Dania360';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => _PS_VERSION_];
        $this->currencies = true;
        $this->currencies_mode = 'checkbox';
        parent::__construct();
        $this->displayName = 'Terral';
        $this->description = 'Portada, compra el look, estilos y pago de prueba de la tienda de ejemplo Terral.';
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayHeader')
            && $this->registerHook('displayHome')
            && $this->registerHook('displayAfterBodyOpeningTag')
            && $this->registerHook('displayProductAdditionalInfo')
            && $this->registerHook('displayFooterAfter')
            && $this->registerHook('paymentOptions')
            && $this->registerHook('displayPaymentReturn');
    }

    public function hookDisplayHeader()
    {
        $this->context->controller->registerStylesheet('terral-css', 'modules/' . $this->name . '/views/css/terral.css', ['media' => 'all', 'priority' => 900]);
        $this->context->controller->registerJavascript('terral-js', 'modules/' . $this->name . '/views/js/terral.js', ['position' => 'bottom', 'priority' => 900]);
        return '<link rel="preload" href="' . $this->_path . 'views/fonts/instrument-serif.woff2" as="font" type="font/woff2" crossorigin>'
            . '<meta name="robots" content="noindex, nofollow">';
    }

    public function hookDisplayAfterBodyOpeningTag()
    {
        return '<div class="terral-aviso">Tienda de demostración hecha por <a href="https://tiendas.dania360.com/">Dania360</a>: puedes comprar de prueba, no se cobra nada ni se envía nada.</div>';
    }

    public function hookDisplayHome()
    {
        $destacados = '';
        $modulo = Module::getInstanceByName('ps_featuredproducts');
        if ($modulo && $modulo->active) {
            $destacados = $modulo->renderWidget('displayHome', []);
        }

        $look = [];
        foreach (TerralDatos::look() as $punto) {
            $id = (int) Product::getIdByReference($punto['ref']);
            if (!$id) {
                continue;
            }
            $p = new Product($id, false, $this->context->language->id);
            $portada = Product::getCover($id);
            $look[] = [
                'x' => $punto['x'],
                'y' => $punto['y'],
                'nombre' => $p->name,
                'precio' => $this->context->getCurrentLocale()->formatPrice(Product::getPriceStatic($id, true), $this->context->currency->iso_code),
                'url' => $this->context->link->getProductLink($p),
                'img' => $portada ? $this->context->link->getImageLink($p->link_rewrite, $id . '-' . $portada['id_image'], 'home_default') : '',
                'nota' => $punto['nota'],
            ];
        }

        $categorias = [];
        foreach (TerralDatos::categorias() as $clave => $cat) {
            $idCat = (int) Configuration::get('TERRAL_CAT_' . strtoupper($clave));
            if ($idCat) {
                $categorias[] = [
                    'nombre' => $cat['nombre'],
                    'texto' => $cat['corta'],
                    'url' => $this->context->link->getCategoryLink($idCat),
                    'img' => $this->_path . 'views/img/productos/' . $cat['foto'],
                ];
            }
        }

        $this->context->smarty->assign([
            'terral_ruta' => $this->_path,
            'terral_destacados' => $destacados,
            'terral_look' => $look,
            'terral_categorias' => $categorias,
            'terral_tienda' => $this->context->link->getCategoryLink((int) Configuration::get('PS_HOME_CATEGORY')),
        ]);

        return $this->fetch('module:terral/views/templates/hook/portada.tpl');
    }

    public function hookDisplayProductAdditionalInfo($params)
    {
        return '<ul class="terral-ventajas-ficha">'
            . '<li><b>Envío gratis</b> desde 60 €</li>'
            . '<li><b>Hecho a mano</b> por talleres del Mediterráneo</li>'
            . '<li><b>30 días</b> para cambios y devoluciones</li>'
            . '</ul>';
    }

    public function hookDisplayFooterAfter()
    {
        return '<div class="terral-pie-base">© Terral · Tienda de ejemplo hecha por <a href="https://tiendas.dania360.com/">Dania360</a> con PrestaShop · Fotos creadas para la marca</div>';
    }

    /* ---------- Pago de prueba ---------- */

    public function hookPaymentOptions($params)
    {
        if (!$this->active) {
            return [];
        }
        $opcion = new PaymentOption();
        $opcion->setModuleName($this->name)
            ->setCallToActionText('Pago de prueba')
            ->setAction($this->context->link->getModuleLink($this->name, 'validacion', [], true))
            ->setAdditionalInformation('<p class="terral-pago-info">Tienda de demostración: no se pide tarjeta y no se cobra nada. Al confirmar verás cómo termina un pedido real.</p>');
        return [$opcion];
    }

    public function hookDisplayPaymentReturn($params)
    {
        return '<p class="terral-pago-info">Este pedido es de prueba: no se ha cobrado nada y no se enviará nada.</p>';
    }
}
