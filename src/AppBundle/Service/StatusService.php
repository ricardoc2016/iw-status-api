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
     * createQueryBuilder method.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function createQueryBuilder() : QueryBuilder
    {
        return $this->_db->createQueryBuilder();
    }


}