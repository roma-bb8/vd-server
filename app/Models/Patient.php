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

class Patient extends Model {

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
     * @var string $first
     *
     * @Assert\Regex(
     *     message = "The name of the patient is not correct. In this field, you can use the Ukrainian, Russian, English alphabet and not more than 50 characters.",
     *     pattern = "/^[a-zA-Zа-яА-ЯёЁҐґІіЇїЄє]{1,50}$/u"
     * )
     */
    private $first;

    /**
     * @var string $second
     *
     * @Assert\Regex(
     *     message = "The patient's patronymic is not correct. In this field, you can use the Ukrainian, Russian, English alphabet and not more than 50 characters.",
     *     pattern = "/^[a-zA-Zа-яА-ЯёЁҐґІіЇїЄє]{1,50}$/u"
     * )
     */
    private $second;

    /**
     * @var string $last
     *
     * @Assert\Regex(
     *     message = "The patient's name is not correct. In this field, you can use the Ukrainian, Russian, English alphabet and not more than 50 characters.",
     *     pattern = "/^[a-zA-Zа-яА-ЯёЁҐґІіЇїЄє]{1,50}$/u"
     * )
     */
    private $last;

    /**
     * @var boolean $sex
     *
     * @Assert\Type(
     *     message = "Invalid sex of the patient! Sex must be male or female.",
     *     type = "boolean"
     * )
     */
    private $sex;

    /**
     * @var string $birthday
     *
     * @Assert\Date(
     *     message = "The patient's date of birth is not correct."
     * )
     */
    private $birthday;

    /**
     * @var string $phone
     *
     * @Assert\Regex(
     *     message = "The patient's phone number is not correct. The phone must consist of digits and a length of 12 characters.",
     *     pattern = "/^\d{12}$/"
     * )
     */
    private $phone;

    /**
     * @var string $address
     *
     * @Assert\Regex(
     *     message = "The address of the patient is not correct.",
     *     pattern = "/^[0-9a-zA-Zа-яА-ЯёЁҐґІіЇїЄє.,()\\\\\/\s]{1,128}$/u"
     * )
     */
    private $address;

    /**
     * @var string $note
     *
     * @Assert\Regex(
     *     message = "The patient's note is not correct.",
     *     pattern = "/^[0-9a-zA-Zа-яА-ЯёЁҐґІіЇїЄє.,!?\s]{1,6500}$/u"
     * )
     */
    private $note;


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
    public function getFirst() {

        if (empty($this->first)) throw new AcmeInvalidParameterException('first', parent::EMPTY_MESSAGE);

        return $this->first;
    }

    /**
     * @param string $first
     * @throws AcmeInvalidParameterException
     */
    public function setFirst($first) {

        if (empty($first)) throw new AcmeInvalidParameterException('first', parent::EMPTY_MESSAGE);

        $this->first = (string) $this->formatName($first);
    }

    /**
     * @return string
     * @throws AcmeInvalidParameterException
     */
    public function getSecond() {

        if (empty($this->second)) throw new AcmeInvalidParameterException('second', parent::EMPTY_MESSAGE);

        return $this->second;
    }

    /**
     * @param string $second
     * @throws AcmeInvalidParameterException
     */
    public function setSecond($second) {

        if (empty($second)) throw new AcmeInvalidParameterException('second', self::EMPTY_MESSAGE);

        $this->second = (string) $this->formatName($second);
    }

    /**
     * @return string
     * @throws AcmeInvalidParameterException
     */
    public function getLast() {

        if (empty($this->last)) throw new AcmeInvalidParameterException('last', parent::EMPTY_MESSAGE);

        return $this->last;
    }

    /**
     * @param string $last
     * @throws AcmeInvalidParameterException
     */
    public function setLast($last) {

        if (empty($last)) throw new AcmeInvalidParameterException('last', parent::EMPTY_MESSAGE);

        $this->last = (string) $this->formatName($last);
    }

    /**
     * @return boolean
     * @throws AcmeInvalidParameterException
     */
    public function isSex() {

        if (!isset($this->sex)) throw new AcmeInvalidParameterException('sex', parent::EMPTY_MESSAGE);

        return $this->sex;
    }

    /**
     * @param boolean $sex
     * @throws AcmeInvalidParameterException
     */
    public function setSex($sex) {
        $this->sex = $sex;
    }

    /**
     * @return string
     * @throws AcmeInvalidParameterException
     */
    public function getBirthday() {

        if (empty($this->birthday)) throw new AcmeInvalidParameterException('birthday', parent::EMPTY_MESSAGE);

        return $this->birthday;
    }

    /**
     * @param string $birthday
     * @throws AcmeInvalidParameterException
     */
    public function setBirthday($birthday) {

        if (empty($birthday)) throw new AcmeInvalidParameterException('birthday', parent::EMPTY_MESSAGE);

        $this->birthday = (string) $birthday;
    }

    /**
     * @return string
     * @throws AcmeInvalidParameterException
     */
    public function getPhone() {

        if (empty($this->phone)) throw new AcmeInvalidParameterException('phone', parent::EMPTY_MESSAGE);

        return $this->phone;
    }

    /**
     * @param string $phone
     * @throws AcmeInvalidParameterException
     */
    public function setPhone($phone) {

        if (empty($phone)) throw new AcmeInvalidParameterException('phone', parent::EMPTY_MESSAGE);

        $this->phone = (string) str_replace('+', '', $phone);
    }

    /**
     * @return string
     * @throws AcmeInvalidParameterException
     */
    public function getAddress() {

        if (empty($this->address)) throw new AcmeInvalidParameterException('address', parent::EMPTY_MESSAGE);

        return $this->address;
    }

    /**
     * @param string $address
     * @throws AcmeInvalidParameterException
     */
    public function setAddress($address) {

        if (empty($address)) throw new AcmeInvalidParameterException('address', parent::EMPTY_MESSAGE);

        $this->address = (string) $address;
    }

    /**
     * @return string
     */
    public function getNote() {

        return $this->note;
    }

    /**
     * @param string $note
     * @throws AcmeInvalidParameterException
     */
    public function setNote($note) {

        $this->note = (string) $note;
    }
}
