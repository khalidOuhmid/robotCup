<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests for LoginController functionality.
 */
class LoginControllerTest extends WebTestCase
{
    /**
     * Tests that the login page loads successfully.
     */
    public function testLoginPageIsAccessible(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Login');
    }

    /**
     * Tests that logged in users are redirected from login page.
     */
    public function testLoggedInUserIsRedirected(): void
    {
        $client = static::createClient();
        
        // Get test user from repository
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('test@example.com');
        
        // Login the user
        $client->loginUser($testUser);
        
        // Try to access login page
        $client->request('GET', '/login');
        
        $this->assertResponseRedirects('/scores');
    }

    /**
     * Tests that invalid credentials show error message.
     */
    public function testLoginWithInvalidCredentials(): void
    {
        $client = static::createClient();
        
        $client->request('POST', '/login', [
            'email' => 'invalid@example.com',
            'password' => 'wrongpassword'
        ]);

        $this->assertResponseStatusCodeSame(401);
        $this->assertSelectorExists('.error');
    }

    /**
     * Tests that logout functionality works.
     */
    public function testLogout(): void
    {
        $client = static::createClient();
        
        // First login a user
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('test@example.com');
        $client->loginUser($testUser);
        
        // Then try to logout
        $client->request('GET', '/logout');
        
        // Should redirect to login page
        $this->assertResponseRedirects('/login');
    }
}
