<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Employee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;

/**
 * @method Employee|null find($id, $lockMode = null, $lockVersion = null)
 * @method Employee|null findOneBy(array $criteria, array $orderBy = null)
 * @method Employee[]    findAll()
 * @method Employee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmployeeRepository extends ServiceEntityRepository
{
    /** @var EntityManagerInterface */
    private $manager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $manager)
    {
        parent::__construct($registry, Employee::class);
        $this->manager = $manager;
    }

    /**
     * @param string $category
     * @return Employee[] Returns an array of Employee objects
     */
    public function findWithCategoryFilter(string $category)
    {
        return $this->createQueryBuilder('e')
            ->leftJoin(Category::class, 'c', Join::WITH, 'c.id = e.category')
            ->leftJoin(Employee::class, 'p', Join::WITH, 'p.id = e.parent')
            ->where('c.name != :category')
            ->setParameter('category', $category)
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?Employee
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param Category $category
     * @param Employee $parent
     *
     * @return Employee
     */
    public function save(string $firstName, string $lastName, string $email, Category $category, Employee $parent): Employee
    {
        $employee = new Employee();

        $employee->setFirstName($firstName);
        $employee->setLastName($lastName);
        $employee->setEmail($email);
        $employee->setCategory($category);
        $employee->setParent($parent);

        $this->manager->persist($employee);
        $this->manager->flush();

        return $employee;
    }
}
