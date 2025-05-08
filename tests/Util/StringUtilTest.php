<?php

/*
 * This file is part of the webmozart/console package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webmozart\Console\Tests\Util;

use Webmozart\Console\Tests\TestCase as PHPUnit_Framework_TestCase;
use Webmozart\Console\Util\StringUtil;

/**
 * @since  1.0
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class StringUtilTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getParseStringTests
     */
    public function testParseString($input, $output, $nullable = true)
    {
        $this->assertSame($output, StringUtil::parseString($input, $nullable));
    }

    public function getParseStringTests()
    {
        return [
            ['', ''],
            ['string', 'string'],
            ['null', null],
            ['null', 'null', false],
            ['false', 'false'],
            ['true', 'true'],
            ['no', 'no'],
            ['yes', 'yes'],
            ['off', 'off'],
            ['on', 'on'],
            ['0', '0'],
            ['1', '1'],
            ['1.23', '1.23'],
            [null, null],
            [null, 'null', false],
            [true, 'true'],
            [false, 'false'],
            [0, '0'],
            [1, '1'],
            [1.23, '1.23'],
        ];
    }

    /**
     * @dataProvider getValidParseBooleanTests
     */
    public function testParseBoolean($input, $output, $nullable = true)
    {
        $this->assertSame($output, StringUtil::parseBoolean($input, $nullable));
    }

    public function getValidParseBooleanTests()
    {
        return [
            ['', false],
            ['null', null],
            ['false', false],
            ['true', true],
            ['no', false],
            ['yes', true],
            ['off', false],
            ['on', true],
            ['0', false],
            ['1', true],
            [null, null],
            [true, true],
            [false, false],
            [0, false],
            [1, true],
        ];
    }

    /**
     * @dataProvider getInvalidParseBooleanTests
     * @expectedException \Webmozart\Console\Api\Args\Format\InvalidValueException
     */
    public function testParseBooleanFailsIfInvalid($input, $nullable = true)
    {
        StringUtil::parseBoolean($input, $nullable);
    }

    public function getInvalidParseBooleanTests()
    {
        return [
            ['string'],
            ['null', false],
            ['1.23'],
            [null, false],
            [1.23],
        ];
    }

    /**
     * @dataProvider getValidParseIntegerTests
     */
    public function testParseInteger($input, $output, $nullable = true)
    {
        $this->assertSame($output, StringUtil::parseInteger($input, $nullable));
    }

    public function getValidParseIntegerTests()
    {
        return [
            ['null', null],
            ['0', 0],
            ['1', 1],
            ['1.23', 1],
            [null, null],
            [true, 1],
            [false, 0],
            [0, 0],
            [1, 1],
            [1.23, 1],
        ];
    }

    /**
     * @dataProvider getInvalidParseIntegerTests
     * @expectedException \Webmozart\Console\Api\Args\Format\InvalidValueException
     */
    public function testParseIntegerFailsIfInvalid($input, $nullable = true)
    {
        StringUtil::parseInteger($input, $nullable);
    }

    public function getInvalidParseIntegerTests()
    {
        return [
            [''],
            ['string'],
            ['null', false],
            ['false'],
            ['true'],
            ['no'],
            ['yes'],
            ['off'],
            ['on'],
            [null, false],
        ];
    }

    /**
     * @dataProvider getValidParseFloatTests
     */
    public function testParseFloat($input, $output, $nullable = true)
    {
        $this->assertSame($output, StringUtil::parseFloat($input, $nullable));
    }

    public function getValidParseFloatTests()
    {
        return [
            ['null', null],
            ['0', 0.0],
            ['1', 1.0],
            ['1.23', 1.23],
            [null, null],
            [true, 1.0],
            [false, 0.0],
            [0, 0.0],
            [1, 1.0],
            [1.23, 1.23],
        ];
    }

    /**
     * @dataProvider getInvalidParseFloatTests
     * @expectedException \Webmozart\Console\Api\Args\Format\InvalidValueException
     */
    public function testParseFloatFailsIfInvalid($input, $nullable = true)
    {
        StringUtil::parseFloat($input, $nullable);
    }

    public function getInvalidParseFloatTests()
    {
        return [
            [''],
            ['string'],
            ['null', false],
            ['false'],
            ['true'],
            ['no'],
            ['yes'],
            ['off'],
            ['on'],
            [null, false],
        ];
    }
}
