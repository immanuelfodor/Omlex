<?php

/*
 * This file is part of the Omlex library.
 *
 * (c) Michael H. Arieli <excelwebzone@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Omlex\Provider;

use Omlex\Provider;

/**
 * Vimeo provider.
 *
 * @author Michael H. Arieli <excelwebzone@gmail.com>
 */
class Vimeo extends Provider
{
    /**
     * {@inheritdoc}
     */
    public function __construct($endpoint = null, array $schemes = array(), $url = null, $name = null)
    {
        return parent::__construct(
            'http://www.vimeo.com/api/oembed.json', //or xml
            array(
                'http://*.vimeo.com/*',
                'http://*.vimeo.com/groups/*/*',
            ),
            'http://www.vimeo.com',
            'Vimeo'
        );
    }
}
