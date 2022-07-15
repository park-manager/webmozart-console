<?php

/*
 * This file is part of the webmozart/console package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webmozart\Console\Tests\UI\Component;

use Webmozart\Console\Tests\TestCase as PHPUnit_Framework_TestCase;
use Webmozart\Console\IO\BufferedIO;
use Webmozart\Console\UI\Component\EmptyLine;

/**
 * @since  1.0
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class EmptyLineTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var BufferedIO
     */
    private $io;

    protected function doSetUp()
    {
        $this->io = new BufferedIO();
    }

    public function testRender()
    {
        $line = new EmptyLine();
        $line->render($this->io);

        $this->assertSame("\n", $this->io->fetchOutput());
    }

    public function testRenderIgnoresIndentation()
    {
        $line = new EmptyLine();
        $line->render($this->io, 10);

        $this->assertSame("\n", $this->io->fetchOutput());
    }
}
