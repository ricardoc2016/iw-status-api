<?php
/**
 * StatusService.php file.
 *
 * @category   Frontend
 * @package    STA
 * @subpackage Service
 * @author     Ricardo Canaletti <ricardo.canaletti@lacamaradelcrimen2016.com.sw>
 * @license    MIT
 */

namespace AppBundle\Service;

use AppBundle\Exception\ApiValidationException;
use AppBundle\Model\Status;
use AppBundle\Service\ErrorCodes;
use AppBundle\Model\StatusCollection;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\RouterInterface;


/**
 * StatusService.php file.
 *
 * @category   Frontend
 * @package    STA
 * @subpackage Service
 * @author     Ricardo Canaletti <ricardo.canaletti@lacamaradelcrimen2016.com.sw>
 * @license    MIT
 */
class StatusService
{
    /**
     * Field _simpleValidatorService
     *
     * @var SimpleValidatorService
     */
    private $_simpleValidatorService;

    /**
     * Field _db.
     *
     * @var Connection
     */
    private $_db;

    /**
     * Field _logger.
     *
     * @var LoggerInterface
     */
    private $_logger;

    /**
     * Field _mailer
     *
     * @var \Swift_Mailer
     */
    private $_mailer;

    /**
     * Field _router
     *
     * @var RouterInterface
     */
    private $_router;

    /**
     * Field _mailerFrom
     *
     * @var string
     */
    private $_mailerFrom;


    /**
     * StatusService constructor.
     *
     * @param \Swift_Mailer          $mailer                 - Mailer.
     * @param SimpleValidatorService $simpleValidatorService - Simple Validator Service.
     * @param Connection             $db                     - DB.
     * @param LoggerInterface        $logger                 - Logger.
     * @param RouterInterface        $router                 - Router.
     * @param string                 $mailerFrom             - Mailer From.
     */
    public function __construct(
        \Swift_Mailer $mailer,
        SimpleValidatorService $simpleValidatorService,
        Connection $db,
        LoggerInterface $logger,
        RouterInterface $router,
        string $mailerFrom
    ) {
        $this->_mailer = $mailer;
        $this->_simpleValidatorService = $simpleValidatorService;
        $this->_db = $db;
        $this->_logger = $logger;
        $this->_router = $router;
        $this->_mailerFrom = $mailerFrom;
    }

    /**
     * find method.
     *
     * @param array $filters - Filters.
     * @param array $options - Options.
     *
     * @throws ApiValidationException
     *
     * @return StatusCollection
     */
    public function find(array $filters = [], array $options = []) : StatusCollection
    {
        $options = array_merge(
            [
                'page'              => 1,
                'limit'             => 20,
                'orderBy'           => [
                    ['created_at', 'DESC']
                ]
            ],
            $options
        );

        if (!$this->_simpleValidatorService->isIntegerGreaterOrEqualThan($options['page'], 1)) {
            throw new ApiValidationException(ErrorCodes::ERR_INVALID_PAGE);
        }

        if (!$this->_simpleValidatorService->isIntegerGreaterOrEqualThan($options['limit'], 1)) {
            throw new ApiValidationException(ErrorCodes::ERR_INVALID_ROWS);
        }

        $qb = $this->createQueryBuilder();

        $qb->select(
            'st.id AS "id"',
            'st.status AS "status"',
            'st.created_at AS "createdAt"',
            'st.email AS "email"',
            'st.confirm_code AS "confirmCode"',
            'st.confirmed_at AS "confirmedAt"',
            'st.delete_confirm_code AS "deleteConfirmCode"',
            'st.delete_confirmed_at AS "deleteConfirmedAt"'
        )
            ->from('sta_status', 'st');

        if (isset($filters['id'])) {
            if (!$this->_simpleValidatorService->isIntegerGreaterOrEqualThan($filters['id'], 1)) {
                throw new ApiValidationException(ErrorCodes::ERR_INVALID_QUERY);
            }

            $qb->andWhere('st.id = :id')
                ->setParameter('id', $filters['id']);
        }

        if (isset($filters['status'])) {
            if (!$this->_simpleValidatorService->isStringLessOrEqualThan($filters['status'], 120)) {
                throw new ApiValidationException(ErrorCodes::ERR_INVALID_QUERY);
            }

            $qb->andWhere('st.status LIKE :statusLike')
                ->setParameter('statusLike', '%'.$filters['status'].'%');
        }

        $qb->setFirstResult($options['page'] === 1 ? 0 : (($options['page'] - 1) * $options['limit']));
        $qb->setMaxResults($options['limit']);

        if ($options['orderBy']) {
            foreach ($options['orderBy'] as $orderData) {
                $qb->addOrderBy($orderData[0], $orderData[1]);
            }
        }

        $res = $this->_db->fetchAll($qb->getSQL(), $qb->getParameters());

        $statusCollection = new StatusCollection(['collection' => $res]);

        return $statusCollection;
    }

    /**
     * findOneBy.
     *
     * @param array $filters - Filters.
     * @param array $options - Options.
     *
     * @throws ApiValidationException
     *
     * @return Status|null
     */
    public function findOneBy(array $filters, array $options = [])
    {
        $res = $this->find($filters, ['limit' => 1]);

        return $res->count() ?
            $res->getCollection()[0] :
            null;
    }

    /**
     * Creates a status.
     *
     * @param array $data - Status data.
     *
     * @throws ApiValidationException
     * @throws \Exception
     *
     * @return Status
     */
    public function create(array $data)
    {
        if (!isset($data['email'])) {
            throw new ApiValidationException(ErrorCodes::ERR_POST_MISSING_EMAIL);
        }

        $valService = $this->_simpleValidatorService;

        if (!$valService->isString($data['email'])
            || ($data['email'] !== Status::ANONYMOUS_EMAIL && !$valService->isValidEmail($data['email']))
        ) {
            throw new ApiValidationException(ErrorCodes::ERR_POST_INVALID_EMAIL);
        }

        if (!isset($data['status'])) {
            throw new ApiValidationException(ErrorCodes::ERR_POST_MISSING_STATUS);
        }

        if (!$valService->isStringLessOrEqualThan($data['status'], 120) || $data['status'] === '') {
            throw new ApiValidationException(ErrorCodes::ERR_POST_INVALID_STATUS);
        }

        $status = new Status(
            [
                'email'                 => $data['email'],
                'status'                => $data['status']
            ]
        );

        if (!$status->isAnonymous()) {
            // VERY simple confirmation code...

            $status->setConfirmCode($this->generateConfirmCode('confirm-code'));
        } else {
            $status->setConfirmedAt($status->createDateTimeInstance('now'));
        }

        $status->setCreatedAt($status->createDateTimeInstance('now'));

        try {
            $this->beginTransaction();

            $sql = 'INSERT INTO sta_status (
                status,
                email,
                created_at,
                confirm_code
            ) VALUES (
                :status,
                :email,
                :createdAt,
                :confirmCode
            )';

            $this->_db->executeUpdate(
                $sql,
                [
                    'status'                => $status->getStatus(),
                    'email'                 => $status->getEmail(),
                    'createdAt'             => $status->getCreatedAt()->format('Y-m-d H:i:s'),
                    'confirmCode'           => $status->getConfirmCode()
                ]
            );

            $status->setId($this->_db->lastInsertId());

            if (!$status->isAnonymous()) {
                $this->sendConfirmationEmail($status);
            }

            $this->commit();

            return $status;
        } catch (\Exception $e) {
            $this->rollback();

            throw $e;
        }
    }

    /**
     * Confirms a removal or a status message.
     *
     * @param Status $status - Status.
     * @param string $code   - Code.
     *
     * @throws ApiValidationException
     * @throws \Exception
     *
     * @return void
     */
    public function confirm(Status $status, string $code)
    {
        if ($status->isAnonymous()) {
            throw new ApiValidationException(ErrorCodes::ERR_CONFIRM_ANONYMOUS);
        }

        $dateField = null;
        $codeField = null;
        $method = null;

        if ($status->getDeleteConfirmCode() === $code) {
            // Delete directly

            $status->setDeleteConfirmedAt($status->createDateTimeInstance('now'))
                ->setDeleteConfirmCode(null);

            $this->delete($status);

            return;
        }

        if ($status->getConfirmCode() !== $code) {
            throw new ApiValidationException(ErrorCodes::ERR_CONFIRM_CODE_NOT_FOUND);
        }

        $status->setConfirmedAt($status->createDateTimeInstance('now'));

        try {
            $this->beginTransaction();

            $sql = 'UPDATE sta_status
            SET
                confirmed_at = :currentDate,
                confirm_code = :code
            WHERE id = :id';

            $this->_db->executeUpdate(
                $sql,
                [
                    'id'            => $status->getId(),
                    'currentDate'   => $status->getConfirmedAt()->format('Y-m-d H:i:s'),
                    'code'          => null
                ]
            );

            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();

            throw $e;
        }
    }

    /**
     * delete.
     *
     * @param Status $status - Status.
     *
     * @throws \Exception
     *
     * @return void
     */
    public function delete(Status $status)
    {
        if ($status->isAnonymous()) {
            throw new ApiValidationException(ErrorCodes::ERR_DELETE_ANONYMOUS);
        }

        try {
            $this->beginTransaction();

            if ($status->isDeleteConfirmed()) {
                // Do remove it

                $this->_db->executeUpdate(
                    'DELETE FROM sta_status WHERE id = :id',
                    ['id' => $status->getId()]
                );
            } else {
                $status->setDeleteConfirmCode($this->generateConfirmCode('delete-confirm-code'));

                $this->_db->executeUpdate(
                    'UPDATE sta_status SET delete_confirm_code = :code WHERE id = :id',
                    [
                        'id'                => $status->getId(),
                        'code'              => $status->getDeleteConfirmCode()
                    ]
                );

                $this->sendDeleteConfirmationEmail($status);
            }

            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();

            throw $e;
        }
    }

    /**
     * sendDeleteConfirmationEmail.
     *
     * @param Status $status - Status.
     *
     * @return void
     */
    protected function sendDeleteConfirmationEmail(Status $status)
    {
        $link = $this->_router->generate(
            'sta_confirm_by_code',
            [
                'id'        => $status->getId(),
                'code'      => $status->getDeleteConfirmCode()
            ],
            RouterInterface::ABSOLUTE_PATH
        );
        $id = $status->getId();

        $html = <<<HTML
Hi,

You need to confirm the removal of Status message ID {$id}. Please, click in the following link:

- <a href="{$link}">Confirm!</a>

Thanks.
HTML;


        $this->sendEmail($status->getEmail(), 'Removal Confirmation E-Mail', $html);
    }

    /**
     * sendConfirmationEmail.
     *
     * @param Status $status - Status.
     *
     * @return void
     */
    protected function sendConfirmationEmail(Status $status)
    {
        $link = $this->_router->generate(
            'sta_confirm_by_code',
            [
                'id'        => $status->getId(),
                'code'      => $status->getConfirmCode()
            ],
            RouterInterface::ABSOLUTE_PATH
        );

        $html = <<<HTML
Hi,

You need to confirm your E-Mail. Please, click in the following link:

- <a href="{$link}">Confirm!</a>

Thanks.
HTML;


        $this->sendEmail($status->getEmail(), 'Status Message Confirmation E-Mail', $html);
    }

    /**
     * Sends a confirmation email.
     *
     * @param string $email   - E-Mail.
     * @param string $subject - Subject.
     * @param string $body    - Body.
     *
     * @return void
     */
    protected function sendEmail($email, $subject, $body)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($this->_mailerFrom)
            ->setTo($email)
            ->setBody($body)
        ;

        $res = $this->_mailer->send($message);

        if (!$res) {
            throw new \RuntimeException('Could NOT send an e-mail to "'.$email.'".');
        }
    }

    /**
     * createQueryBuilder method.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function createQueryBuilder() : QueryBuilder
    {
        return $this->_db->createQueryBuilder();
    }

    /**
     * beginTransaction.
     *
     * @return self
     */
    protected function beginTransaction() : self
    {
        if (!$this->isTransactionActive()) {
            $this->_db->beginTransaction();
        }

        return $this;
    }

    /**
     * commit.
     *
     * @return self
     */
    protected function commit() : self
    {
        if ($this->isTransactionActive()) {
            $this->_db->commit();
        }

        return $this;
    }

    /**
     * rollback.
     *
     * @return self
     */
    protected function rollback() : self
    {
        if ($this->isTransactionActive()) {
            $this->_db->rollback();
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
        return $this->_db->isTransactionActive();
    }

    /**
     * generateConfirmCode.
     *
     * @return string
     */
    protected function generateConfirmCode($prefix) : string
    {
        return sha1(uniqid($prefix, true).microtime(true).rand(1000, 9999));
    }
}