<?php

//SALE LINES
use App\SaleLine\Infrastructure\Entrypoint\Http\IndexBySaleController as SaleLineIndexBySaleController;


// SALES
use App\Sale\Infrastructure\Entrypoint\Http\GetController as SaleGetController;
use App\Sale\Infrastructure\Entrypoint\Http\IndexController as SaleIndexController;
use App\Sale\Infrastructure\Entrypoint\Http\PostController as SalePostController;
use App\Sale\Infrastructure\Entrypoint\Http\CheckoutOrderPostController as CheckoutOrderPostController;

//ORDER LINES
use App\OrderLine\Infrastructure\Entrypoint\Http\PostController as OrderLinePostController;
use App\OrderLine\Infrastructure\Entrypoint\Http\PutController as OrderLinePutController;
use App\OrderLine\Infrastructure\Entrypoint\Http\GetController as OrderLineGetController;
use App\OrderLine\Infrastructure\Entrypoint\Http\IndexController as OrderLineIndexController;
use App\OrderLine\Infrastructure\Entrypoint\Http\DeleteController as OrderLineDeleteController;

//ORDERS
use App\Order\Infrastructure\Entrypoint\Http\PostController as OrderPostController;
use App\Order\Infrastructure\Entrypoint\Http\PutController as OrderPutController;
use App\Order\Infrastructure\Entrypoint\Http\GetController as OrderGetController;
use App\Order\Infrastructure\Entrypoint\Http\IndexController as OrderIndexController;
use App\Order\Infrastructure\Entrypoint\Http\DeleteController as OrderDeleteController;
use App\Order\Infrastructure\Entrypoint\Http\OpenIndexController as OrderOpenIndexController;

// FAMILY
use App\Family\Infrastructure\Entrypoint\Http\GetController as FamilyGetController;
use App\Family\Infrastructure\Entrypoint\Http\IndexController as FamilyIndexController;
use App\Family\Infrastructure\Entrypoint\Http\PostController as FamilyPostController;
use App\Family\Infrastructure\Entrypoint\Http\PutController as FamilyPutController;
use App\Family\Infrastructure\Entrypoint\Http\DeleteController as FamilyDeleteController;

// PRODUCT
use App\Product\Infrastructure\Entrypoint\Http\GetController as ProductGetController;
use App\Product\Infrastructure\Entrypoint\Http\IndexController as ProductIndexController;
use App\Product\Infrastructure\Entrypoint\Http\PostController as ProductPostController;
use App\Product\Infrastructure\Entrypoint\Http\PutController as ProductPutController;
use App\Product\Infrastructure\Entrypoint\Http\DeleteController as ProductDeleteController;

// RESTAURANT
use App\Restaurant\Infrastructure\Entrypoint\Http\DeleteController as RestaurantDeleteController;
use App\Restaurant\Infrastructure\Entrypoint\Http\GetController as RestaurantGetController;
use App\Restaurant\Infrastructure\Entrypoint\Http\IndexController as RestaurantIndexController;
use App\Restaurant\Infrastructure\Entrypoint\Http\PostController as RestaurantPostController;
use App\Restaurant\Infrastructure\Entrypoint\Http\PutController as RestaurantPutController;
use App\Restaurant\Infrastructure\Entrypoint\Http\ChangePasswordPatchController as RestaurantChangePasswordPatchController;

// TABLE
use App\Table\Infrastructure\Entrypoint\Http\DeleteController as TableDeleteController;
use App\Table\Infrastructure\Entrypoint\Http\GetController as TableGetController;
use App\Table\Infrastructure\Entrypoint\Http\IndexController as TableIndexController;
use App\Table\Infrastructure\Entrypoint\Http\PostController as TablePostController;
use App\Table\Infrastructure\Entrypoint\Http\PutController as TablePutController;


// TAX
use App\Tax\Infrastructure\Entrypoint\Http\GetController as TaxGetController;
use App\Tax\Infrastructure\Entrypoint\Http\IndexController as TaxIndexController;
use App\Tax\Infrastructure\Entrypoint\Http\PostController as TaxPostController;
use App\Tax\Infrastructure\Entrypoint\Http\PutController as TaxPutController;
use App\Tax\Infrastructure\Entrypoint\Http\DeleteController as TaxDeleteController;

// USER
use App\User\Infrastructure\Entrypoint\Http\GetController as UserGetController;
use App\User\Infrastructure\Entrypoint\Http\IndexController as UserIndexController;
use App\User\Infrastructure\Entrypoint\Http\LoginPostController as UserLoginPostController;
use App\User\Infrastructure\Entrypoint\Http\LogoutPostController as UserLogoutPostController;
use App\User\Infrastructure\Entrypoint\Http\MeGetController as UserMeGetController;
use App\User\Infrastructure\Entrypoint\Http\PostController as UserPostController;
use App\User\Infrastructure\Entrypoint\Http\PutController as UserPutController;
use App\User\Infrastructure\Entrypoint\Http\DeleteController as UserDeleteController;

// ZONE
use App\Zone\Infrastructure\Entrypoint\Http\DeleteController as ZoneDeleteController;
use App\Zone\Infrastructure\Entrypoint\Http\GetController as ZoneGetController;
use App\Zone\Infrastructure\Entrypoint\Http\IndexController as ZoneIndexController;
use App\Zone\Infrastructure\Entrypoint\Http\PostController as ZonePostController;
use App\Zone\Infrastructure\Entrypoint\Http\PutController as ZonePutController;

use Illuminate\Support\Facades\Route;

/*
 PUBLIC ROUTES
*/

Route::post('/login', UserLoginPostController::class);

/*
 PROTECTED ROUTES
*/

Route::middleware('auth:sanctum')->group(function () {
    // AUTH
    Route::post('/logout', UserLogoutPostController::class);

    Route::get('/me', UserMeGetController::class);

    // USERS
    Route::post('/users', UserPostController::class);
    Route::get('/users', UserIndexController::class);
    Route::get('/users/{id}', UserGetController::class);
    Route::put('/users/{id}', UserPutController::class);
    Route::delete('/users/{id}', UserDeleteController::class);

    // TAXES
    Route::post('/taxes', TaxPostController::class);
    Route::get('/taxes', TaxIndexController::class);
    Route::get('/taxes/{id}', TaxGetController::class);
    Route::put('/taxes/{id}', TaxPutController::class);
    Route::delete('/taxes/{id}', TaxDeleteController::class);

    // FAMILIES
    Route::post('/families', FamilyPostController::class);
    Route::get('/families', FamilyIndexController::class);
    Route::get('/families/{id}', FamilyGetController::class);
    Route::put('/families/{id}', FamilyPutController::class);
    Route::delete('/families/{id}', FamilyDeleteController::class);

    // PRODUCTS
    Route::post('/products', ProductPostController::class);
    Route::get('/products', ProductIndexController::class);
    Route::get('/products/{id}', ProductGetController::class);
    Route::put('/products/{id}', ProductPutController::class);
    Route::delete('/products/{id}', ProductDeleteController::class);

    // RESTAURANTS
    Route::post('/restaurants', RestaurantPostController::class);
    Route::get('/restaurants', RestaurantIndexController::class);
    Route::get('/restaurants/{id}', RestaurantGetController::class);
    Route::put('/restaurants/{id}', RestaurantPutController::class);
    Route::patch('/restaurants/{id}/password', RestaurantChangePasswordPatchController::class);
    Route::delete('/restaurants/{id}', RestaurantDeleteController::class);

    // ZONES
    Route::post('/zones', ZonePostController::class);
    Route::get('/zones', ZoneIndexController::class);
    Route::get('/zones/{id}', ZoneGetController::class);
    Route::put('/zones/{id}', ZonePutController::class);
    Route::delete('/zones/{id}', ZoneDeleteController::class);

    // TABLES
    Route::post('/tables', TablePostController::class);
    Route::get('/tables', TableIndexController::class);
    Route::get('/tables/{id}', TableGetController::class);
    Route::put('/tables/{id}', TablePutController::class);
    Route::delete('/tables/{id}', TableDeleteController::class);

    // ORDERS
    Route::post('/orders', OrderPostController::class);
    Route::put('/orders/{id}', OrderPutController::class);
    Route::get('/orders', OrderIndexController::class);
    Route::get('/orders/open', OrderOpenIndexController::class);
    Route::get('/orders/{id}', OrderGetController::class);
    Route::delete('/orders/{id}', OrderDeleteController::class);
 

    // ORDER LINES
    Route::post('/order-lines', OrderLinePostController::class);
    Route::put('/order-lines/{id}', OrderLinePutController::class);
    Route::get('/order-lines', OrderLineIndexController::class);
    Route::get('/order-lines/{id}', OrderLineGetController::class);
    Route::delete('/order-lines/{id}', OrderLineDeleteController::class);

    // SALES
    Route::post('/sales', SalePostController::class);
    Route::get('/sales', SaleIndexController::class);
    Route::get('/sales/{id}', SaleGetController::class);

    // SALE LINES
    Route::get('/sales/{saleId}/lines', SaleLineIndexBySaleController::class);
    Route::post('/orders/{orderId}/checkout', CheckoutOrderPostController::class);
});
