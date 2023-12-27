<?php

/**
 * Copyright (c) 2020 Magic Data Programs SL. Todos los Derechos Reservados.
 *
 */

class DatosLlamadaTpvCli {

    public $merchant_user_nr;
    public $terminal_nr;
    public $amount;
    public $product;
    public $url_ok;
    public $url_fail;
    public $url_notif;
    public $order_number;
    public $transaction_type;
    public $merchant_name;
    public $id_operation;
    public $plugin;
    public $order_currency;

    function toJSON() {
        return json_encode(get_object_vars($this));;
    }

}
