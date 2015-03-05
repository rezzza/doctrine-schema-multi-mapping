<?php

namespace Rezzza\DoctrineSchemaMultiMapping\UI\Console;

use Doctrine\Bundle\DoctrineBundle\Command\Proxy\CreateSchemaDoctrineCommand;
use Doctrine\Bundle\DoctrineBundle\Command\Proxy\DoctrineCommandHelper;
use Doctrine\ORM\Tools\SchemaTool;
use Rezzza\DoctrineSchemaMultiMapping\Domain\MetadataFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;

class SchemaCreateCommand extends CreateSchemaDoctrineCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('rezzza:doctrine-multi-mapping:schema:create')
            ->setDescription('Executes (or dumps) the SQL needed to generate the database schema')
            ->addOption('em', null, InputOption::VALUE_OPTIONAL, 'The entity manager to use for this command')
            ->setHelp(<<<EOT
The <info>rezzza:doctrine-multi-mapping:schema:create</info> command executes the SQL needed to
generate the database schema for the default entity manager:

<info>php app/console rezzza:doctrine-multi-mapping:schema:create</info>

You can also generate the database schema for a specific entity manager:

<info>php app/console rezzza:doctrine-multi-mapping:schema:create --em=default</info>

Finally, instead of executing the SQL, you can output the SQL:

<info>php app/console rezzza:doctrine-multi-mapping:schema:create --dump-sql</info>
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
