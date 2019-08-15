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

use Omlex\Exception\ObjectException;

/**
 * Base class for objects.
 *
 * @author Michael H. Arieli <excelwebzone@gmail.com>
 */
abstract class Common
{
    /**
     * Raw object returned from API
     *
     * @var object
     */
    protected $object = null;

    /**
     * Required fields per the specification
     *
     * @var array
     */
    protected $required = array();

    /**
     * Constructor
     *
     * @param object $object Raw object returned from the API
     *
     * @throws ObjectException on missing fields
     */
    public function __construct($object)
    {
        $this->object = $object;

        $this->required[] = 'version';

        foreach ($this->required as $field) {
            if (!property_exists($this->object, $field)) {
                throw new ObjectException(sprintf('Object is missing required "%s" attribute', $field));
            }
        }
    }

    /**
     * Get object variable
     *
     * @param string $var Variable to get
     *
     * @return mixed Attribute's value or null if it's not set/exists
     */
    public function __get($var)
    {
        if (property_exists($this->object, $var)) {
            return $this->object->$var;
        }

        return null;
    }

    /**
     * Is variable set?
     *
     * @param string $var Variable name to check
     *
     * @return Boolean True if set, false if not
     */
    public function __isset($var)
    {
        if (property_exists($this->object, $var)) {
            return (isset($this->object->$var));
        }

        return false;
    }

    /**
     * Require a sane __toString for all objects
     *
     * @return string
     */
    abstract public function __toString();
}
