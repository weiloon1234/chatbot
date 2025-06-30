<?php

use Illuminate\Support\Facades\Route;

Route::post('/settings', [\App\Http\Controllers\Api\Admin\SettingController::class, 'fetch']);

Route::post('/logout', [\App\Http\Controllers\Api\Admin\AccountController::class, 'logout']);
Route::post('/login', [\App\Http\Controllers\Api\Admin\AccountController::class, 'login']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::group(['prefix' => 'article', 'namespace' => 'Article'], function () {
        Route::post('/article/build_form', [\App\Http\Controllers\Api\Admin\Article\ArticleController::class, 'buildForm'])->middleware('adminHasPermission:Manage article');
        Route::post('/article/submit_form', [\App\Http\Controllers\Api\Admin\Article\ArticleController::class, 'submitForm'])->middleware('adminHasPermission:Manage article');
        Route::post('/article/delete', [\App\Http\Controllers\Api\Admin\Article\ArticleController::class, 'delete'])->middleware('adminHasPermission:Manage article');

        Route::post('/category/build_form', [\App\Http\Controllers\Api\Admin\Article\ArticleCategoryController::class, 'buildForm'])->middleware('adminHasPermission:Manage article category');
        Route::post('/category/submit_form', [\App\Http\Controllers\Api\Admin\Article\ArticleCategoryController::class, 'submitForm'])->middleware('adminHasPermission:Manage article category');
        Route::post('/category/delete', [\App\Http\Controllers\Api\Admin\Article\ArticleCategoryController::class, 'delete'])->middleware('adminHasPermission:Manage article category');
    });

    Route::group(['prefix' => 'management', 'namespace' => 'Management'], function () {
        Route::post('/admin/build_form', [\App\Http\Controllers\Api\Admin\Management\AdminController::class, 'buildForm'])->middleware('adminHasPermission:Manage admin');
        Route::post('/admin/submit_form', [\App\Http\Controllers\Api\Admin\Management\AdminController::class, 'submitForm'])->middleware('adminHasPermission:Manage admin');
        Route::post('/admin/delete', [\App\Http\Controllers\Api\Admin\Management\AdminController::class, 'delete'])->middleware('adminHasPermission:Manage admin');

        Route::post('/admin_group/build_form', [\App\Http\Controllers\Api\Admin\Management\AdminGroupController::class, 'buildForm'])->middleware('adminHasPermission:Manage admin group');
        Route::post('/admin_group/submit_form', [\App\Http\Controllers\Api\Admin\Management\AdminGroupController::class, 'submitForm'])->middleware('adminHasPermission:Manage admin group');
        Route::post('/admin_group/delete', [\App\Http\Controllers\Api\Admin\Management\AdminGroupController::class, 'delete'])->middleware('adminHasPermission:Manage admin group');
    });

    Route::group(['prefix' => 'other', 'namespace' => 'Other'], function () {
        Route::post('/bank/build_form', [\App\Http\Controllers\Api\Admin\Other\BankController::class, 'buildForm'])->middleware('adminHasPermission:Manage bank');
        Route::post('/bank/submit_form', [\App\Http\Controllers\Api\Admin\Other\BankController::class, 'submitForm'])->middleware('adminHasPermission:Manage bank');
        Route::post('/bank/delete', [\App\Http\Controllers\Api\Admin\Other\BankController::class, 'delete'])->middleware('adminHasPermission:Manage bank');

        Route::post('/company_bank/build_form', [\App\Http\Controllers\Api\Admin\Other\CompanyBankController::class, 'buildForm'])->middleware('adminHasPermission:Manage company bank');
        Route::post('/company_bank/submit_form', [\App\Http\Controllers\Api\Admin\Other\CompanyBankController::class, 'submitForm'])->middleware('adminHasPermission:Manage company bank');
        Route::post('/company_bank/delete', [\App\Http\Controllers\Api\Admin\Other\CompanyBankController::class, 'delete'])->middleware('adminHasPermission:Manage company bank');

        Route::post('/country/build_form', [\App\Http\Controllers\Api\Admin\Other\CountryController::class, 'buildForm'])->middleware('adminHasPermission:Manage country');
        Route::post('/country/submit_form', [\App\Http\Controllers\Api\Admin\Other\CountryController::class, 'submitForm'])->middleware('adminHasPermission:Manage country');

        Route::post('/page/build_form', [\App\Http\Controllers\Api\Admin\Other\PageController::class, 'buildForm'])->middleware('adminHasPermission:Manage page');
        Route::post('/page/submit_form', [\App\Http\Controllers\Api\Admin\Other\PageController::class, 'submitForm'])->middleware('adminHasPermission:Manage page');
        Route::post('/page/delete', [\App\Http\Controllers\Api\Admin\Other\PageController::class, 'delete'])->middleware('adminHasPermission:Manage page');

        Route::post('/setting/build_form', [\App\Http\Controllers\Api\Admin\Other\SettingController::class, 'buildForm'])->middleware('adminHasPermission:Manage setting');
        Route::post('/setting/submit_form', [\App\Http\Controllers\Api\Admin\Other\SettingController::class, 'submitForm'])->middleware('adminHasPermission:Manage setting');
    });

    Route::group(['prefix' => 'user', 'namespace' => 'User'], function () {
        Route::post('/user/build_form', [\App\Http\Controllers\Api\Admin\User\UserController::class, 'buildForm'])->middleware('adminHasPermission:Manage user');
        Route::post('/user/submit_form', [\App\Http\Controllers\Api\Admin\User\UserController::class, 'submitForm'])->middleware('adminHasPermission:Manage user');
        Route::post('/user/login_account', [\App\Http\Controllers\Api\Admin\User\UserController::class, 'loginAccount'])->middleware('adminHasPermission:Manage user');
        Route::post('/user/toggle_ban_status', [\App\Http\Controllers\Api\Admin\User\UserController::class, 'toggleBanStatus'])->middleware('adminHasPermission:Manage user');
        Route::post('/user/load_statistics', [\App\Http\Controllers\Api\Admin\User\UserController::class, 'loadStatistics'])->middleware('adminHasPermission:Manage user');
        Route::post('/user/check_user', [\App\Http\Controllers\Api\Admin\User\UserController::class, 'checkUser'])->middleware('adminHasPermission:Manage user,Manage user credit');

        Route::post('/credit/submit_form', [\App\Http\Controllers\Api\Admin\User\UserCreditController::class, 'submitForm'])->middleware('adminHasPermission:Manage user credit');

        Route::post('/deposit/build_form', [\App\Http\Controllers\Api\Admin\User\DepositController::class, 'buildForm'])->middleware('adminHasPermission:Manage user deposit');
        Route::post('/deposit/submit_form', [\App\Http\Controllers\Api\Admin\User\DepositController::class, 'submitForm'])->middleware('adminHasPermission:Manage user deposit');

        Route::post('/withdrawal/build_form', [\App\Http\Controllers\Api\Admin\User\WithdrawalController::class, 'buildForm'])->middleware('adminHasPermission:Manage user withdrawal');
        Route::post('/withdrawal/submit_form', [\App\Http\Controllers\Api\Admin\User\WithdrawalController::class, 'submitForm'])->middleware('adminHasPermission:Manage user withdrawal');
    });

    Route::post('/editor_upload_image', [\App\Http\Controllers\Api\Admin\EditorController::class, 'uploadImage']);

    Route::post('/dt', [\App\Http\Controllers\Api\Admin\DataTableController::class, 'load']);

    Route::post('/me', [\App\Http\Controllers\Api\Admin\AccountController::class, 'fetch']);
    Route::post('/notifications', [\App\Http\Controllers\Api\Admin\NotificationController::class, 'fetch']);
    Route::post('/change_language', [\App\Http\Controllers\Api\Admin\AccountController::class, 'changeLanguage']);
    Route::post('/profile_submit', [\App\Http\Controllers\Api\Admin\AccountController::class, 'profileSubmit']);
    Route::post('/security_submit', [\App\Http\Controllers\Api\Admin\AccountController::class, 'securitySubmit']);

    Route::post('/etc/check_whatsapp', [\App\Http\Controllers\Api\Admin\EtcController::class, 'checkWhatsapp']);
    Route::post('/etc/check_whatsapp_status', [\App\Http\Controllers\Api\Admin\EtcController::class, 'checkWhatsappStatus']);
    Route::post('/etc/disconnect_whatsapp', [\App\Http\Controllers\Api\Admin\EtcController::class, 'disconnectWhatsapp']);
    Route::post('/etc/send_message_whatsapp', [\App\Http\Controllers\Api\Admin\EtcController::class, 'sendMessageWhatsapp']);
    Route::post('/etc/load_log', [\App\Http\Controllers\Api\Admin\EtcController::class, 'loadLog']);
    Route::post('/etc/clear', [\App\Http\Controllers\Api\Admin\EtcController::class, 'clear'])->block();
});
