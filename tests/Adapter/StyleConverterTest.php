<?php

/*
 * This file is part of the webmozart/console package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webmozart\Console\Tests\Adapter;

use Webmozart\Console\Tests\TestCase as PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Webmozart\Console\Adapter\StyleConverter;
use Webmozart\Console\Api\Formatter\Style;

/**
 * @since  1.0
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class StyleConverterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getTestCases
     */
    public function testConvert($style, $converted)
    {
        $this->assertEquals($converted, StyleConverter::convert($style));
    }

    public function getTestCases()
    {
        return [
            [
                Style::tag('tag'),
                new OutputFormatterStyle(),
            ],
            [
                Style::noTag(),
                new OutputFormatterStyle(),
            ],
            [
                Style::noTag()->fgBlack(),
                new OutputFormatterStyle('black'),
            ],
            [
                Style::noTag()->fgBlue(),
                new OutputFormatterStyle('blue'),
            ],
            [
                Style::noTag()->fgCyan(),
                new OutputFormatterStyle('cyan'),
            ],
            [
                Style::noTag()->fgGreen(),
                new OutputFormatterStyle('green'),
            ],
            [
                Style::noTag()->fgMagenta(),
                new OutputFormatterStyle('magenta'),
            ],
            [
                Style::noTag()->fgRed(),
                new OutputFormatterStyle('red'),
            ],
            [
                Style::noTag()->fgWhite(),
                new OutputFormatterStyle('white'),
            ],
            [
                Style::noTag()->fgYellow(),
                new OutputFormatterStyle('yellow'),
            ],
            [
                Style::noTag()->bgBlack(),
                new OutputFormatterStyle(null, 'black'),
            ],
            [
                Style::noTag()->bgBlue(),
                new OutputFormatterStyle(null, 'blue'),
            ],
            [
                Style::noTag()->bgCyan(),
                new OutputFormatterStyle(null, 'cyan'),
            ],
            [
                Style::noTag()->bgGreen(),
                new OutputFormatterStyle(null, 'green'),
            ],
            [
                Style::noTag()->bgMagenta(),
                new OutputFormatterStyle(null, 'magenta'),
            ],
            [
                Style::noTag()->bgRed(),
                new OutputFormatterStyle(null, 'red'),
            ],
            [
                Style::noTag()->bgWhite(),
                new OutputFormatterStyle(null, 'white'),
            ],
            [
                Style::noTag()->bgYellow(),
                new OutputFormatterStyle(null, 'yellow'),
            ],
            [
                Style::noTag()->bold(),
                new OutputFormatterStyle(null, null, ['bold']),
            ],
            [
                Style::noTag()->underlined(),
                new OutputFormatterStyle(null, null, ['underscore']),
            ],
            [
                Style::noTag()->inverse(),
                new OutputFormatterStyle(null, null, ['reverse']),
            ],
            [
                Style::noTag()->blinking(),
                new OutputFormatterStyle(null, null, ['blink']),
            ],
            [
                Style::noTag()->hidden(),
                new OutputFormatterStyle(null, null, ['conceal']),
            ],
            [
                Style::noTag()->fgWhite()->bgBlack()->bold()->hidden(),
                new OutputFormatterStyle('white', 'black', ['bold', 'conceal']),
            ],
        ];
    }
}
