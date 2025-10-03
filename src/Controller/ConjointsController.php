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

        $user = $this->getUser();
       
        $commentaire = new Commentaires();
        
        $conjoint = $entityManager->getRepository(Conjoints::class)->find($id);
        $commentaire->setConjoint($conjoint);
        $commentaire->setUser($user);

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
        if(count($notes) >= 1)
        {
        $moyenne = array_sum($notes) / count($notes);
        $moyenne = round($moyenne, 1);
        }
        else{
            $moyenne ='';
        }


        return $this->render('conjoints/show.html.twig', [
            'conjoint' => $conjoint,
            "form" => $form,
            'moyenne' => $moyenne,
            'user' => $user
        ]);
    }

    #[Route('/{id}/edit', name: 'app_conjoints_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Conjoints $conjoint, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ConjointsType::class, $conjoint);
        $form->handleRequest($request);

        $oldimage = $conjoint->getImg();

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('img')->getData();

            if($file)
            {
                $newName = time() . '-' . $file->getClientOriginalName();
                $conjoint->setImg($newName);
                $file->move($this->getParameter('photo_dir'), $newName);
            }
            $entityManager->flush();

            unlink($this->getParameter('photo_dir').'/'.$oldimage);

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
         $oldimage = $conjoint->getImg();

        if ($this->isCsrfTokenValid('delete'.$conjoint->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($conjoint);
            $entityManager->flush();

            unlink($this->getParameter('photo_dir').'/'.$oldimage);

        }

        return $this->redirectToRoute('app_conjoints_index', [], Response::HTTP_SEE_OTHER);
    }
        #[Route('/conjoints/{id}/emprunter', name: 'app_conjoints_emprunter')]
    public function emprunter(Conjoints $conjoint, EntityManagerInterface $em): Response
    {
        // Vérifie si le conjoint est déjà emprunté
        if ($conjoint->getEmprunteur()) {
            $this->addFlash('error', 'Ce conjoint est déjà emprunté.');
            return $this->redirectToRoute('app_conjoints_index');
        }

        // On empêche le propriétaire de s’emprunter lui-même
        if ($conjoint->getProprietaire() === $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas emprunter votre propre conjoint.');
            return $this->redirectToRoute('app_conjoints_index');
        }

        // On définit l’emprunteur et l’état d’acceptation
        $conjoint->setEmprunteur($this->getUser());
        $conjoint->setAccept(false); // le propriétaire doit encore valider

        $em->flush();

        $this->addFlash('success', 'Demande d\'emprunt envoyée.');
        return $this->redirectToRoute('app_conjoints_index');
    }

        #[Route('/conjoints/{id}/accepter', name: 'app_conjoints_accepter')]
    public function accepter(Conjoints $conjoint, EntityManagerInterface $em): Response
    {
        // Vérifie que l'utilisateur est bien le propriétaire
        if ($conjoint->getProprietaire() !== $this->getUser()) {
            $this->addFlash('error', 'Vous n\'êtes pas propriétaire de ce conjoint.');
            return $this->redirectToRoute('app_conjoints_index');
        }

        // Validation de l’emprunt
        $conjoint->setAccept(true);
        $em->flush();

        $this->addFlash('success', 'Vous avez accepté l\'emprunt.');
        return $this->redirectToRoute('app_conjoints_index');
    }

        #[Route('/conjoints/{id}/rendre', name: 'app_conjoints_rendre')]
    public function rendre(Conjoints $conjoint, EntityManagerInterface $em): Response
    {
        // Vérifie que l’utilisateur qui rend est bien l’emprunteur
        if ($conjoint->getEmprunteur() !== $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas rendre ce conjoint.');
            return $this->redirectToRoute('app_conjoints_index');
        }

        // Rendre le conjoint disponible
        $conjoint->setEmprunteur(null);
        $conjoint->setAccept(false);

        $em->flush();

        $this->addFlash('success', 'Vous avez rendu ce conjoint.');
        return $this->redirectToRoute('app_conjoints_index');
    }

        #[Route('/conjoints/{id}/refus', name: 'app_conjoints_refus')]
    public function refus(Conjoints $conjoint, EntityManagerInterface $em): Response
    {
        // Vérifie que l’utilisateur qui rend est bien l’emprunteur
        if ($conjoint->getProprietaire() == $this->getUser()) {
            // Refuser le conjoint disponible
        $conjoint->setEmprunteur(null);
        $conjoint->setAccept(false);

        $em->flush();
        }

        return $this->redirectToRoute('app_conjoints_index');
    }




    
}
