<?php

namespace Etki\Kit\Stream;

use Etki\Specification\Api\Stream\Exception\RuntimeException;
use Etki\Specification\Api\Stream\ReadableStreamInterface;
use Etki\Specification\Api\Stream\SeekableStreamInterface;
use Etki\Specification\Api\Stream\WritableStreamInterface;

/**
 * File stream implementation.
 *
 * @version 0.1.0
 * @since   0.1.0
 * @package Etki\Kit\Stream
 * @author  Etki <etki@etki.name>
 */
class FileStream extends AbstractStream implements
    ReadableStreamInterface,
    WritableStreamInterface,
    SeekableStreamInterface
{
    /**
     * Shared read lock constant.
     *
     * @since 0.1.0
     */
    const SHARED_LOCK = LOCK_SH;

    /**
     * Shared exclusive lock constant.
     *
     * @since 0.1.0
     */
    const EXCLUSIVE_LOCK = LOCK_EX;
    /**
     * Shared non-blocking lock constant.
     *
     * @since 0.1.0
     */
    const NON_BLOCKING_LOCK = LOCK_NB;
    
    /**
     * Standard fopen's file handle.
     *
     * @type resource
     * @since 0.1.0
     */
    private $handle;
    /**
     * Provided file path.
     *
     * @type string
     * @since 0.1.0
     */
    private $filePath;

    /**
     * Initializer.
     * 
     * @param string $filePath    Path to file.
     * @param int    $lockingMode Lock to apply.
     * 
     * @since 0.1.0
     */
    public function __construct(string $filePath, $lockingMode = null)
    {
        $this->filePath = $filePath;
        $handle = fopen($filePath, 'a+b');
        if (!$handle) {
            $message = sprintf(
                'Couldn\'t create stream with provided file path (`%s`)',
                $filePath
            );
            throw new RuntimeException($message);
            // todo substitute with StreamCreationException
        }
        $this->handle = $handle;
        if ($lockingMode != null) {
            flock($this->handle, $lockingMode);
        }
    }

    /**
     * Returns current cursor position.
     *
     * @return int
     * @since 0.1.0
     */
    public function getPosition() : int
    {
        $this->assertIsOpened();
        return ftell($this->handle);
    }

    /**
     * Moves cursor to provided position.
     *
     * @param int $position Position to move cursor to.
     *
     * @return void
     * @since 0.1.0
     */
    public function seek(int $position)
    {
        $this->assertIsOpened();
        fseek($this->handle, $position);
    }

    /**
     * Closes stream.
     *
     * @return void
     * @since 0.1.0
     */
    public function close()
    {
        if (!$this->isClosed()) {
            flock($this->handle, LOCK_UN);
            fclose($this->handle);
            $this->handle = null;
        }
        $this->setClosed();
    }

    /**
     * Tells if stream is closed.
     *
     * @return bool
     * @since 0.1.0
     */
    public function isClosed() : bool
    {
        return !$this->handle;
    }

    /**
     * Reads data from stream.
     *
     * @param int $length How much data should be read.
     *
     * @return int[]
     * @since 0.1.0
     */
    public function read(int $length) : array
    {
        $this->assertIsOpened();
        return unpack('C*', fread($this->handle, $length));
    }

    /**
     * Tells if there's any remaining data available.
     *
     * @return bool
     * @since 0.1.0
     */
    public function hasData() : bool
    {
        $this->assertIsOpened();
        return filesize($this->filePath) - 1 > $this->getPosition();
    }

    /**
     * Writes data to stream.
     *
     * @param int[] $data Byte array.
     *
     * @return int
     * @since 0.1.0
     */
    public function write(array $data) : int
    {
        $this->assertIsOpened();
        $this->assertByteArrayIsValid($data);
        array_unshift($data, 'C*');
        return fwrite($this->handle, call_user_func_array('pack', $data));
    }

    /**
     * Simple resource releaser.
     * 
     * @since 0.1.0
     */
    public function __destruct()
    {
        $this->close();
    }
}
