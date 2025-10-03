<?php

namespace App\Controller;

use App\Entity\Commentaires;
use App\Entity\Conjoints;
use App\Form\CommentairesType;
use App\Form\ConjointsType;
use App\Repository\ConjointsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/conjoints')]
final class ConjointsController extends AbstractController
{
    #[Route(name: 'app_conjoints_index', methods: ['GET'])]
    public function index(ConjointsRepository $conjointsRepository): Response
    {
        return $this->render('conjoints/index.html.twig', [
            'conjoints' => $conjointsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_conjoints_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $conjoint = new Conjoints();
        $form = $this->createForm(ConjointsType::class, $conjoint);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('img')->getData();

            if($file)
            {
                $newName = time() . '-' . $file->getClientOriginalName();
                $conjoint->setImg($newName);
                $file->move($this->getParameter('photo_dir'), $newName);
            }
            $entityManager->persist($conjoint);
            $entityManager->flush();

            return $this->redirectToRoute('app_conjoints_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('conjoints/new.html.twig', [
            'conjoint' => $conjoint,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_conjoints_show')]
    public function show(Conjoints $conjoint, Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        $commentaire = new Commentaires();
        $form = $this->createForm(CommentairesType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($commentaire);
            $entityManager->flush();

            return $this->redirectToRoute('app_conjoints_show', ['id'=> $id], Response::HTTP_SEE_OTHER);
        }

        $commentaires = $conjoint->getCommentaires();
        $notes = [];
        foreach ($commentaires as $commentaire)
        {
            $notes[] = $commentaire->getNote();
        }
        $moyenne = array_sum($notes) / count($notes);
        $moyenne = round($moyenne, 1);


        return $this->render('conjoints/show.html.twig', [
            'conjoint' => $conjoint,
            "form" => $form,
            'moyenne' => $moyenne,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_conjoints_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Conjoints $conjoint, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ConjointsType::class, $conjoint);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_conjoints_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('conjoints/edit.html.twig', [
            'conjoint' => $conjoint,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_conjoints_delete', methods: ['POST'])]
    public function delete(Request $request, Conjoints $conjoint, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$conjoint->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($conjoint);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_conjoints_index', [], Response::HTTP_SEE_OTHER);
    }
}
