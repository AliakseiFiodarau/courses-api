<?php

namespace App\DataFixtures;

use DateTimeImmutable;
use App\Entity\Course;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CourseFixtures extends Fixture
{

    public const COURSE_NAMES_ARRAY = [
        'books',
        'tea',
        'sneakers',
        'cars',
        'cats',
        'memes',
        'vodka'
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::COURSE_NAMES_ARRAY as $courseName) {
            $course = new Course;
            $course->setName($courseName);
            $course->setCreatedAt(new DateTimeImmutable('now'));

            $manager->persist($course);

            $this->addReference($courseName, $course);
        }

        $manager->flush();
    }
}
