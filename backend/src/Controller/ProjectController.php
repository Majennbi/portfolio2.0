<?php

namespace App\Controller;

use App\Entity\Projects;
use App\Repository\SkillsRepository;
use App\Repository\ProjectsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ProjectController extends AbstractController
{
    #[Route('/api/projects', name: 'project', methods: ['GET'])]
    public function getAllProjects(ProjectsRepository $projectsRepository, SerializerInterface $serializer): JsonResponse
    {
        $allProjects = $projectsRepository->findAll();
        $jsonAllProjects = $serializer->serialize($allProjects, 'json', ['groups' => 'getProjects']);

        return new JsonResponse(
            $jsonAllProjects,
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/api/projects/{id}', name: 'projectById', methods: ['GET'])]
    public function getProjectById(int $id, ProjectsRepository $projectsRepository, SerializerInterface $serializer): JsonResponse
    {
        $project = $projectsRepository->find($id);
        if (!$project) {
            throw new NotFoundHttpException('Project not found');
        }

        $jsonProject = $serializer->serialize($project, 'json', ['groups' => 'getProjects']);
        return new JsonResponse(
            $jsonProject,
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/api/projects/{id}', name: 'deleteProject', methods: ['DELETE'])]
    public function deleteProject(int $id, ProjectsRepository $projectsRepository, EntityManagerInterface $em): JsonResponse
    {
        $project = $projectsRepository->find($id);
        if (!$project) {
            throw new NotFoundHttpException('Project not found');
        }
        $em->remove($project);
        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/projects', name: 'addProject', methods: ['POST'])]
    public function addProject(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        UrlGeneratorInterface $urlGenerator,
        SkillsRepository $skillsRepository,
        ValidatorInterface $validator
    ): JsonResponse {
        $context = [
            'datetime_format' => 'Y-m-d H:i:s',
            'groups' => 'getProjects'
        ];

        $project = $serializer->deserialize($request->getContent(), Projects::class, 'json', $context);

        $errors = $validator->validate($project);

        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        // Récupération de l'ensemble des données envoyées sous forme de tableau
        $content = $request->toArray();

        // Récupération de l'idSkills. S'il n'est pas défini, alors on met -1 par défaut.
        $idSkills = $content['idSkills'] ?? -1;

        // On cherche le skill correspondant à l'idSkills et on l'ajoute au projet
        // Si "find" ne trouve pas de skill correspondant, alors $skill sera null
        $skills = $skillsRepository->find($idSkills);
        if ($skills) {
            $project->setSkills(new ArrayCollection([$skills]));
        } else {
            $project->setSkills(new ArrayCollection());
        }

        $em->persist($project);
        $em->flush();

        $jsonProject = $serializer->serialize($project, 'json', ['groups' => 'getProjects']);

        $location = $urlGenerator->generate(
            'projectById',
            ['id' => $project->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new JsonResponse($jsonProject, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/api/projects/{id}', name: 'updateProject', methods: ['PUT'])]
    public function updateProject(
        Request $request,
        SerializerInterface $serializer,
        Projects $currentProject,
        EntityManagerInterface $em,
        SkillsRepository $skillsRepository,
        ValidatorInterface $validator
    ): JsonResponse {
        $updatedProject = $serializer->deserialize(
            $request->getContent(),
            Projects::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentProject]
        );

        $errors = $validator->validate($updatedProject);

        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $content = $request->toArray();
        $idSkills = $content['idSkills'] ?? -1;
        $skills = $skillsRepository->find($idSkills);
        if ($skills) {
            $updatedProject->setSkills(new ArrayCollection([$skills]));
        } else {
            $updatedProject->setSkills(new ArrayCollection());
        }

        $em->persist($updatedProject);
        $em->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
