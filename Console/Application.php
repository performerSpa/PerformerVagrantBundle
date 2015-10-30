<?php
/**
 * Created by PhpStorm.
 * User: alexandru.moisei
 * Date: 27/10/15
 * Time: 10:45
 */

namespace Performer\VagrantBundle\Console;

use Performer\VagrantBundle\Command\VagrantCommand;
use Performer\VagrantBundle\Command\VagrantSshCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

class Application extends BaseApplication
{
    /**
     * Gets the default commands that should always be available.
     *
     * @return array An array of default Command instances
     */
    protected function getDefaultCommands()
    {
        // Keep the core default commands to have the HelpCommand and ListCommand
        // which is used when using the --help and --list option
        $defaultCommands = parent::getDefaultCommands();

        foreach (VagrantCommand::commands() as $command) {
            $defaultCommands[] = new VagrantCommand($command);
        }

        foreach (VagrantSshCommand::classes() as $class) {
            $defaultCommands[] = new VagrantSshCommand($class);
        }

        return $defaultCommands;
    }

    /**
     * Override to exclude original kernel commands and to include additional commands from user
     */
    protected function registerCommands()
    {
        //parent::registerCommands();

        $container = $this->getKernel()->getContainer();
        $user_commands = $container->getParameter('performer_vagrant.users.remote_commands');

        if ($user_commands !== null) {
            foreach($user_commands as $class) {
                if (class_exists($class)) {
                    $this->add(new VagrantSshCommand($class));
                }
            }
        }
    }

    /**
     * Gets the default input definition.
     * Override to add the --ansi option required, to color the output
     *
     * @return InputDefinition An InputDefinition instance
     */
    protected function getDefaultInputDefinition()
    {
        $ansi = 'ansi';
        $inputDefinitions = parent::getDefaultInputDefinition();

        if ($inputDefinitions->hasOption($ansi)) {
            $options = array();

            foreach($inputDefinitions->getOptions() as $option) {
                if ($option->getName() !== $ansi) {
                    $options[] = $option;
                }
            }

            $options[] = new InputOption($ansi, '', InputOption::VALUE_REQUIRED, 'Force ANSI output', true);
            $inputDefinitions->setOptions($options);
        }

        return $inputDefinitions;
    }
}