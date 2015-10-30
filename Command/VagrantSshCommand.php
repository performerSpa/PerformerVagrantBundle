<?php
/**
 * Created by PhpStorm.
 * User: alexandru.moisei
 * Date: 27/10/15
 * Time: 10:45
 */

namespace Performer\VagrantBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class VagrantSshCommand extends BasicCommand
{
    /** @var string */
    public $vagrant_ssh = 'vagrant:ssh';

    /** @var Command */
    private $command;

    static private $classes = array(
        'Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineEntityCommand',
        'Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCrudCommand',
        'Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineFormCommand',
        'Doctrine\Bundle\DoctrineBundle\Command\CreateDatabaseDoctrineCommand',
        'Doctrine\Bundle\DoctrineBundle\Command\DropDatabaseDoctrineCommand',
        'Doctrine\Bundle\DoctrineBundle\Command\Proxy\UpdateSchemaDoctrineCommand',
        'Doctrine\Bundle\DoctrineBundle\Command\Proxy\DropSchemaDoctrineCommand',
        'Doctrine\Bundle\DoctrineBundle\Command\GenerateEntitiesDoctrineCommand',
        'Doctrine\Bundle\DoctrineBundle\Command\Proxy\RunDqlDoctrineCommand',
        'Doctrine\Bundle\DoctrineBundle\Command\Proxy\RunSqlDoctrineCommand',
        'Doctrine\Bundle\DoctrineBundle\Command\Proxy\ConvertMappingDoctrineCommand',
        'Doctrine\Bundle\DoctrineBundle\Command\ImportMappingDoctrineCommand',
        'Doctrine\Bundle\DoctrineBundle\Command\Proxy\InfoDoctrineCommand',
        'Doctrine\Bundle\FixturesBundle\Command\LoadDataFixturesDoctrineCommand',
        'Doctrine\Bundle\MigrationsBundle\Command\MigrationsMigrateDoctrineCommand',
        'Doctrine\Bundle\MigrationsBundle\Command\MigrationsDiffDoctrineCommand',
        'Doctrine\Bundle\MigrationsBundle\Command\MigrationsExecuteDoctrineCommand',
        'Doctrine\Bundle\MigrationsBundle\Command\MigrationsGenerateDoctrineCommand',
        'Doctrine\Bundle\MigrationsBundle\Command\MigrationsLatestDoctrineCommand',
        'Doctrine\Bundle\MigrationsBundle\Command\MigrationsStatusDoctrineCommand',
        'Doctrine\Bundle\MigrationsBundle\Command\MigrationsVersionDoctrineCommand',
        'Symfony\Bundle\FrameworkBundle\Command\CacheClearCommand',
        'Symfony\Bundle\FrameworkBundle\Command\CacheWarmupCommand'
    );

    static function classes()
    {
        $available = array();

        foreach (self::$classes as $class) {
            if (class_exists($class)) {
                $available[] = $class;
            }
        }

        return $available;
    }

    public function __construct($class)
    {
        /** @var Command $command */
        $this->command = new $class();

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName($this->vagrant_ssh . ':' . $this->command->getName())
            ->setDescription('Vagrant SSH ' . $this->command->getDescription())
            ->setDefinition($this->command->getDefinition());

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ssh = $this->getApplication()->find($this->vagrant_ssh);

        $arguments = $this->basicGetArgumentsWithoutSelf($input);
        $options = $input->getOptions();

        $parameters = array(
            'command' => $this->vagrant_ssh,
            '--ssh_command' => $this->command->getName(),
            '--ssh_command_arguments' => $this->basicProceedArray($arguments),
            '--ssh_command_options' => $this->basicProceedArray($options),
        );

        $output->writeln($this->helper->formatSection('Info', json_encode($parameters)));

        $greetInput = new ArrayInput($parameters);
        $returnCode = $ssh->run($greetInput, $output);
        $output->writeln($this->helper->formatSection('Finishing', $returnCode));
    }
}
