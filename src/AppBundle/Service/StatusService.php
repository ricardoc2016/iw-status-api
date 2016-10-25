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

use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;


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
     * @param Connection      $db     - DB.
     * @param LoggerInterface $logger - Logger.
     */
    public function __construct(Connection $db, LoggerInterface $logger)
    {
        $this->_db = $db;
        $this->_logger = $logger;
    }

    /**
     * find method.
     *
     * @param array $filters - Filters.
     * @param array $options - Options.
     *
     * @return array
     */
    public function find(array $filters = [], array $options = [])
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

        $qb = $this->createQueryBuilder();

        $qb->select(
            'st.id',
            'st.status',
            'st.created_at',
            'st.email'
        )
            ->from('sta_status', 'st');

        if (isset($filters['status'])) {
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

        return $res;
    }

    /**
     * createQueryBuilder method.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function createQueryBuilder()
    {
        return $this->_db->createQueryBuilder();
    }
}