<?php

namespace App\Controller\Admin;

use App\Entity\Categories;
use App\Form\CategoriesFormType;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/categories', name:'admin_categories_')]
class CategoriesController extends AbstractController
{
    #[Route('/', name:'index')]
    public function index(CategoriesRepository $categoriesRepository): Response
    {
        $categories = $categoriesRepository->findBy([], ['categoryOrder' => 'asc']);
        
        return $this->render('admin/categories/index.html.twig', compact('categories'));
    }

    #[Route('/ajout', name: 'add')]
    public function add(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // on crée une nouvelle catégorie
        $category = new Categories();

        // on crée le formulaire
        $form = $this->createForm(CategoriesFormType::class, $category);

        // on traite la requête du formulaire
        $form->handleRequest($request);

        // on vérifie si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {

            // on génère le slug avec le nom du produit
            $slug = $slugger->slug($category->getName());
            $category->setSlug($slug);

            // on va stocker les informations
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'Catégorie ajoutée avec succès');

            // on redirige
            return $this->redirectToRoute('admin_categories_index');
        }

        return $this->render('admin/categories/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/edition/{id}', name: 'edit')]
    public function edit(Categories $category, Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('CATEGORY_EDIT', $category);

        $form = $this->createForm(CategoriesFormType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $slug = $slugger->slug($category->getName());
            $category->setSlug($slug);

            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'Catégorie modifiée avec succès');

            return $this->redirectToRoute('admin_categories_index');
        }

        return $this->render('admin/categories/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category
        ]);

    }

    #[Route('/suppression/{id}', name: 'delete')]
    public function delete(Categories $category): Response
    {
        // on vérifie si l'utilisateur peut supprimer avec le voter
        $this->denyAccessUnlessGranted('CATEGORY_DELETE', $category);

        return $this->render('admin/categories/index.html.twig');
    }

}