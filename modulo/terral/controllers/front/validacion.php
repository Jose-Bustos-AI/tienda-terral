<?php
/**
 * Pago de prueba: crea el pedido como pagado sin cobrar nada.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class TerralValidacionModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        $cart = $this->context->cart;
        if (!$cart->id || 0 == $cart->id_customer || 0 == $cart->id_address_delivery || 0 == $cart->id_address_invoice || !$this->module->active) {
            Tools::redirect($this->context->link->getPageLink('order', true, null, ['step' => 1]));
        }
        $customer = new Customer((int) $cart->id_customer);
        if (!Validate::isLoadedObject($customer)) {
            Tools::redirect($this->context->link->getPageLink('order', true, null, ['step' => 1]));
        }
        $total = (float) $cart->getOrderTotal(true, Cart::BOTH);
        $this->module->validateOrder(
            (int) $cart->id,
            (int) Configuration::get('PS_OS_PAYMENT'),
            $total,
            'Pago de prueba',
            null,
            [],
            (int) $this->context->currency->id,
            false,
            $customer->secure_key
        );
        Tools::redirect($this->context->link->getPageLink('order-confirmation', true, null, [
            'id_cart' => (int) $cart->id,
            'id_module' => (int) $this->module->id,
            'id_order' => (int) $this->module->currentOrder,
            'key' => $customer->secure_key,
        ]));
    }
}
