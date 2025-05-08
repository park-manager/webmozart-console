<?php

/*
 * This file is part of the webmozart/console package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webmozart\Console\Tests\Api\Args\Format;

use Webmozart\Console\Tests\TestCase as PHPUnit_Framework_TestCase;
use Webmozart\Console\Api\Args\Format\Argument;

/**
 * @since  1.0
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class ArgumentTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $argument = new Argument('argument');

        $this->assertSame('argument', $argument->getName());
        $this->assertFalse($argument->isRequired());
        $this->assertTrue($argument->isOptional());
        $this->assertFalse($argument->isMultiValued());
        $this->assertNull($argument->getDefaultValue());
        $this->assertNull($argument->getDescription());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFailIfNameNull()
    {
        new Argument(null);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFailIfNameEmpty()
    {
        new Argument('');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFailIfNameNoString()
    {
        new Argument(1234);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFailIfNameContainsSpaces()
    {
        new Argument('foo bar');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFailIfNameStartsWithHyphen()
    {
        new Argument('-argument');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFailIfNameDoesNotStartWithLetter()
    {
        new Argument('1argument');
    }

    /**
     * @dataProvider getInvalidFlagCombinations
     * @expectedException \InvalidArgumentException
     */
    public function testFailIfInvalidFlagCombination($flags)
    {
        new Argument('argument', $flags);
    }

    public function getInvalidFlagCombinations()
    {
        return [
            [Argument::REQUIRED | Argument::OPTIONAL],
            [Argument::STRING | Argument::BOOLEAN],
            [Argument::STRING | Argument::INTEGER],
            [Argument::STRING | Argument::FLOAT],
            [Argument::BOOLEAN | Argument::INTEGER],
            [Argument::BOOLEAN | Argument::FLOAT],
            [Argument::INTEGER | Argument::FLOAT],
        ];
    }

    public function testRequiredArgument()
    {
        $argument = new Argument('argument', Argument::REQUIRED);

        $this->assertSame('argument', $argument->getName());
        $this->assertTrue($argument->isRequired());
        $this->assertFalse($argument->isOptional());
        $this->assertFalse($argument->isMultiValued());
        $this->assertNull($argument->getDefaultValue());
        $this->assertNull($argument->getDescription());
    }

    /**
     * @expectedException \Webmozart\Console\Api\Args\Format\InvalidValueException
     */
    public function testFailIfRequiredArgumentAndDefaultValue()
    {
        new Argument('argument', Argument::REQUIRED, null, 'Default');
    }

    public function testOptionalArgument()
    {
        $argument = new Argument('argument', Argument::OPTIONAL);

        $this->assertSame('argument', $argument->getName());
        $this->assertFalse($argument->isRequired());
        $this->assertTrue($argument->isOptional());
        $this->assertFalse($argument->isMultiValued());
        $this->assertNull($argument->getDefaultValue());
        $this->assertNull($argument->getDescription());
    }

    public function testOptionalArgumentWithDefaultValue()
    {
        $argument = new Argument('argument', Argument::OPTIONAL, null, 'Default');

        $this->assertSame('argument', $argument->getName());
        $this->assertFalse($argument->isRequired());
        $this->assertTrue($argument->isOptional());
        $this->assertFalse($argument->isMultiValued());
        $this->assertSame('Default', $argument->getDefaultValue());
        $this->assertNull($argument->getDescription());
    }

    public function testMultiValuedArgument()
    {
        $argument = new Argument('argument', Argument::MULTI_VALUED);

        $this->assertSame('argument', $argument->getName());
        $this->assertFalse($argument->isRequired());
        $this->assertTrue($argument->isOptional());
        $this->assertTrue($argument->isMultiValued());
        $this->assertSame([], $argument->getDefaultValue());
        $this->assertNull($argument->getDescription());
    }

    public function testRequiredMultiValuedArgument()
    {
        $argument = new Argument('argument', Argument::MULTI_VALUED | Argument::REQUIRED);

        $this->assertSame('argument', $argument->getName());
        $this->assertTrue($argument->isRequired());
        $this->assertFalse($argument->isOptional());
        $this->assertTrue($argument->isMultiValued());
        $this->assertSame([], $argument->getDefaultValue());
        $this->assertNull($argument->getDescription());
    }

    public function testOptionalMultiValuedArgument()
    {
        $argument = new Argument('argument', Argument::MULTI_VALUED | Argument::OPTIONAL);

        $this->assertSame('argument', $argument->getName());
        $this->assertFalse($argument->isRequired());
        $this->assertTrue($argument->isOptional());
        $this->assertTrue($argument->isMultiValued());
        $this->assertSame([], $argument->getDefaultValue());
        $this->assertNull($argument->getDescription());
    }

    public function testMultiValuedArgumentWithDefaultValue()
    {
        $argument = new Argument('argument', Argument::MULTI_VALUED, null, ['one', 'two']);

        $this->assertSame('argument', $argument->getName());
        $this->assertFalse($argument->isRequired());
        $this->assertTrue($argument->isOptional());
        $this->assertTrue($argument->isMultiValued());
        $this->assertSame(['one', 'two'], $argument->getDefaultValue());
        $this->assertNull($argument->getDescription());
    }

    /**
     * @expectedException \Webmozart\Console\Api\Args\Format\InvalidValueException
     */
    public function testFailIfMultiValuedAndDefaultValueNoArray()
    {
        new Argument('argument', Argument::MULTI_VALUED, null, 'foobar');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFailIfFlagsNoInt()
    {
        new Argument('argument', '0');
    }

    public function testSetDescription()
    {
        $argument = new Argument('argument', 0, 'Description');

        $this->assertSame('Description', $argument->getDescription());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFailIfDescriptionEmpty()
    {
        new Argument('argument', 0, '');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFailIfDescriptionNoString()
    {
        new Argument('argument', 0, 1234);
    }

    /**
     * @dataProvider getValidParseValueTests
     */
    public function testParseValue($flags, $input, $output)
    {
        $argument = new Argument('argument', $flags);

        $this->assertSame($output, $argument->parseValue($input));
    }

    public function getValidParseValueTests()
    {
        return [
            [0, '', ''],
            [0, 'string', 'string'],
            [0, '1', '1'],
            [0, '1.23', '1.23'],
            [0, 'null', 'null'],
            [Argument::NULLABLE, 'null', null],
            [0, 'true', 'true'],
            [0, 'false', 'false'],

            [Argument::STRING, '', ''],
            [Argument::STRING, 'string', 'string'],
            [Argument::STRING, '1', '1'],
            [Argument::STRING, '1.23', '1.23'],
            [Argument::STRING, 'null', 'null'],
            [Argument::STRING | Argument::NULLABLE, 'null', null],
            [Argument::STRING, 'true', 'true'],
            [Argument::STRING, 'false', 'false'],

            [Argument::BOOLEAN, 'true', true],
            [Argument::BOOLEAN, 'false', false],
            [Argument::BOOLEAN | Argument::NULLABLE, 'null', null],

            [Argument::INTEGER, '1', 1],
            [Argument::INTEGER, '1.23', 1],
            [Argument::INTEGER, '0', 0],
            [Argument::INTEGER | Argument::NULLABLE, 'null', null],

            [Argument::FLOAT, '1', 1.0],
            [Argument::FLOAT, '1.23', 1.23],
            [Argument::FLOAT, '0', 0.0],
            [Argument::FLOAT | Argument::NULLABLE, 'null', null],
        ];
    }

    /**
     * @dataProvider getInvalidParseValueTests
     * @expectedException \Webmozart\Console\Api\Args\Format\InvalidValueException
     */
    public function testParseValueFailsIfInvalid($flags, $input)
    {
        $argument = new Argument('argument', $flags);

        $argument->parseValue($input);
    }

    public function getInvalidParseValueTests()
    {
        return [
            [Argument::BOOLEAN, 'null'],
            [Argument::INTEGER, 'null'],
            [Argument::FLOAT, 'null'],
        ];
    }
}
