<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Employee;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class EmployeeFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $employees = [
            ["first_name" => "CEO", "last_name" => "X", "email" => "ceo@example.com", "category" => "Board", "parent" => null],
            ["first_name" => "Head", "last_name" => "A", "email" => "head.a@example.com", "category" => "Management", "parent" => "ceo@example.com"],
            ["first_name" => "Head", "last_name" => "B", "email" => "head.b@example.com", "category" => "Management", "parent" => "ceo@example.com"],
            ["first_name" => "Manager", "last_name" => "C", "email" => "manager.c@example.com", "category" => "Management", "parent" => "head.a@example.com"],
            ["first_name" => "Manager", "last_name" => "D", "email" => "manager.d@example.com", "category" => "Management", "parent" => "head.a@example.com"],
            ["first_name" => "CEAccounterO", "last_name" => "H", "email" => "accounter.h@example.com", "category" => "Accounting", "parent" => "head.b@example.com"],
            ["first_name" => "Developer", "last_name" => "E", "email" => "developer.e@example.com", "category" => "Development", "parent" => "manager.c@example.com"],
            ["first_name" => "Developer", "last_name" => "G", "email" => "developer.g@example.com", "category" => "Development", "parent" => "manager.d@example.com"],
            ["first_name" => "Designer", "last_name" => "F", "email" => "designer.f@example.com", "category" => "Development", "parent" => "manager.c@example.com"],
        ];

        $tmpEmployeeList = [];
        foreach ($employees as $employeeData) {
            $employee = new Employee();
            $employee->setFirstName($employeeData["first_name"]);
            $employee->setLastName($employeeData["last_name"]);
            $employee->setEmail($employeeData["email"]);
            /** @var Category $category */
            $category = $this->getReference($employeeData["category"]);
            $employee->setCategory($category);
            $manager->persist($employee);
            $tmpEmployeeList[$employeeData["email"]] = $employee;
        }
        foreach ($employees as $employeeData) {
            if ($employeeData["parent"] !== null) {
                $tmpEmployeeList[$employeeData["email"]]->setParent($tmpEmployeeList[$employeeData["parent"]]);
            }
            $manager->persist($tmpEmployeeList[$employeeData["email"]]);
        }

        $manager->flush();
    }

    /**
     * @inheritDoc
     */
    public function getOrder()
    {
        return 2;
    }
}
