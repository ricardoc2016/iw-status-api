<?php
/**
 * File SimpleValidatorService.php.
 *
 * PHP version 5.3+
 *
 * @category   Frontend
 * @package    Intraway
 * @subpackage Intraway
 * @author     Gustavo Falco <gustavo.falco@intraway.com>
 * @copyright  2014 Intraway Corp.
 * @license    Intraway Corp. <http://www.intraway.com>
 * @link       http://www.intraway.com
 */

namespace AppBundle\Service;

/**
 * SimpleValidatorService.php file.
 *
 * @category   Frontend
 * @package    STA
 * @subpackage Service
 * @author     Ricardo Canaletti <ricardo.canaletti@lacamaradelcrimen2016.com.sw>
 * @license    MIT
 */
class SimpleValidatorService
{
    /**
     * isString.
     *
     * @param mixed $value - Value.
     *
     * @return bool
     */
    public function isString($value) : bool
    {
        return is_string($value);
    }

    /**
     * isStringLessOrEqualThan.
     *
     * @param mixed $value - Value.
     * @param int   $max   - Max.
     *
     * @return bool
     */
    public function isStringLessOrEqualThan($value, int $max)
    {
        return $this->isString($value) && strlen($value) <= $max;
    }

    /**
     * isInteger.
     *
     * @param string $value - Value.
     *
     * @return bool
     */
    public function isInteger($value) : bool
    {
        return is_numeric($value) && preg_match('/^[0-9]+$/', $value) ?
            true :
            false;
    }

    /**
     * isIntegerGreaterOrEqualThan.
     *
     * @param int $value - Value.
     * @param int $min   - Min.
     *
     * @return bool
     */
    public function isIntegerGreaterOrEqualThan($value, int $min) : bool
    {
        return $this->isInteger($value) && $value >= $min;
    }
}