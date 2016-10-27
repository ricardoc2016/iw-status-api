<?php
/**
 * ErrorCodes.php file.
 *
 * @category   Frontend
 * @package    STA
 * @subpackage Service
 * @author     Ricardo Canaletti <ricardo.canaletti@lacamaradelcrimen2016.com.sw>
 * @license    MIT
 */

namespace AppBundle\Service;


/**
 * ErrorCodes.php file.
 *
 * @category   Frontend
 * @package    STA
 * @subpackage Service
 * @author     Ricardo Canaletti <ricardo.canaletti@lacamaradelcrimen2016.com.sw>
 * @license    MIT
 */
class ErrorCodes
{
    const ERR_INVALID_PAGE                  = 1000;
    const ERR_INVALID_ROWS                  = 1001;
    const ERR_INVALID_QUERY                 = 1002;

    const ERR_UNKNOWN                       = 9999;


    /**
     * Field messages
     *
     * @var array
     */
    public static $messages = [
        self::ERR_INVALID_PAGE          => 'Parameter "p" MUST be an integer >= 1',
        self::ERR_INVALID_ROWS          => 'Parameter "r" MUST be an integer >= 1',
        self::ERR_INVALID_QUERY         => 'Parameter "q" MUST be a string with a maximum of 120 characters.',

        self::ERR_UNKNOWN               => 'Unknown Error.'
    ];

    /**
     * getMessage.
     *
     * @param int $errorCode - Error Code.
     *
     * @return string
     */
    public static function getMessage(int $errorCode) : string
    {
        if (!isset(self::$messages[$errorCode])) {
            throw new \InvalidArgumentException('Error Code "'.$errorCode.'" is invalid.');
        }

        return self::$messages[$errorCode];
    }
}