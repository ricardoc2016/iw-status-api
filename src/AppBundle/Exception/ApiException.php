<?php
/**
 * ApiException.php file.
 *
 * @category   Frontend
 * @package    STA
 * @subpackage Exception
 * @author     Ricardo Canaletti <ricardo.canaletti@lacamaradelcrimen2016.com.sw>
 * @license    MIT
 */

namespace AppBundle\Exception;

use Exception;
use Symfony\Component\HttpFoundation\Response;

/**
 * ApiException.php file.
 *
 * @category   Frontend
 * @package    STA
 * @subpackage Exception
 * @author     Ricardo Canaletti <ricardo.canaletti@lacamaradelcrimen2016.com.sw>
 * @license    MIT
 */
class ApiException extends \Exception
{
    /**
     * Field _httpStatusCode.
     *
     * @var int
     */
    private $_httpStatusCode;


    /**
     * ApiException constructor.
     *
     * @param string     $message        - Message.
     * @param int        $code           - Code.
     * @param int        $httpStatusCode - HTTP Status code.
     * @param \Exception $previous       - Previous Exception
     */
    public function __construct($message, $code, $httpStatusCode = Response::HTTP_BAD_REQUEST, \Exception $previous)
    {
        parent::__construct($message, $code, $previous);

        $this->_httpStatusCode = $httpStatusCode;
    }


    /**
     * Field $_httpStatusCode Getter.
     *
     * @return int
     */
    public function getHttpStatusCode()
    {
        return $this->_httpStatusCode;
    }
}