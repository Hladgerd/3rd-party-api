<?php

namespace Tests\Feature\API;

use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CreateContactTest extends TestCase
{
    private static string $validUri = '/api/create-contact/';
    private static string $invalidUri = '/api/create_contact/';
    private static string $invalidEmail = 'joe.max.example';
    private static array $invalidName = ['Joe Max'];
    private static int $permissionCode = 5;
    private static string $permissionType = 'doi+';

    /**
     * Happy path
     */
    public function test_valid_request_returns_created_response_code(): void
    {
        $payload = [
            'email'     => fake()->safeEmail(),
            'first_name' => fake()->firstName,
            'last_name'  => fake()->lastName,

        ];

        $this->json('post', self::$validUri, $payload)
            ->assertStatus(Response::HTTP_CREATED);
    }

    public function test_result_is_returned_in_valid_format(): void
    {
        $payload = [
            'email'     => fake()->safeEmail(),
            'first_name' => fake()->firstName,
            'last_name'  => fake()->lastName,

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
            'email'     => fake()->safeEmail(),
            'first_name' => fake()->firstName,
            'last_name'  => fake()->lastName,

        ];

        $response = $this->post(self::$validUri, $payload)->json();

        $contact = $this->get('/api/get-contact/' . $payload['email'])->json();
        $expectedMessage = ['message' => 'Contact was created or updated successfully with id ' . $contact['id'][0]];

        $this->assertEquals($expectedMessage, $response);
    }

    public function test_contact_successfully_created_without_name(): void
    {
        $payload = [
            'email' => fake()->safeEmail(),
        ];

        $response = $this->post(self::$validUri, $payload)->json();

        $contact = $this->get('/api/get-contact/' . $payload['email'])->json();
        $expectedMessage = ['message' => 'Contact was created or updated successfully with id ' . $contact['id'][0]];

        $this->assertEquals($expectedMessage, $response);
    }

    public function test_contact_created_with_DOI_plus_permission(): void
    {
        $payload = [
            'email'     => fake()->safeEmail(),
            'first_name' => fake()->firstName,
            'last_name'  => fake()->lastName,

        ];

        $this->post(self::$validUri, $payload);
        $contact = $this->get('/api/get-contact/' . $payload['email'])->json();

        $this->assertEquals(self::$permissionCode, $contact['permission']['code']);
        $this->assertEquals(self::$permissionType, $contact['permission']['type']);
    }

    public function test_if_contact_exists_its_name_updated(): void
    {
        $payloadToCreate = [
            'email'     => fake()->safeEmail(),
            'first_name' => fake()->firstName,
            'last_name'  => fake()->lastName,

        ];

        $payloadToUpdate = [
            'email'     => $payloadToCreate['email'],
            'first_name' => fake()->firstName,
            'last_name'  => fake()->lastName,

        ];

        $this->post(self::$validUri, $payloadToCreate);
        $this->post(self::$validUri, $payloadToUpdate);

        $updatedContact = $this->get('/api/get-contact/' . $payloadToCreate['email'])->json();

        $this->assertEquals($payloadToUpdate['first_name'], $updatedContact['standard_fields']['FIRSTNAME']);
        $this->assertEquals($payloadToUpdate['last_name'], $updatedContact['standard_fields']['LASTNAME']);
    }


    /**
     * Negative tests
     */
    public function test_request_invalid_uri_returns_error_message(): void
    {
        $payload = [
            'email'     => fake()->safeEmail(),
            'first_name' => fake()->firstName,
            'last_name'  => fake()->lastName,

        ];

        $this->json('post', self::$invalidUri, $payload)
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'message' => 'Incorrect route. Try something else',
            ]);
    }

    public function test_request_with_empty_payload_returns_error_message(): void
    {
        $payload = [];

        $this->json('post', self::$validUri, $payload)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'message' => 'The email field is required.',
                'errors' => ['email' => ['The email field is required.']]
            ]);
    }

    public function test_request_without_email_returns_error_message(): void
    {
        $payload = [
            'first_name' => fake()->firstName,
            'last_name'  => fake()->lastName,
        ];

        $this->json('post', self::$validUri, $payload)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'message' => 'The email field is required.',
                'errors' => ['email' => ['The email field is required.']]
            ]);
    }

    public function test_request_with_invalid_email_format_returns_error_message(): void
    {
        $payload = [
            'email'     => self::$invalidEmail,
            'first_name' => fake()->firstName,
            'last_name'  => fake()->lastName,
        ];

        $this->json('post', self::$validUri, $payload)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'message' => 'The email field must be a valid email address.',
                'errors' => ['email' => ['The email field must be a valid email address.']]
            ]);
    }

    public function test_request_with_invalid_first_name_format_returns_error_message(): void
    {
        $payload = [
            'email'     => fake()->safeEmail(),
            'first_name' => self::$invalidName,
            'last_name'  => fake()->lastName,
        ];

        $this->json('post', self::$validUri, $payload)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'message' => 'The first name field must be a string.',
                'errors' => ['first_name' => ['0' => 'The first name field must be a string.']]
            ]);
    }

    public function test_request_with_invalid_last_name_format_returns_error_message(): void
    {
        $payload = [
            'email'     => fake()->safeEmail(),
            'first_name' => fake()->firstName,
            'last_name'  => self::$invalidName,
        ];

        $this->json('post', self::$validUri, $payload)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'message' => 'The last name field must be a string.',
                'errors' => ['last_name' => ['0' => 'The last name field must be a string.']]
            ]);
    }

    public function test_if_contact_exists_no_new_contact_created(): void
    {
        $payloadToCreate = [
            'email'      => fake()->safeEmail(),
            'first_name' => fake()->firstName,
            'last_name'  => fake()->lastName,

        ];

        $payloadToUpdate = [
            'email'      => $payloadToCreate['email'],
            'first_name' => fake()->firstName,
            'last_name'  => fake()->lastName,

        ];

        $createdContact = $this->post(self::$validUri, $payloadToCreate)->json();
        $updatedContact = $this->post(self::$validUri, $payloadToUpdate)->json();

        $this->assertEquals($createdContact, $updatedContact);
    }

}
