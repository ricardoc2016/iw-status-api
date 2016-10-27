<?php
/**
 * StatusCollection.php file.
 *
 * @category   Frontend
 * @package    STA
 * @subpackage Model
 * @author     Ricardo Canaletti <ricardo.canaletti@lacamaradelcrimen2016.com.sw>
 * @license    MIT
 */

namespace AppBundle\Model;


/**
 * StatusCollection.php file.
 *
 * @category   Frontend
 * @package    STA
 * @subpackage Model
 * @author     Ricardo Canaletti <ricardo.canaletti@lacamaradelcrimen2016.com.sw>
 * @license    MIT
 */
class StatusCollection extends AbstractModel
{
    /**
     * Field _collection
     *
     * @var array
     */
    private $_collection = [];

    /**
     * Getter for field collection.
     *
     * @return array
     */
    public function getCollection() : array
    {
        return $this->_collection;
    }

    /**
     * Setter for field $collection.
     *
     * @param array $collection - collection.
     *
     * @return self
     */
    public function setCollection(array $collection) : self
    {
        $this->_collection = [];

        foreach ($collection as $status) {
            if (is_array($status)) {
                $status = new Status($status);
            }

            $this->_collection[] = $status;
        }

        return $this;
    }

    /**
     * toArray.
     *
     * @param array $options - Options.
     *
     * @return array
     */
    public function toArray(array $options = []) : array
    {
        $collection = [];

        /** @var Status $status */
        foreach ($this->getCollection() as $status) {
            $collection[] = $status->toArray();
        }

        return $collection;
    }
}