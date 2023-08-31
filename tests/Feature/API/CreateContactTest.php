<?php

namespace Tests\Feature\API;

use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CreateContactTest extends TestCase
{
    private static string $validUri = '/api/create-contact/';
    private static int $permissionCode = 5;
    private static string $permissionType = 'doi+';

    /**
     * Happy path
     */
    public function test_valid_request_returns_created_response_code(): void
    {
        $payload = [
            'email'      => fake()->safeEmail(),
            'firstName' => fake()->firstName,
            'lastName'  => fake()->lastName,

        ];

        $this->json('post', self::$validUri, $payload)
            ->assertStatus(Response::HTTP_CREATED);
    }

    public function test_result_is_returned_in_valid_format(): void
    {
        $payload = [
            'email'      => fake()->safeEmail(),
            'firstName' => fake()->firstName,
            'lastName'  => fake()->lastName,

        ];

        $this->json('post', self::$validUri, $payload)
            ->assertJsonStructure(
                [
                    'message'
                ]
            );
    }

    public function test_result_indicates_success_message(): void
    {
        $payload = [
            'email'      => fake()->safeEmail(),
            'firstName' => fake()->firstName,
            'lastName'  => fake()->lastName,

        ];

        $response = $this->post(self::$validUri, $payload)->json();

        $contact = $this->get('/api/get-contact/' . $payload['email'])->json();
        $expectedMessage = ['message' => 'Contact was created or updated successfully with id ' . $contact['id'][0]];

        $this->assertEquals($expectedMessage, $response);
    }

    public function test_contact_created_with_DOI_plus_permission(): void
    {
        $payload = [
            'email'      => fake()->safeEmail(),
            'firstName' => fake()->firstName,
            'lastName'  => fake()->lastName,

        ];

        $this->post(self::$validUri, $payload);
        $contact = $this->get('/api/get-contact/' . $payload['email'])->json();

        $this->assertEquals(self::$permissionCode, $contact['permission']['code']);
        $this->assertEquals(self::$permissionType, $contact['permission']['type']);
    }

    public function test_if_contact_exists_its_name_updated(): void
    {
        $payloadToCreate = [
            'email'      => fake()->safeEmail(),
            'firstName' => fake()->firstName,
            'lastName'  => fake()->lastName,

        ];

        $payloadToUpdate = [
            'email'      => $payloadToCreate['email'],
            'firstName' => fake()->firstName,
            'lastName'  => fake()->lastName,

        ];

        $this->post(self::$validUri, $payloadToCreate);
        $this->post(self::$validUri, $payloadToUpdate);

        $updatedContact = $this->get('/api/get-contact/' . $payloadToCreate['email'])->json();

        $this->assertEquals($payloadToUpdate['firstName'], $updatedContact['standard_fields']['FIRSTNAME']);
        $this->assertEquals($payloadToUpdate['lastName'], $updatedContact['standard_fields']['LASTNAME']);
    }




}
