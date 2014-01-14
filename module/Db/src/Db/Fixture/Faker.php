<?php

namespace Db\Fixture;

use DateTime;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr;
use Faker\Generator;

/**
 * Generate random data for all model classes
 */
class Faker implements FixtureInterface
{
    /**
     * @var EntityManager
     */
    protected $manager;

    /**
     * @var Generator
     */
    protected $faker;

    protected $counters = [];

    public function load(ObjectManager $manager)
    {

        $this->manager = $manager;
        $this->faker = $generator = \Faker\Factory::create();
        $populator = new \Faker\ORM\Doctrine\Populator($generator, $manager);

        $populator->addEntity('Db\Entity\Artist', 100);
        $populator->addEntity('Db\Entity\Album', 1000);

        $populator->execute();
    }
}

