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
 * @author  Evert Harmeling <evert.harmeling@freshheads.com>
 */
class Twitter extends Provider
{
    /**
     * {@inheritdoc}
     */
    public function __construct($endpoint = null, array $schemes = array(), $url = null, $name = null)
    {
        return parent::__construct(
            'https://api.twitter.com/1/statuses/oembed.json',
            array(
                'http://twitter.com/*/status/*',
                'https://twitter.com/*/status/*',
            ),
            'http://twitter.com',
            'Twitter'
        );
    }
}
