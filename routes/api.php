<?php

use App\Http\Controllers\Api\DealOfferController;
use App\Http\Controllers\Api\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\BusinessController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\CollectionController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\AdController;
use App\Http\Controllers\Api\FavouriteBusinessController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('upload-file', function (Request $request) {   
    return upload($request);
});

Route::controller(UserController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('signup', 'signUp');
    Route::post('resend-otp', 'resendOtp');
    Route::post('social-login', 'socialLogin');
    Route::post('verify-email', 'verifyEmail');
    Route::post('forgot-password', 'forgotPassword');
    Route::post('change-password', 'ChangePassword');
});
Route::controller(PaymentController::class)->group(function () {
        Route::post('webhook', 'webhook');
    });
Route::middleware(['auth:sanctum'])->group(function () {
    Route::controller(UserController::class)->group(function () {
        Route::post('update-password', 'updatePassword');
        Route::get('interests', 'interests');
        Route::post('user-interests', 'userInterests');
        Route::get('my-businesses', 'myBusinesses');
        Route::get('select-businesses', 'selectBusinesses');
        Route::get('search-businesses', 'searchBusinesses');
        Route::get('checked-in-business', 'checkedInBusiness');
        Route::get('my-notifications', 'myNotifications');
        Route::get('plans', 'plans');
        Route::get('logout', 'logOut');
        Route::post('delete-account', 'destroy');
        Route::post('clear-history', 'clearHistory');
    });
    Route::controller(ProfileController::class)->group(function () {
        Route::get('search-users', 'searchUsers');
        Route::get('profile', 'profile');
        Route::post('profile', 'updateProfile');
        Route::get('stories', 'stories');
        Route::post('story', 'story');
        Route::post('read-story', 'readStory');
        Route::post('delete-story', 'deleteStory');
        Route::post('support', 'support');
        Route::get('close-account', 'closeAccount');
        Route::post('report-user', 'reportUser');
        Route::post('update-fcm-token', 'updateFcmToken');
        Route::post('update-business-distance', 'updateBusinessDistance');
        Route::post('block-unblock', 'blockUnBlock');
        Route::get('blocked-users', 'blockedUsers');
        Route::post('user-subscription', 'userSubscription');
        Route::post('cancel-subscription', 'cancelSubscription');
        Route::post('subscribe-news-letter', 'subscribe');
        Route::get('activity-feed', 'activityFeed');
    });
    Route::controller(BusinessController::class)->group(function () {
        Route::get('business', 'index');
        Route::get('business-detail/{id}', 'show');
        Route::get('buss-near-by-me', 'businessesNearByMe');
        Route::get('new-businesses', 'newBusinesses');
        Route::get('similar-businesses', 'similarBusinesses');
        Route::get('category-businesses/{id}', 'categoryBusinesses'); 
        Route::post('business', 'store');
        Route::post('recommendation-business', 'recommendationBusiness');
        Route::post('business/{id}', 'update');
        Route::delete('business/{id}', 'destroy');
        Route::get('language', 'language');
        Route::post('check-in', 'checkIn');
        Route::post('report', 'report');
        Route::get('available-jobs', 'availableJobs');
        Route::get('business-hirings/{id}', 'businessHiring');
        Route::post('job-contact', 'jobContact');
        Route::get('featured-testimonials', 'featuredTestimonials');
    });    
    Route::controller(ChatController::class)->group(function () {
        Route::get('group-chat/{id}', 'index');
        Route::get('group-members/{id}', 'groupMembers');
        Route::post('group-chat', 'store');
        Route::get('chat', 'chatMessages');
        Route::get('chat-users', 'chatUsers');
        Route::post('chat', 'chat');
        Route::post('chat-request', 'chatRequest');
        Route::get('chat-request-users', 'chatRequestUsers');
        Route::post('active-in-chat', 'activeInChat');
        Route::post('delete-chat', 'deleteChat');
    });

    Route::controller(AddressController::class)->group(function () {
        Route::get('continents', 'continents');
        Route::get('countries/{continent}', 'countries');
    });
    Route::controller(CategoryController::class)->group(function () {      
        Route::get('categories', 'categories');
        Route::get('more-categories', 'moreCategories');
        Route::get('sub-categories', 'subCategories');
        Route::get('graphic-categories', 'graphicCategories');
        Route::get('category-groups', 'groups');
        Route::get('group-category/{id}', 'groupCategory');
    });
    Route::controller(ReviewController::class)->group(function () {
        Route::get('reviews', 'index');
        Route::get('review', 'show');
        Route::post('review', 'store');
        Route::post('update-review', 'update');
        Route::get('reviews-files', 'reviewFiles');
        Route::get('single-reviews-file', 'singleReviewFiles');
        Route::post('like-un-like-review', 'likeUnLike');
        Route::post('review-reply', 'reply');
        Route::post('delete-review', 'destroy');
        Route::post('report-review', 'report');
        Route::get('featured-reviews', 'featuredReviews');
    });
    Route::controller(NotificationController::class)->group(function () {
        Route::get('app-notification/{type}', 'index');
        Route::post('app-notification', 'store');
    });
    Route::controller(CollectionController::class)->group(function () {
        Route::get('collection', 'index');
        Route::get('single-collection', 'show');
        Route::post('collection', 'store');
        Route::post('featured/{id}', 'featured');
        Route::delete('collection/{id}', 'delete');
        Route::post('add-to-collection', 'userCollection');
    });

    Route::controller(EventController::class)->group(function () {
        Route::get('event', 'index');
        Route::post('event', 'store');
        Route::get('event/{id}', 'show');
        Route::delete('event/{id}', 'delete');
        Route::post('event/interested', 'interested');
        Route::post('event/going', 'goingToEvent');
        Route::post('event/discussion', 'discussion');
        Route::get('event-categories', 'eventCategories');
    });
    Route::controller(AdController::class)->group(function () {
        Route::get('ad', 'index');
        Route::get('ad-detail', 'show');
        Route::post('ad', 'store');
        Route::get('goals', 'goals');
        Route::post('set-goals', 'setGoals');
        Route::post('set-audience-location', 'setAudienceLocation');
        Route::post('set-budget-duration', 'setBudgetDuration');
        Route::post('stop-ad', 'stopAp');
        Route::post('ad-payment', 'adPayment');
    });
    Route::controller(DealOfferController::class)->group(function () {
        Route::get('offers', 'index');
        Route::get('offers/{id}', 'show');
        Route::post('offers', 'store');
        Route::delete('offers/{id}', 'destroy');
    });
    Route::controller(FavouriteBusinessController::class)->group(function () {
        Route::get('favourite-businesses', 'index');
        Route::get('favourite-business', 'show');
    });
    Route::controller(PaymentController::class)->group(function () {
        Route::post('create-payment-intent', 'createPaymentIntent');
    });
});