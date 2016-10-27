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

use AppBundle\Service\ErrorCodes;
use AppBundle\Model\Status;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * InstallCommand.php file.
 *
 * @category   Frontend
 * @package    STA
 * @subpackage Test/Controller
 * @author     Ricardo Canaletti <ricardo.canaletti@lacamaradelcrimen2016.com.sw>
 * @license    MIT
 */

class StatusControllerTest extends WebTestCase
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

    public function testGet()
    {
        $client = static::createClient();

        $client->request('GET', '/status');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode($response->getContent(), true);

        $this->assertEmpty($json);

        $this->beginTransaction();

        $this->createStatus('a@a.com', 'My status', '2015-01-01 00:00:00')
            ->createStatus('b@b.com', 'My other status', '2015-01-01 01:00:00')
            ->createStatus('c@c.com', 'My oooother status', '2015-01-01 02:00:00');

        $this->commit();

        $client->request('GET', '/status');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode($response->getContent(), true);

        $this->assertNotEmpty($json);
        $this->assertCount(3, $json);

        $this->assertInternalType('int', $json[0]['id']);
        $this->assertEquals('c@c.com', $json[0]['email']);
        $this->assertEquals('My oooother status', $json[0]['status']);
        $this->assertEquals('2015-01-01T02:00:00Z', $json[0]['created_at']);

        $this->assertInternalType('int', $json[1]['id']);
        $this->assertEquals('b@b.com', $json[1]['email']);
        $this->assertEquals('My other status', $json[1]['status']);
        $this->assertEquals('2015-01-01T01:00:00Z', $json[1]['created_at']);

        $this->assertInternalType('int', $json[2]['id']);
        $this->assertEquals('a@a.com', $json[2]['email']);
        $this->assertEquals('My status', $json[2]['status']);
        $this->assertEquals('2015-01-01T00:00:00Z', $json[2]['created_at']);

        // Use page and limit

        $client->request('GET', '/status?p=1&r=1');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode($response->getContent(), true);

        $this->assertNotEmpty($json);
        $this->assertCount(1, $json);

        $this->assertInternalType('int', $json[0]['id']);
        $this->assertEquals('c@c.com', $json[0]['email']);
        $this->assertEquals('My oooother status', $json[0]['status']);
        $this->assertEquals('2015-01-01T02:00:00Z', $json[0]['created_at']);

        // Other page...

        $client->request('GET', '/status?p=2&r=1');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode($response->getContent(), true);

        $this->assertNotEmpty($json);
        $this->assertCount(1, $json);

        $this->assertInternalType('int', $json[0]['id']);
        $this->assertEquals('b@b.com', $json[0]['email']);
        $this->assertEquals('My other status', $json[0]['status']);
        $this->assertEquals('2015-01-01T01:00:00Z', $json[0]['created_at']);

        // Other page + rows...

        $client->request('GET', '/status?p=1&r=2');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode($response->getContent(), true);

        $this->assertNotEmpty($json);
        $this->assertCount(2, $json);

        $this->assertInternalType('int', $json[0]['id']);
        $this->assertEquals('c@c.com', $json[0]['email']);
        $this->assertEquals('My oooother status', $json[0]['status']);
        $this->assertEquals('2015-01-01T02:00:00Z', $json[0]['created_at']);

        $this->assertInternalType('int', $json[1]['id']);
        $this->assertEquals('b@b.com', $json[1]['email']);
        $this->assertEquals('My other status', $json[1]['status']);
        $this->assertEquals('2015-01-01T01:00:00Z', $json[1]['created_at']);

        // Other page + rows...

        $client->request('GET', '/status?p=2&r=2');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode($response->getContent(), true);

        $this->assertNotEmpty($json);
        $this->assertCount(1, $json);

        $this->assertInternalType('int', $json[0]['id']);
        $this->assertEquals('a@a.com', $json[0]['email']);
        $this->assertEquals('My status', $json[0]['status']);
        $this->assertEquals('2015-01-01T00:00:00Z', $json[0]['created_at']);

        // Query...

        $client->request('GET', '/status?p=1&r=1&q=%20other');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode($response->getContent(), true);

        $this->assertNotEmpty($json);
        $this->assertCount(1, $json);

        $this->assertInternalType('int', $json[0]['id']);
        $this->assertEquals('b@b.com', $json[0]['email']);
        $this->assertEquals('My other status', $json[0]['status']);
        $this->assertEquals('2015-01-01T01:00:00Z', $json[0]['created_at']);

        // Query (2)

        $client->request('GET', '/status?p=2&r=1&q=%20other');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode($response->getContent(), true);

        $this->assertEmpty($json);

        // Query (3)

        $client->request('GET', '/status?p=1&r=20&q=other');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode($response->getContent(), true);

        $this->assertNotEmpty($json);
        $this->assertCount(2, $json);

        $this->assertInternalType('int', $json[0]['id']);
        $this->assertEquals('c@c.com', $json[0]['email']);
        $this->assertEquals('My oooother status', $json[0]['status']);
        $this->assertEquals('2015-01-01T02:00:00Z', $json[0]['created_at']);

        $this->assertInternalType('int', $json[1]['id']);
        $this->assertEquals('b@b.com', $json[1]['email']);
        $this->assertEquals('My other status', $json[1]['status']);
        $this->assertEquals('2015-01-01T01:00:00Z', $json[1]['created_at']);
    }

    /**
     * @dataProvider getErrorsDataProvider
     */
    public function test_getErrors($url, $code, $statusCode = Response::HTTP_BAD_REQUEST)
    {
        $client = static::createClient();
        $client->request('GET', $url);
        $response = $client->getResponse();

        $this->assertEquals($statusCode, $response->getStatusCode());

        $json = json_decode($response->getContent(), true);

        $this->assertCount(3, $json);

        $this->assertEquals($json['code'], $code);
        $this->assertEquals($json['message'], ErrorCodes::getMessage($code));
    }



    // Data Providers

    public function getErrorsDataProvider()
    {
        return [
            [
                '/status?p=a',
                ErrorCodes::ERR_INVALID_PAGE
            ],
            [
                '/status?p=1&r=a',
                ErrorCodes::ERR_INVALID_ROWS
            ],
            [
                '/status?p=0',
                ErrorCodes::ERR_INVALID_PAGE
            ],
            [
                '/status?p=-1',
                ErrorCodes::ERR_INVALID_PAGE
            ],
            [
                '/status?p=1&r=0',
                ErrorCodes::ERR_INVALID_ROWS
            ],
            [
                '/status?p=1&r=1&q='.str_repeat('a', 121),
                ErrorCodes::ERR_INVALID_QUERY
            ],
        ];
    }

    // Helper Methods

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
}