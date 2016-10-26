<?php
/**
 * Status.php file.
 *
 * @category   Frontend
 * @package    STA
 * @subpackage Model
 * @author     Ricardo Canaletti <ricardo.canaletti@lacamaradelcrimen2016.com.sw>
 * @license    MIT
 */

namespace AppBundle\Model;


/**
 * Status.php file.
 *
 * @category   Frontend
 * @package    STA
 * @subpackage Model
 * @author     Ricardo Canaletti <ricardo.canaletti@lacamaradelcrimen2016.com.sw>
 * @license    MIT
 */
class Status extends AbstractModel
{
    /**
     * Field _id
     *
     * @var int
     */
    private $_id;

    /**
     * Field _email
     *
     * @var string
     */
    private $_email;

    /**
     * Field _status
     *
     * @var string
     */
    private $_status;

    /**
     * Field _createdAt
     *
     * @var \DateTime
     */
    private $_createdAt;



    /**
     * Getter for field id.
     *
     * @return int
     */
    public function getId() : int
    {
        return $this->_id;
    }

    /**
     * Setter for field $id.
     *
     * @param int $id - id.
     *
     * @return self
     */
    public function setId(int $id) : self
    {
        $this->_id = $id;

        return $this;
    }

    /**
     * Getter for field email.
     *
     * @return string
     */
    public function getEmail() : string
    {
        return $this->_email;
    }

    /**
     * Setter for field $email.
     *
     * @param string $email - email.
     *
     * @return self
     */
    public function setEmail(string $email) : self
    {
        $this->_email = $email;

        return $this;
    }

    /**
     * Getter for field status.
     *
     * @return string
     */
    public function getStatus() : string
    {
        return $this->_status;
    }

    /**
     * Setter for field $status.
     *
     * @param string $status - status.
     *
     * @return self
     */
    public function setStatus(string $status) : self
    {
        $this->_status = $status;

        return $this;
    }

    /**
     * Getter for field createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt() : \DateTime
    {
        return $this->_createdAt;
    }

    /**
     * Setter for field $createdAt.
     *
     * @param \DateTime $createdAt - createdAt.
     *
     * @return self
     */
    public function setCreatedAt($createdAt) : self
    {
        $this->_createdAt = $createdAt;

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
        return [
            'id'                => $this->getId(),
            'email'             => $this->getEmail(),
            'status'            => $this->getStatus(),
            'createdAt'         => $this->getCreatedAt()->format('Y-m-d H:i:s')
        ];
    }
}