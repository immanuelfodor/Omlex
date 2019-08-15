<?php

/*
 * This file is part of the Omlex library.
 *
 * (c) Michael H. Arieli <excelwebzone@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Omlex;

use Omlex\Exception\NoSupportException;
use Omlex\Exception\ObjectException;

/**
 * Base class for consuming Omlex objects.
 *
 * @author Michael H. Arieli <excelwebzone@gmail.com>
 */
abstract class OmlexObject
{
    /**
     * Valid object types
     *
     * @var array
     */
    static protected $types = array(
        'photo' => 'Photo',
        'video' => 'Video',
        'link'  => 'Link',
        'rich'  => 'Rich'
    );

    /**
     * Create an Omlex object from result
     *
     * @param OmlexObject $object Raw object returned from API
     *
     * @return OmlexObject Instance of object driver
     *
     * @throws ObjectException    On object errors
     * @throws NoSupportException When object type is not supported or unknown
     */
    static public function factory($object)
    {
        if (!isset($object->type)) {
            throw new ObjectException('The object has no type.');
        }

        $type = (string)$object->type;
        if (!isset(self::$types[$type])) {
            throw new NoSupportException(sprintf('The object type "%s" is unknown or invalid.', $type));
        }

        $class = '\\Omlex\\Object\\'.self::$types[$type];
        if (!class_exists($class)) {
            throw new ObjectException(sprintf('The object class "%s" is invalid or not found.', $class));
        }

        $instance = new $class($object);
        return $instance;
    }

    /**
     * Instantiation is not allowed
     */
    private function __construct()
    {
    }
}
