<?php

namespace Ferranfg\Base\Models;

use Darryldecode\Cart\Facades\CartFacade;

class Cart extends CartFacade
{
    /**
     * Load from session or initialize new cart.
     *
     * @var array
     */
    public static function init($alias = 'cart')
    {
        parent::session($alias);

        return parent::getContent();
    }

    /**
     * Save new product on current cart session.
     *
     * @var array
     */
    public static function add($alias = 'cart', $data)
    {
        parent::session($alias);
        parent::add($data);

        return parent::getContent();
    }

    /**
     * Remove the product on current cart session.
     *
     * @var array
     */
    public static function remove($alias = 'cart', $id)
    {
        parent::session($alias);
        parent::remove($id);

        return parent::getContent();
    }
}