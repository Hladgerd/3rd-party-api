<?php

namespace Tests\Feature\API;

use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class GetContactTest extends TestCase
{
    private static string $correctUri = '/api/get-contact/';
    private static string $correctEmail = 'joe.max@example.com';
    private static string $firstName = 'Joe';
    private static string $lastName = 'Max';


    /**
     * Happy path
     */
    public function test_request_returns_successful_response(): void
    {
        $this->json('get', self::$correctUri . self::$correctEmail)
            ->assertStatus(Response::HTTP_OK);
    }

    public function test_data_is_returned_in_valid_format(): void
    {
        $this->json('get', self::$correctUri . self::$correctEmail)
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
        $response = $this->get(self::$correctUri . self::$correctEmail)->json();

        $this->assertEquals(self::$correctEmail, $response['email']);
        $this->assertEquals(self::$firstName, $response['standard_fields']['FIRSTNAME']);
        $this->assertEquals(self::$lastName, $response['standard_fields']['LASTNAME']);

    }
}
