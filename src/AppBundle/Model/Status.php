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
    const ANONYMOUS_EMAIL           = 'anonymous';


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
     * Field _confirmedAt
     *
     * @var \DateTime|null
     */
    private $_confirmedAt;

    /**
     * Field _confirmCode
     *
     * @var string|null
     */
    private $_confirmCode;

    /**
     * Field _deleteConfirmedAt
     *
     * @var \DateTime|null
     */
    private $_deleteConfirmedAt;

    /**
     * Field _deleteConfirmCode
     *
     * @var string|null
     */
    private $_deleteConfirmCode;


    /**
     * Getter for field id.
     *
     * @return int|null
     */
    public function getId()
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
        $this->_createdAt = $this->createDateTimeInstance($createdAt);

        return $this;
    }

    /**
     * Getter for field confirmedAt.
     *
     * @return \DateTime|null
     */
    public function getConfirmedAt()
    {
        return $this->_confirmedAt;
    }

    /**
     * Setter for field $confirmedAt.
     *
     * @param \DateTime|string $confirmedAt - confirmedAt.
     *
     * @return self
     */
    public function setConfirmedAt($confirmedAt) : self
    {
        $this->_confirmedAt = $this->createDateTimeInstance($confirmedAt);

        return $this;
    }

    /**
     * Getter for field confirmCode.
     *
     * @return string|null
     */
    public function getConfirmCode()
    {
        return $this->_confirmCode;
    }

    /**
     * Setter for field $confirmCode.
     *
     * @param string|null $confirmCode - confirmCode.
     *
     * @return self
     */
    public function setConfirmCode($confirmCode) : self
    {
        $this->_confirmCode = $confirmCode;

        return $this;
    }

    /**
     * Getter for field deleteConfirmedAt.
     *
     * @return \DateTime|null
     */
    public function getDeleteConfirmedAt()
    {
        return $this->_deleteConfirmedAt;
    }

    /**
     * Setter for field $deleteConfirmedAt.
     *
     * @param \DateTime|null $deleteConfirmedAt - deleteConfirmedAt.
     *
     * @return $this
     */
    public function setDeleteConfirmedAt($deleteConfirmedAt)
    {
        $this->_deleteConfirmedAt = $this->createDateTimeInstance($deleteConfirmedAt);

        return $this;
    }

    /**
     * Getter for field deleteConfirmCode.
     *
     * @return null|string
     */
    public function getDeleteConfirmCode()
    {
        return $this->_deleteConfirmCode;
    }

    /**
     * Setter for field $deleteConfirmCode.
     *
     * @param null|string $deleteConfirmCode - deleteConfirmCode.
     *
     * @return $this
     */
    public function setDeleteConfirmCode($deleteConfirmCode)
    {
        $this->_deleteConfirmCode = $deleteConfirmCode;

        return $this;
    }

    /**
     * isDeleteConfirmed.
     *
     * @return bool
     */
    public function isDeleteConfirmed() : bool
    {
        return $this->getDeleteConfirmedAt() !== null;
    }

    /**
     * isAnonymous.
     *
     * @return bool
     */
    public function isAnonymous()
    {
        return $this->getEmail() === self::ANONYMOUS_EMAIL;
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
            'created_at'        => $this->getCreatedAt()->format('Y-m-d\TH:i:s\Z')
        ];
    }
}