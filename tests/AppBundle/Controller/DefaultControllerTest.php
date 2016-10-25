<?php

/**
 * InstallCommand.php file.
 *
 * @category   Frontend
 * @package    STA
 * @subpackage Test/Controller
 * @author     Ricardo Canaletti <ricardo.canaletti@lacamaradelcrimen2016.com.sw>
 * @license    MIT
 */


namespace Tests\AppBundle\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * InstallCommand.php file.
 *
 * @category   Frontend
 * @package    STA
 * @subpackage Test/Controller
 * @author     Ricardo Canaletti <ricardo.canaletti@lacamaradelcrimen2016.com.sw>
 * @license    MIT
 */

class DefaultControllerTest extends WebTestCase
{
    const TEST_STATUS_PREFIX = 'TEST!!!';


    /**
     * Field db.
     *
     * @var Connection
     */
    protected static $db;

    /**
     * Field container.
     *
     * @var ContainerInterface
     */
    protected static $container;


    /**
     * setUpBeforeClass method.
     *
     * @return void
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::$container = self::$kernel->getContainer();
        self::$db = self::$container->get('database_connection');
    }

    /**
     * setUp method.
     *
     * @return void
     */
    public function setUp()
    {
        parent::tearDown();
    }

    /**
     * tearDown method.
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * cleanUp method.
     *
     * @return void
     */
    public function cleanUp()
    {
        self::$db->exec('DELETE FROM sta_status');
    }


    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        print_r($client->getResponse()->getContent());
    }
}
