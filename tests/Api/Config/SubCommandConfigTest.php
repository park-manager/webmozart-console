<?php

/*
 * This file is part of the webmozart/console package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webmozart\Console\Tests\Api\Config;

use Webmozart\Console\Tests\TestCase as PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Helper\HelperSet;
use Webmozart\Console\Api\Config\ApplicationConfig;
use Webmozart\Console\Api\Config\CommandConfig;
use Webmozart\Console\Api\Config\SubCommandConfig;
use Webmozart\Console\Args\DefaultArgsParser;
use Webmozart\Console\Handler\NullHandler;

/**
 * @since  1.0
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class SubCommandConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var SubCommandConfig
     */
    private $config;

    /**
     * @var CommandConfig
     */
    private $parentConfig;

    /**
     * @var ApplicationConfig
     */
    private $applicationConfig;

    protected function doSetUp()
    {
        $this->applicationConfig = new ApplicationConfig();
        $this->parentConfig = new CommandConfig('command', $this->applicationConfig);
        $this->config = new SubCommandConfig('sub-command', $this->parentConfig, $this->applicationConfig);
    }

    public function testCreate()
    {
        $config = new SubCommandConfig();

        $this->assertNull($config->getParentConfig());
        $this->assertNull($config->getApplicationConfig());
        $this->assertNull($config->getName());
    }

    public function testCreateWithArguments()
    {
        $applicationConfig = new ApplicationConfig();
        $parentConfig = new CommandConfig('command', $applicationConfig);
        $config = new SubCommandConfig('sub-command', $parentConfig, $applicationConfig);

        $this->assertSame($parentConfig, $config->getParentConfig());
        $this->assertSame($applicationConfig, $config->getApplicationConfig());
        $this->assertSame('sub-command', $config->getName());
    }

    public function testGetHelperSetReturnsParentHelperSetByDefault()
    {
        $helperSet = new HelperSet();

        $this->parentConfig->setHelperSet($helperSet);

        $this->assertSame($helperSet, $this->config->getHelperSet());
    }

    public function testGetHandlerReturnsParentHandlerByDefault()
    {
        $handler = new NullHandler();

        $this->parentConfig->setHandler($handler);

        $this->assertSame($handler, $this->config->getHandler());
    }

    public function testGetHandlerMethodReturnsParentHandlerByDefault()
    {
        $this->parentConfig->setHandlerMethod('method');

        $this->assertSame('method', $this->config->getHandlerMethod());
    }

    public function testGetArgsParserReturnsParentArgsParserByDefault()
    {
        $parser = new DefaultArgsParser();

        $this->parentConfig->setArgsParser($parser);

        $this->assertSame($parser, $this->config->getArgsParser());
    }

    public function testLenientArgsParsingDefaultsToParentValue()
    {
        $this->parentConfig->enableLenientArgsParsing();

        $this->assertTrue($this->config->isLenientArgsParsingEnabled());

        $this->parentConfig->disableLenientArgsParsing();

        $this->assertFalse($this->config->isLenientArgsParsingEnabled());
    }
}
