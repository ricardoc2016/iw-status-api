<?php
/**
 * InstallCommand.php file.
 *
 * @category   Frontend
 * @package    STA
 * @subpackage Command
 * @author     Ricardo Canaletti <ricardo.canaletti@lacamaradelcrimen2016.com.sw>
 * @license    MIT
 */

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * InstallCommand.php file.
 *
 * @category   Frontend
 * @package    STA
 * @subpackage Command
 * @author     Ricardo Canaletti <ricardo.canaletti@lacamaradelcrimen2016.com.sw>
 * @license    MIT
 */
class InstallCommand extends Command implements ContainerAwareInterface
{
    /**
     * Field _container.
     *
     * @var ContainerInterface
     */
    private $_container;

    /**
     * setContainer method.
     *
     * @param ContainerInterface|null $container - DIC
     *
     * @return void
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->_container = $container;
    }


    /**
     * configure method.
     *
     * @return void
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('sta:install')
            ->setDescription('Executes installation tasks.');
    }

    /**
     * execute method.
     *
     * @param InputInterface  $input  - Input.
     * @param OutputInterface $output - Output.
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('');
        $output->writeln(' <info>Installing IW Status API...</info>');
        $output->writeln('');

        $dbDriver = $this->_container->getParameter('database_driver');

        if ($dbDriver === 'pdo_sqlite') {
            $output->writeln('<comment>   . Creating SQLite Database Schema...</comment>');

            $db = $this->_container->get('database_connection');

            $db->exec('DROP TABLE IF EXISTS sta_status');

            $db->exec(
                'CREATE TABLE IF NOT EXISTS sta_status (
                    id INTEGER PRIMARY KEY NOT NULL,
                    email TEXT NOT NULL,
                    status TEXT NOT NULL,
                    created_at DATE NOT NULL,
                    confirmed_at DATE,
                    confirm_code TEXT
                )'
            );

            $db->exec(
                'CREATE INDEX idx_sta_status_01 ON sta_status (status)'
            );

            $db->exec(
                'CREATE INDEX idx_sta_status_02 ON sta_status (email)'
            );
        }

        $output->writeln('');
        $output->writeln(' <info>Operation Complete!</info>');
        $output->writeln('');
    }

    
}