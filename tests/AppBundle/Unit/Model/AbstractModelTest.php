<?php
/**
 * AbstractModelTest.php file.
 *
 * @category   Frontend
 * @package    STA
 * @subpackage Tests/AppBundle/Unit/Model
 * @author     Ricardo Canaletti <ricardo.canaletti@lacamaradelcrimen2016.com.sw>
 * @license    MIT
 */

namespace Tests\AppBundle\Unit\Service;

use AppBundle\Model\AbstractModel;
use AppBundle\Service\ErrorCodes;


/**
 * ErrorCodesTest.php file.
 *
 * @category   Frontend
 * @package    STA
 * @subpackage Tests/AppBundle/Unit/Model
 * @author     Ricardo Canaletti <ricardo.canaletti@lacamaradelcrimen2016.com.sw>
 * @license    MIT
 */
class AbstractModelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test_createDateTimeInstance_shouldThrowExceptionWithInvalidArguments.
     *
     * @expectedException \InvalidArgumentException
     */
    public function test_createDateTimeInstance_shouldThrowExceptionWithInvalidArguments()
    {
        $exampleModel = new ExampleModel();

        $exampleModel->createDateTimeInstance(array());
    }

    public function test_getOption_shouldReturnGivenOption()
    {
        $exampleModel = new ExampleModel(['id' => 1, 'name' => 'myName'], ['option1' => 'value1']);

        $this->assertEquals('value1', $exampleModel->getOption('option1'));
        $this->assertEquals('myDefault', $exampleModel->getOption('iDontExist', 'myDefault'));
    }
}

class ExampleModel extends AbstractModel
{
    /**
     * Field _id
     *
     * @var int
     */
    private $_id;

    /**
     * Field _name
     *
     * @var string
     */
    private $_name;



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
     * Getter for field name.
     *
     * @return string
     */
    public function getName() : string
    {
        return $this->_name;
    }

    /**
     * Setter for field $name.
     *
     * @param string $name - name.
     *
     * @return self
     */
    public function setName(string $name) : self
    {
        $this->_name = $name;

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

    }


}