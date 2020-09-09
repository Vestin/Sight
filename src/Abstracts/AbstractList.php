<?php

/*
 * This file is part of the bardoqi/sight package.
 *
 * (c) BardoQi <67158925@qq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bardoqi\Sight\Abstracts;

use ArrayAccess;
use Countable;
use Iterator;

/**
 * Class AbstractList
 *
 * @package Bardoqi\Sight\Abstracts
 */
abstract class AbstractList implements ArrayAccess,Iterator,Countable
{

    /**
     * @var array
     */
    protected $data;

    /**
     * FieldMapping Constructor
     *
     *
     */
    public function __construct()
    {

    }

    /**
     *
     * @return \Bardoqi\Sight\Mapping\FieldMapping
     */
    public static function of(){
        return new static();
    }

    /**
     * Assigns a value to the specified offset
     *
     * @param string $offset ,The offset to assign the value to
     * @param mixed  $value ,The value to set
     * @access public
     * @abstracting ArrayAccess
     */
    public function offsetSet($offset,$value) {
        $this->data[$offset] = $value;

    }

    /**
     * Whether or not an offset exists
     *
     * @param string $offset ,An offset to check for
     * @access public
     * @return boolean
     * @abstracting ArrayAccess
     */
    public function offsetExists($offset) {
        return isset($this->data[$offset]);
    }

    /**
     * Unsets an offset
     *
     * @param string $offset ,The offset to unset
     * @access public
     * @abstracting ArrayAccess
     */
    public function offsetUnset($offset) {
        if ($this->offsetExists($offset)) {
            unset($this->data[$offset]);
        }
    }

    /**
     * Returns the value at specified offset
     *
     * @param string $offset ,The offset to retrieve
     * @access public
     * @return mixed
     * @abstracting ArrayAccess
     */
    public function offsetGet($offset) {
        return $this->offsetExists($offset) ? $this->data[$offset] : null;
    }

    /**
     * @return mixed
     */
    public function rewind() {
        return reset( $this->data);
    }

    /**
     * @return mixed
     */
    public function current() {
        return current( $this->data);
    }

    /**
     * @return mixed
     */
    public function key() {
        return key($this->data);
    }

    /**
     * @return mixed
     */
    public function next() {
        return next($this->data);
    }

    /**
     * @return bool
     */
    public function valid() {
        return key($this->data) !== null;
    }

}