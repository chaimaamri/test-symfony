<?php

namespace App\Controller;

use App\Repository\FaculteRepository;
use App\Entity\Faculte;
use App\Form\FaculteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/facultes')]
class FaculteController extends AbstractController
{
    #[Route('/', name: 'faculte_index', methods: ['GET'])]
    public function index(FaculteRepository $faculteRepository): Response
    {
        $facultes = $faculteRepository->findAll();

        return $this->render('faculte/index.html.twig', [
            'facultes' => $facultes,
        ]);
    }

    #[Route('/{id}', name: 'faculte_show', methods: ['GET'])]
    public function show(int $id, FaculteRepository $faculteRepository): Response
    {
        $faculte = $faculteRepository->find($id);
        if (!$faculte) {
            throw $this->createNotFoundException('Faculte not found');
        }

        return $this->render('faculte/show.html.twig', [
            'faculte' => $faculte,
        ]);
    }

    #[Route('/new', name: 'faculte_new', methods: ['GET','POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $faculte = new Faculte();
        $form = $this->createForm(FaculteType::class, $faculte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($faculte);
            $em->flush();

            $this->addFlash('success', 'Faculte created.');
            return $this->redirectToRoute('faculte_index');
        }

        return $this->render('faculte/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'faculte_edit', methods: ['GET','POST'])]
    public function edit(int $id, Request $request, FaculteRepository $faculteRepository, EntityManagerInterface $em): Response
    {
        $faculte = $faculteRepository->find($id);
        if (!$faculte) {
            throw $this->createNotFoundException('Faculte not found');
        }

        $form = $this->createForm(FaculteType::class, $faculte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Faculte updated.');
            return $this->redirectToRoute('faculte_show', ['id' => $faculte->getId()]);
        }

        return $this->render('faculte/edit.html.twig', [
            'form' => $form->createView(),
            'faculte' => $faculte,
        ]);
    }

    #[Route('/{id}/delete', name: 'faculte_delete', methods: ['POST'])]
    public function delete(int $id, Request $request, FaculteRepository $faculteRepository, EntityManagerInterface $em): Response
    {
        $faculte = $faculteRepository->find($id);
        if (!$faculte) {
            throw $this->createNotFoundException('Faculte not found');
        }

        if ($this->isCsrfTokenValid('delete'.$faculte->getId(), $request->request->get('_token'))) {
            // Detach related departements so DB constraints don't block deletion
            foreach ($faculte->getDepartements() as $departement) {
                $departement->setFaculte(null);
                $em->persist($departement);
            }

            $em->remove($faculte);
            $em->flush();
            $this->addFlash('success', 'Faculte deleted.');
        } else {
            $this->addFlash('error', 'Invalid CSRF token.');
        }

        return $this->redirectToRoute('faculte_index');
    }
}
