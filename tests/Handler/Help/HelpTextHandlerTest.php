<?php

/*
 * This file is part of the webmozart/console package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webmozart\Console\Tests\Handler\Help;

use Webmozart\Console\Tests\TestCase as PHPUnit_Framework_TestCase;
use Webmozart\Console\Api\Application\Application;
use Webmozart\Console\Api\Args\Args;
use Webmozart\Console\Api\Command\Command;
use Webmozart\Console\Config\DefaultApplicationConfig;
use Webmozart\Console\ConsoleApplication;
use Webmozart\Console\Handler\Help\HelpTextHandler;
use Webmozart\Console\IO\BufferedIO;

/**
 * @since  1.0
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class HelpTextHandlerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Application
     */
    private $application;

    /**
     * @var Command
     */
    private $command;

    /**
     * @var Command
     */
    private $helpCommand;

    /**
     * @var BufferedIO
     */
    private $io;

    /**
     * @var HelpTextHandler
     */
    private $handler;

    protected function doSetUp()
    {
        $config = DefaultApplicationConfig::create()
            ->setDisplayName('The Application')
            ->setVersion('1.2.3')
            ->beginCommand('the-command')->end()
        ;

        $this->application = new ConsoleApplication($config);
        $this->command = $this->application->getCommand('the-command');
        $this->helpCommand = $this->application->getCommand('help');
        $this->io = new BufferedIO();
        $this->handler = new HelpTextHandler();
    }

    public function testRenderCommandText()
    {
        $args = new Args($this->helpCommand->getArgsFormat());
        $args->setArgument('command', 'the-command');

        $status = $this->handler->handle($args, $this->io, $this->command);

        $expected = <<<'EOF'
            USAGE
              console the-command

            GLOBAL OPTIONS
            EOF;

        $this->assertStringStartsWith($expected, $this->io->fetchOutput());
        $this->assertSame(0, $status);
    }

    public function testRenderApplicationText()
    {
        $args = new Args($this->helpCommand->getArgsFormat());

        $status = $this->handler->handle($args, $this->io, $this->command);

        $expected = <<<'EOF'
            The Application version 1.2.3

            USAGE
              console
            EOF;

        $this->assertStringStartsWith($expected, $this->io->fetchOutput());
        $this->assertSame(0, $status);
    }
}
