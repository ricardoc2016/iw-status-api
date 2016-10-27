<?php

/**
 * InstallCommandTest.php file.
 *
 * @category   Frontend
 * @package    STA
 * @subpackage Tests/AppBundle/Functional/Controller
 * @author     Ricardo Canaletti <ricardo.canaletti@lacamaradelcrimen2016.com.sw>
 * @license    MIT
 */


namespace Tests\AppBundle\Functional\Controller;

use AppBundle\Command\InstallCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tests\AppBundle\Functional\AbstractTestCase;

/**
 * InstallCommandTest.php file.
 *
 * @category   Frontend
 * @package    STA
 * @subpackage Tests/AppBundle/Functional/Controller
 * @author     Ricardo Canaletti <ricardo.canaletti@lacamaradelcrimen2016.com.sw>
 * @license    MIT
 */

class InstallCommandTest extends AbstractTestCase
{
    public function test_installCommand()
    {
        $this->beginTransaction();

        $this->createStatus('a@a.com', 'ASDASDA', '2012-01-01 02:00:00');

        $this->commit();

        $application = new Application(self::$kernel);

        $command = new InstallCommand();
        $command->setContainer(self::$container);

        $application->add($command);

        $command = $application->find('sta:install');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName()
        ));

        $output = $commandTester->getDisplay();

        // SQL Lite DB should be rebuilt.

        $res = self::$db->fetchAll('SELECT * FROM sta_status');

        $this->assertEmpty($res);
    }
}
