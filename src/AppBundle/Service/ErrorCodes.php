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

    const ERR_POST_MISSING_EMAIL            = 2000;
    const ERR_POST_INVALID_EMAIL            = 2001;
    const ERR_POST_MISSING_STATUS           = 2002;
    const ERR_POST_INVALID_STATUS           = 2003;

    const ERR_CONFIRM_CODE_NOT_FOUND        = 3000;

    const ERR_DELETE_ANONYMOUS              = 4000;

    const ERR_STATUS_NOT_FOUND              = 9997;
    const ERR_RESOURCE_NOT_FOUND            = 9998;
    const ERR_UNKNOWN                       = 9999;


    /**
     * Field messages
     *
     * @var array
     */
    public static $messages = [
        self::ERR_INVALID_PAGE                  => 'Parameter "p" MUST be an integer >= 1',
        self::ERR_INVALID_ROWS                  => 'Parameter "r" MUST be an integer >= 1',
        self::ERR_INVALID_QUERY                 => 'Parameter "q" MUST be a string with a maximum of 120 characters.',

        self::ERR_POST_MISSING_EMAIL            => 'Missing "email" parameter.',
        self::ERR_POST_INVALID_EMAIL            => 'Parameter "email" MUST be a valid e-mail.',
        self::ERR_POST_MISSING_STATUS           => 'Missing parameter "status".',
        self::ERR_POST_INVALID_STATUS           => 'Parameter "string" MUST be a string with a maximum of 120 characters.',

        self::ERR_CONFIRM_CODE_NOT_FOUND        => 'This status message does not have the confirmation code received.',

        self::ERR_DELETE_ANONYMOUS              => 'Can\'t delete an anonymous status.',

        self::ERR_STATUS_NOT_FOUND              => 'Status message not found.',
        self::ERR_RESOURCE_NOT_FOUND            => 'Resource not found.',
        self::ERR_UNKNOWN                       => 'Unknown Error.'
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