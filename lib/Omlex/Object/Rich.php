<?php

/*
 * This file is part of the Omlex library.
 *
 * (c) Michael H. Arieli <excelwebzone@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Omlex\Object;

/**
 * Rich object.
 *
 * @author Michael H. Arieli <excelwebzone@gmail.com>
 */
class Rich extends Common
{
    protected $required = array(
        'html', 'width', 'height'
    );

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->html;
    }
}
