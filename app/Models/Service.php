<?php
/**
 * Created by PhpStorm.
 * User: sommelier
 * Date: 08.12.16
 * Time: 6:31
 */

namespace Acme\Models;


use Acme\Api\Model;
use Acme\Exception\AcmeInvalidParameterException;
use Symfony\Component\Validator\Constraints as Assert;

class Service extends Model {

    /**
     * @var integer $id
     *
     * @Assert\Range(
     *      minMessage = "Such a record does not exist! Error in ID. ID must be greater than or equal to {{ limit }}.",
     *      min = 1
     * )
     */
    private $id;

    /**
     * @var string $name
     *
     * @Assert\Regex(
     *     message = "The name of the service is not correct. In this field, you can use the Ukrainian, Russian, English alphabet, numbers and indent (space) and not more than 100 characters.",
     *     pattern = "/^[0-9a-zA-Zа-яА-ЯёЁҐґІіЇїЄє\s]{1,100}$/u"
     * )
     */
    private $name;

    /**
     * @var float $price
     *
     * @Assert\Type(
     *     message = "This price should be of type {{ type }}.",
     *     type = "float"
     * )
     * @Assert\Range(
     *     minMessage = "The price of the service is not corrector, the price can not be negative or zero. Minimal value {{ limit }}.",
     *     min = 1
     * )
     */
    private $price;


    /**
     * @return integer
     * @throws AcmeInvalidParameterException
     */
    public function getId() {

        if (empty($this->id)) throw new AcmeInvalidParameterException('id', parent::EMPTY_MESSAGE);

        return $this->id;
    }

    /**
     * @param integer $id
     * @throws AcmeInvalidParameterException
     */
    public function setId($id) {

        if (empty($id)) throw new AcmeInvalidParameterException('id', parent::EMPTY_MESSAGE);

        $this->id = (integer) $id;
    }

    /**
     * @return string
     * @throws AcmeInvalidParameterException
     */
    public function getName() {

        if (empty($this->name)) throw new AcmeInvalidParameterException('name', parent::EMPTY_MESSAGE);

        return $this->name;
    }

    /**
     * @param string $name
     * @throws AcmeInvalidParameterException
     */
    public function setName($name) {

        if (empty($name)) throw new AcmeInvalidParameterException('name', parent::EMPTY_MESSAGE);

        $this->name = (string) $name;
    }

    /**
     * @return float
     * @throws AcmeInvalidParameterException
     */
    public function getPrice() {

        if (empty($this->price)) throw new AcmeInvalidParameterException('price', parent::EMPTY_MESSAGE);

        return $this->price;
    }

    /**
     * @param float $price
     * @throws AcmeInvalidParameterException
     */
    public function setPrice($price) {

        if (empty($price)) throw new AcmeInvalidParameterException('price', parent::EMPTY_MESSAGE);

        $this->price = (float) $price;
    }
}
