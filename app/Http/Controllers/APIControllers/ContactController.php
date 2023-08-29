<?php

namespace App\Http\Controllers\APIControllers;

use App\Http\Controllers\Controller;
use de\xqueue\maileon\api\client\contacts\Contact;
use de\xqueue\maileon\api\client\contacts\ContactsService;
use de\xqueue\maileon\api\client\contacts\Permission;
use de\xqueue\maileon\api\client\contacts\StandardContactField;
use de\xqueue\maileon\api\client\contacts\SynchronizationMode;

class ContactController extends Controller
{
    /**
     * Create a contact in Maileon
     */
    public function store(string $email, $fname="", $lname="")
    {
        $contactsService = new ContactsService([
            'API_KEY' => config('services.maileon.key'),
            'DEBUG'=> true // Remove on production config!
        ]);

        $standard_fields = array(
            StandardContactField::$FIRSTNAME => $fname,
            StandardContactField::$LASTNAME => $lname,
        );

        $newContact = new Contact();
        $newContact->email = $email;
        $newContact->standard_fields = $standard_fields;
        $newContact->permission = Permission::$DOI_PLUS;

        $sync_mode = SynchronizationMode::$UPDATE;

        $response = $contactsService->createContact($newContact, $sync_mode);

        return response()
            ->json($response)
            ->setStatusCode(201);
    }

    /**
     * Request basic contact data identified by their email address
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

}
