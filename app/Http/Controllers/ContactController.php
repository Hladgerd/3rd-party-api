<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Models\Contact;

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
    public function store(StoreContactRequest $request)
    {
       //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $email)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContactRequest $request, Contact $contact)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        //
    }
}
