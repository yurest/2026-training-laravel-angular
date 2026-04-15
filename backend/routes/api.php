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

use App\Zone\Infrastructure\Entrypoint\Http\PostController as ZonePostController;
use App\Zone\Infrastructure\Entrypoint\Http\IndexController as ZoneIndexController;
use App\Zone\Infrastructure\Entrypoint\Http\GetController as ZoneGetController;
use App\Zone\Infrastructure\Entrypoint\Http\PutController as ZonePutController;
use App\Zone\Infrastructure\Entrypoint\Http\DeleteController as ZoneDeleteController;

use App\Table\Infrastructure\Entrypoint\Http\PostController as TablePostController;
use App\Table\Infrastructure\Entrypoint\Http\IndexController as TableIndexController;
use App\Table\Infrastructure\Entrypoint\Http\GetController as TableGetController;
use App\Table\Infrastructure\Entrypoint\Http\PutController as TablePutController;
use App\Table\Infrastructure\Entrypoint\Http\DeleteController as TableDeleteController;

use App\User\Infrastructure\Entrypoint\Http\PostController as UserPostController;
use App\User\Infrastructure\Entrypoint\Http\LoginPostController as UserLoginPostController;
use App\User\Infrastructure\Entrypoint\Http\IndexController as UserIndexController;
use App\User\Infrastructure\Entrypoint\Http\GetController as UserGetController;
use App\User\Infrastructure\Entrypoint\Http\PutController as UserPutController;
use App\User\Infrastructure\Entrypoint\Http\DeleteController as UserDeleteController;

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

Route::post('/zones', ZonePostController::class);
Route::get('/zones', ZoneIndexController::class);
Route::get('/zones/{id}', ZoneGetController::class);
Route::put('/zones/{id}', ZonePutController::class);
Route::delete('/zones/{id}', ZoneDeleteController::class);

Route::post('/tables', TablePostController::class);
Route::get('/tables', TableIndexController::class);
Route::get('/tables/{id}', TableGetController::class);
Route::put('/tables/{id}', TablePutController::class);
Route::delete('/tables/{id}', TableDeleteController::class);

Route::post('/users', UserPostController::class);
Route::post('/login', UserLoginPostController::class);
Route::get('/users', UserIndexController::class);
Route::get('/users/{id}', UserGetController::class);
Route::put('/users/{id}', UserPutController::class);
Route::delete('/users/{id}', UserDeleteController::class);