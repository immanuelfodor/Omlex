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
 * YouTube provider.
 *
 * @author Michael H. Arieli <excelwebzone@gmail.com>
 */
class YouTube extends Provider
{
    /**
     * {@inheritdoc}
     */
    public function __construct($endpoint = null, array $schemes = array(), $url = null, $name = null)
    {
        return parent::__construct(
            'http://www.youtube.com/oembed',
            array(
                'http://*.youtube.com/*',
                'https://*.youtube.com/*',
                'http://*.youtu.be/*',
                'https://*.youtu.be/*',
            ),
            'http://www.youtube.com',
            'YouTube'
        );
    }
}
