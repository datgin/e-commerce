<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Api\Attribute\AttributeController;
use App\Http\Controllers\Api\Attribute\AttributeValueController;
use App\Http\Controllers\Api\Brand\BrandController;
use App\Http\Controllers\Api\BulkActionController;
use App\Http\Controllers\Api\Catalogue\CatalogueController;
use App\Http\Controllers\Api\ManageImageController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('register', [AccountController::class, 'register']);

Route::post('login', [AccountController::class, 'login']);


Route::get('/users', [UserController::class, 'index']);

Route::middleware(['auth:api'])->group(function () {
    Route::get('me', [AccountController::class, 'me']);
    route::post('refresh-token', [AccountController::class, 'refreshToken']);
    route::get('logout', [AccountController::class, 'logout']);

    // routes/api.php

    Route::post('handle-bulkAction', [BulkActionController::class, 'handleBulkAction']);

    Route::apiResources([
        'catalogues' => CatalogueController::class,
        'brands' => BrandController::class,
        'attributes' => AttributeController::class,
        'attribute-values' => AttributeValueController::class,
    ]);

    Route::get('uploads', [ManageImageController::class, 'getImages']);
    Route::post('uploads', [ManageImageController::class, 'postImages']);
    Route::delete('uploads/{id}', [ManageImageController::class, 'destroyImages']);
});

Route::get('verify/{id}', [AccountController::class, 'verify'])->name('verify');
