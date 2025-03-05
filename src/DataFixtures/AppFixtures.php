<?php

namespace App\DataFixtures;
use App\Entity\Article;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for($i=1;$i <=10;$i++){
            $Article = new Article();
            $Article->setTitle("title n° $i")
                    ->setTopic("topic de nous n°$i")
                    ->setCreateAt(new \DateTimeImmutable());
                    $manager->persist($Article);

        }
        $manager->flush();
    }
}
