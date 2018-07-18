<?php
/**
 * Created by PhpStorm.
 * User: sommelier
 * Date: 05.03.17
 * Time: 7:31
 */

namespace Acme\Exception;


final class AcmeNotFound extends AcmeException {

    /**
     * @var string $id
     */
    private $id;

    /**
     * @var string $messageID
     */
    private $messageID;

    /**
     * AcmeNotFound constructor.
     * @param string $messageID
     * @param string $id
     */
    public function __construct($messageID, $id = '') {

        $this->id = $id;
        $this->messageID = $messageID;
    }

    /**
     * @return string
     */
    public function getID() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getMessageID() {
        return $this->messageID;
    }
}
