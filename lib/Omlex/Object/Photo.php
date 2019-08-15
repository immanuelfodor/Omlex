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
 * Photo object.
 *
 * @author Michael H. Arieli <excelwebzone@gmail.com>
 */
class Photo extends Common
{
    protected $required = array(
        'url', 'width', 'height'
    );

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        $title = isset($this->title) ? $this->title : null;

        return sprintf('<img src="%s" width="%s" height="%s" alt="%s" />', $this->url, $this->width, $this->height, $title);
    }
}
