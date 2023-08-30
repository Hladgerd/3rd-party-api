<?php

namespace Tests\Feature\API;

use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class GetContactTest extends TestCase
{
    private static string $validUri = '/api/get-contact/';
    private static string $invalidUri = '/api/get_contact/';
    private static string $validEmail = 'joe.max@example.com';
    private static string $invalidEmail = 'joe.max.example.com';
    private static string $nonExistentEmail = 'joe.max@example.hu';
    private static string $firstName = 'Joe';
    private static string $lastName = 'Max';


    /**
     * Happy path
     */
    public function test_request_valid_uri_returns_successful_response(): void
    {
        $this->json('get', self::$validUri . self::$validEmail)
            ->assertStatus(Response::HTTP_OK);
    }

    public function test_data_is_returned_in_valid_format(): void
    {
        $this->json('get', self::$validUri . self::$validEmail)
            ->assertJsonStructure(
                [
                    'id',
                    'email',
                    'permission',
                    'external_id',
                    'anonymous',
                    'created',
                    'updated',
                    'standard_fields' => [
                        'FIRSTNAME',
                        'LASTNAME'
                    ],
                    'custom_fields',
                    'preferences'
                ]
            );
    }

    public function test_valid_contact_details_are_returned(): void
    {
        $response = $this->get(self::$validUri . self::$validEmail)->json();

        $this->assertEquals(self::$validEmail, $response['email']);
        $this->assertEquals(self::$firstName, $response['standard_fields']['FIRSTNAME']);
        $this->assertEquals(self::$lastName, $response['standard_fields']['LASTNAME']);

    }


    /**
     * Negative tests
     */
    public function test_request_invalid_uri_returns_error_message(): void
    {
        $this->json('get', self::$invalidUri . self::$validEmail)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'message' => 'Incorrect route. Try something else',
            ]);
    }

    public function test_request_with_invalid_email_returns_error_message(): void
    {
        $this->json('get', self::$validUri . self::$invalidEmail)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'message' => 'Incorrect route. Try something else',
            ]);
    }

    public function test_request_without_email_returns_error_message(): void
    {
        $this->json('get', self::$validUri)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'message' => 'Incorrect route. Try something else',
            ]);
    }

    public function test_contact_not_found_with_nonexistent_email(): void
    {
        $this->json('get', self::$validUri . self::$nonExistentEmail)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'message' => [
                    '0' => 'contact with email ' . self::$nonExistentEmail . ' isn\'t found',
                ]
            ]);

    }
}
