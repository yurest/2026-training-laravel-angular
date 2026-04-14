<?php

use App\User\Infrastructure\Entrypoint\Http\PostController;
use Illuminate\Support\Facades\Route;
use App\User\Infrastructure\Entrypoint\Http\LoginPostController;

use App\Tax\Infrastructure\Entrypoint\Http\DeleteController as TaxDeleteController;
use App\Tax\Infrastructure\Entrypoint\Http\GetController as TaxGetController;
use App\Tax\Infrastructure\Entrypoint\Http\IndexController as TaxIndexController;
use App\Tax\Infrastructure\Entrypoint\Http\PostController as TaxPostController;
use App\Tax\Infrastructure\Entrypoint\Http\PutController as TaxPutController;

use App\Family\Infrastructure\Entrypoint\Http\DeleteController as FamilyDeleteController;
use App\Family\Infrastructure\Entrypoint\Http\GetController as FamilyGetController;
use App\Family\Infrastructure\Entrypoint\Http\IndexController as FamilyIndexController;
use App\Family\Infrastructure\Entrypoint\Http\PostController as FamilyPostController;
use App\Family\Infrastructure\Entrypoint\Http\PutController as FamilyPutController;

use App\Product\Infrastructure\Entrypoint\Http\PostController as ProductPostController;
use App\Product\Infrastructure\Entrypoint\Http\IndexController as ProductIndexController;
use App\Product\Infrastructure\Entrypoint\Http\GetController as ProductGetController;
use App\Product\Infrastructure\Entrypoint\Http\PutController as ProductPutController;
use App\Product\Infrastructure\Entrypoint\Http\DeleteController as ProductDeleteController;
use App\Restaurant\Infrastructure\Entrypoint\Http\PostController as RestaurantPostController;
use App\Restaurant\Infrastructure\Entrypoint\Http\IndexController as RestaurantIndexController;
use App\Restaurant\Infrastructure\Entrypoint\Http\GetController as RestaurantGetController;
use App\Restaurant\Infrastructure\Entrypoint\Http\PutController as RestaurantPutController;
use App\Restaurant\Infrastructure\Entrypoint\Http\DeleteController as RestaurantDeleteController;
use App\Restaurant\Infrastructure\Entrypoint\Http\ChangePasswordPatchController as RestaurantChangePasswordPatchController;


Route::post('/users', PostController::class);
Route::post('/login', LoginPostController::class);

Route::post('/taxes', TaxPostController::class);
Route::get('/taxes', TaxIndexController::class);
Route::get('/taxes/{id}', TaxGetController::class);
Route::put('/taxes/{id}', TaxPutController::class);
Route::delete('/taxes/{id}', TaxDeleteController::class);

Route::post('/families', FamilyPostController::class);
Route::get('/families', FamilyIndexController::class);
Route::get('/families/{id}', FamilyGetController::class);
Route::put('/families/{id}', FamilyPutController::class);
Route::delete('/families/{id}', FamilyDeleteController::class);

Route::post('/products', ProductPostController::class);
Route::get('/products', ProductIndexController::class);
Route::get('/products/{id}', ProductGetController::class);
Route::put('/products/{id}', ProductPutController::class);
Route::delete('/products/{id}', ProductDeleteController::class);

Route::post('/restaurants', RestaurantPostController::class);
Route::get('/restaurants', RestaurantIndexController::class);
Route::get('/restaurants/{id}', RestaurantGetController::class);
Route::put('/restaurants/{id}', RestaurantPutController::class);
Route::patch('/restaurants/{id}/password', RestaurantChangePasswordPatchController::class);
Route::delete('/restaurants/{id}', RestaurantDeleteController::class);
