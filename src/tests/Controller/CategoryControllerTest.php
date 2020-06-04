<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategoryControllerTest extends WebTestCase
{
    public function testCategoriesList()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'user@mail.dd',
            'PHP_AUTH_PW'   => '123456',
        ]);

        $client->request('GET', '/categories');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}