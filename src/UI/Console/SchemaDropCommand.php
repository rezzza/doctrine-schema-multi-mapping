<?php

namespace Rezzza\DoctrineSchemaMultiMapping\UI\Console;

use Doctrine\Bundle\DoctrineBundle\Command\Proxy\DoctrineCommandHelper;
use Doctrine\Bundle\DoctrineBundle\Command\Proxy\DropSchemaDoctrineCommand;
use Doctrine\ORM\Tools\SchemaTool;
use Rezzza\DoctrineSchemaMultiMapping\Domain\MetadataFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;

class SchemaDropCommand extends DropSchemaDoctrineCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('rezzza:doctrine-multi-mapping:schema:drop')
            ->setDescription('Executes (or dumps) the SQL needed to drop the current database schema')
            ->addOption('em', null, InputOption::VALUE_OPTIONAL, 'The entity manager to use for this command')
            ->setHelp(<<<EOT
The <info>rezzza:doctrine-multi-mapping:schema:drop</info> command generates the SQL needed to
drop the database schema of the default entity manager:

<info>php app/console rezzza:doctrine-multi-mapping:schema:drop --dump-sql</info>

Alternatively, you can execute the generated queries:

<info>php app/console rezzza:doctrine-multi-mapping:schema:drop --force</info>

You can also optionally specify the name of a entity manager to drop the
schema for:

<info>php app/console rezzza:doctrine-multi-mapping:schema:drop --em=default</info>
EOT
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        DoctrineCommandHelper::setApplicationEntityManager($this->getApplication(), $input->getOption('em'));

        $em        = $this->getHelper('em')->getEntityManager();
        $factory   = new MetadataFactory();
        $metadatas = $factory->getAllMetadata($em);

        if ( ! empty($metadatas)) {
            // Create SchemaTool
            $tool = new SchemaTool($em);

            return $this->executeSchemaCommand($input, $output, $tool, $metadatas);
        } else {
            $output->writeln('No Metadata Classes to process.');
            return 0;
        }
    }
}
