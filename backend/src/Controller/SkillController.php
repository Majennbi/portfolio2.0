<?php

namespace App\Controller;

use App\Entity\Skills;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class SkillController extends AbstractController
{
    #[Route('/skill', name: 'app_skill')]
    public function index(): Response
    {
        return $this->render('skill/index.html.twig', [
            'controller_name' => 'SkillController',
        ]);
    }
}
