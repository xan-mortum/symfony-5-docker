<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EmployeeRepository")
 */
class Employee
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="employees")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Employee", inversedBy="employees")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Employee", mappedBy="parent")
     */
    private $employees;

    public function __construct()
    {
        $this->employees = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getParent(): ?Employee
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getEmployees(): Collection
    {
        return $this->employees;
    }

    /**
     * @return int
     */
    public function getEmployeesCount(): int
    {
        $count = count($this->employees);
        foreach ($this->employees as $employee) {
            $count += $employee->getEmployeesCount();
        }
        return $count;
    }

    /**
     * @param string $category
     * @return int
     */
    public function getEmployeesCountWithCategoryFilter(?string $category): int
    {
        $count = 0;
        /** @var Employee $employee */
        foreach ($this->employees as $employee) {
            if ($employee->getCategory()->getName() === $category) {
                continue;
            }
            $count++;
            $count += $employee->getEmployeesCountWithCategoryFilter($category);
        }
        return $count;
    }

    /**
     * @param Employee $employee
     * @return $this
     */
    public function addEmployee(self $employee): self
    {
        if (!$this->employees->contains($employee)) {
            $this->employees[] = $employee;
            $employee->setParent($this);
        }

        return $this;
    }

    /**
     * @param Employee $employee
     * @return $this
     */
    public function removeEmployee(self $employee): self
    {
        if ($this->employees->contains($employee)) {
            $this->employees->removeElement($employee);
            // set the owning side to null (unless already changed)
            if ($employee->getParent() === $this) {
                $employee->setParent(null);
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $subordinates = [];
        foreach ($this->getEmployees() as $employee) {
            $subordinates[] = $employee->toArray();
        }
        return [
            "id" => $this->id,
            "firstName" => $this->firstName,
            "lastName" => $this->lastName,
            "email" => $this->email,
            "category" => $this->getCategory()->toArray(),
            "subordinatesCount" => $this->getEmployeesCount(),
            "subordinates" => $subordinates
        ];
    }

    public function toArrayWithCategoryFilter(?string $category): array
    {
        $subordinates = [];
        foreach ($this->getEmployees() as $employee) {
            if ($employee->getCategory()->getName() === $category) {
                continue;
            }
            $subordinates[] = $employee->toArrayWithCategoryFilter($category);
        }
        return [
            "id" => $this->id,
            "firstName" => $this->firstName,
            "lastName" => $this->lastName,
            "email" => $this->email,
            "category" => $this->getCategory()->toArray(),
            "parent" => $this->getParent() === null ?: $this->getParent()->getEmail(),
            "subordinatesCount" => $this->getEmployeesCountWithCategoryFilter($category),
            "subordinates" => $subordinates
        ];
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }
}
