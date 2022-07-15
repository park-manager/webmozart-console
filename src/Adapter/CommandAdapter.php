<?php

/*
 * This file is part of the webmozart/console package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webmozart\Console\Adapter;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Webmozart\Assert\Assert;

/**
 * Adapts a `Command` instance of this package to Symfony's {@link Command} API.
 *
 * @since  1.0
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
abstract class AbstractCommandAdapter extends Command
{
    /**
     * @var \Webmozart\Console\Api\Command\Command
     */
    private $adaptedCommand;

    /**
     * Creates the adapter.
     *
     * @param \Webmozart\Console\Api\Command\Command $adaptedCommand The adapted command.
     * @param Application                            $application    The application.
     */
    public function __construct(\Webmozart\Console\Api\Command\Command $adaptedCommand, Application $application)
    {
        parent::setName($adaptedCommand->getName());

        parent::__construct();

        $this->adaptedCommand = $adaptedCommand;

        $config = $adaptedCommand->getConfig();

        parent::setDefinition(new ArgsFormatInputDefinition($this->adaptedCommand->getArgsFormat()));
        parent::setApplication($application);
        parent::setDescription($config->getDescription() ?? '');
        parent::setHelp($config->getHelp() ?? '');
        parent::setAliases($adaptedCommand->getAliases());

        if ($helperSet = $config->getHelperSet()) {
            parent::setHelperSet($helperSet);
        }
    }

    /**
     * Returns the adapted command.
     *
     * @return Command The adapted command.
     */
    public function getAdaptedCommand()
    {
        return $this->adaptedCommand;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return $this->adaptedCommand->getConfig()->isEnabled();
    }

    /**
     * Executes the command.
     *
     * @param InputInterface  $input  The console input.
     * @param OutputInterface $output The console output.
     *
     * @return int The exit status.
     */
    public function run(InputInterface $input, OutputInterface $output): int
    {
        /* @var ArgsInput $input */
        /* @var IOOutput $output */
        Assert::isInstanceOf($input, 'Webmozart\Console\Adapter\ArgsInput');
        Assert::isInstanceOf($output, 'Webmozart\Console\Adapter\IOOutput');

        return $this->adaptedCommand->handle($input->getArgs(), $output->getIO());
    }
}

class CommandAdapter extends AbstractCommandAdapter
{
    /**
     * Does nothing.
     *
     * @param callable $code The code.
     *
     * @return static The current instance.
     */
    public function setCode(callable $code): static
    {
        return $this;
    }
}
