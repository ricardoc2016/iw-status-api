<?php
/**
 * AbstractModel.php file.
 *
 * @category   Frontend
 * @package    STA
 * @subpackage Model
 * @author     Ricardo Canaletti <ricardo.canaletti@lacamaradelcrimen2016.com.sw>
 * @license    MIT
 */

namespace AppBundle\Model;


/**
 * AbstractModel.php file.
 *
 * @category   Frontend
 * @package    STA
 * @subpackage Model
 * @author     Ricardo Canaletti <ricardo.canaletti@lacamaradelcrimen2016.com.sw>
 * @license    MIT
 */
abstract class AbstractModel
{
    /**
     * Field _options
     *
     * @var array
     */
    private $_options;


    /**
     * AbstractModel constructor.
     *
     * @param array $data    - Data.
     * @param array $options - Options.
     */
    public function __construct(array $data = [], array $options = [])
    {
        $this->setModelData($data)
            ->setModelOptions($options);
    }

    /**
     * Sets this model's data.
     *
     * @param array $data - Data.
     *
     * @return self
     */
    public function setModelData(array $data) : self
    {
        foreach ($data as $k => $v) {
            $method = 'set'.ucfirst($k);

            if (method_exists($this, $method)) {
                $this->$method($v);
            }
        }

        return $this;
    }

    /**
     * Sets this model's options.
     *
     * @param array $options - Options.
     *
     * @return self
     */
    public function setModelOptions(array $options) : self
    {
        $this->_options = $options;

        return $this;
    }

    /**
     * Returns this model's options.
     *
     * @return array
     */
    public function getOptions() : array
    {
        return $this->_options;
    }

    /**
     * Returns a specific option.
     *
     * @param string $name    - Name.
     * @param mixed  $default - Default value.
     *
     * @return mixed
     */
    public function getOption(string $name, $default = null)
    {
        return array_key_exists($name, $this->_options) ?
            $this->_options[$name] :
            $default;
    }

    /**
     * toArray.
     *
     * @param array $options - Options.
     *
     * @return array
     */
    abstract public function toArray(array $options = []) : array;
}