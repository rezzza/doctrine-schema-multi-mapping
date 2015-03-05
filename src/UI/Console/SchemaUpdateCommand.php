<?php

namespace Rezzza\DoctrineSchemaMultiMapping\UI\Console;

use Doctrine\Bundle\DoctrineBundle\Command\Proxy\DoctrineCommandHelper;
use Doctrine\Bundle\DoctrineBundle\Command\Proxy\UpdateSchemaDoctrineCommand;
use Doctrine\ORM\Tools\SchemaTool;
use Rezzza\DoctrineSchemaMultiMapping\Domain\MetadataFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;

class SchemaUpdateCommand extends UpdateSchemaDoctrineCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('rezzza:doctrine-multi-mapping:schema:update')
            ->setDescription('Executes (or dumps) the SQL needed to update the database schema to match the current mapping metadata')
            ->addOption('em', null, InputOption::VALUE_OPTIONAL, 'The entity manager to use for this command')
            ->setHelp(<<<EOT
The <info>rezzza:doctrine-multi-mapping:schema:update</info> command generates the SQL needed to
synchronize the database schema with the current mapping metadata of the
default entity manager.

For example, if you add metadata for a new column to an entity, this command
would generate and output the SQL needed to add the new column to the database:

<info>php app/console rezzza:doctrine-multi-mapping:schema:update --dump-sql</info>

Alternatively, you can execute the generated queries:

<info>php app/console rezzza:doctrine-multi-mapping:schema:update --force</info>

You can also update the database schema for a specific entity manager:

<info>php app/console rezzza:doctrine-multi-mapping:schema:update --em=default</info>
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
