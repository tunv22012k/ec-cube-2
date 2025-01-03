<?php

namespace Plugin\ProductOptions;

use Eccube\Common\EccubeNav;

class Nav implements EccubeNav
{
    /**
     * @return array
     */
    public static function getNav()
    {
        return [
            'product' => [
                'children' => [
                    'options' => [
                        'name'  => 'admin.product.options_menu',
                        'url'   => 'plugin_admin_options_list',
                    ],
                ],
            ],
        ];
    }
}
