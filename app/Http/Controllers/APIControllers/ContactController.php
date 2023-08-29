<?php

namespace App\Http\Controllers\APIControllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactRequest;
use de\xqueue\maileon\api\client\contacts\Contact;
use de\xqueue\maileon\api\client\contacts\ContactsService;
use de\xqueue\maileon\api\client\contacts\Permission;
use de\xqueue\maileon\api\client\contacts\StandardContactField;
use de\xqueue\maileon\api\client\contacts\SynchronizationMode;
use Symfony\Component\HttpFoundation\Response;

class ContactController extends Controller
{
    private static ContactsService $contactsService;

    public function __construct()
    {
        if(!isset(self::$contactsService)) {
            self::$contactsService = new ContactsService([
                'API_KEY' => config('services.maileon.key'),
                'DEBUG'=> true // Remove on production config!
            ]);
        }
        return self::$contactsService;
    }

    /**
     * Create a contact in Maileon
     */
    public function store(StoreContactRequest $request)
    {
        $standardFields = array();
        $standardFields[StandardContactField::$FIRSTNAME]=$request->validated('first_name');
        $standardFields[StandardContactField::$LASTNAME]=$request->validated('last_name');

        $newContact = new Contact();
        $newContact->email = $request->validated('email');
        $newContact->standard_fields = $standardFields;
        $newContact->permission = Permission::$DOI_PLUS;

        $syncMode = SynchronizationMode::$UPDATE;

        $response = self::$contactsService->createContact($newContact, $syncMode);

        return response()
            ->json($response)
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Request basic contact data identified by their email address
     */
    public function show(string $email)
    {
        $getContact = self::$contactsService->getContactByEmail(
            email: $email,
        );

        if (!$getContact->isSuccess()) {
            die($getContact->getResultXML()->message);
        }

        $contact = $getContact->getResult();

        return response()->json($contact);
    }

}
