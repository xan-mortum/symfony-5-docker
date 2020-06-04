<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\EmployeeRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Employee;
use Swagger\Annotations as SWG;

class EmployeeController extends AbstractController
{
    /** @var EmployeeRepository */
    private $employeeRepository;

    /** @var CategoryRepository */
    private $categoryRepository;

    public function __construct(EmployeeRepository $employeeRepository, CategoryRepository $categoryRepository)
    {
        $this->employeeRepository = $employeeRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @Route("/employee_tree", name="get_employee_list_with_subordinated", methods={"GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the employee with child",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(
     *             type="object",
     *             @SWG\Property(property="id", type="integer"),
     *             @SWG\Property(property="firstName", type="string"),
     *             @SWG\Property(property="lastName", type="string"),
     *             @SWG\Property(property="email", type="string"),
     *             @SWG\Property(
     *                 property="category",
     *                 type="object",
     *                 @SWG\Property(property="id", type="integer"),
     *                 @SWG\Property(property="name", type="string")
     *             ),
     *             @SWG\Property(property="subordinatesCount", type="integer"),
     *             @SWG\Property(
     *                 property="subordinates",
     *                 type="array",
     *                 @SWG\Items(ref=@Model(type=Employee::class))
     *             )
     *         )
     *     )
     * )
     * @SWG\Parameter(
     *     name="filter",
     *     in="query",
     *     required=false,
     *     type="array",
     *     @SWG\Items(
     *         type="string",
     *     )
     * )
     * @SWG\Tag(name="employee_tree")
     *
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $filter = $request->query->get('filter');
        if ($filter) {
            $employees = $this->employeeRepository->findWithCategoryFilter($filter);
        } else {
            $employees = $this->employeeRepository->findAll();
        }

        $data = [];
        foreach ($employees as $employee) {
            $data[] = $employee->toArrayWithCategoryFilter($filter);
        }

        array_multisort(array_column($data, 'subordinatesCount'), SORT_DESC, $data);

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/employees", name="add_new_employee", methods={"POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns id of new employee",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(
     *             type="object",
     *             @SWG\Property(property="id", type="integer"),
     *             @SWG\Property(property="status", type="string")
     *         )
     *     )
     * )
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="firstName", type="string"),
     *         @SWG\Property(property="lastName", type="string"),
     *         @SWG\Property(property="email", type="string"),
     *         @SWG\Property(property="category", type="string"),
     *         @SWG\Property(property="parent", type="string")
     *     )
     * )
     *
     * @SWG\Tag(name="add_employee")
     *
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new NotFoundHttpException(json_last_error_msg());
        }

        $firstName = $data['firstName'] ?? null;
        $lastName = $data['lastName'] ?? null;
        $email = $data['email'] ?? null;
        $categoryName = $data['category'] ?? null;
        $parentEmail = $data['parent'] ?? null;

        if (!$firstName || !$lastName || !$email || !$categoryName || !$parentEmail) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }

        $category = $this->categoryRepository->findOneBy(["name" => $categoryName]);
        if (!$category) {
            throw new NotFoundHttpException('Category name not found!');
        }

        $parent = $this->employeeRepository->findOneBy(["email" => $parentEmail]);
        if (!$parent) {
            throw new NotFoundHttpException('Employee with email ' . $parentEmail . ' not found!');
        }

        try {
            $employee = $this->employeeRepository->save($firstName, $lastName, $email, $category, $parent);
        } catch (\Exception $e) {
            throw new NotFoundHttpException($e->getMessage());
        }

        return new JsonResponse(['status' => 'employee created!', 'id' => $employee->getId()], Response::HTTP_CREATED);
    }
}