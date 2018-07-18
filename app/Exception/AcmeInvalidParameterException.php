<?php
/**
 * Created by PhpStorm.
 * User: sommelier
 * Date: 04.03.17
 * Time: 21:14
 */

namespace Acme\Exception;


final class AcmeInvalidParameterException extends AcmeException {

    /**
     * @var string $field
     */
    private $field;

    /**
     * @var string $description
     */
    private $description;

    /**
     * AcmeInvalidParameterException constructor.
     * @param string $field
     * @param string $description
     */
    public function __construct($field, $description) {

        $this->field = $field;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getField() {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }
}
