<?php
/**
 * Created by PhpStorm.
 * User: sommelier
 * Date: 08.12.16
 * Time: 10:13
 */

namespace Acme\Models;


use Acme\Api\Model;
use Acme\Exception\AcmeInvalidParameterException;
use Symfony\Component\Validator\Constraints as Assert;

class Protocol extends Model {

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
     * @var string $table
     *
     * @Assert\Type(
     *     message = "The value {{ value }} is not a valid {{ type }}.",
     *     type = "string"
     * )
     */
    private $table;

    /**
     * @var string $name
     *
     * @Assert\Regex(
     *     message = "The protocol name is not correct. In this field, you can use the Ukrainian, Russian, English alphabet, numbers and indent (space) and not more than 50 characters.",
     *     pattern = "/^[0-9a-zA-Zа-яА-ЯёЁҐґІіЇїЄє\s]{1,50}$/u"
     * )
     */
    private $name;

    /**
     * @var string $type
     *
     * @Assert\Choice(
     *     message = "This type is not valid.",
     *     choices = { "TYPE_SAMPLE", "TYPE_TEMPLATE" }
     * )
     */
    private $type;

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
    public function getTable() {

        if (empty($this->table)) throw new AcmeInvalidParameterException('table', parent::EMPTY_MESSAGE);

        return $this->table;
    }

    /**
     * @param string $table
     * @throws AcmeInvalidParameterException
     */
    public function setTable($table) {

        if (empty($table)) throw new AcmeInvalidParameterException('table', parent::EMPTY_MESSAGE);

        $this->table = (string) strtolower($table);
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
     * @return string
     * @throws AcmeInvalidParameterException
     */
    public function getType() {

        if (empty($this->type)) throw new AcmeInvalidParameterException('type', parent::EMPTY_MESSAGE);

        return $this->type;
    }

    /**
     * @param string $type
     * @throws AcmeInvalidParameterException
     */
    public function setType($type) {

        if (empty($type)) throw new AcmeInvalidParameterException('type', parent::EMPTY_MESSAGE);

        $this->type = (string) strtoupper($type);
    }
}
