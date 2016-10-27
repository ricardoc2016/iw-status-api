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
     * Creates a \DateTime instance.
     *
     * @param null|string|\DateTime $date     - Date.
     * @param string                $format   - Format.
     * @param string                $timezone - Timezone.
     *
     * @return \DateTime|null
     */
    protected function createDateTimeInstance(
        $date,
        string $format = 'Y-m-d H:i:s',
        string $timezone = 'UTC'
    ) {
        if ($date === null) {
            return null;
        }

        $timezone = new \DateTimeZone($timezone);

        if (is_string($date)) {
            $date = \DateTime::createFromFormat($format, $date, $timezone);
        } else if (is_object($date) && $date instanceof \DateTime) {
            /** @var \DateTime $date */
            $date->setTimezone($timezone);
        } else {
            throw new \InvalidArgumentException('$date MUST be NULL, a date string or an instance of \DateTime.');
        }

        return $date;
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