<?php

/**
 * AbstractTestCase.php file.
 *
 * @category   Frontend
 * @package    STA
 * @subpackage Tests/AppBundle/Functional
 * @author     Ricardo Canaletti <ricardo.canaletti@lacamaradelcrimen2016.com.sw>
 * @license    MIT
 */


namespace Tests\AppBundle\Functional;

use AppBundle\Service\ErrorCodes;
use AppBundle\Model\Status;
use AppBundle\Service\StatusService;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

/**
 * AbstractTestCase.php file.
 *
 * @category   Frontend
 * @package    STA
 * @subpackage Tests/AppBundle/Functional
 * @author     Ricardo Canaletti <ricardo.canaletti@lacamaradelcrimen2016.com.sw>
 * @license    MIT
 */

class AbstractTestCase extends WebTestCase
{
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

        self::bootKernel();

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
        parent::setUp();

        $this->cleanUp();
    }

    /**
     * tearDown method.
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        $this->cleanUp();
    }

    /**
     * cleanUp method.
     *
     * @return void
     */
    public function cleanUp()
    {
        $this->beginTransaction();

        self::$db->executeUpdate('DELETE FROM sta_status');

        $this->commit();
    }

    /**
     * beginTransaction.
     *
     * @return self
     */
    protected function beginTransaction() : self
    {
        if (!$this->isTransactionActive()) {
            self::$db->beginTransaction();
        }

        return $this;
    }

    /**
     * isTransactionActive.
     *
     * @return bool
     */
    protected function isTransactionActive() : bool
    {
        return self::$db->isTransactionActive();
    }

    /**
     * commit.
     *
     * @throws \Doctrine\DBAL\ConnectionException
     *
     * @return self
     */
    protected function commit() : self
    {
        if ($this->isTransactionActive()) {
            self::$db->commit();
        }

        return $this;
    }

    /**
     * rollback.
     *
     * @throws \Doctrine\DBAL\ConnectionException
     *
     * @return self
     */
    protected function rollback() : self
    {
        if ($this->isTransactionActive()) {
            self::$db->rollback();
        }

        return $this;
    }

    /**
     * getStatusService.
     *
     * @return StatusService
     */
    protected function getStatusService() : StatusService
    {
        return self::$container->get('sta.service.status');
    }

    /**
     * Creates a status on the DB.
     *
     * @param string      $email     - Email.
     * @param string      $status    - Status.
     * @param null|string $createdAt - Created At.
     *
     * @throws \Doctrine\DBAL\DBALException
     *
     * @return self
     */
    protected function createStatus(string $email, string $status, string $createdAt) : self
    {
        self::$db->executeUpdate(
            'INSERT INTO sta_status (
                email,
                status,
                created_at,
                confirm_code
            ) VALUES (
                :email,
                :status,
                :createdAt,
                :confirmCode
            )',
            [
                'email'             => $email,
                'status'            => $status,
                'createdAt'         => $createdAt,
                'confirmCode'       => $email === Status::ANONYMOUS_EMAIL ?
                    null :
                    rand(100000, 999999)
            ]
        );

        return $this;
    }
}
