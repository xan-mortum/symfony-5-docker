<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EmployeeControllerTest extends WebTestCase
{
    public function testEmployeeList()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'user@mail.dd',
            'PHP_AUTH_PW'   => '123456',
        ]);

        $client->request('GET', '/employee_tree');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $data = $client->getResponse()->getContent();
        $tree = json_decode($data, true);

        $this->assertEquals(9, count($tree));

        foreach ($tree as $employee) {
            if ($employee['email'] === 'ceo@example.com') {
                $this->assertEquals(8, $employee['subordinatesCount']);
            }
            if ($employee['email'] === 'head.a@example.com') {
                $this->assertEquals(5, $employee['subordinatesCount']);
            }
        }
    }

    public function testEmployeeListWithFilter()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'user@mail.dd',
            'PHP_AUTH_PW'   => '123456',
        ]);
        
        $client->request('GET', '/employee_tree?filter=Development');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $data = $client->getResponse()->getContent();
        $tree = json_decode($data, true);

        $this->assertEquals(6, count($tree));

        foreach ($tree as $employee) {
            if ($employee['email'] === 'ceo@example.com') {
                $this->assertEquals(5, $employee['subordinatesCount']);
            }
            if ($employee['email'] === 'head.a@example.com') {
                $this->assertEquals(2, $employee['subordinatesCount']);
            }
        }
    }

    public function testAddEmployeeDuplicateEmail()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'admin@mail.dd',
            'PHP_AUTH_PW'   => 'qwerty',
        ]);
        
        $newEmployeeData = [
            "firstName" => "test",
            "lastName" => "lt",
            "email" => "headChild.a@example.com",
            "category" => "Development",
            "parent" => "head.a@example.com",
        ];
        $newEmployeeDataString = json_encode($newEmployeeData);

        $client->request('POST', '/employees', [], [], [], $newEmployeeDataString);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $client->getResponse()->getContent();

        $client->request('GET', '/employee_tree');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $data = $client->getResponse()->getContent();
        $tree = json_decode($data, true);

        $this->assertEquals(10, count($tree));

        foreach ($tree as $employee) {
            if ($employee['email'] === 'ceo@example.com') {
                $this->assertEquals(9, $employee['subordinatesCount']);
            }
            if ($employee['email'] === 'head.a@example.com') {
                $this->assertEquals(6, $employee['subordinatesCount']);
            }
        }

        $client->request('POST', '/employees', [], [], [], $newEmployeeDataString);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());

        $client->request('GET', '/employee_tree');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $data = $client->getResponse()->getContent();
        $tree = json_decode($data, true);

        $this->assertEquals(10, count($tree));

        foreach ($tree as $employee) {
            if ($employee['email'] === 'ceo@example.com') {
                $this->assertEquals(9, $employee['subordinatesCount']);
            }
            if ($employee['email'] === 'head.a@example.com') {
                $this->assertEquals(6, $employee['subordinatesCount']);
            }
        }
    }

    public function testAddEmployeeWrongCategory()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'admin@mail.dd',
            'PHP_AUTH_PW'   => 'qwerty',
        ]);
        
        $newEmployeeData = [
            "firstName" => "test",
            "lastName" => "lt",
            "email" => "headChild.a@example.com",
            "category" => "NoDevelopment",
            "parent" => "head.a@example.com",
        ];
        $newEmployeeDataString = json_encode($newEmployeeData);

        $client->request('POST', '/employees', [], [], [], $newEmployeeDataString);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        $client->getResponse()->getContent();
    }

    public function testAddEmployeeWrongParent()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'admin@mail.dd',
            'PHP_AUTH_PW'   => 'qwerty',
        ]);
        
        $newEmployeeData = [
            "firstName" => "test",
            "lastName" => "lt",
            "email" => "headChild.a@example.com",
            "category" => "Development",
            "parent" => "no-head.a@example.com",
        ];
        $newEmployeeDataString = json_encode($newEmployeeData);

        $client->request('POST', '/employees', [], [], [], $newEmployeeDataString);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        $client->getResponse()->getContent();
    }

    public function testAddEmployeeWrongUser()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'user@mail.dd',
            'PHP_AUTH_PW'   => '123456',
        ]);

        $newEmployeeData = [
            "firstName" => "test",
            "lastName" => "lt",
            "email" => "headChild.a@example.com",
            "category" => "Development",
            "parent" => "head.a@example.com",
        ];
        $newEmployeeDataString = json_encode($newEmployeeData);

        $client->request('POST', '/employees', [], [], [], $newEmployeeDataString);
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
        $client->getResponse()->getContent();
    }
}