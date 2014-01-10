<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2013 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\Schema\Table;
use Doctrine\ORM\EntityManager;
use Zend\Console\ColorInterface;
use Zend\Console\Request as ConsoleRequest;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\Controller\AbstractActionController;
use ZF\Configuration\ConfigResource;
use Zend\Config\Writer\PhpArray as PhpArrayWriter;
use Zend\Filter\FilterChain;
use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\Console\Prompt;
use Doctrine\DBAL\DBALException;

class ApplicationController extends AbstractActionController
{
    public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);
        $events->attach('dispatch', function($e) {
            $request = $e->getRequest();
            if (!$request instanceof ConsoleRequest) {
                throw new \RuntimeException(sprintf(
                    '%s can only be executed in a console environment',
                    __CLASS__
                ));
            }
        }, 100);
        return $this;
    }

    public function purgeAction()
    {
        /** @var Console $console */
        $console = $this->getServiceLocator()->get('Console');

        $objectManagerAlias = 'doctrine.entitymanager.orm_default';
        /** @var EntityManager $objectManager */
        $objectManager = $this->getServiceLocator()->get($objectManagerAlias);
        $metadataFactory = $objectManager->getMetadataFactory();

        // Collect table names
        $tables = [];
        foreach($metadataFactory->getAllMetadata() as $metadata) {
             $tables[] = $metadata->getTableName();
        }

        // Display prompt
        if(strtolower(Prompt\Line::prompt(
            'Are you sure you want to delete all data from ' . count($tables). ' tables in the database? [type yes] '
        )) !== 'yes'){
            $console->writeLine('Aborting');
            return;
        }

        // Truncate tables
        try {
            $console->writeLine('- Disabling foreign key checks');
            $objectManager->getConnection()->executeQuery('SET FOREIGN_KEY_CHECKS=0');

            $console->write('- Truncating tables ');
            foreach($tables as $table){
                $objectManager->getConnection()->executeQuery(sprintf(
                    'TRUNCATE TABLE %s',
                    $objectManager->getConnection()->quoteIdentifier($table)
                ));
                $console->write('.', ColorInterface::GREEN);
            }
            $console->writeLine(' done!', ColorInterface::GREEN);

            $console->writeLine('- Re-enabling foreign key checks');
            $objectManager->getConnection()->executeQuery('SET FOREIGN_KEY_CHECKS=1');
        } catch(DBALException $e) {
            $console->writeLine();
            $console->writeLine(sprintf(
                "A DB exception occured!\n%s: %s",
                get_class($e),
                $e->getMessage()
            ), ColorInterface::YELLOW);
            throw $e;
        }

        $console->writeLine('All done! The database is now clean.', ColorInterface::GREEN);
    }

    public function dropAction()
    {
        $console = $this->getConsole();
        $objectManagerAlias = 'doctrine.entitymanager.orm_default';
        /** @var EntityManager $objectManager */
        $objectManager = $this->getServiceLocator()->get($objectManagerAlias);

        // Collect table names
        $tables = array_map(
            function(Table $table){
                return $table->getName();
            },
            $objectManager->getConnection()->getSchemaManager()->listTables()
        );

        // Display prompt
        if(strtolower(Prompt\Line::prompt(
            'Are you sure you want to DROP ' . count($tables). ' tables from the database? [type yes] '
        )) !== 'yes'){
            $console->writeLine('Aborting');
            return;
        }

        // Truncate tables
        try {
            $console->writeLine('- Disabling foreign key checks');
            $objectManager->getConnection()->executeQuery('SET FOREIGN_KEY_CHECKS=0');

            $console->write('- Dropping tables ');
            foreach($tables as $table){
                $objectManager->getConnection()->executeQuery(sprintf(
                    'DROP TABLE IF EXISTS %s',
                    $objectManager->getConnection()->quoteIdentifier($table)
                ));
                $console->write('.', ColorInterface::GREEN);
            }
            $console->writeLine(' done!', ColorInterface::GREEN);

            $console->writeLine('- Re-enabling foreign key checks');
            $objectManager->getConnection()->executeQuery('SET FOREIGN_KEY_CHECKS=1');
        }catch(DBALException $e){
            $console->writeLine();
            $console->writeLine(sprintf(
                "A DB exception occured!\n%s: %s",
                get_class($e),
                $e->getMessage()
            ), ColorInterface::YELLOW);
            throw $e;
        }

        $console->writeLine('All done! Tables have been dropped.', ColorInterface::GREEN);
    }


    /**
     * @return Console
     */
    protected function getConsole()
    {
        return $this->getServiceLocator()->get('Console');
    }
}

