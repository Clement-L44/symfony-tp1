<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CreateProduitType;
use App\Repository\ProduitRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Produit;
use Symfony\Component\HttpFoundation\Request;

class ProduitController extends AbstractController
{
    #[Route('/produit', name: 'app_produit')]
    public function index(ProduitRepository $produitRepository): Response
    {
        dd($this->getUser());
        $produits = $produitRepository->findAll();

        return $this->render('produit/index.html.twig', [
            'produits' => $produits,
        ]);
    }

    #[Route('/produit/new', name: 'app_produit_new')]
    public function new(ManagerRegistry $doctrine, Request $request, ProduitRepository $produitRepository): Response
    {
        $pr = $produitRepository->findAll();
        $produit = new Produit();
        $form = $this->createForm(CreateProduitType::class, $produit);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            //dd($produit);
            $em = $doctrine->getManager();
            $em->persist($produit);
            $em->flush();
            return $this->redirectToRoute('app_produit');
        }

        return $this->render('produit/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/produit/{id}', name: 'app_produit_update')]
    public function update(ManagerRegistry $doctrine, $id, ProduitRepository $produitRepository, Request $request): Response
    {

        $produit = $produitRepository->find($id);
        $form = $this->createForm(CreateProduitType::class, $produit);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $doctrine->getManager();

            $em->persist($produit);
            $em->flush();

            return $this->redirectToRoute('app_produit');
        }

        return $this->render('produit/produit.html.twig', [
            'form' => $form->createView(),
            'id' => $id,
        ]);
    }

    
    #[Route('/produit/remove/{id}', name: 'app_produit_remove')]
    public function remove(ManagerRegistry $doctrine, $id, ProduitRepository $produitRepository): Response
    {
        $em = $doctrine->getManager();
        $produit = $produitRepository->find($id);
        $em->remove($produit);
        $em->flush();

        $this->addFlash(
            'success',
            'Le produit a été supprimé !'
        );

        return $this->redirectToRoute('app_produit');

    }
}
