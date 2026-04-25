<?php

use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\WalletController;
use App\Http\Controllers\Admin\WithdrawController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DuplicateBidController;
use App\Http\Controllers\Admin\AdminGamePriceController;
use App\Http\Controllers\Admin\AdminGameResultController;
use App\Http\Controllers\Admin\AdminBidHistoryController;





/*
|--------------------------------------------------------------------------
| ROOT
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| AUTH (Public)
|--------------------------------------------------------------------------
*/

Route::get('/login', function () {
    return view('admin.login');
})->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('admin.login');

/*
|--------------------------------------------------------------------------
| ADMIN PROTECTED ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:admin'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */
    Route::post('/logout', function () {
        Auth::guard('admin')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [AdminController::class, 'dashboard'])
        ->name('admin.dashboard');

    /*
    |--------------------------------------------------------------------------
    | USERS (DataTables)
    |--------------------------------------------------------------------------
    */
Route::prefix('users')
    ->middleware('permission:users.view')
    ->group(function () {

        Route::get('/', [AdminController::class, 'usersPage'])
            ->name('admin.users');

        Route::get('/data', [AdminController::class, 'usersData'])
            ->name('admin.users.data');

        // ✅ calls toggleUserStatus — unique name, no conflict with any other method
        Route::patch('/{id}/toggle-status', [AdminController::class, 'toggleUserStatus'])
            ->name('admin.user.toggle');

        // ✅ calls destroyUser
        Route::delete('/{id}', [AdminController::class, 'destroyUser'])
            ->middleware('permission:users.delete')
            ->name('admin.user.delete');
    });



    /*
    |--------------------------------------------------------------------------
    | WALLET HISTORY
    |--------------------------------------------------------------------------
    */
    Route::get('/wallet-history', [WalletController::class, 'index'])
        ->middleware('permission:wallet.view')
        ->name('admin.wallet.history');

    /*
    |--------------------------------------------------------------------------
    | PAYMENT WITHDRAW
    |--------------------------------------------------------------------------
    */
    Route::get('/payment-withdraw', [WithdrawController::class, 'index'])
        ->middleware('permission:withdraw.view')
        ->name('admin.payment.withdraw');


    Route::patch('/payment-withdraw/{id}/status', [WithdrawController::class, 'updateStatus'])
        ->middleware('permission:withdraw.view')
        ->name('admin.payment.withdraw.status');

    /*
    |--------------------------------------------------------------------------
    | PAYMENT HISTORY
    |--------------------------------------------------------------------------
    */
    Route::get('/payment-history', [PaymentController::class, 'index'])
        ->middleware('permission:payment.view')
        ->name('admin.payment.history');

    /*
    |--------------------------------------------------------------------------
    | WINNING HISTORY
    |--------------------------------------------------------------------------
    */
    Route::get('/winning-history', [AdminController::class, 'winningHistory'])
        ->middleware('permission:winning.view')
        ->name('admin.winning.history');

    /*
    |--------------------------------------------------------------------------
    | NOTIFICATIONS
    |--------------------------------------------------------------------------
    */
    Route::get('/notification', [NotificationController::class, 'index'])
        ->middleware('permission:notification.view')
        ->name('admin.notification');

    Route::post('/notification/store', [NotificationController::class, 'store'])
        ->middleware('permission:notification.view')
        ->name('admin.notification.store');



    Route::put('/notification/{notification}', [NotificationController::class, 'update'])
        ->middleware('permission:notification.view')
        ->name('admin.notification.update');

    Route::delete('/notification/{notification}', [NotificationController::class, 'destroy'])
        ->middleware('permission:notification.view')
        ->name('admin.notification.delete');

    /*
    |--------------------------------------------------------------------------
    | CHANGE PASSWORD
    |--------------------------------------------------------------------------
    */
    Route::get('/change-password', function () {
        return view('admin.change-password');
    })->name('admin.change.password');

    Route::post('/change-password', [AdminController::class, 'updatePassword'])
        ->name('admin.password.update');

    /*
    |--------------------------------------------------------------------------
    | MANAGE ADMIN
    |--------------------------------------------------------------------------
    */
    Route::get('/manage-admin', [AdminController::class, 'manageAdmin'])
        ->middleware('permission:manage.admin')
        ->name('admin.manage');

    Route::get('/add-admin', [AdminController::class, 'create'])
        ->middleware('permission:manage.admin')
        ->name('admin.add');

    Route::post('/admin-store', [AdminController::class, 'store'])
        ->middleware('permission:manage.admin')
        ->name('admin.store');

    Route::get('/admin/view/{id}', [AdminController::class, 'viewAdmin'])
        ->middleware('permission:manage.admin')
        ->name('admin.view');

    Route::patch('/admin/{id}/status', [AdminController::class, 'updateStatus'])
        ->middleware('permission:manage.admin')
        ->name('admin.updateStatus');


    Route::get('/admin/details/{id}', [AdminController::class, 'getAdminDetails'])
        ->middleware('permission:manage.admin')
        ->name('admin.details');


    Route::patch('/admin/{id}/update', [AdminController::class, 'updateAdmin'])
        ->middleware('permission:manage.admin')
        ->name('admin.update');

    Route::delete('/admin/{id}/delete', [AdminController::class, 'destroyAdmin'])
        ->middleware('permission:manage.admin')
        ->name('admin.destroy');



    // Games modue routes

    Route::prefix('games')
        ->middleware('permission:manage.game')
        ->group(function () {

            Route::get('/', [GameController::class, 'index'])
                ->name('admin.games');

            Route::get('/create', [GameController::class, 'create'])
                ->name('admin.games.create');

            Route::post('/store', [GameController::class, 'store'])
                ->name('admin.games.store');

            Route::get('/edit/{id}', [GameController::class, 'edit'])
                ->name('admin.games.edit');

            Route::put('/update/{id}', [GameController::class, 'update'])->name('admin.games.update');


            Route::delete('/delete/{id}', [GameController::class, 'destroy'])
                ->name('admin.games.delete');

            Route::patch('/toggle-status/{id}', [GameController::class, 'toggleStatus'])
                ->name('admin.games.toggle');
        });




    // chart route

    Route::middleware(['auth:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/bid-chart', [\App\Http\Controllers\Admin\BidChartController::class, 'bidsPage'])
            ->name('bid.chart');

        Route::post('/bid/cancel/{type}/{id}', [\App\Http\Controllers\Admin\BidChartController::class, 'cancelBid'])->name('bid.cancel');

    });


    Route::prefix('admin')->middleware('auth')->group(function () {
        Route::get('/duplicate-bids', [DuplicateBidController::class, 'index'])->name('admin.duplicate.bids');
        Route::post('/duplicate-bids/change-number/{id}', [DuplicateBidController::class, 'changeNumber'])->name('admin.duplicate.bids.change');
        Route::delete('/duplicate-bids/delete/{id}', [DuplicateBidController::class, 'delete'])->name('admin.duplicate.bids.delete');
        Route::post('/duplicate-bids/add-new', [DuplicateBidController::class, 'addNewBid'])->name('admin.duplicate.bids.add');
        Route::post('/duplicate-bids/cancel/{id}', [DuplicateBidController::class, 'cancel'])->name('admin.duplicate.bids.cancel'); // ✅ fixed double admin
    });


    //  manually wallet module for admin 

    Route::prefix('admin')
        ->middleware(['auth:admin'])
        ->group(function () {

            Route::get('/wallet-management', [WalletController::class, 'walletDashboard'])
                ->name('admin.wallet');

            Route::post('/wallet-management/update', [WalletController::class, 'updateWallet'])
                ->name('admin.wallet.update');

            Route::get('/wallet/find-user', [WalletController::class, 'findUser'])
                ->name('admin.wallet.find.user');
        });

});


Route::prefix('admin')
    ->middleware(['auth:admin'])
    ->name('admin.')
    ->group(function () {

        Route::get('/game-price', [AdminGamePriceController::class, 'index'])
            ->name('game-price.index');

        Route::post('/game-price/update', [AdminGamePriceController::class, 'update'])
            ->name('game-price.update');
    });

Route::get('/admin/game-result',          [AdminGameResultController::class, 'index'])->name('admin.game-result.index');
Route::post('/admin/game-result/declare', [AdminGameResultController::class, 'declare'])->name('admin.game-result.declare');
Route::get('/admin/bid-history', [AdminBidHistoryController::class, 'index'])
    ->name('admin.bid-history.index');




// /
