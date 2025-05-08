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
use Webmozart\Console\Api\Command\Command;
use Webmozart\Console\Api\Command\CommandCollection;
use Webmozart\Console\Api\Config\CommandConfig;
use Webmozart\Console\Util\SimilarCommandName;

/**
 * @since  1.0
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class SimilarCommandNameTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getInputOutput
     */
    public function testFindSimilarNames($input, array $suggestions)
    {
        $commands = new CommandCollection([
            new Command(
                CommandConfig::create('package')->addAlias('package-alias')
            ),
            new Command(
                CommandConfig::create('pack')->addAlias('pack-alias')
            ),
            new Command(CommandConfig::create('pack')),
        ]);

        $this->assertSame($suggestions, SimilarCommandName::find($input, $commands));
    }

    public function getInputOutput()
    {
        return [
            ['pac', ['pack', 'package']],
            ['pack', ['pack', 'package']],
            ['pack-', ['pack']],
            ['pack-a', ['pack']],
            ['pack-al', ['pack-alias']],
            ['pack-ali', ['pack-alias']],
            ['pack-alia', ['pack-alias']],
            ['pack-alias', ['pack-alias', 'package-alias']],
            ['packa', ['pack', 'package']],
            ['packag', ['package', 'pack']],
            ['package', ['package']],
            ['package-', ['package']],
            ['package-a', ['package']],
            ['package-al', defined('HHVM_VERSION') || PHP_VERSION_ID >= 70000 ? ['package'] : ['package-alias']],
            ['package-ali', ['package-alias']],
            ['package-alia', ['package-alias', 'pack-alias']],
            ['package-alias', ['package-alias', 'pack-alias']],
        ];
    }
}
