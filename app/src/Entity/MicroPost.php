<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MicroPostRepository")
 */
class MicroPost
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string",length=300)
     * @Assert\NotBlank()
     * @Assert\Length(min=10, minMessage="Символов должно быть больше 10")
     */
    private $text;

    /**
     * @ORM\Column(type="datetime",length=300)
     */
    private $time;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="posts")
     * @ORM\JoinColumn()
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return mixed
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param mixed $text
     */
    public function setText($text): void
    {
        $this->text = $text;
    }

    /**
     * @param mixed $time
     */
    public function setTime($time): void
    {
        $this->time = $time;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }
}
