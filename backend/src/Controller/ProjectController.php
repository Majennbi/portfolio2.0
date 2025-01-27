<?php

namespace App\Controller;

use App\Repository\ProjectsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
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

    #[Route('/api/projects/{id}', name: 'project_by_id', methods: ['GET'])]
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
}
