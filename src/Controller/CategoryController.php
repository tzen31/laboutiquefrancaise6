<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    private $entityManager;

    /**
     * ProductController constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/categorie/{slug}', name: 'app_category')]
    public function index($slug, CategoryRepository $categoryRepository): Response
    {
        //dd($slug);
        //$category = $categoryRepository->findAll();
        //$category = $categoryRepository->findOneByName($slug);
        //$category = $this->entityManager->getRepository(Category::class)->findOneBySlug($slug);
        $category = $categoryRepository->findOneBySlug($slug);        
        //dd($category);

        if (!$category){
            return $this->redirectToRoute('app_home');
        }

        return $this->render('category/index.html.twig', [
            //'controller_name' => 'CategoryController',
            'category' => $category,
        ]);
    }
}
