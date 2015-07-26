<?php

namespace Etki\Kit\Stream;

use Etki\Specification\Api\Stream\Exception\ClosedStreamOperationException;
use Etki\Specification\Api\Stream\Exception\InvalidDataArrayContentException;
use Etki\Specification\Api\Stream\Exception\InvalidPositionException;
use Etki\Specification\Api\Stream\ReadableStreamInterface;
use Etki\Specification\Api\Stream\SeekableStreamInterface;
use Etki\Specification\Api\Stream\WritableStreamInterface;

/**
 * Simplest stream implementation possible.
 *
 * @version 0.1.0
 * @since   0.1.0
 * @package Etki\Kit\Stream
 * @author  Etki <etki@etki.name>
 */
class ArrayStream implements
    ReadableStreamInterface,
    WritableStreamInterface,
    SeekableStreamInterface
{
    /**
     * Flag that reports current stream state.
     *
     * @type bool
     * @since 0.1.0
     */
    private $isActive = true;

    /**
     * Internal content.
     *
     * @type int[]
     * @since 0.1.0
     */
    private $content = [];

    /**
     * Cursor pointing to content.
     *
     * @type int
     * @since 0.1.0
     */
    private $cursor = 0;

    /**
     * Initializer.
     *
     * @param int[] $initialContent Initial set of bytes
     *
     * @since 0.1.0
     */
    public function __construct(array $initialContent = null)
    {
        if ($initialContent) {
            $this->write($initialContent);
        }
    }

    /**
     * Closes stream.
     *
     * @return void
     * @since 0.1.0
     */
    public function close()
    {
        $this->isActive = false;
    }

    /**
     * Tells if stream is already closed.
     *
     * @return bool
     * @since 0.1.0
     */
    public function isClosed()
    {
        return !$this->isActive;
    }

    /**
     * Reads bytes from stream.
     *
     * @param int $length Number of bytes to return at max.
     *
     * @return int[]
     * @since 0.1.0
     */
    public function read(int $length) : array
    {
        $this->assertIsOpened();
        $remainingLength = count($this->content) - $this->cursor;
        $length = $remainingLength > $length ? $length : $remainingLength;
        $offset = $this->cursor;
        $this->cursor += $length;
        return array_slice($this->content, $offset, $length, false);
    }

    /**
     * Writes data to stream.
     *
     * @param int[] $bytes Byte array to write.
     *
     * @return int Number of bytes written.
     * @since 0.1.0
     */
    public function write(array $bytes) : int
    {
        $this->assertIsOpened();
        $exceptionTemplate = 'ArrayStream has been provided with ' .
            'inapplicable data (%s), expecting array of integers';
        foreach ($bytes as $byte) {
            if (!is_int($byte)) {
                $message = sprintf($exceptionTemplate, gettype($byte));
                throw new InvalidDataArrayContentException($message);
            }
            if ($byte < 0 || $byte > 255) {
                $message = sprintf($exceptionTemplate, $byte);
                throw new InvalidDataArrayContentException($message);
            }
        }
        $length = count($bytes);
        array_splice($this->content, $this->cursor, $length, $bytes);
        return $length;
    }

    /**
     * Retrieves current position.
     *
     * @return int
     * @since 0.1.0
     */
    public function getPosition() : int
    {
        $this->assertIsOpened();
        return $this->cursor;
    }

    /**
     * Traverses through stream.
     *
     * @param int $position Position to traverse to.
     *
     * @return void
     * @since 0.1.0
     */
    public function seek(int $position)
    {
        $this->assertIsOpened();
        if ($position > (count($this->content) - 1) || $position < 0) {
            throw new InvalidPositionException;
        }
        $this->cursor = $position;
    }

    /**
     * Internal method to throw exception whenever somebody tries to access
     * closed stream.
     *
     * @return void
     * @since 0.1.0
     */
    private function assertIsOpened()
    {
        if (!$this->isActive) {
            throw new ClosedStreamOperationException;
        }
    }
}
