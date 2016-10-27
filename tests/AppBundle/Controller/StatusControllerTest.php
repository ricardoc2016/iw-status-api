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
use AppBundle\Service\StatusService;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

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

    public function testGetById()
    {
        $client = static::createClient();

        $client->request('GET', '/status/asd');
        $response = $client->getResponse();

        $this->assertEquals(404, $response->getStatusCode());

        $json = json_decode($response->getContent(), true);

        $this->assertCount(3, $json);

        $this->assertEquals($json['code'], ErrorCodes::ERR_RESOURCE_NOT_FOUND);
        $this->assertEquals($json['message'], ErrorCodes::getMessage(ErrorCodes::ERR_RESOURCE_NOT_FOUND));
        $this->assertEquals($json['link'], self::$container->getParameter('site_url').'/docs');

        $client->request('GET', '/status/123131321211');
        $response = $client->getResponse();

        $this->assertEquals(404, $response->getStatusCode());

        $json = json_decode($response->getContent(), true);

        $this->assertCount(3, $json);

        $this->assertEquals($json['code'], ErrorCodes::ERR_STATUS_NOT_FOUND);
        $this->assertEquals($json['message'], ErrorCodes::getMessage(ErrorCodes::ERR_STATUS_NOT_FOUND));
        $this->assertEquals($json['link'], self::$container->getParameter('site_url').'/docs');

        $this->beginTransaction();

        $this->createStatus('a@a.com', 'My status', '2015-01-01 00:00:00')
            ->createStatus('b@b.com', 'My other status', '2015-01-01 01:00:00')
            ->createStatus('c@c.com', 'My oooother status', '2015-01-01 02:00:00');

        $this->commit();

        $statusService = $this->getStatusService();
        $status = $statusService->findOneBy(['email' => 'a@a.com']);

        $client->request('GET', '/status/'.$status->getId());
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode($response->getContent(), true);

        $this->assertCount(4, $json);

        $this->assertEquals($status->getId(), $json['id']);
        $this->assertEquals($status->getEmail(), $json['email']);
        $this->assertEquals($status->getStatus(), $json['status']);
        $this->assertEquals($status->getCreatedAt()->format('Y-m-d\TH:i:s\Z'), $json['created_at']);
    }

    public function testPost()
    {
        $client = static::createClient();

        $data = [
            'email'             => Status::ANONYMOUS_EMAIL,
            'status'            => 'My Status!'
        ];

        $client->enableProfiler();

        $this->postStatus($client, $data);

        $mailCollector = $client->getProfile()->getCollector('swiftmailer');

        $this->assertEquals(0, $mailCollector->getMessageCount());

        $response = $client->getResponse();

        $this->assertEquals(201, $response->getStatusCode());
        $json = json_decode($response->getContent(), true);

        $this->assertCount(4, $json);
        $this->assertInternalType('int', $json['id']);
        $this->assertEquals($data['email'], $json['email']);
        $this->assertEquals($data['status'], $json['status']);

        $date = new \DateTime($json['created_at']);

        $this->assertEquals($json['created_at'], $date->format('Y-m-d\TH:i:s\Z'));

        // Send a non-anonymous status

        $data = [
            'email'             => 'a@a.com',
            'status'            => 'My Status 2!'
        ];

        $client->enableProfiler();

        $this->postStatus($client, $data);

        $status = $this->getStatusService()->findOneBy(['status' => 'My Status 2!']);

        $mailCollector = $client->getProfile()->getCollector('swiftmailer');

        $this->assertEquals(1, $mailCollector->getMessageCount());

        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];

        // Asserting email data
        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertEquals('Status Message Confirmation E-Mail', $message->getSubject());
        $this->assertEquals(self::$container->getParameter('mailer_from'), key($message->getFrom()));
        $this->assertEquals($status->getEmail(), key($message->getTo()));

        $url = self::$container->get('router')->generate(
            'sta_confirm_by_code',
            [
                'id'        => $status->getId(),
                'code'      => $status->getConfirmCode()
            ],
            RouterInterface::ABSOLUTE_PATH
        );
        $link = '<a href="'.$url.'">';

        $this->assertContains($link, $message->getBody());

        $response = $client->getResponse();

        $this->assertEquals(201, $response->getStatusCode());
        $json = json_decode($response->getContent(), true);

        $this->assertCount(4, $json);
        $this->assertInternalType('int', $json['id']);
        $this->assertEquals($data['email'], $json['email']);
        $this->assertEquals($data['status'], $json['status']);

        $date = new \DateTime($json['created_at']);

        $this->assertEquals($json['created_at'], $date->format('Y-m-d\TH:i:s\Z'));
    }

    public function testDelete()
    {
        $client = static::createClient();

        $client->request('DELETE', '/status/2312312');

        $response = $client->getResponse();

        $this->assertEquals(404, $response->getStatusCode());

        $json = json_decode($response->getContent(), true);

        $this->assertEquals($json['code'], ErrorCodes::ERR_STATUS_NOT_FOUND);
        $this->assertEquals($json['message'], ErrorCodes::getMessage(ErrorCodes::ERR_STATUS_NOT_FOUND));
        $this->assertEquals($json['link'], self::$container->getParameter('site_url').'/docs');

        $this->beginTransaction();

        $this->createStatus('a@a.com', 'My status', '2015-01-01 00:00:00')
            ->createStatus('b@b.com', 'My other status', '2015-01-01 01:00:00')
            ->createStatus(Status::ANONYMOUS_EMAIL, 'My oooother status', '2015-01-01 02:00:00');

        $this->commit();

        $statusService = $this->getStatusService();

        $status = $statusService->findOneBy(['status' => 'My status']);
        $anonymStatus = $statusService->findOneBy(['status' => 'My oooother status']);

        // Anonymous status CAN'T be deleted

        $client->request('DELETE', '/status/'.$anonymStatus->getId());

        $response = $client->getResponse();

        $this->assertEquals(400, $response->getStatusCode());

        $json = json_decode($response->getContent(), true);

        $this->assertEquals($json['code'], ErrorCodes::ERR_DELETE_ANONYMOUS);
        $this->assertEquals($json['message'], ErrorCodes::getMessage(ErrorCodes::ERR_DELETE_ANONYMOUS));
        $this->assertEquals($json['link'], self::$container->getParameter('site_url').'/docs');

        // Test deleting a status

        $client->enableProfiler();

        $client->request('DELETE', '/status/'.$status->getId());

        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode($response->getContent(), true);

        $mailCollector = $client->getProfile()->getCollector('swiftmailer');

        $this->assertEquals(1, $mailCollector->getMessageCount());

        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];

        // Asserting email data
        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertEquals('Removal Confirmation E-Mail', $message->getSubject());
        $this->assertEquals(self::$container->getParameter('mailer_from'), key($message->getFrom()));
        $this->assertEquals($status->getEmail(), key($message->getTo()));

        $status = $statusService->findOneBy(['status' => 'My status']);

        $this->assertNotNull($status);

        $url = self::$container->get('router')->generate(
            'sta_confirm_by_code',
            [
                'id'        => $status->getId(),
                'code'      => $status->getDeleteConfirmCode()
            ],
            RouterInterface::ABSOLUTE_PATH
        );
        $link = '<a href="'.$url.'">';

        $this->assertContains($link, $message->getBody());

        // Confirm with an invalid code

        $client->request('GET', '/status/'.$status->getId().'/confirmation/asdadas');

        $response = $client->getResponse();

        $this->assertEquals(400, $response->getStatusCode());

        $json = json_decode($response->getContent(), true);

        $this->assertEquals($json['code'], ErrorCodes::ERR_CONFIRM_CODE_NOT_FOUND);
        $this->assertEquals($json['message'], ErrorCodes::getMessage(ErrorCodes::ERR_CONFIRM_CODE_NOT_FOUND));
        $this->assertEquals($json['link'], self::$container->getParameter('site_url').'/docs');

        // Confirm with valid code...

        $client->request('GET', '/status/'.$status->getId().'/confirmation/'.$status->getDeleteConfirmCode());

        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $status = $statusService->findOneBy(['status' => 'My status']);

        $this->assertNull($status);
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
        $this->assertEquals($json['link'], self::$container->getParameter('site_url').'/docs');
    }

    /**
     * @dataProvider postErrorsDataProvider
     */
    public function test_postErrors(array $data, $code, $statusCode = Response::HTTP_BAD_REQUEST)
    {
        $client = static::createClient();

        $this->postStatus($client, $data);

        $response = $client->getResponse();

        $this->assertEquals($statusCode, $response->getStatusCode());

        $json = json_decode($response->getContent(), true);

        $this->assertCount(3, $json);

        $this->assertEquals($json['code'], $code);
        $this->assertEquals($json['message'], ErrorCodes::getMessage($code));
        $this->assertEquals($json['link'], self::$container->getParameter('site_url').'/docs');
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

    public function postErrorsDataProvider()
    {
        return [
            [
                [],
                ErrorCodes::ERR_POST_MISSING_EMAIL
            ],
            [
                [
                    'email'     => 'asd'
                ],
                ErrorCodes::ERR_POST_INVALID_EMAIL
            ],
            [
                [
                    'email'     => 'asd@'
                ],
                ErrorCodes::ERR_POST_INVALID_EMAIL
            ],
            [
                [
                    'email'     => Status::ANONYMOUS_EMAIL,
                    'status'    => null
                ],
                ErrorCodes::ERR_POST_MISSING_STATUS
            ],
            [
                [
                    'email'     => Status::ANONYMOUS_EMAIL,
                    'status'    => ''
                ],
                ErrorCodes::ERR_POST_INVALID_STATUS
            ],
            [
                [
                    'email'     => 'a@a.com',
                    'status'    => str_repeat('a', 121)
                ],
                ErrorCodes::ERR_POST_INVALID_STATUS
            ],
        ];
    }

    // Helper Methods

    protected function postStatus(Client $client, array $data)
    {
        $client->request('POST', '/status', [], [], ['Content-Type' => 'application/json'], json_encode($data));
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
}
