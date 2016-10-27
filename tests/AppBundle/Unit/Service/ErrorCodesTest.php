<?php
/**
 * ErrorCodesTest.php file.
 *
 * @category   Frontend
 * @package    STA
 * @subpackage Tests/AppBundle/Unit/Service
 * @author     Ricardo Canaletti <ricardo.canaletti@lacamaradelcrimen2016.com.sw>
 * @license    MIT
 */

namespace Tests\AppBundle\Unit\Service;
use AppBundle\Service\ErrorCodes;


/**
 * ErrorCodesTest.php file.
 *
 * @category   Frontend
 * @package    STA
 * @subpackage Tests/AppBundle/Unit/Service
 * @author     Ricardo Canaletti <ricardo.canaletti@lacamaradelcrimen2016.com.sw>
 * @license    MIT
 */
class ErrorCodesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test_getMessage_shouldThrowExceptionIfMessageDoesNotExist.
     *
     * @expectedException \InvalidArgumentException
     */
    public function test_getMessage_shouldThrowExceptionIfMessageDoesNotExist()
    {
        ErrorCodes::getMessage(123213213123211);
    }
}