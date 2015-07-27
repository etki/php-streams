<?php

namespace Etki\Kit\Stream;

use Etki\Specification\Api\Stream\Exception\ClosedStreamOperationException;
use Etki\Specification\Api\Stream\Exception\InvalidDataArrayContentException;
use Etki\Specification\Api\Stream\StreamInterface;

/**
 * Some basic repeated functionality.
 *
 * @version 0.1.0
 * @since   0.1.0
 * @package Etki\Kit\Stream
 * @author  Etki <etki@etki.name>
 */
abstract class AbstractStream implements StreamInterface
{
    /**
     * Flag that represents opened/closed stream state.
     *
     * @type bool
     * @since 0.1.0
     */
    private $isOpened = false;

    /**
     * Tells if stream is closed.
     *
     * @return bool
     * @since 0.1.0
     */
    public function isClosed() : bool
    {
        return !$this->isOpened;
    }

    /**
     * Sets stream as active.
     *
     * @return void
     * @since 0.1.0
     */
    protected function setOpened()
    {
        $this->isOpened = true;
    }

    /**
     * Sets stream as inactive.
     *
     * @return void
     * @since 0.1.0
     */
    protected function setClosed()
    {
        $this->isOpened = false;
    }

    /**
     * Validates byte array contents.
     *
     * @param int[] $bytes Byte array.
     *
     * @throws InvalidDataArrayContentException
     *
     * @return void
     * @since 0.1.0
     */
    protected function assertByteArrayIsValid(array &$bytes)
    {
        foreach ($bytes as $byte) {
            if (!is_int($byte)) {
                $format = 'Provided data array contained value of type `%s`, ' .
                    'while only integer byte representation [0-255] is ' .
                    'allowed.';
                $message = sprintf($format, gettype($byte));
                throw new InvalidDataArrayContentException($message);
            }
            if ($byte < 0 || $byte > 255) {
                $format = 'Provided data array contained value `%d`, which ' .
                    'is beyond byte value limit';
                $message = sprintf($format, $byte);
                throw new InvalidDataArrayContentException($message);
            }
        }
    }

    /**
     * Verifies that currently used stream is still alive.
     * 
     * @throws ClosedStreamOperationException
     *
     * @return void
     * @since 0.1.0
     */
    protected function assertIsOpened()
    {
        if (!$this->isClosed()) {
            throw new ClosedStreamOperationException;
        }
    }
}
