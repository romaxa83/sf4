<?php

namespace App\Event;

use App\Entity\User;
use Symfony\Component\EventDispatcher\Event;

class UserRegisterEvent extends Event
{
    const Name = 'user.register';

    /**
     * @var User
     */
    private $registerUser;

    public function __construct(User $registerUser)
    {
        $this->registerUser = $registerUser;
    }

    /**
     * @return User
     */
    public function getUserRegister(): User
    {
        return $this->registerUser;
    }
}