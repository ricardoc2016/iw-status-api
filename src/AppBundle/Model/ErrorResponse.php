<?php
/**
 * ErrorResponse.php file.
 *
 * @category   Frontend
 * @package    STA
 * @subpackage Model
 * @author     Ricardo Canaletti <ricardo.canaletti@lacamaradelcrimen2016.com.sw>
 * @license    MIT
 */

namespace AppBundle\Model;


/**
 * ErrorResponse.php file.
 *
 * @category   Frontend
 * @package    STA
 * @subpackage Model
 * @author     Ricardo Canaletti <ricardo.canaletti@lacamaradelcrimen2016.com.sw>
 * @license    MIT
 */
class ErrorResponse extends AbstractModel
{
    /**
     * Field _code
     *
     * @var int
     */
    private $_code;

    /**
     * Field _message
     *
     * @var string
     */
    private $_message;

    /**
     * Field _link
     *
     * @var string
     */
    private $_link;


    /**
     * Getter for field code.
     *
     * @return int
     */
    public function getCode() : int
    {
        return $this->_code;
    }

    /**
     * Setter for field $code.
     *
     * @param int $code - code.
     *
     * @return self
     */
    public function setCode(int $code) : self
    {
        $this->_code = $code;

        return $this;
    }

    /**
     * Getter for field message.
     *
     * @return string
     */
    public function getMessage() : string
    {
        return $this->_message;
    }

    /**
     * Setter for field $message.
     *
     * @param string $message - message.
     *
     * @return self
     */
    public function setMessage(string $message) : self
    {
        $this->_message = $message;

        return $this;
    }

    /**
     * Getter for field link.
     *
     * @return string
     */
    public function getLink() : string
    {
        return $this->_link;
    }

    /**
     * Setter for field $link.
     *
     * @param string $link - link.
     *
     * @return self
     */
    public function setLink(string $link) : self
    {
        $this->_link = $link;

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
            'code'              => $this->getCode(),
            'message'           => $this->getMessage(),
            'link'              => $this->getLink()
        ];
    }
}