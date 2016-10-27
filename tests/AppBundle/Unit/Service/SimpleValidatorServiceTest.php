<?php
/**
 * SimpleValidatorServiceTest.php file.
 *
 * @category   Frontend
 * @package    STA
 * @subpackage Tests/AppBundle/Unit/Service
 * @author     Ricardo Canaletti <ricardo.canaletti@lacamaradelcrimen2016.com.sw>
 * @license    MIT
 */

namespace Tests\AppBundle\Unit\Service;

use AppBundle\Service\SimpleValidatorService;


/**
 * SimpleValidatorServiceTest.php file.
 *
 * @category   Frontend
 * @package    STA
 * @subpackage Tests/AppBundle/Unit/Service
 * @author     Ricardo Canaletti <ricardo.canaletti@lacamaradelcrimen2016.com.sw>
 * @license    MIT
 */
class SimpleValidatorServiceTest extends \PHPUnit_Framework_TestCase
{
    public function test_isValidEmail_returnsFalseIfItsNotAString()
    {
        $simpleValidatorService = new SimpleValidatorService();

        $this->assertFalse($simpleValidatorService->isValidEmail(array()));
    }
}