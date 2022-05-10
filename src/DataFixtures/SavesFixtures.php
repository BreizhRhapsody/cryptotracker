<?php

namespace App\DataFixtures;

use App\Entity\SaveOfJourney;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use DateTime;

class SavesFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $date1 = new DateTime('2022-05-09');
        $save1 = new SaveOfJourney();
        $save1->setDate($date1);
        $save1->setProfit(mt_rand(500, 3000));
        $manager->persist($save1);

        $date2 = new DateTime('2022-05-10');
        $save2 = new SaveOfJourney();
        $save2->setDate($date2);
        $save2->setProfit(mt_rand(500, 3000));
        $manager->persist($save2);

        $date3 = new DateTime('2022-05-11');
        $save3 = new SaveOfJourney();
        $save3->setDate($date3);
        $save3->setProfit(mt_rand(500, 3000));
        $manager->persist($save3);

        $date4 = new DateTime('2022-05-12');
        $save4 = new SaveOfJourney();
        $save4->setDate($date4);
        $save4->setProfit(mt_rand(500, 3000));
        $manager->persist($save4);

        $manager->flush();
    }
}
