<?php
/**
 * Created by PhpStorm.
 * User: sommelier
 * Date: 07.12.16
 * Time: 13:56
 */

namespace Acme\Models;


use Acme\Api\Model;
use Acme\Exception\AcmeInvalidParameterException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

class Worker extends Model implements UserInterface {

    const ROLE_NONE = 'ROLE_NONE';

    const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
    const ROLE_ADMIN = 'ROLE_ADMIN';

    const ROLE_NURSE = 'ROLE_NURSE';
    const ROLE_DOCTOR = 'ROLE_DOCTOR';

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
     *     message = "The name of the employee is not correct. In this field, you can use the Ukrainian, Russian, English alphabet and not more than 50 characters.",
     *     pattern = "/^[a-zA-Zа-яА-ЯёЁҐґІіЇїЄє]{1,50}$/u"
     * )
     */
    private $first;

    /**
     * @var string $second
     *
     * @Assert\Regex(
     *     message = "The patronymic of the employee is not correct. In this field, you can use the Ukrainian, Russian, English alphabet and not more than 50 characters.",
     *     pattern = "/^[a-zA-Zа-яА-ЯёЁҐґІіЇїЄє]{1,50}$/u"
     * )
     */
    private $second;

    /**
     * @var string $last
     *
     * @Assert\Regex(
     *     message = "The surname of the employee is not correct. In this field, you can use the Ukrainian, Russian, English alphabet and not more than 50 characters.",
     *     pattern = "/^[a-zA-Zа-яА-ЯёЁҐґІіЇїЄє]{1,50}$/u"
     * )
     */
    private $last;

    /**
     * @var boolean $sex
     *
     * @Assert\Type(
     *     message = "Invalid employee sex! Sex must be male or female.",
     *     type = "boolean"
     * )
     */
    private $sex;

    /**
     * @var string $birthday
     *
     * @Assert\Date(
     *     message = "The date of birth of the employee is not correct."
     * )
     */
    private $birthday;

    /**
     * @var string $email
     *
     * @Assert\Email(
     *     message = "This email '{{ value }}' is not valid.",
     *     strict = true,
     *     checkHost = true,
     *     checkMX = true
     * )
     */
    private $email;

    /**
     * @var string $phone
     *
     * @Assert\Regex(
     *     message = "The employee's phone number is not correct. The phone must consist of digits and a length of 12 characters.",
     *     pattern = "/^\d{12}$/"
     * )
     */
    private $phone;

    /**
     * @var Profession $profession
     *
     * @Assert\Valid()
     */
    private $profession;

    /**
     * @var boolean $status
     *
     * @Assert\Type(
     *     message = "An employee can be active or inactive.",
     *     type = "boolean"
     * )
     */
    private $status;

    /**
     * @var string $salt
     */
    private $salt;

    /**
     * @var string $password
     */
    private $password;

    /**
     * @var array $roles
     *
     * @Assert\Choice(
     *     message = "This roles is not valid.",
     *     choices = { "ROLE_NONE", "ROLE_SUPER_ADMIN", "ROLE_ADMIN", "ROLE_NURSE", "ROLE_DOCTOR" },
     *     multiple = true
     * )
     */
    private $roles;


    /**
     * Worker constructor.
     */
    public function __construct() {
        $this->profession = new Profession();
    }

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
    public function getEmail() {

        if (empty($this->email)) throw new AcmeInvalidParameterException('email', parent::EMPTY_MESSAGE);

        return $this->email;
    }

    /**
     * @param string $email
     * @throws AcmeInvalidParameterException
     */
    public function setEmail($email) {

        if (empty($email)) throw new AcmeInvalidParameterException('email', parent::EMPTY_MESSAGE);

        $this->email = (string) strtolower($email);
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
     * @return Profession
     */
    public function getProfession() {
        return $this->profession;
    }

    /**
     * @param Profession $profession
     * @throws AcmeInvalidParameterException
     */
    public function setProfession(Profession $profession) {

        if (empty($profession)) throw new AcmeInvalidParameterException('profession', parent::EMPTY_MESSAGE);

        if (!$profession instanceof Profession) throw new AcmeInvalidParameterException('profession', 'The object must be Profession');

        $this->profession = (object) $profession;
    }

    /**
     * @return boolean
     * @throws AcmeInvalidParameterException
     */
    public function isStatus() {

        if (!isset($this->status)) throw new AcmeInvalidParameterException('status', parent::EMPTY_MESSAGE);

        return $this->status;
    }

    /**
     * @param boolean $status
     * @throws AcmeInvalidParameterException
     */
    public function setStatus($status) {

        $this->status = $status;
    }

    /**
     * @param string $salt
     * @throws AcmeInvalidParameterException
     */
    public function setSalt($salt) {

        if (empty($salt)) throw new AcmeInvalidParameterException('salt', parent::EMPTY_MESSAGE);

        $this->salt = (string) strtolower($salt);
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string The salt
     * @throws AcmeInvalidParameterException
     */
    public function getSalt() {

        if (empty($this->salt)) throw new AcmeInvalidParameterException('salt', parent::EMPTY_MESSAGE);

        return $this->salt;
    }

    /**
     * @param string $password
     * @throws AcmeInvalidParameterException
     */
    public function setPassword($password) {

        if (empty($password)) throw new AcmeInvalidParameterException('password', parent::EMPTY_MESSAGE);

        $this->password = (string) $password;
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     * @throws AcmeInvalidParameterException
     */
    public function getPassword() {

        if (empty($this->password)) throw new AcmeInvalidParameterException('password', parent::EMPTY_MESSAGE);

        return $this->password;
    }

    /**
     * @param array $roles
     * @throws AcmeInvalidParameterException
     */
    public function setRoles($roles) {

        if (empty($roles)) throw new AcmeInvalidParameterException('roles', parent::EMPTY_MESSAGE);

        $this->roles = (array) $roles;
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return array The user roles
     * @throws AcmeInvalidParameterException
     */
    public function getRoles() {

        if (empty($this->roles)) throw new AcmeInvalidParameterException('roles', parent::EMPTY_MESSAGE);

        return $this->roles;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The phone for user
     * @throws AcmeInvalidParameterException
     */
    public function getUsername() {

        if (empty($this->phone)) throw new AcmeInvalidParameterException('phone', parent::EMPTY_MESSAGE);

        return $this->phone;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials() {
        $this->roles = array(self::ROLE_NONE);
    }
}
