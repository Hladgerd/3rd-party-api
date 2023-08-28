<?php

namespace App\Http\Controllers;

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
    public function store($email)
    {
        $contactsService = new ContactsService([
            'API_KEY' => config('services.maileon.key'),
            'DEBUG'=> true // Remove on production config!
        ]);

        $standard_fields = array(
            StandardContactField::$FIRSTNAME => 'Elek',
            StandardContactField::$LASTNAME => 'Teszt',
        );

        $newContact = new Contact();
        $newContact->email = $email;
        $newContact->standard_fields = $standard_fields;
        $newContact->permission = Permission::$DOI_PLUS;

        $sync_mode = SynchronizationMode::$UPDATE;

        $response = $contactsService->createContact($newContact, $sync_mode);

        return response()->json($response);
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

}
