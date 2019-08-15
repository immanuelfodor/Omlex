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
 * Link object.
 *
 * @author Michael H. Arieli <excelwebzone@gmail.com>
 */
class Link extends Common
{
    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return sprintf('<a href="%s">%s</a>', $this->url, $this->title);
    }
}
