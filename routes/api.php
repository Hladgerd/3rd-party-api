<?php

use App\Http\Controllers\APIControllers\ContactController;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/**  CONTACT */
Route::get('/get-contact/{email}', [ContactController::class, 'show']);
Route::post('/create-contact', [ContactController::class, 'store']);


/**  NON-EXISTENT ROUTES */
Route::fallback(function(){
    return response()->json([
        'message' => 'Incorrect route. Try something else'], Response::HTTP_NOT_FOUND);
});
