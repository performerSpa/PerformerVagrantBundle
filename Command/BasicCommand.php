<?php
/**
 * Created by PhpStorm.
 * User: alexandru.moisei
 * Date: 27/10/15
 * Time: 10:45
 */

namespace Performer\VagrantBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

abstract class BasicCommand extends ContainerAwareCommand
{
    /** @var $helper FormatterHelper */
    protected $helper;

    /**
     * Initializes the command just after the input has been validated.
     *
     * This is mainly useful when a lot of commands extends one main command
     * where some things need to be initialized based on the input arguments and options.
     *
     * @param InputInterface $input An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $this->helper = $this->getHelperSet()->get('formatter');
//        $this->rootDir = $this->getContainer()->get('kernel')->getRootDir();
//        $this->env = $this->getContainer()->get('kernel')->getEnvironment();
    }

    /**
     * @param string $command
     * @param OutputInterface $output
     * @param callable|null $callback A valid PHP callback
     */
    protected function basicProcess($command, OutputInterface $output, $callback = null)
    {
        $process = new Process($command);
        $process->setTimeout(600);

        $output->writeln($this->helper->formatSection('Executing', $process->getCommandLine(), 'comment'));
        $process->start();

        $process->wait(
            function ($type, $buffer) use ($output) {
                if (Process::ERR == $type) {
                    $output->write($this->helper->formatSection('Error', $buffer, 'error'));
                } else {
                    $output->write($this->helper->formatSection('Progress', $buffer, 'comment'));
                }
            }
        );

        if ($process->isTerminated()) {
            $output->writeln($this->helper->formatSection('Finishing', $process->getCommandLine(), 'comment'));

            if (null !== $callback) {
                $callback();
            }
        }
    }

    public function basicProceedArray($array)
    {
        $arguments = array();

        foreach (array_keys($array) as $key) {
            if ($array[$key] !== null && $array[$key] !== false && !is_array($array[$key])) {
                $arguments[$key] = $array[$key];
            }

            if (is_array($array[$key]) && count($array[$key]) != 0) {
                $arguments[$key] = $array[$key];
            }
        }

        return $arguments;
    }

    public function basicGetArgumentsWithoutSelf(InputInterface $input)
    {
        $args = $input->getArguments();

        if (is_array($args) && array_key_exists('command', $args)) {
            unset($args['command']);
        }

        return $args;
    }
}
