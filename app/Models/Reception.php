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

class Reception extends Model {

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
     * @var Patient $patient
     *
     * @Assert\Valid()
     */
    private $patient;

    /**
     * @var Worker $worker
     *
     * @Assert\Valid()
     */
    private $worker;

    /**
     * @var string $time
     *
     * @Assert\DateTime(
     *     message = "The time of reception is not correct, the time can not be past or current."
     * )
     */
    private $time;

    /**
     * @var boolean $active
     *
     * @Assert\Type(
     *     message = "The reception can be only two kinds of active or inactive.",
     *     type = "boolean"
     * )
     */
    private $active;

    /**
     * @var boolean $paid
     *
     * @Assert\Type(
     *     message = "The reception can only be of two kinds paid or paid for.",
     *     type = "boolean"
     * )
     */
    private $paid;


    /**
     * Reception constructor.
     */
    public function __construct() {

        $this->patient = new Patient();
        $this->worker = new Worker();
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
     * @return Patient
     */
    public function getPatient() {
        return $this->patient;
    }

    /**
     * @param Patient $patient
     * @throws AcmeInvalidParameterException
     */
    public function setPatient(Patient $patient) {

        if (empty($patient)) throw new AcmeInvalidParameterException('patient', parent::EMPTY_MESSAGE);

        if (!$patient instanceof Patient) throw new AcmeInvalidParameterException('patient', 'The object must be Patient');

        $this->patient = (object) $patient;
    }

    /**
     * @return Worker
     */
    public function getWorker() {
        return $this->worker;
    }

    /**
     * @param Worker $worker
     * @throws AcmeInvalidParameterException
     */
    public function setWorker(Worker $worker) {

        if (empty($worker)) throw new AcmeInvalidParameterException('worker', parent::EMPTY_MESSAGE);

        if (!$worker instanceof Worker) throw new AcmeInvalidParameterException('patient', 'The object must be Worker');

        $this->worker = (object) $worker;
    }

    /**
     * @return string
     * @throws AcmeInvalidParameterException
     */
    public function getTime() {

        if (empty($this->time)) throw new AcmeInvalidParameterException('time', parent::EMPTY_MESSAGE);

        return $this->time;
    }

    /**
     * @param string $time
     * @throws AcmeInvalidParameterException
     */
    public function setTime($time) {

        if (empty($time)) throw new AcmeInvalidParameterException('time', parent::EMPTY_MESSAGE);

        if ($time < date('Y-m-d H:i:s')) throw new AcmeInvalidParameterException('time', 'Time can not be passed');

        $this->time = (string) $time;
    }

    /**
     * @return boolean
     * @throws AcmeInvalidParameterException
     */
    public function isActive() {

        if (!isset($this->active)) throw new AcmeInvalidParameterException('active', parent::EMPTY_MESSAGE);

        return $this->active;
    }

    /**
     * @param boolean $active
     * @throws AcmeInvalidParameterException
     */
    public function setActive($active) {

       $this->active = $active;
    }

    /**
     * @return boolean
     * @throws AcmeInvalidParameterException
     */
    public function isPaid() {

        if (!isset($this->paid)) throw new AcmeInvalidParameterException('paid', parent::EMPTY_MESSAGE);

        return $this->paid;
    }

    /**
     * @param boolean $paid
     * @throws AcmeInvalidParameterException
     */
    public function setPaid($paid) {

        $this->paid = $paid;
    }
}
