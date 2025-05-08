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
use Webmozart\Console\Api\Args\Format\CommandName;

/**
 * @since  1.0
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class CommandNameTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getValidNames
     */
    public function testCreate($string)
    {
        $commandName = new CommandName($string);

        $this->assertSame($string, $commandName->toString());
    }

    /**
     * @dataProvider getValidNames
     */
    public function testCreateWithAliases($string)
    {
        $commandName = new CommandName('cmd', ['alias', $string]);

        $this->assertSame('cmd', $commandName->toString());
        $this->assertSame(['alias', $string], $commandName->getAliases());
    }

    public function testToString()
    {
        $commandName = new CommandName('cmd');

        $this->assertSame('cmd', (string) $commandName);
    }

    /**
     * @dataProvider getInvalidNames
     * @expectedException \InvalidArgumentException
     */
    public function testCreateFailsIfInvalidString($string)
    {
        new CommandName($string);
    }

    /**
     * @dataProvider getInvalidNames
     * @expectedException \InvalidArgumentException
     */
    public function testCreateFailsIfInvalidAlias($string)
    {
        new CommandName('cmd', [$string]);
    }

    public function getValidNames()
    {
        return [
            ['command'],
            ['COMMAND'],
            ['command-name'],
            ['c'],
            ['command1'],
        ];
    }

    public function getInvalidNames()
    {
        return [
            ['command_name'],
            ['command&'],
            [''],
            [null],
            [1234],
            [true],
        ];
    }

    public function testMatch()
    {
        $commandName = new CommandName('cmd', ['alias1', 'alias2']);

        $this->assertTrue($commandName->match('cmd'));
        $this->assertTrue($commandName->match('alias1'));
        $this->assertTrue($commandName->match('alias2'));
        $this->assertFalse($commandName->match('foo'));
    }
}
