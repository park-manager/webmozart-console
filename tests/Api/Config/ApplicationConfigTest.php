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
use Symfony\Component\EventDispatcher\EventDispatcher;
use Webmozart\Console\Api\Config\ApplicationConfig;
use Webmozart\Console\Api\Config\CommandConfig;
use Webmozart\Console\Api\Formatter\Style;
use Webmozart\Console\Api\Formatter\StyleSet;
use Webmozart\Console\Formatter\DefaultStyleSet;
use Webmozart\Console\Resolver\DefaultResolver;

/**
 * @since  1.0
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class ApplicationConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ApplicationConfig
     */
    private $config;

    protected function doSetUp()
    {
        $this->config = new ApplicationConfig();
    }

    public function testCreate()
    {
        $config = new ApplicationConfig();

        $this->assertNull($config->getName());
        $this->assertNull($config->getDisplayName());
        $this->assertNull($config->getVersion());
        $this->assertNull($config->getEventDispatcher());
        $this->assertSame([], $config->getCommandConfigs());
    }

    public function testCreateWithArguments()
    {
        $config = new ApplicationConfig('name', 'version');

        $this->assertSame('name', $config->getName());
        $this->assertSame('version', $config->getVersion());
    }

    public function testStaticCreate()
    {
        $config = ApplicationConfig::create();

        $this->assertNull($config->getName());
        $this->assertNull($config->getDisplayName());
        $this->assertNull($config->getVersion());
        $this->assertNull($config->getEventDispatcher());
        $this->assertSame([], $config->getCommandConfigs());
    }

    public function testStaticCreateWithArguments()
    {
        $config = ApplicationConfig::create('name', 'version');

        $this->assertSame('name', $config->getName());
        $this->assertSame('version', $config->getVersion());
    }

    public function testSetName()
    {
        $this->config->setName('the-name');

        $this->assertSame('the-name', $this->config->getName());
    }

    public function testSetNameNull()
    {
        $this->config->setName('the-name');
        $this->config->setName(null);

        $this->assertNull($this->config->getName());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetNameFailsIfEmpty()
    {
        $this->config->setName('');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetNameFailsIfSpaces()
    {
        $this->config->setName('the name');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetNameFailsIfNoString()
    {
        $this->config->setName(1234);
    }

    public function testSetDisplayName()
    {
        $this->config->setDisplayName('The Name');

        $this->assertSame('The Name', $this->config->getDisplayName());
    }

    public function testSetDisplayNameNull()
    {
        $this->config->setDisplayName('The Name');
        $this->config->setDisplayName(null);

        $this->assertNull($this->config->getDisplayName());
    }

    public function testSetDisplayNameFailsIfEmpty()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->config->setDisplayName('');
    }

    public function testSetDisplayNameFailsIfNoString()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->config->setDisplayName(1234);
    }

    public function testGetDisplayNameReturnsHumanizedNameByDefault()
    {
        $this->config->setName('the-name');

        $this->assertSame('The Name', $this->config->getDisplayName());
    }

    public function testSetVersion()
    {
        $this->config->setVersion('version');

        $this->assertSame('version', $this->config->getVersion());
    }

    public function testSetVersionNull()
    {
        $this->config->setVersion('version');
        $this->config->setVersion(null);

        $this->assertNull($this->config->getVersion());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetVersionFailsIfEmpty()
    {
        $this->config->setVersion('');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetVersionFailsIfNoString()
    {
        $this->config->setVersion(1234);
    }

    public function testSetHelp()
    {
        $this->config->setHelp('help');

        $this->assertSame('help', $this->config->getHelp());
    }

    public function testSetHelpNull()
    {
        $this->config->setHelp('help');
        $this->config->setHelp(null);

        $this->assertNull($this->config->getHelp());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetHelpFailsIfEmpty()
    {
        $this->config->setHelp('');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetHelpFailsIfNoString()
    {
        $this->config->setHelp(1234);
    }

    public function testSetEventDispatcher()
    {
        $dispatcher = new EventDispatcher();

        $this->config->setEventDispatcher($dispatcher);

        $this->assertSame($dispatcher, $this->config->getEventDispatcher());
    }

    public function testAddEventListener()
    {
        $dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $listener = function () {};

        $dispatcher->expects($this->once())
            ->method('addListener')
            ->with('event-name', $listener, 123);

        $this->config->setEventDispatcher($dispatcher);
        $this->config->addEventListener('event-name', $listener, 123);
    }

    public function testAddEventSubscriber()
    {
        $dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $subscriber = $this->getMock('Symfony\Component\EventDispatcher\EventSubscriberInterface');

        $dispatcher->expects($this->once())
            ->method('addSubscriber')
            ->with($subscriber);

        $this->config->setEventDispatcher($dispatcher);
        $this->config->addEventSubscriber($subscriber);
    }

    public function testRemoveEventListener()
    {
        $dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $listener = function () {};

        $dispatcher->expects($this->once())
            ->method('removeListener')
            ->with('event-name', $listener);

        $this->config->setEventDispatcher($dispatcher);
        $this->config->removeEventListener('event-name', $listener);
    }

    public function testRemoveEventSubscriber()
    {
        $dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $subscriber = $this->getMock('Symfony\Component\EventDispatcher\EventSubscriberInterface');

        $dispatcher->expects($this->once())
            ->method('removeSubscriber')
            ->with($subscriber);

        $this->config->setEventDispatcher($dispatcher);
        $this->config->removeEventSubscriber($subscriber);
    }

    public function testSetCatchExceptions()
    {
        $this->config->setCatchExceptions(true);
        $this->assertTrue($this->config->isExceptionCaught());

        $this->config->setCatchExceptions(false);
        $this->assertFalse($this->config->isExceptionCaught());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetCatchExceptionsFailsIfNull()
    {
        $this->config->setCatchExceptions(null);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetCatchExceptionsFailsIfNoBoolean()
    {
        $this->config->setCatchExceptions(1234);
    }

    public function testSetTerminateAfterRun()
    {
        $this->config->setTerminateAfterRun(true);
        $this->assertTrue($this->config->isTerminatedAfterRun());

        $this->config->setTerminateAfterRun(false);
        $this->assertFalse($this->config->isTerminatedAfterRun());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetTerminateAfterRunFailsIfNull()
    {
        $this->config->setTerminateAfterRun(null);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetTerminateAfterRunFailsIfNoBoolean()
    {
        $this->config->setTerminateAfterRun(1234);
    }

    public function testSetCommandResolver()
    {
        $resolver = new DefaultResolver();

        $this->config->setCommandResolver($resolver);

        $this->assertSame($resolver, $this->config->getCommandResolver());
    }

    public function testDefaultCommandResolver()
    {
        $resolver = new DefaultResolver();

        $this->assertEquals($resolver, $this->config->getCommandResolver());
    }

    public function testSetIOFactory()
    {
        $factory = function () {};

        $this->config->setIOFactory($factory);

        $this->assertSame($factory, $this->config->getIOFactory());
    }

    public function testSetIOFactoryNull()
    {
        $factory = function () {};

        $this->config->setIOFactory($factory);
        $this->config->setIOFactory(null);

        $this->assertNull($this->config->getIOFactory());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetIOFactoryFailsIfNeitherCallableNorNull()
    {
        $this->config->setIOFactory(1234);
    }

    public function testDefaultIOFactory()
    {
        $this->assertNull($this->config->getIOFactory());
    }

    public function testSetDebug()
    {
        $this->assertFalse($this->config->isDebug());
        $this->config->setDebug(true);
        $this->assertTrue($this->config->isDebug());
        $this->config->setDebug(false);
        $this->assertFalse($this->config->isDebug());
    }

    public function testSetStyleSet()
    {
        $styleSet = new StyleSet();

        $this->config->setStyleSet($styleSet);

        $this->assertSame($styleSet, $this->config->getStyleSet());
    }

    public function testDefaultStyleSet()
    {
        $styleSet = new DefaultStyleSet();

        $this->assertEquals($styleSet, $this->config->getStyleSet());
    }

    public function testAddStyle()
    {
        $style = Style::tag('custom');

        $this->config->addStyle($style);

        $styleSet = new DefaultStyleSet();
        $styleSet->add($style);

        $this->assertEquals($styleSet, $this->config->getStyleSet());
    }

    public function testAddStyles()
    {
        $style1 = Style::tag('custom1');
        $style2 = Style::tag('custom2');

        $this->config->addStyles([$style1, $style2]);

        $styleSet = new DefaultStyleSet();
        $styleSet->add($style1);
        $styleSet->add($style2);

        $this->assertEquals($styleSet, $this->config->getStyleSet());
    }

    public function testRemoveStyle()
    {
        $style1 = Style::tag('custom1');
        $style2 = Style::tag('custom2');

        $this->config->addStyles([$style1, $style2]);
        $this->config->removeStyle('custom1');

        $styleSet = new DefaultStyleSet();
        $styleSet->add($style2);

        $this->assertEquals($styleSet, $this->config->getStyleSet());
    }

    public function testBeginCommand()
    {
        $this->config
            ->beginCommand('command1')->end()
            ->beginCommand('command2')->end()
        ;

        $this->assertEquals([
            new CommandConfig('command1', $this->config),
            new CommandConfig('command2', $this->config),
        ], $this->config->getCommandConfigs());
    }

    public function testEditCommand()
    {
        $this->config->addCommandConfig($config1 = new CommandConfig('command1'));

        $this->assertSame($config1, $this->config->editCommand('command1'));
    }

    public function testAddCommandConfig()
    {
        $this->config->addCommandConfig($config1 = new CommandConfig('command1'));
        $this->config->addCommandConfig($config2 = new CommandConfig('command2'));

        $this->assertSame([$config1, $config2], $this->config->getCommandConfigs());
    }

    public function testAddCommandConfigs()
    {
        $this->config->addCommandConfig($config1 = new CommandConfig('command1'));
        $this->config->addCommandConfigs([
            $config2 = new CommandConfig('command2'),
            $config3 = new CommandConfig('command3'),
        ]);

        $this->assertSame([$config1, $config2, $config3], $this->config->getCommandConfigs());
    }

    public function testSetCommandConfigs()
    {
        $this->config->addCommandConfig($config1 = new CommandConfig('command1'));
        $this->config->setCommandConfigs([
            $config2 = new CommandConfig('command2'),
            $config3 = new CommandConfig('command3'),
        ]);

        $this->assertSame([$config2, $config3], $this->config->getCommandConfigs());
    }

    public function testGetCommandConfig()
    {
        $this->config->addCommandConfig($config = new CommandConfig());

        $config->setName('command');

        $this->assertSame($config, $this->config->getCommandConfig('command'));
    }

    /**
     * @expectedException \Webmozart\Console\Api\Command\NoSuchCommandException
     */
    public function testGetCommandConfigFailsIfCommandNotFound()
    {
        $this->config->getCommandConfig('command');
    }

    public function testHasCommandConfig()
    {
        $this->config->addCommandConfig($config = new CommandConfig());

        $this->assertFalse($this->config->hasCommandConfig('command'));
        $this->assertFalse($this->config->hasCommandConfig('foobar'));

        $config->setName('command');

        $this->assertTrue($this->config->hasCommandConfig('command'));
        $this->assertFalse($this->config->hasCommandConfig('foobar'));
    }

    public function testHasCommandConfigs()
    {
        $this->assertFalse($this->config->hasCommandConfigs());

        $this->config->addCommandConfig($config = new CommandConfig());

        $this->assertTrue($this->config->hasCommandConfigs());
    }
}
