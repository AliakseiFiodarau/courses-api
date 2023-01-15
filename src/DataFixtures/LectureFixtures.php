<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Course;
use DateTimeImmutable;
use App\Entity\Lecture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class LectureFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * Loading fixture.
     *
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 50; $i++) {
            $courseName = array_rand(array_flip(CourseFixtures::COURSE_NAMES_ARRAY));
            $course = $this->getReference($courseName);

            $lecture = new Lecture;
            $lecture->setName($courseName . '_lecture_name');
            /** @var Course $course */
            $lecture->setBlogId($course);
            $lecture->setCreatedAt(new DateTimeImmutable('now'));

            $manager->persist($lecture);
        }

        $manager->flush();
    }

    /**
     * Get fixtures sequence.
     *
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            CourseFixtures::class
        ];
    }
}
