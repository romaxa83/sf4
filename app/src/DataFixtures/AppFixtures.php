<?php

namespace App\DataFixtures;

use App\Entity\MicroPost;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0;$i < 10;$i++){
            $microPost = new MicroPost();
            $microPost->setText('Some random text '. rand(0,10000));
            $microPost->setTime(new \DateTime('2019-01-01'));

            $manager->persist($microPost);
        }

        $manager->flush();
    }
}
