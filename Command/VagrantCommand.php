<?php
/**
 * Created by PhpStorm.
 * User: alexandru.moisei
 * Date: 27/10/15
 * Time: 10:45
 */

namespace Performer\VagrantBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class VagrantCommand extends BasicCommand
{
    const init = 'init';
    const up = 'up';
    const provision = 'provision';
    const reload = 'reload';
    const resume = 'resume';
    const status = 'status';
    const suspend = 'suspend';
    const halt = 'halt';
    const ssh = 'ssh';
    const destroy = 'destroy';

    static function commands()
    {
        return array(
            self::init,
            self::up,
            self::provision,
            self::reload,
            self::resume,
            self::status,
            self::suspend,
            self::halt,
            self::ssh,
            self::destroy
        );
    }

    /** @var string */
    private $vagrant = 'vagrant';

    /** @var null | string */
    private $command;

    /** @var $ssh_php string */
    protected $ssh_php = '';

    /** @var $ssh_site string */
    protected $ssh_site = '';

    /** @var $ssh_console string */
    protected $ssh_console = '';

    public function __construct($command)
    {
        parent::__construct($this->vagrant . ':' . $command);
        $this->command = $command;
    }

    protected function configure()
    {
        parent::configure();
        $this->setDescription('Vagrant Commands');

        if ($this->getName() == $this->vagrant . ':' . self::ssh) {
            $this->addOption('ssh_command', null, InputOption::VALUE_OPTIONAL);
            $this->addOption('ssh_command_arguments', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL);
            $this->addOption('ssh_command_options', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $process = $this->vagrant . ' ' . $this->command . $this->configSSH($input, $output);
        $this->basicProcess($process, $output, null);
    }

    private function configSSH(InputInterface $input, OutputInterface $output)
    {
        if ($this->getName() == $this->vagrant . ':' . self::ssh) {
            $param_command = $input->getOption('ssh_command');

            if ($param_command !== null) {
                return ' --command \'' . $this->constructPhpCall($input, $output, $param_command) . '\'';
            }
        }

        return '';
    }

    private function constructPhpCall(InputInterface $input, OutputInterface $output, $param_command)
    {
        $this->ssh_php = $this->getContainer()->getParameter('performer_vagrant.defaults.remote_php_interpreter');
        $this->ssh_site = $this->getContainer()->getParameter('performer_vagrant.defaults.remote_site_dir');
        $this->ssh_console = $this->getContainer()->getParameter('performer_vagrant.defaults.remote_symfony_console');

        return $this->ssh_php . ' ' . $this->ssh_site . $this->ssh_console . ' ' . $param_command . $this->parseArguments(
            $input,
            $output
        ) . $this->parseOptions($input, $output);
    }

    private function parseArguments(InputInterface $input, OutputInterface $output)
    {
        $array_command_arguments = $input->getOption('ssh_command_arguments');
        $output->writeln($this->helper->formatSection('Info', json_encode($array_command_arguments), 'info'));
        $str = '';

        if ($array_command_arguments !== null) {
            foreach ($array_command_arguments as $value) {
                $str .= ' ' . $value;
            }
        }

        return $str;
    }

    private function parseOptions(InputInterface $input, OutputInterface $output)
    {
        $array_command_options = $input->getOption('ssh_command_options');
        $output->writeln($this->helper->formatSection('Info', json_encode($array_command_options), 'info'));
        $str = '';

        if ($array_command_options !== null) {
            foreach (array_keys($array_command_options) as $key) {
                $str .= ' --' . $key;

                if ($array_command_options[$key] !== true) {
                    $str .= '=' . $array_command_options[$key];
                }
            }
        }

        return $str;
    }
}
