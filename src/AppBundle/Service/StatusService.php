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
     * StatusService constructor.
     *
     * @param SimpleValidatorService $simpleValidatorService - Simple Validator Service.
     * @param Connection             $db                     - DB.
     * @param LoggerInterface        $logger                 - Logger.
     */
    public function __construct(SimpleValidatorService $simpleValidatorService, Connection $db, LoggerInterface $logger)
    {
        $this->_simpleValidatorService = $simpleValidatorService;
        $this->_db = $db;
        $this->_logger = $logger;
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
            'st.email AS "email"'
        )
            ->from('sta_status', 'st');

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

        return $res ?
            $res[0] :
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

            $status->setConfirmCode(sha1(uniqid('confirm-code', true).microtime(true).rand(1000, 9999)));
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

            $this->sendEmail($status);

            $this->commit();

            return $status;
        } catch (\Exception $e) {
            $this->rollback();

            throw $e;
        }
    }

    /**
     * Sends a confirmation email.
     *
     * @param Status $status - Status.
     *
     * @return void
     */
    protected function sendEmail(Status $status)
    {

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
}