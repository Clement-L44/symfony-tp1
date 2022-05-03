<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Category;
use Symfony\Component\HttpFoundation\Request;
use App\Form\CreateCategoryType;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(CategoryRepository $categoryRepository): Response
    {

        $categories = $categoryRepository->findAll();


        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/category/new', name: 'app_category_new')]
    public function new(ManagerRegistry $doctrine, Request $request): Response
    {

        $category = new Category();
        $form = $this->createForm(CreateCategoryType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $doctrine->getManager();
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('app_category');
        }

        return $this->render('category/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/category/{id}', name: 'app_category_update')]
    public function update(ManagerRegistry $doctrine, $id, CategoryRepository $categoryRepository, Request $request): Response
    {

        $category = $categoryRepository->find($id);
        $form = $this->createForm(CreateCategoryType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $doctrine->getManager();
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('app_category');
        }

        return $this->render('category/category.html.twig', [
            'form' => $form->createView(),
            'id' => $id,
        ]);
    }

    
    #[Route('/category/remove/{id}', name: 'app_category_remove')]
    public function remove(ManagerRegistry $doctrine, $id, CategoryRepository $categoryRepository): Response
    {
        $em = $doctrine->getManager();
        $category = $categoryRepository->find($id);
        foreach($category->getProduits() as $produit){
            $category->removeProduit($produit);
            $em->persist($produit);
            /* onDelete Cascade Symfony 6 ?? */
            $em->remove($produit);
            $em->flush();
        }

        $em->remove($category);
        $em->flush();

        $this->addFlash(
            'success',
            'La category a été supprimé !'
        );

        return $this->redirectToRoute('app_category');

    }
}
