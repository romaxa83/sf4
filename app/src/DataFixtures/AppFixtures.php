<?php

namespace App\DataFixtures;

use App\Entity\MicroPost;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private const USERS = [
        [
            'username' => 'ben_king',
            'email' => 'ben_king@ben.com',
            'password' => 'ben_king',
            'fullName' => 'Ben King'
        ],
        [
            'username' => 'john_doe',
            'email' => 'john_doe@doe.com',
            'password' => 'john_doe',
            'fullName' => 'John Doe'
        ],
        [
            'username' => 'alice_cooper',
            'email' => 'alice_cooper@dot.com',
            'password' => 'alice_cooper',
            'fullName' => 'Alice Cooper'
        ]
    ];

    private const POST_TEXT = [
        'On sait depuis longtemps que travailler avec du texte',
        'Je obecně známou věcí, že člověk',
        'Давно выяснено, что при оценке дизайна',
        'Позната је чињеница да ће читалац бити спутан',
        'Chúng ta vẫn biết rằng, làm việc với một đoạn văn bản',
        'tāpēc, kas tas nodrošina vairāk vai mazāk vienmērīgu',
        'ცნობილი ფაქტია, რომ გვერდის წაკითხვად შიგთავსს შეუძლი',
        'Es ist ein lang erwiesener Fakt, dass ein Leser vom Text abgele',
        'Është një fakt gjerësisht i njohur që lexuesi do të hutohet',
    ];

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadMicroPost($manager);
    }

    private function loadMicroPost(ObjectManager $manager)
    {
        for ($i = 0;$i < 30;$i++){
            $microPost = new MicroPost();
            $microPost->setText(self::POST_TEXT[rand(0,count(self::POST_TEXT) - 1)]);
            $date = (new \DateTime())->modify('-' . rand(0,10) . 'day');
            $microPost->setTime($date);
            $microPost->setUser($this->getReference(
                self::USERS[rand(0,count(self::USERS) - 1)]['username']
            ));

            $manager->persist($microPost);
        }

        $manager->flush();
    }

    private function loadUsers(ObjectManager $manager)
    {
        foreach (self::USERS as $userData){
            $user = new User();
            $user->setUsername($userData['username']);
            $user->setFullName($userData['fullName']);
            $user->setEmail($userData['email']);
            $user->setPassword($this->passwordEncoder->encodePassword($user,$userData['password']));

            $this->addReference($userData['username'],$user);

            $manager->persist($user);
        }
        $manager->flush();
    }
}
