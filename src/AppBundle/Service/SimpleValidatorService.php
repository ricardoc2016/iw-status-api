<?php
/**
 * SimpleValidatorService.php file.
 *
 * @category   Frontend
 * @package    STA
 * @subpackage Service
 * @author     Ricardo Canaletti <ricardo.canaletti@lacamaradelcrimen2016.com.sw>
 * @license    MIT
 */

namespace AppBundle\Service;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Validation;

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

    /**
     * isValidEmail.
     *
     * @param mixed $value - Value.
     *
     * @return bool
     */
    public function isValidEmail($value) : bool
    {
        if (!$this->isString($value)) {
            return false;
        }

        $validation = Validation::createValidator();
        $violations = $validation->validate(
            $value,
            [
                new Email()
            ]
        );

        return count($violations) === 0;
    }
}