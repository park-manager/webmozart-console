<?php

/*
 * This file is part of the webmozart/console package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webmozart\Console\Tests\Args;

use Webmozart\Console\Tests\TestCase as PHPUnit_Framework_TestCase;
use Webmozart\Console\Args\StringArgs;

/**
 * @since  1.0
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class StringArgsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getStringsToParse
     */
    public function testCreate($string, array $tokens)
    {
        $args = new StringArgs($string);

        $this->assertSame($tokens, $args->getTokens());
    }

    public function getStringsToParse()
    {
        return [
            ['', []],
            ['foo', ['foo']],
            ['  foo  bar  ', ['foo', 'bar']],
            ['"quoted"', ['quoted']],
            ["'quoted'", ['quoted']],
            ["'a\rb\nc\td'", ["a\rb\nc\td"]],
            ["'a'\r'b'\n'c'\t'd'", ['a', 'b', 'c', 'd']],
            ['"quoted \'twice\'"', ['quoted \'twice\'']],
            ["'quoted \"twice\"'", ['quoted "twice"']],
            ['"quoted \'three \"times\"\'"', ['quoted \'three "times"\'']],
            ["'quoted \"three 'times'\"'", ['quoted "three \'times\'"']],
            ["\\'escaped\\'", ['\'escaped\'']],
            ['\"escaped\"', ['"escaped"']],
            ["\\'escaped more\\'", ['\'escaped', 'more\'']],
            ['\"escaped more\"', ['"escaped', 'more"']],
            ['-a', ['-a']],
            ['-azc', ['-azc']],
            ['-awithavalue', ['-awithavalue']],
            ['-a"foo bar"', ['-afoo bar']],
            ['-a"foo bar""foo bar"', ['-afoo barfoo bar']],
            ['-a\'foo bar\'', ['-afoo bar']],
            ['-a\'foo bar\'\'foo bar\'', ['-afoo barfoo bar']],
            ['-a\'foo bar\'"foo bar"', ['-afoo barfoo bar']],
            ['--long-option', ['--long-option']],
            ['--long-option=foo', ['--long-option=foo']],
            ['--long-option="foo bar"', ['--long-option=foo bar']],
            ['--long-option="foo bar""another"', ['--long-option=foo baranother']],
            ['--long-option=\'foo bar\'', ['--long-option=foo bar']],
            ["--long-option='foo bar''another'", ['--long-option=foo baranother']],
            ["--long-option='foo bar'\"another\"", ['--long-option=foo baranother']],
            ['foo -a -ffoo --long bar', ['foo', '-a', '-ffoo', '--long', 'bar']],
            ['\\\' \\"', ['\'', '"']],
        ];
    }
}
