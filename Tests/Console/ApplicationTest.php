<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Performer\VagrantBundle\Tests;

use Performer\VagrantBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Tests\Command\CacheClearCommand\Fixture\TestAppKernel;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    public function testBundleCommandsAreRegistered()
    {
        $kernel = new TestAppKernel('dev', true);

        $application = new Application($kernel);
        $application->doRun(new ArrayInput(array('list')), new NullOutput());

        $commands = $application->all();

        foreach ($commands as $command) {
            var_dump($command->getName());
        }
    }
}
