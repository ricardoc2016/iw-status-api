<?php
/**
 * ApiValidationException.php file.
 *
 * @category   Frontend
 * @package    STA
 * @subpackage Exception
 * @author     Ricardo Canaletti <ricardo.canaletti@lacamaradelcrimen2016.com.sw>
 * @license    MIT
 */

namespace AppBundle\Exception;

use Symfony\Component\HttpFoundation\Response;

/**
 * ApiValidationException.php file.
 *
 * @category   Frontend
 * @package    STA
 * @subpackage Exception
 * @author     Ricardo Canaletti <ricardo.canaletti@lacamaradelcrimen2016.com.sw>
 * @license    MIT
 */
class ApiValidationException extends ApiException
{
    /**
     * ApiException constructor.
     *
     * @param int             $code           - Code.
     * @param int             $httpStatusCode - HTTP Status code.
     * @param \Exception|null $previous       - Previous Exception
     */
    public function __construct($code, $httpStatusCode = Response::HTTP_BAD_REQUEST, \Exception $previous = null)
    {
        parent::__construct($code, Response::HTTP_BAD_REQUEST, $previous);
    }
}