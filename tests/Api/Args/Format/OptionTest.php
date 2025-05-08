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
use Webmozart\Console\Api\Args\Format\Option;

/**
 * @since  1.0
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class OptionTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $option = new Option('option');

        $this->assertSame('option', $option->getLongName());
        $this->assertNull($option->getShortName());
        $this->assertTrue($option->isLongNamePreferred());
        $this->assertFalse($option->isShortNamePreferred());
        $this->assertFalse($option->acceptsValue());
        $this->assertFalse($option->isValueRequired());
        $this->assertFalse($option->isValueOptional());
        $this->assertFalse($option->isMultiValued());
        $this->assertNull($option->getDefaultValue());
        $this->assertNull($option->getDescription());
        $this->assertSame('...', $option->getValueName());
    }

    public function testDashedLongName()
    {
        $option = new Option('--option');

        $this->assertSame('option', $option->getLongName());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFailIfLongNameNull()
    {
        new Option(null);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFailIfLongNameEmpty()
    {
        new Option('');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFailIfLongNameNoString()
    {
        new Option(1234);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFailIfLongNameContainsOneCharacterOnly()
    {
        new Option('f');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFailIfLongNameContainsSpaces()
    {
        new Option('foo bar');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFailIfLongNameStartsWithSingleHyphen()
    {
        new Option('-option');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFailIfLongNameStartsWithThreeHyphens()
    {
        new Option('---option');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFailIfLongNameDoesNotStartWithLetter()
    {
        new Option('1option');
    }

    /**
     * @dataProvider getInvalidFlagCombinations
     * @expectedException \InvalidArgumentException
     */
    public function testFailIfInvalidFlagCombination($flags)
    {
        new Option('option', 'o', $flags);
    }

    public function getInvalidFlagCombinations()
    {
        return [
            [Option::NO_VALUE | Option::OPTIONAL_VALUE],
            [Option::NO_VALUE | Option::REQUIRED_VALUE],
            [Option::NO_VALUE | Option::MULTI_VALUED],
            [Option::OPTIONAL_VALUE | Option::MULTI_VALUED],
            [Option::PREFER_SHORT_NAME | Option::PREFER_LONG_NAME],
            [Option::STRING | Option::BOOLEAN],
            [Option::STRING | Option::INTEGER],
            [Option::STRING | Option::FLOAT],
            [Option::BOOLEAN | Option::INTEGER],
            [Option::BOOLEAN | Option::FLOAT],
            [Option::INTEGER | Option::FLOAT],
        ];
    }

    public function testShortName()
    {
        $option = new Option('option', 'o');

        $this->assertSame('o', $option->getShortName());
    }

    public function testDashedShortName()
    {
        $option = new Option('option', '-o');

        $this->assertSame('o', $option->getShortName());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFailIfShortNameEmpty()
    {
        new Option('option', '');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFailIfShortNameNoString()
    {
        new Option('option', 1234);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFailIfShortNameLongerThanOneLetter()
    {
        new Option('option', 'ab');
    }

    public function testNoValue()
    {
        $option = new Option('option', null, Option::NO_VALUE);

        $this->assertFalse($option->acceptsValue());
        $this->assertFalse($option->isValueRequired());
        $this->assertFalse($option->isValueOptional());
        $this->assertFalse($option->isMultiValued());
        $this->assertNull($option->getDefaultValue());
    }

    /**
     * @expectedException \Webmozart\Console\Api\Args\Format\InvalidValueException
     */
    public function testFailIfNoValueAndDefaultValue()
    {
        new Option('option', null, Option::NO_VALUE, null, 'Default');
    }

    public function testOptionalValue()
    {
        $option = new Option('option', null, Option::OPTIONAL_VALUE);

        $this->assertTrue($option->acceptsValue());
        $this->assertFalse($option->isValueRequired());
        $this->assertTrue($option->isValueOptional());
        $this->assertFalse($option->isMultiValued());
        $this->assertNull($option->getDefaultValue());
    }

    public function testOptionalValueWithDefaultValue()
    {
        $option = new Option('option', null, Option::OPTIONAL_VALUE, null, 'Default');

        $this->assertTrue($option->acceptsValue());
        $this->assertFalse($option->isValueRequired());
        $this->assertTrue($option->isValueOptional());
        $this->assertFalse($option->isMultiValued());
        $this->assertSame('Default', $option->getDefaultValue());
    }

    public function testRequiredValue()
    {
        $option = new Option('option', null, Option::REQUIRED_VALUE);

        $this->assertTrue($option->acceptsValue());
        $this->assertTrue($option->isValueRequired());
        $this->assertFalse($option->isValueOptional());
        $this->assertFalse($option->isMultiValued());
        $this->assertNull($option->getDefaultValue());
    }

    public function testRequiredValueWithDefaultValue()
    {
        $option = new Option('option', null, Option::REQUIRED_VALUE, null, 'Default');

        $this->assertTrue($option->acceptsValue());
        $this->assertTrue($option->isValueRequired());
        $this->assertFalse($option->isValueOptional());
        $this->assertFalse($option->isMultiValued());
        $this->assertSame('Default', $option->getDefaultValue());
    }

    public function testMultiValued()
    {
        $option = new Option('option', null, Option::MULTI_VALUED);

        $this->assertTrue($option->acceptsValue());
        $this->assertTrue($option->isValueRequired());
        $this->assertFalse($option->isValueOptional());
        $this->assertTrue($option->isMultiValued());
        $this->assertSame([], $option->getDefaultValue());
    }

    public function testMultiValuedWithDefaultValue()
    {
        $option = new Option('option', null, Option::MULTI_VALUED, null, ['one', 'two']);

        $this->assertTrue($option->acceptsValue());
        $this->assertTrue($option->isValueRequired());
        $this->assertFalse($option->isValueOptional());
        $this->assertTrue($option->isMultiValued());
        $this->assertSame(['one', 'two'], $option->getDefaultValue());
    }

    /**
     * @expectedException \Webmozart\Console\Api\Args\Format\InvalidValueException
     */
    public function testFailIfMultiValuedAndDefaultValueNoArray()
    {
        new Option('option', null, Option::MULTI_VALUED, null, 'foo');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFailIfFlagsNoInt()
    {
        new Option('option', null, '0');
    }

    public function testSetDescription()
    {
        $option = new Option('option', null, 0, 'Description');

        $this->assertSame('Description', $option->getDescription());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFailIfDescriptionNoString()
    {
        new Option('option', null, 0, 1234);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFailIfDescriptionEmpty()
    {
        new Option('option', null, 0, '');
    }

    public function testSetValueName()
    {
        $option = new Option('option', null, 0, null, null, 'value-name');

        $this->assertSame('value-name', $option->getValueName());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFailIfValueNameNoString()
    {
        new Option('option', null, 0, null, null, 1234);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFailIfValueNameNull()
    {
        new Option('option', null, 0, null, null, null);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFailIfValueNameEmpty()
    {
        new Option('option', null, 0, null, null, '');
    }

    public function testPreferLongName()
    {
        $option = new Option('option', null, Option::PREFER_LONG_NAME);

        $this->assertTrue($option->isLongNamePreferred());
        $this->assertFalse($option->isShortNamePreferred());
    }

    public function testPreferShortName()
    {
        $option = new Option('option', 'o', Option::PREFER_SHORT_NAME);

        $this->assertFalse($option->isLongNamePreferred());
        $this->assertTrue($option->isShortNamePreferred());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFailIfNoShortNameAndPreferShortName()
    {
        new Option('option', null, Option::PREFER_SHORT_NAME);
    }

    /**
     * @dataProvider getValidParseValueTests
     */
    public function testParseValue($flags, $input, $output)
    {
        $option = new Option('option', 'o', $flags);

        $this->assertSame($output, $option->parseValue($input));
    }

    public function getValidParseValueTests()
    {
        return [
            [0, '', ''],
            [0, 'string', 'string'],
            [0, '1', '1'],
            [0, '1.23', '1.23'],
            [0, 'null', 'null'],
            [Option::NULLABLE, 'null', null],
            [0, 'true', 'true'],
            [0, 'false', 'false'],

            [Option::STRING, '', ''],
            [Option::STRING, 'string', 'string'],
            [Option::STRING, '1', '1'],
            [Option::STRING, '1.23', '1.23'],
            [Option::STRING, 'null', 'null'],
            [Option::STRING | Option::NULLABLE, 'null', null],
            [Option::STRING, 'true', 'true'],
            [Option::STRING, 'false', 'false'],

            [Option::BOOLEAN, 'true', true],
            [Option::BOOLEAN, 'false', false],
            [Option::BOOLEAN | Option::NULLABLE, 'null', null],

            [Option::INTEGER, '1', 1],
            [Option::INTEGER, '1.23', 1],
            [Option::INTEGER, '0', 0],
            [Option::INTEGER | Option::NULLABLE, 'null', null],

            [Option::FLOAT, '1', 1.0],
            [Option::FLOAT, '1.23', 1.23],
            [Option::FLOAT, '0', 0.0],
            [Option::FLOAT | Option::NULLABLE, 'null', null],
        ];
    }

    /**
     * @dataProvider getInvalidParseValueTests
     * @expectedException \Webmozart\Console\Api\Args\Format\InvalidValueException
     */
    public function testParseValueFailsIfInvalid($flags, $input)
    {
        $option = new Option('option', 'o', $flags);

        $option->parseValue($input);
    }

    public function getInvalidParseValueTests()
    {
        return [
            [Option::BOOLEAN, 'null'],
            [Option::INTEGER, 'null'],
            [Option::FLOAT, 'null'],
        ];
    }
}
