<?php

namespace App\Controller;

use App\Entity\PerfilTip;
use App\Entity\Category;
use App\Entity\PerfilCard;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CategoryRepository;

class CategoryController extends AbstractController
{
    #[Route('/api/category', methods: ['GET'])]
    public function categories(
        CategoryRepository $categoryRepository,
    ): JsonResponse
    {  
        $categories = $categoryRepository->findAll();

        $data = array_map(fn ($category) => [
            'id' => $category->getId(),
            'name' => $category->getName(),
            'cards' => array_map(fn ($card) => [
                'id' => $card->getId(),
            ], $category->getCards()->toArray())
        ], $categories);

        return $this->json($data);
    }
    
    #[Route('/api/category', methods: ['POST'])]
    public function createCategory(
        Request $request,
        EntityManagerInterface $em,
        CategoryRepository $categoryRepository,
    ): JsonResponse
    {  
        $data = $request->toArray();
        $category = $categoryRepository->findOneBy(['name' => $data['name']]);
        if(!$category) {
            $category = new Category;
            $category->setName($data['name']);
            $em->persist($category);
            $em->flush();
        }
        return $this->json([
            'id' => $category->getId(),
            'name' => $category->getName(),
        ]); 
    }
}
