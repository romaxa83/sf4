<?php

namespace App\DataFixtures;

use App\Entity\MicroPost;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
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
        $this->loadMicroPost($manager);
        $this->loadUsers($manager);
    }

    private function loadMicroPost(ObjectManager $manager)
    {
        for ($i = 0;$i < 10;$i++){
            $microPost = new MicroPost();
            $microPost->setText('Some random text '. rand(0,10000));
            $microPost->setTime(new \DateTime('2019-01-01'));

            $manager->persist($microPost);
        }

        $manager->flush();
    }

    private function loadUsers(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('ben_king');
        $user->setFullName('Ben King');
        $user->setEmail('ben.king@ben.com');
        $user->setPassword($this->passwordEncoder->encodePassword($user,'benking'));

        $manager->persist($user);
        $manager->flush();
    }
}
