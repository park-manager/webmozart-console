<?php

/*
 * This file is part of the webmozart/console package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webmozart\Console\Tests\Resolver;

use Webmozart\Console\Tests\TestCase as PHPUnit_Framework_TestCase;
use Webmozart\Console\Api\Args\Args;
use Webmozart\Console\Api\Args\Format\Option;
use Webmozart\Console\Api\Config\ApplicationConfig;
use Webmozart\Console\Api\Resolver\CannotResolveCommandException;
use Webmozart\Console\Args\StringArgs;
use Webmozart\Console\ConsoleApplication;
use Webmozart\Console\Resolver\DefaultResolver;

/**
 * @since  1.0
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class DefaultResolverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ConsoleApplication
     */
    private static $application;

    /**
     * @var DefaultResolver
     */
    private $resolver;

    public static function setUpBeforeClass(): void
    {
        $config = ApplicationConfig::create()
            ->addOption('option', 'o')
            ->addOption('value', 'v', Option::OPTIONAL_VALUE)
            ->addArgument('arg')

            ->beginCommand('package')
                ->addAlias('package-alias')
                ->beginSubCommand('add')
                    ->addAlias('add-alias')
                ->end()
                ->beginSubCommand('addon')->end()
                ->beginOptionCommand('delete', 'd')
                    ->addAlias('delete-alias')
                ->end()
                ->beginOptionCommand('delete-all')->end()
            ->end()

            ->beginCommand('pack')->end()

            ->beginCommand('default')
                ->markDefault()
            ->end()

            ->beginCommand('stash')
                ->beginSubCommand('save')
                    ->markDefault()
                    ->beginOptionCommand('do', 'D')->end()
                ->end()
            ->end()

            ->beginCommand('server')
                ->beginOptionCommand('list')
                    ->markDefault()
                ->end()
            ->end()

            ->beginCommand('bind')
                ->beginSubCommand('list')
                    ->markDefault()
                    ->beginOptionCommand('do', 'D')->end()
                ->end()
                ->beginSubCommand('add')
                    ->markDefault()
                    ->addArgument('binding')
                    ->beginOptionCommand('do', 'D')->end()
                ->end()
            ->end()
        ;

        self::$application = new ConsoleApplication($config);
    }

    protected function doSetUp()
    {
        $this->resolver = new DefaultResolver('default');
    }

    /**
     * @dataProvider getInputOutputTests
     */
    public function testResolveCommand($inputString, $commandName)
    {
        $resolvedCommand = $this->resolver->resolveCommand(new StringArgs($inputString), self::$application);

        $this->assertInstanceOf('Webmozart\Console\Api\Resolver\ResolvedCommand', $resolvedCommand);
        $this->assertSame($commandName, $resolvedCommand->getCommand()->getName());
    }

    public function getInputOutputTests()
    {
        return [
            // no options
            ['package', 'package'],
            ['package arg', 'package'],
            ['pack', 'pack'],
            ['pack arg', 'pack'],
            ['package add', 'add'],
            ['package add arg', 'add'],
            ['package addon', 'addon'],
            ['package addon arg', 'addon'],

            // options with simple command
            ['package -o', 'package'],
            ['package --option', 'package'],
            ['package -v1', 'package'],
            ['package -v 1', 'package'],
            ['package --value="1"', 'package'],
            ['package --value=\'1\'', 'package'],

            // options+args with simple command
            ['package -o arg', 'package'],
            ['package --option arg', 'package'],
            ['package -v1 arg', 'package'],
            ['package -v 1 arg', 'package'],
            ['package --value="1" arg', 'package'],
            ['package --value=\'1\' arg', 'package'],

            // options before sub-command not possible
            ['package -o add', 'package'],
            ['package --option add', 'package'],
            ['package -v1 add', 'package'],
            ['package -v 1 add', 'package'],
            ['package --value="1" add', 'package'],
            ['package --value=\'1\' add', 'package'],

            // options after sub-command
            ['package add -o', 'add'],
            ['package add --option', 'add'],
            ['package add -v1', 'add'],
            ['package add -v 1', 'add'],
            ['package add --value="1"', 'add'],
            ['package add --value=\'1\'', 'add'],

            // options+args after sub-command
            ['package add -o arg', 'add'],
            ['package add --option arg', 'add'],
            ['package add -v1 arg', 'add'],
            ['package add -v 1 arg', 'add'],
            ['package add --value="1" arg', 'add'],
            ['package add --value=\'1\' arg', 'add'],

            // options before long option command
            ['package -o --delete', 'delete'],
            ['package --option --delete', 'delete'],
            ['package -v1 --delete', 'delete'],
            ['package -v 1 --delete', 'delete'],
            ['package --value="1" --delete', 'delete'],
            ['package --value=\'1\' --delete', 'delete'],

            // options before short option command
            ['package -o -d', 'delete'],
            ['package --option -d', 'delete'],
            ['package -v1 -d', 'delete'],
            ['package -v 1 -d', 'delete'],
            ['package --value="1" -d', 'delete'],
            ['package --value=\'1\' -d', 'delete'],

            // options after long option command
            ['package --delete -o', 'delete'],
            ['package --delete --option', 'delete'],
            ['package --delete -v1', 'delete'],
            ['package --delete -v 1', 'delete'],
            ['package --delete --value="1"', 'delete'],
            ['package --delete --value=\'1\'', 'delete'],

            // options after short option command
            ['package -d -o', 'delete'],
            ['package -d --option', 'delete'],
            ['package -d -v1', 'delete'],
            ['package -d -v 1', 'delete'],
            ['package -d --value="1"', 'delete'],
            ['package -d --value=\'1\'', 'delete'],

            // options+args after long option command
            ['package --delete -o arg', 'delete'],
            ['package --delete --option arg', 'delete'],
            ['package --delete -v1 arg', 'delete'],
            ['package --delete -v 1 arg', 'delete'],
            ['package --delete --value="1" arg', 'delete'],
            ['package --delete --value=\'1\' arg', 'delete'],

            // options+args after short option command
            ['package -d -o arg', 'delete'],
            ['package -d --option arg', 'delete'],
            ['package -d -v1 arg', 'delete'],
            ['package -d -v 1 arg', 'delete'],
            ['package -d --value="1" arg', 'delete'],
            ['package -d --value=\'1\' arg', 'delete'],

            // aliases
            ['package-alias', 'package'],
            ['package-alias arg', 'package'],
            ['package add-alias', 'add'],
            ['package add-alias arg', 'add'],
//            array('package --delete-alias', 'delete'),
//            array('package --delete-alias arg', 'delete'),

            // aliases with options
            ['package-alias -o', 'package'],
            ['package-alias --option', 'package'],
            ['package-alias -v1', 'package'],
            ['package-alias -v 1', 'package'],
            ['package-alias --value="1"', 'package'],
            ['package-alias --value=\'1\'', 'package'],

            ['package-alias -o arg', 'package'],
            ['package-alias --option arg', 'package'],
            ['package-alias -v1 arg', 'package'],
            ['package-alias -v 1 arg', 'package'],
            ['package-alias --value="1" arg', 'package'],
            ['package-alias --value=\'1\' arg', 'package'],

            ['package add-alias -o', 'add'],
            ['package add-alias --option', 'add'],
            ['package add-alias -v1', 'add'],
            ['package add-alias -v 1', 'add'],
            ['package add-alias --value="1"', 'add'],
            ['package add-alias --value=\'1\'', 'add'],

            ['package add-alias -o arg', 'add'],
            ['package add-alias --option arg', 'add'],
            ['package add-alias -v1 arg', 'add'],
            ['package add-alias -v 1 arg', 'add'],
            ['package add-alias --value="1" arg', 'add'],
            ['package add-alias --value=\'1\' arg', 'add'],

//            array('package --delete-alias -o', 'delete'),
//            array('package --delete-alias --option', 'delete'),
//            array('package --delete-alias -v1', 'delete'),
//            array('package --delete-alias -v 1', 'delete'),
//            array('package --delete-alias --value="1"', 'delete'),
//            array('package --delete-alias --value=\'1\'', 'delete'),
//
//            array('package --delete-alias -o arg', 'delete'),
//            array('package --delete-alias --option arg', 'delete'),
//            array('package --delete-alias -v1 arg', 'delete'),
//            array('package --delete-alias -v 1 arg', 'delete'),
//            array('package --delete-alias --value="1" arg', 'delete'),
//            array('package --delete-alias --value=\'1\' arg', 'delete'),

            // regex special chars
            ['package *', 'package'],
            ['package **', 'package'],
            ['package /app/*', 'package'],
            ['package /app/**', 'package'],
            ['package -v * arg', 'package'],
            ['package -v ** arg', 'package'],
            ['package -v /app/* arg', 'package'],
            ['package -v /app/** arg', 'package'],
            ['package add *', 'add'],
            ['package add **', 'add'],
            ['package add /app/*', 'add'],
            ['package add /app/**', 'add'],
            ['package add -v *', 'add'],
            ['package add -v **', 'add'],
            ['package add -v /app/*', 'add'],
            ['package add -v /app/**', 'add'],
            ['package --delete *', 'delete'],
            ['package --delete **', 'delete'],
            ['package --delete /app/*', 'delete'],
            ['package --delete /app/**', 'delete'],
            ['package --delete -v *', 'delete'],
            ['package --delete -v **', 'delete'],
            ['package --delete -v /app/*', 'delete'],
            ['package --delete -v /app/**', 'delete'],

            // stop option parsing after "--"
            ['package -- --delete', 'package'],
            ['package -- -d', 'package'],
            ['package -- add', 'package'],

            // default command
            ['', 'default'],

            // options with default command
            ['-o', 'default'],
            ['--option', 'default'],
            ['-v1', 'default'],
            ['-v 1', 'default'],
            ['--value="1"', 'default'],
            ['--value=\'1\'', 'default'],

            // options+args with default command
            ['-o arg', 'default'],
            ['--option arg', 'default'],
            ['-v1 arg', 'default'],
            ['-v 1 arg', 'default'],
            ['--value="1" arg', 'default'],
            ['--value=\'1\' arg', 'default'],

            // default sub command
            ['stash', 'save'],

            // options with default sub command
            ['stash -o', 'save'],
            ['stash --option', 'save'],
            ['stash -v1', 'save'],
            ['stash -v 1', 'save'],
            ['stash --value="1"', 'save'],
            ['stash --value=\'1\'', 'save'],

            // options+args with default sub command
            ['stash -o arg', 'save'],
            ['stash --option arg', 'save'],
            ['stash -v1 arg', 'save'],
            ['stash -v 1 arg', 'save'],
            ['stash --value="1" arg', 'save'],
            ['stash --value=\'1\' arg', 'save'],

            // default option command
            ['server', 'list'],

            // options with default option command
            ['server -o', 'list'],
            ['server --option', 'list'],
            ['server -v1', 'list'],
            ['server -v 1', 'list'],
            ['server --value="1"', 'list'],
            ['server --value=\'1\'', 'list'],

            // options+args with default option command
            ['server -o arg', 'list'],
            ['server --option arg', 'list'],
            ['server -v1 arg', 'list'],
            ['server -v 1 arg', 'list'],
            ['server --value="1" arg', 'list'],
            ['server --value=\'1\' arg', 'list'],

            // multiple default sub commands
            ['bind', 'list'],

            // options with multiple default sub commands
            ['bind -o', 'list'],
            ['bind --option', 'list'],
            ['bind -v1', 'list'],
            ['bind -v 1', 'list'],
            ['bind --value="1"', 'list'],
            ['bind --value=\'1\'', 'list'],

            // options+args with multiple default sub commands
            ['bind -o arg binding', 'add'],
            ['bind --option arg binding', 'add'],
            ['bind -v1 arg binding', 'add'],
            ['bind -v 1 arg binding', 'add'],
            ['bind --value="1" arg binding', 'add'],
            ['bind --value=\'1\' arg binding', 'add'],
        ];
    }

    public function testSuggestClosestAlternativeIfCommandNotFound()
    {
        try {
            $this->resolver->resolveCommand(new StringArgs('packa'), self::$application);
            $this->fail('Expected a CannotResolveCommandException');
        } catch (CannotResolveCommandException $e) {
            $this->assertRegExp('~Did you mean one of these\?\s+pack\s+package~', $e->getMessage());
        }

        try {
            $this->resolver->resolveCommand(new StringArgs('packag'), self::$application);
            $this->fail('Expected a CannotResolveCommandException');
        } catch (CannotResolveCommandException $e) {
            $this->assertRegExp('~Did you mean one of these\?\s+package\s+pack~', $e->getMessage());
        }
    }

    /**
     * @expectedException \Webmozart\Console\Api\Args\CannotParseArgsException
     */
    public function testRethrowParseError()
    {
        $this->resolver->resolveCommand(new StringArgs('bind --foo'), self::$application);
    }

    public function testDoNotRethrowParseErrorIfLenient()
    {
        $config = ApplicationConfig::create()
            ->beginCommand('package')
                ->enableLenientArgsParsing()
            ->end()
        ;

        $application = new ConsoleApplication($config);
        $command = $application->getCommand('package');

        $rawArgs = new StringArgs('package --foo');
        $resolvedCommand = $this->resolver->resolveCommand($rawArgs, $application, true);

        $args = new Args($command->getArgsFormat(), $rawArgs);

        $this->assertSame($command, $resolvedCommand->getCommand());
        $this->assertEquals($args, $resolvedCommand->getArgs());
    }
}
