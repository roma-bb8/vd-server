<?php
/**
 * Created by PhpStorm.
 * User: sommelier
 * Date: 26.02.17
 * Time: 12:49
 */

namespace Acme\Provider;


use Acme\Models\Worker;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;

class PasswordProvider {

    /**
     * @var string $password
     */
    private $password;

    /**
     * @var EncoderFactory $encoderFactory
     */
    private $encoderFactory;

    /**
     * @var string $hash
     */
    private $hash;


    /**
     * PasswordProvider constructor.
     * @param array $data
     */
    public function __construct($data) {

        $this->initEncoderFactory();

        if (!empty($data['length'])) {
            $this->generate((int)$data['length']);

            return;
        }

        if (!empty($data['password'])) {
            $this->password = (string)$data['password'];

            return;
        }
    }

    /**
     * @param UserInterface $user
     * @return null|string $hash
     */
    public function getHashPassword(UserInterface $user) {

        if ($this->hash !== '') return $this->hash;

        $encoder = $this->encoderFactory->getEncoder($user);

        if ($this->password === '') return $this->password;

        $this->hash = $encoder->encodePassword($this->password, $user->getSalt());

        return $this->hash;
    }

    /**
     * @return string $password;
     */
    public function getPassword() {
        return $this->password;
    }

    private function initEncoderFactory() {

        $this->hash = '';
        $this->password = '';

        $defaultEncoder = new MessageDigestPasswordEncoder('sha512', true, 5000);
        $weakEncoder = new MessageDigestPasswordEncoder('md5', true, 1);

        $encoders = array(
            Worker::class => $defaultEncoder,
            User::class   => $weakEncoder
        );

        $this->encoderFactory = new EncoderFactory($encoders);
    }

    /**
     * @param int $length
     */
    private function generate($length) {

        $abc = array(
            'a',
            'b',
            'c',
            'd',
            'e',
            'f',
            'g',
            'h',
            'i',
            'j',
            'k',
            'l',
            'm',
            'n',
            'o',
            'p',
            'r',
            's',
            't',
            'u',
            'v',
            'x',
            'y',
            'z',
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'R',
            'S',
            'T',
            'U',
            'V',
            'X',
            'Y',
            'Z',
            '1',
            '2',
            '3',
            '4',
            '5',
            '6',
            '7',
            '8',
            '9',
            '0',
            '_',
            '-'
        );

        $password = '';

        for ($i = 0; $i < $length; $i++) {
            $index = rand(0, count($abc) - 1);
            $password .= $abc[$index];
        }

        $this->password = $password;
    }
}
