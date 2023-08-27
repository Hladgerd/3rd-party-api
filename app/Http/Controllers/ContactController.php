<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Models\ContactModel;
use de\xqueue\maileon\api\client\contacts\Contact;
use de\xqueue\maileon\api\client\contacts\ContactsService;
use de\xqueue\maileon\api\client\contacts\Permission;
use de\xqueue\maileon\api\client\contacts\StandardContactField;
use de\xqueue\maileon\api\client\contacts\SynchronizationMode;

class ContactController extends Controller
{

    /**
     * The contact object stores all information you requested.
     *
     * Identifiers (Maileon ID, Maileon external id and email address), marketing permission
     * level, creation date and last update date are always included if they are set in Maileon.
     *
     * ID: $contact->id
     * Email: $contact->email
     * Permission: $contact->permission->getType()
     */
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store()
    {
        $contactsService = new ContactsService([
            'API_KEY' => config('services.maileon.key'),
            'DEBUG'=> true // Remove on production config!
        ]);

// Create the contact object
        $newContact = new Contact();
        $newContact->email = "max.mustermann@xqueue.com";
        $newContact->permission = Permission::$NONE; // The initial permission of the newly created contact. This can be converted to DOI after DOI process or can be set to something else, e.g. SOI, here already

// If required, fill custom fields
        $newContact->standard_fields[StandardContactField::$FIRSTNAME] = "Max";
        $newContact->standard_fields[StandardContactField::$LASTNAME] = "Mustermann";

        $response = $contactsService->createContact($newContact, SynchronizationMode::$UPDATE);

        return $response;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $email)
    {
        $contactsService = new ContactsService([
            'API_KEY' => config('services.maileon.key'),
        ]);

        $getContact = $contactsService->getContactByEmail(
            email: $email,
        );

        if (!$getContact->isSuccess()) {
            die($getContact->getResultXML()->message);
        }

        $contact = $getContact->getResult();

        return response()->json($contact);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContactRequest $request, ContactModel $contact)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContactModel $contact)
    {
        //
    }
}
