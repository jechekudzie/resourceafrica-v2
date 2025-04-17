<?php

use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiConflictTypeController;
use App\Http\Controllers\Api\ApiHuntingController;
use App\Http\Controllers\Api\ApiSpeciesController;
use App\Http\Controllers\Api\ApiWildlifeConflictController;
use App\Http\Controllers\Api\ApiProblemAnimalControlController;
use App\Http\Controllers\Api\ApiPoachingIncidentController;
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
                Route::get('/concessions/{organisation}', [ApiHuntingController::class, 'huntingConcessions']);

                Route::get('/activities/{organisation}', [ApiHuntingController::class, 'huntingActivities']);
                Route::get('/activities/{organisation}/{huntingActivity}', [ApiHuntingController::class, 'showHuntingActivity']);
                Route::post('/activities/{organisation}', [ApiHuntingController::class, 'storeHuntingActivity']);
                Route::put('/activities/{organisation}/{huntingActivity}', [ApiHuntingController::class, 'updateHuntingActivity']);
                Route::delete('/activities/{organisation}/{huntingActivity}', [ApiHuntingController::class, 'destroyHuntingActivity']);

                Route::get('/safari-operators/{organisation}', [ApiHuntingController::class, 'safariOperators']);

                Route::prefix('quotas')->group(function () {
                    Route::get('/{organisation}', [ApiHuntingController::class, 'huntingQuotas']);
                    Route::post('/{organisation}', [ApiHuntingController::class, 'storeQuotaAllocation']);
                    Route::get('/{organisation}/{quotaAllocation}', [ApiHuntingController::class, 'showQuotaAllocation']);
                    Route::put('/{organisation}/{quotaAllocation}', [ApiHuntingController::class, 'updateQuotaAllocation']);
                    Route::delete('/{organisation}/{quotaAllocation}', [ApiHuntingController::class, 'destroyQuotaAllocation']);
                    Route::post('/{organisation}/get-allocation', [ApiHuntingController::class, 'getQuotaAllocationApi']);
                });
            });

            Route::get('/species', [ApiSpeciesController::class, 'index']);
            Route::get('/conflict-types', [ApiConflictTypeController::class, 'index']);

            Route::prefix('wildlife-conflict')->group(function () {
                Route::get('/problem-animal-controls', [ApiWildlifeConflictController::class, 'problemAnimalControls']);
                Route::get('/control-measures/{conflictTypeId}', [ApiWildlifeConflictController::class, 'controlMeasures']);
                Route::get('/conflict-types', [ApiWildlifeConflictController::class, 'conflictTypes']);

                Route::get('/{organisation}/incidents', [ApiWildlifeConflictController::class, 'index']);
                Route::post('/{organisation}/incidents', [ApiWildlifeConflictController::class, 'store']);
                Route::get('/{organisation}/incidents/{wildlifeConflictIncident}', [ApiWildlifeConflictController::class, 'show']);
                Route::put('/{organisation}/incidents/{wildlifeConflictIncident}', [ApiWildlifeConflictController::class, 'update']);
                Route::delete('/{organisation}/incidents/{wildlifeConflictIncident}', [ApiWildlifeConflictController::class, 'destroy']);

                Route::get('/{organisation}/problem-animal-controls', [ApiProblemAnimalControlController::class, 'index']);
                Route::post('/{organisation}/problem-animal-controls', [ApiProblemAnimalControlController::class, 'store']);
                Route::get('/{organisation}/problem-animal-controls/{problemAnimalControl}', [ApiProblemAnimalControlController::class, 'show']);
                Route::put('/{organisation}/problem-animal-controls/{problemAnimalControl}', [ApiProblemAnimalControlController::class, 'update']);
                Route::delete('/{organisation}/problem-animal-controls/{problemAnimalControl}', [ApiProblemAnimalControlController::class, 'destroy']);
            });

            Route::prefix('poaching')->group(function () {
                Route::get('/methods', [ApiPoachingIncidentController::class, 'poachingMethods']);
                Route::get('/{organisation}/poaching-incidents', [ApiPoachingIncidentController::class, 'index']);
                Route::post('/{organisation}/poaching-incidents', [ApiPoachingIncidentController::class, 'store']);
                Route::get('/{organisation}/poaching-incidents/{poachingIncident}', [ApiPoachingIncidentController::class, 'show']);
                Route::put('/{organisation}/poaching-incidents/{poachingIncident}', [ApiPoachingIncidentController::class, 'update']);
                Route::delete('/{organisation}/poaching-incidents/{poachingIncident}', [ApiPoachingIncidentController::class, 'destroy']);
                Route::post('{organisation}/poaching-incidents/{poachingIncident}/poachers', [ApiPoachingIncidentController::class, 'addPoacher']
                )->name('api.poaching-incidents.poachers.store');

            });

        });
    });

});
