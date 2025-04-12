<?php

use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiHuntingController;
use App\Http\Controllers\Api\ApiSpeciesController;
use App\Http\Controllers\Api\ApiWildlifeConflictController;
use App\Http\Middleware\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Organisation\QuotaAllocationController;
use App\Http\Controllers\Api\ConflictOutcomeController;
use App\Models\City;

//organisation types
Route::get('/admin/organisation-types', [ApiController::class, 'fetchTemplate'])->name('admin.organisation-types.index');
//organisations
Route::get('/admin/organisations', [ApiController::class, 'fetchOrganisationInstances'])->name('admin.organisations.index');
//organisation
Route::get('/admin/organisations/{organisation}/edit', [ApiController::class, 'fetchOrganisation'])->name('admin.organisations.edit');

// Add this new route for getting children
Route::get('/admin/organisations/get-children/{organisation}', [ApiController::class, 'getOrganisationChildren'])
    ->name('admin.organisations.get-children');

Route::get('/admin/organisations/by-type/{typeId}', [ApiController::class, 'getOrganisationsByType'])
    ->name('admin.organisations.by-type');

// Quota allocation routes

Route::get('/organisations/{organisation}/quota-allocations', [QuotaAllocationController::class, 'getQuotaAllocation']);

// Conflict Outcome Dynamic Fields
Route::get('/conflict-outcomes/{conflictOutcome}/dynamic-fields', [ConflictOutcomeController::class, 'getDynamicFields']);

// Get cities by province
Route::get('/provinces/{province}/cities', function ($province) {
    return City::where('province_id', $province)->get();
});

Route::group(['middleware' => [JsonResponse::class]], function () {

    Route::prefix('v1')->group(function () {

        Route::post('/login', [ApiAuthController::class, 'login'])->name('login.api');
        Route::post('/register', [ApiAuthController::class, 'register'])->name('register.api');

        Route::middleware('auth:api')->group(function () {
            Route::get('/user', function (Request $request) {
                return $request->user();
            });
            Route::get('/my-account', [ApiAuthController::class, 'getMyProfile'])->name('my.profile')->middleware('throttle:3,10');
            Route::post('/update-my-pin', [ApiAuthController::class, 'updateMyPassword'])->name('update.my.password');
            Route::put('/update-my-profile', [ApiAuthController::class, 'updateMyProfile'])->name('update.user.profile');
            Route::post('/delete-account', [ApiAuthController::class, 'requestAccountDeletion'])->name('delete.account');

            Route::prefix('hunting')->group(function () {
                Route::get('/concessions', [ApiHuntingController::class, 'huntingConcessions']);
                Route::get('/safari-operators/{organisation}', [ApiHuntingController::class, 'safariOperators']);
            });

            Route::get('/species', [ApiSpeciesController::class, 'index']);

            Route::prefix('wildlife-conflict')->group(function () {
                Route::get('/', [ApiWildlifeConflictController::class, 'index']);
                Route::get('/problem-animal-controls', [ApiWildlifeConflictController::class, 'problemAnimalControls']);
                Route::get('/control-measures', [ApiWildlifeConflictController::class, 'controlMeasures']);
                Route::get('/conflict-types', [ApiWildlifeConflictController::class, 'conflictTypes']);
            });

        });
    });

});
