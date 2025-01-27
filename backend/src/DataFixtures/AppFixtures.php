<?php

namespace App\DataFixtures;

use App\Entity\Projects;
use App\Entity\Skills;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use DateTimeImmutable;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Créer des compétences (skills)
        $skills = [];
        for ($i = 1; $i <= 5; $i++) {
            $skill = new Skills();
            $skill->setName("Skill $i")
                ->setIcon("icon$i.png")
                ->setLevel("Level $i")
                ->setCreatedAt(new DateTimeImmutable());

            $manager->persist($skill);
            $skills[] = $skill;
        }

        // Créer des projets (projects) et les associer aux compétences
        for ($i = 1; $i <= 10; $i++) {
            $project = new Projects();
            $project->setTitle("Project $i")
                ->setDescription("Description for project $i")
                ->setImage("image$i.jpg")
                ->setLink("http://example.com/project$i")
                ->setDuration("Duration $i")
                ->setCreatedAt(new DateTimeImmutable())
                ->setUpdatedAt(new DateTimeImmutable());

            // Associer des compétences aléatoires au projet
            foreach ($skills as $skill) {
                if (rand(0, 1)) {
                    $project->addSkill($skill);
                }
            }

            $manager->persist($project);
        }

        $manager->flush();
    }
}