<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Skills;
use DateTimeImmutable;
use App\Entity\Projects;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Créer des users "Visiteurs"
        $user = new User();
        $user->setEmail('visiteur@visiteur.fr');
        $user->setRoles(['ROLE_VISITEUR']);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, 'visiteur'));

        $manager->persist($user);

        // Créer des users "Admin"
        $user = new User();
        $user->setEmail('admin@admin.fr');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, 'admin'));

        $manager->persist($user);

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
