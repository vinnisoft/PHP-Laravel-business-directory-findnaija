<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SubAdminController;
use App\Http\Controllers\Admin\InterestController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BusinessController;
use App\Http\Controllers\Admin\FavouriteBusinessController;
use App\Http\Controllers\Admin\BusinessReportController;
use App\Http\Controllers\Admin\BusinessLanguageController;
use App\Http\Controllers\Admin\EventCategoryController;
use App\Http\Controllers\Admin\CustomNotificationController;
use App\Http\Controllers\Admin\CategoryGroupController;
use App\Http\Controllers\Admin\PlanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



Auth::routes();

Route::middleware(['auth', 'permission'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('dashboard');
    Route::resource('users', UserController::class);
    Route::resource('admins', SubAdminController::class);
    Route::resource('interest', InterestController::class);
    Route::resource('category', CategoryController::class);
    Route::resource('category-group', CategoryGroupController::class);
    Route::resource('business', BusinessController::class);
    Route::resource('favorite-business', FavouriteBusinessController::class);
    Route::resource('business-report', BusinessReportController::class);
    Route::resource('business-language', BusinessLanguageController::class);
    Route::resource('event-category', EventCategoryController::class);
    Route::resource('plan', PlanController::class);
    Route::controller(BusinessController::class)->group(function () {
        Route::get('new-business', 'newBusiness')->name('new.business');
        Route::get('recomended-business', 'recomendedBusiness')->name('recomended.business');
        Route::get('get-sub-category', 'getSubCategory')->name('getSubCategory');
        Route::post('approve-business', 'approve')->name('business.approve');
        Route::post('delete-business-image', 'deleteImage')->name('businessImage.delete');
        Route::post('business-cover-image', 'coverImage')->name('coverImage');
    });
    Route::controller(CategoryController::class)->group(function () {
        Route::post('set-category', 'setCategory')->name('setCategory');
        Route::delete('delete-sub-category/{id}', 'deleteSubCategory')->name('deleteSubCategory');
        Route::delete('delete-option/{id}', 'deleteOption')->name('deleteOption');
    });
    Route::controller(UserController::class)->group(function () {
        Route::get('support', 'support')->name('support.index');
    });
    Route::controller(CustomNotificationController::class)->group(function () {
        Route::get('custom-notification', 'index')->name('custom-notification.index');
        Route::get('custom-notification/create', 'create')->name('custom-notification.create');
        Route::post('custom-notification', 'send')->name('custom-notification.store');
        Route::get('custom-notification/resend/{id}', 'recend')->name('custom-notification.recend');
        Route::get('custom-notification/edit/{id}', 'edit')->name('custom-notification.edit');
        Route::put('custom-notification/update/{id}', 'update')->name('custom-notification.update');
        Route::delete('custom-notification/{id}/destroy', 'destroy')->name('custom-notification.destroy');
    });
});

Route::get('seeder',function(){ Artisan::call("db:seed"); });
Route::get('migrate',function(){ Artisan::call("migrate"); });