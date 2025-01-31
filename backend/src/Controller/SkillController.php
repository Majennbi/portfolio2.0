<?php

namespace App\Controller;

use App\Entity\Skills;
use App\Repository\SkillsRepository;
use App\Repository\ProjectsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


final class SkillController extends AbstractController
{
    #[Route('/api/skills', name: 'skills', methods: ['GET'])]
    public function getAllSkills(SkillsRepository $skillsRepository, SerializerInterface $serializer): JsonResponse
    {

        $allSkills = $skillsRepository->findAll();
        $jsonAllSkills = $serializer->serialize($allSkills, 'json', ['groups' => 'getSkills']);

        return new JsonResponse(
            $jsonAllSkills,
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/api/skills/{id}', name: 'skillById', methods: ['GET'])]
    public function getSkillById(int $id, SkillsRepository $skillsRepository, SerializerInterface $serializer): JsonResponse
    {
        $skill = $skillsRepository->find($id);
        if ($skill) {
            $jsonSkill = $serializer->serialize($skill, 'json', ['groups' => 'getSkills']);
            return new JsonResponse(
                $jsonSkill,
                Response::HTTP_OK,
                [],
                true
            );
        }

        return new JsonResponse(
            null,
            Response::HTTP_NOT_FOUND,
            []
        );
    }

    #[Route('/api/skills/{id}', name: 'deleteSkill', methods: ['DELETE'])]
    public function deleteSkill(int $id, SkillsRepository $skillsRepository, EntityManagerInterface $em): JsonResponse
    {
        $skill = $skillsRepository->find($id);
        if (!$skill) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }
        $em->remove($skill);
        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/skills', name: 'addSkill', methods: ['POST'])]
    public function addSkill(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        UrlGeneratorInterface $urlGenerator,
        ProjectsRepository $projectsRepository
    ): JsonResponse {
        $context = [
            'datetime_format' => 'Y-m-d H:i:s',
            'groups' => 'getSkills'
        ];

        $skill = $serializer->deserialize($request->getContent(), Skills::class, 'json', $context);

        //Récupération de l'ensemble des données envoyées sous forme de tableau
        $content = $request->toArray();

        //Récupération de l'idSkill. S'il n'est pas défini, alors on met -1 par défaut.
        $idProjects = $content['idProjects'] ?? -1;

        //On cherche le skill correspondant à l'idSkill et on l'ajoute au projet
        //Si "find" ne trouve pas de skill correspondant, alors $skill sera null
        $skill->setProjects(new ArrayCollection([$projectsRepository->find($idProjects)]));

        $em->persist($skill);
        $em->flush();

        $jsonSkill = $serializer->serialize($skill, 'json', ['groups' => 'getSkills']);

        $location = $urlGenerator->generate(
            'skillById',
            ['id' => $skill->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new JsonResponse($jsonSkill, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/api/skills/{id}', name: 'updateSkill', methods: ['PUT'])]
    public function updateProject(
        Request $request,
        SerializerInterface $serializer,
        Skills $currentSkill,
        EntityManagerInterface $em,
        ProjectsRepository $projectsRepository
    ): JsonResponse {
        $updatedSkill = $serializer->deserialize(
            $request->getContent(),
            Skills::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentSkill]
        );

        $content = $request->toArray();
        $idProjects = $content['idProjects'] ?? -1;
        $updatedSkill->setProjects(new ArrayCollection([$projectsRepository->find($idProjects)]));

        $em->persist($updatedSkill);
        $em->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
