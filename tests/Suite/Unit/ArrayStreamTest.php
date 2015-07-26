<?php

namespace Etki\Kit\Stream\Test\Suite\Unit;

use Codeception\TestCase\Test;
use Etki\Kit\Stream\ArrayStream;
use UnitTester;

/**
 * Test for suimple array-based stream implementation
 *
 * @version 0.1.0
 * @since   0.1.0
 * @package Etki\Kit\Stream\Test\Suite\Unit
 * @author  Etki <etki@etki.name>
 */
class ArrayStreamTest extends Test
{
    /**
     * Tester instance.
     * 
     * @type UnitTester
     */
    protected $tester;

    /**
     * Creates test instance.
     *
     * @param int[] $content Initial content.
     *
     * @return ArrayStream
     * @since 0.1.0
     */
    private function createTestInstance(array $content = null)
    {
        return new ArrayStream($content);
    }

    // tests

    /**
     * Tests traversing.
     *
     * @return void
     * @since 0.1.0
     */
    public function testTraverse()
    {
        $testData = array_fill(0, 10, 0);
        $instance = $this->createTestInstance($testData);
        $this->assertSame(0, $instance->getPosition());
        $this->assertSame($testData, $instance->read(count($testData)));
        $this->assertSame([], $instance->read(count($testData)));
        $this->assertSame(count($testData), $instance->getPosition());
        $instance->seek(count($testData) - 1);
        $this->assertSame(count($testData) - 1, $instance->getPosition());
        $this->assertSame([0], $instance->read(count($testData)));
    }
}
