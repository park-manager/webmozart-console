<?php

declare(strict_types=1);

namespace Webmozart\Console\Tests;

use PHPUnit\Framework\MockObject\MockObject;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * Returns a mock object for the specified class.
     *
     * @psalm-template RealInstanceType of object
     * @psalm-param class-string<RealInstanceType> $originalClassName
     * @psalm-return MockObject&RealInstanceType
     */
    protected function getMock(string $class): object
    {
        return $this->createMock($class);
    }

    public static function setUpBeforeClass(): void
    {
        self::doSetUpBeforeClass();
    }

    public static function tearDownAfterClass(): void
    {
        self::doTearDownAfterClass();
    }

    protected function setUp(): void
    {
        $this->doSetUp();
    }

    protected function tearDown(): void
    {
        $this->doTearDown();
    }

    protected static function doSetUpBeforeClass()
    {
        parent::setUpBeforeClass();
    }

    protected static function doTearDownAfterClass()
    {
        parent::tearDownAfterClass();
    }

    protected function doSetUp()
    {
    }

    protected function doTearDown()
    {
    }
}
