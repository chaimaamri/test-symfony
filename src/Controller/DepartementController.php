<?php

namespace App\Controller;

use App\Entity\Departement;
use App\Form\DepartementType;
use App\Repository\DepartementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/departements')]
class DepartementController extends AbstractController
{
    #[Route('/', name: 'departement_index', methods: ['GET'])]
    public function index(DepartementRepository $repo): Response
    {
        $departements = $repo->findAll();

        return $this->render('departement/index.html.twig', [
            'departements' => $departements,
        ]);
    }

    #[Route('/{id}', name: 'departement_show', methods: ['GET'])]
    public function show(int $id, DepartementRepository $repo): Response
    {
        $departement = $repo->find($id);
        if (!$departement) {
            throw $this->createNotFoundException('Departement not found');
        }

        return $this->render('departement/show.html.twig', [
            'departement' => $departement,
        ]);
    }

    #[Route('/new', name: 'departement_new', methods: ['GET','POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $departement = new Departement();
        $form = $this->createForm(DepartementType::class, $departement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($departement);
            $em->flush();

            $this->addFlash('success', 'Departement created.');
            return $this->redirectToRoute('departement_index');
        }

        return $this->render('departement/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
