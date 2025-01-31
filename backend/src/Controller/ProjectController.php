<?php

namespace App\Controller;

use App\Entity\Projects;
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

final class ProjectController extends AbstractController{
    #[Route('/api/projects', name: 'project', methods: ['GET'])]
    public function getAllProjects(ProjectsRepository $projectsRepository, SerializerInterface $serializer): JsonResponse
    {

        $allProjects = $projectsRepository->findAll();
        $jsonAllProjects = $serializer->serialize($allProjects, 'json', ['groups' => 'getProjects']);

        return new JsonResponse (
            $jsonAllProjects, Response::HTTP_OK, [], true
        );
    }

    #[Route('/api/projects/{id}', name: 'projectById', methods: ['GET'])]
    public function getProjectById(int $id, ProjectsRepository $projectsRepository, SerializerInterface $serializer): JsonResponse
    {
        $project = $projectsRepository->find($id);
        if ($project) {
            $jsonProject = $serializer->serialize($project, 'json', ['groups' => 'getProjects']);
            return new JsonResponse (
                $jsonProject, Response::HTTP_OK, [], true
            );
        }

        return new JsonResponse (
            null, Response::HTTP_NOT_FOUND, []);
    }

    #[Route('/api/projects/{id}', name: 'deleteProject', methods: ['DELETE'])]
    public function deleteProject(int $id, ProjectsRepository $projectsRepository, EntityManagerInterface $em): JsonResponse
    {
        $project = $projectsRepository->find($id);
        if (!$project) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }
        $em->remove($project);
        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/projects', name: 'addProject', methods: ['POST'])]
    public function addProject(Request $request, SerializerInterface $serializer, EntityManagerInterface $em,
    UrlGeneratorInterface $urlGenerator, SkillsRepository $skillsRepository): JsonResponse
    {
        $context = [
            'datetime_format' => 'Y-m-d H:i:s',
            'groups' => 'getProjects'
        ];

        $project = $serializer->deserialize($request->getContent(), Projects::class, 'json', $context);

        //Récupération de l'ensemble des données envoyées sous forme de tableau
        $content = $request->toArray();

        //Récupération de l'idSkill. S'il n'est pas défini, alors on met -1 par défaut.
        $idSkills = $content['idSkills'] ?? -1;

        //On cherche le skill correspondant à l'idSkill et on l'ajoute au projet
        //Si "find" ne trouve pas de skill correspondant, alors $skill sera null
        $project->setSkills(new ArrayCollection([$skillsRepository->find($idSkills)]));

        $em->persist($project);
        $em->flush();

        $jsonProject = $serializer->serialize($project, 'json', ['groups' => 'getProjects']);

        $location = $urlGenerator->generate('projectById', ['id' => $project->getId()], 
        UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonProject, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/api/projects/{id}', name: 'updateProject', methods: ['PUT'])]
    public function updateProject(
        Request $request,
        SerializerInterface $serializer,
        Projects $currentProject,
        EntityManagerInterface $em,
        SkillsRepository $skillsRepository
    ): JsonResponse

    {
    $updatedProject = $serializer->deserialize($request->getContent(), Projects::class, 'json', 
    [AbstractNormalizer::OBJECT_TO_POPULATE => $currentProject]);

    $content = $request->toArray();
    $idSkills = $content['idSkills'] ?? -1;
    $updatedProject->setSkills(new ArrayCollection([$skillsRepository->find($idSkills)]));

        $em->persist($updatedProject);
        $em->flush();
    
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
