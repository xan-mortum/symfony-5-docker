<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Entity\Category;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;

class CategoryController extends AbstractController
{
    /** @var CategoryRepository */
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @Route("/categories", name="get_category_list", methods={"GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the categories",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(
     *             type="object",
     *             @SWG\Property(property="id", type="integer"),
     *             @SWG\Property(property="name", type="string"),
     *         )
     *     )
     * )
     * @SWG\Tag(name="categories")
     *
     * @IsGranted("ROLE_USER")
     */
    public function list(): JsonResponse
    {
        $categories = $this->categoryRepository->findAll();

        $data = [];
        foreach ($categories as $category) {
            $data[] = $category->toArray();
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }
}