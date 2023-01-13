<?php

namespace App\DataFixtures;

use DateTimeImmutable;
use App\Entity\Lecture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;


class LectureFixtures extends Fixture //implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
       for ($i = 0; $i < 50; $i++) {
            $courseName = array_rand(array_flip(CourseFixtures::COURSE_NAMES_ARRAY));
            $course = $this->getReference($courseName);

            $lecture = new Lecture;
            $lecture->setName( $courseName . '_lecture_name');
            $lecture->setBlogId($course);
            $lecture->setCreatedAt(new DateTimeImmutable('now'));

            $manager->persist($lecture);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CourseFixtures::class
        ];
    }
}
