<?php

namespace Vivait\TenantBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Vivait\TenantBundle\Kernel\TenantKernel;
use Vivait\TenantBundle\Registry\TenantRegistry;

class ListTenantsCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('vivait:tenants:list')
            ->setDescription('Provides a list of tenants')
            ->addOption(
                'null',
                '0',
                InputOption::VALUE_NONE,
                'Use a null character as a separator (instead of the newline character).'
            )
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                'Force a list of tenants, even if tenanting is disabled for the current environment'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $null = $input->getOption('null');
        $force = $input->getOption('force');

        /** @var TenantKernel $kernel */
        $kernel = $this->getApplication()->getKernel();

        if ($force || $kernel->enableTenanting) {
            /** @var TenantRegistry $registry */
            $registry = $kernel->getContainer()->get('vivait_tenant.registry');

            $separator = $null ? "\0" : "\n";

            foreach ($registry->getAll() as $tenant) {
                $output->write($tenant->getKey() . $separator);
            }
        }

        return 0;
    }
}
