<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\AssociateController;
use App\Http\Controllers\Admin\AutomationController;
use App\Http\Controllers\Admin\AutomationMarketingController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CampaignController as AdminCampaignController;
use App\Http\Controllers\Admin\CategorieController;
use App\Http\Controllers\Admin\CheckInventoryController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Staff\ClientController as StaffClientController;
use App\Http\Controllers\Staff\OrderController as StaffOrderController;
use App\Http\Controllers\Staff\ProductController as StaffProductController;
use App\Http\Controllers\Admin\ConfigController;
use App\Http\Controllers\Admin\DailyReportController;
use App\Http\Controllers\Admin\DebtClientController;
use App\Http\Controllers\Admin\DebtNccController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\importCouponController;
use App\Http\Controllers\Admin\ImportProductController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ReceiptController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReportdebtController;
use App\Http\Controllers\Admin\StorageController;
use App\Http\Controllers\Admin\StoreController as AdminStoreController;
use App\Http\Controllers\Client\SignUpController;
use App\Http\Controllers\Staff\CheckInventoryController as staffcheckController;
use App\Http\Controllers\Staff\WareHomeController;
use App\Http\Controllers\SuperAdmin\StoreController;
use App\Http\Controllers\SuperAdmin\SuperAdminController;
use App\Http\Controllers\Topcv\WebhookController;
use App\Models\Categories;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\SupportController;
use App\Http\Controllers\Admin\TemplateController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\TransferController as AdminTransferController;
use App\Http\Controllers\Admin\ZaloController as AdminZaloController;
use App\Http\Controllers\Admin\ZnsMessageController as AdminZnsMessageController;
use App\Http\Controllers\Message\AdviseController;
use App\Http\Controllers\Message\MediaController;
use App\Http\Controllers\SheetController;
use App\Http\Controllers\SuperAdmin\CampaignController;
use App\Http\Controllers\SuperAdmin\TransactionController as SuperAdminTransactionController;
use App\Http\Controllers\SuperAdmin\TransferController;
use App\Http\Controllers\SuperAdmin\UserController as SuperAdminUserController;
use App\Http\Controllers\SuperAdmin\ZnsMessageController;
use App\Http\Controllers\SuperAdmin\ZaloController;
use App\Http\Controllers\SuperAdminController as ControllersSuperAdminController;
use App\Http\Middleware\CheckLogin;
use App\Http\Middleware\CheckLoginSuperAdmin;
use App\Models\AutomationUser;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Route::get('', [CategorieController::class, 'index']);
Route::post('/check-account', [SignUpController::class, 'checkAccount'])->name('check.account');

// Route::get('/check-phone-exists', [SignUpController::class, 'checkPhoneExists'])->name('check-phone-exists');
// Route::get('/check-email-exists', [SignUpController::class, 'checkEmailExists'])->name('check-email-exists');
Route::get('/dang-ky', [SignUpController::class, 'index'])->name('register.index');
Route::post('/register_account', [SignUpController::class, 'store'])->name('register.signup');

Route::get('', [DashboardController::class, 'default'])->name('default');
Route::post('/{username}/login', [AuthController::class, 'login'])->name('login');
// Route::get('/', function () {
//     return view('auth.login');
// })->name('formlogin');
// Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/verify-otp', [AuthController::class, 'showVerifyOtp'])->name('verify-otp');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('verify_otp_confirm');
Route::middleware(['guest'])->group(function () {
    Route::get('/{username}', function ($username) {
        // dd($username);
        return view('auth.login', compact('username'));
    })->name('formlogin');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/payment', [PaymentController::class, 'showPaymentForm'])->name('payment.form');
    Route::post('/payment', [PaymentController::class, 'processPayment'])->name('payment.process');
    Route::get('/payment-success', [PaymentController::class, 'paymentSuccess'])->name('payment.success');
    Route::post('/payment-notify', [PaymentController::class, 'paymentNotify'])->name('payment.notify');
});
Route::get('forget-password', function () {
    return view('auth.forget-password');
})->name('forget-password');

Route::get('/product', function () {
    return view('Themes.pages.product.index');
})->name('product');
// Route::get('/category',function(){
//         return view('Themes.pages.category.index');
//         })->name('category');
Route::get('/employee', function () {
    return view('Themes.pages.employee.index');
})->name('employee');
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/home', function () {
        // dd(1);
        return view('home');
    })->name('home');

    Route::prefix('{username}/associate')->name('{username}.associate.')->group(function () {
        Route::get('', [AssociateController::class, 'index'])->name('index');
        Route::get('search', [AssociateController::class, 'search'])->name('search');
        Route::post('store', [AssociateController::class, 'store'])->name('store');
        Route::get('detail/{id}', [AssociateController::class, 'detail'])->name('detail');
        Route::put('update/{id}', [AssociateController::class, 'update'])->name('update');
        Route::post('delete', [AssociateController::class, 'delete'])->name('delete');
        Route::post('associate-status', [AssociateController::class, 'updateAssociateStatus'])->name('updateAssociateStatus');
    });
    Route::prefix('{username}/automation')->name('{username}.automation.')->group(function () {
        Route::get('', [AutomationMarketingController::class, 'index'])->name('index');
        Route::post('/update-status', [AutomationMarketingController::class, 'updateUserStatus'])->name('updateStatus');
        Route::post('/update-template', [AutomationMarketingController::class, 'updateUserTemplate'])->name('updateTemplate');
        Route::post('/update-rate-status', [AutomationMarketingController::class, 'updateRateStatus'])->name('updateRateStatus');
        Route::post('/update-rate-template', [AutomationMarketingController::class, 'updateRateTemplate'])->name('updateRateTemplate');
        Route::post('/update-birthday-status', [AutomationMarketingController::class, 'updateBirthdayStatus'])->name('updateBirthdayStatus');
        Route::post('/update-birthday-template', [AutomationMarketingController::class, 'updateBirthdayTemplate'])->name('updateBirthdayTemplate');
        Route::post('/update-birthday-start-time', [AutomationMarketingController::class, 'updateBirthdayStartTime'])->name('updateBirthdayStartTime');
        Route::post('/update-reminder-sendingcycle', [AutomationMarketingController::class, 'updateReminderSendingCycle'])->name('updateReminderSendingCycle');
        Route::post('/update-reminder-start-time', [AutomationMarketingController::class, 'updateReminderStartTime'])->name('updateReminderStartTime');
        Route::post('/update-reminder-template', [AutomationMarketingController::class, 'updateReminderTemplate'])->name('updateReminderTemplate');
        Route::post('/update-reminder-status', [AutomationMarketingController::class, 'updateReminderStatus'])->name('updateReminderStatus');
        Route::post('/update-rate-start-time', [AutomationMarketingController::class, 'updateRateStartTime'])->name('updateRateStartTime');
        Route::post('/update-rate-sendingcycle', [AutomationMarketingController::class, 'updateRateSendingCycle'])->name('updateRateSendingCycle');
        // Route::get('/user', [AutomationController::class, 'user'])->name('user');
        // Route::post('/user', [AutomationController::class, 'userupdate'])->name('user.update');

        // Route::get('/birthday', [AutomationController::class, 'birthday'])->name('birthday');
        // Route::post('/birthday', [AutomationController::class, 'birthdayupdate'])->name('birthday.update');

        // Route::get('/reminder', [AutomationController::class, 'reminder'])->name('reminder');
        // Route::post('/birtreminderhday', [AutomationController::class, 'reminderupdate'])->name('reminder.update');
    });
    Route::prefix('{username}/message')->name('{username}.message.')->group(function () {
        Route::get('/export', [AdminZnsMessageController::class, 'export'])->name('export');
        Route::get('/statusDashboard', [AdminZnsMessageController::class, 'statusDashboard'])->name('statusDashboard');
        Route::get('/status', [AdminZnsMessageController::class, 'status'])->name('status');
        Route::get('', [AdminZnsMessageController::class, 'znsMessage'])->name('znsMessage');
        Route::get('/quota', [AdminZnsMessageController::class, 'znsQuota'])->name('znsQuota');
        Route::get('template', [AdminZnsMessageController::class, 'templateIndex'])->name('znsTemplate');
        Route::get('refresh', [AdminZnsMessageController::class, 'refreshTemplates'])->name('znsTemplateRefresh');
        Route::get('detail', [AdminZnsMessageController::class, 'getTemplateDetail'])->name('znsTemplateDetail');
        Route::get('test', [AdminZnsMessageController::class, 'test'])->name('test');
        Route::get('params', [AdminZnsMessageController::class, 'params'])->name('params');
    });
    Route::prefix('{username}/product')->name('{username}.product.')->group(function () {
        Route::get('', [ProductController::class, 'index'])->name('index');
        Route::post('store', [ProductController::class, 'store'])->name('store');
        Route::post('update', [ProductController::class, 'update'])->name('update');
        Route::post('delete', [ProductController::class, 'delete'])->name('delete');
        Route::get('fetch', [ProductController::class, 'fetch'])->name('fetch');
    });
    Route::prefix('{username}/store')->name('{username}.store.')->group(function () {
        Route::post('/import', [AdminStoreController::class, 'import'])->name('import');
        Route::get('/index', [AdminStoreController::class, 'index'])->name('index');
        Route::get('/detail/{id}', [AdminStoreController::class, 'detail'])->name('detail');
        Route::get('/findByPhone', [AdminStoreController::class, 'findByPhone'])->name('findByPhone');
        Route::post('/delete', [AdminStoreController::class, 'delete'])->name('delete');
        Route::post('/store', [AdminStoreController::class, 'store'])->name('store');
    });
    Route::prefix('{username}/transaction')->name('{username}.transaction.')->group(function () {
        Route::get('/update-notification/{id}', [TransactionController::class, 'updateNotification'])->name('updateNotification');
        Route::get('', [TransactionController::class, 'index'])->name('index');
        Route::get('search', [TransactionController::class, 'search'])->name('search');
        Route::get('payment', [TransactionController::class, 'payment'])->name('payment');
        Route::post('store', [TransactionController::class, 'store'])->name('store');
        Route::get('export/{id}', [TransactionController::class, 'exportPDF'])->name('export');
        Route::get('generateQR', [TransactionController::class, 'generateQrCode'])->name('generate');
    });
    Route::prefix('{username}/campaign')->name('{username}.campaign.')->group(function () {
        Route::get('add', [AdminCampaignController::class, 'add'])->name('add');
        Route::get('', [AdminCampaignController::class, 'index'])->name('index');
        Route::get('fetch', [AdminCampaignController::class, 'fetch'])->name('fetch');
        Route::post('store', [AdminCampaignController::class, 'store'])->name('store');
        Route::get('detail/{id}', [AdminCampaignController::class, 'edit'])->name('detail');
        Route::post('update/{id}', [AdminCampaignController::class, 'update'])->name('update');
        Route::post('delete', [AdminCampaignController::class, 'delete'])->name('delete');
        Route::post('update-status/', [AdminCampaignController::class, 'updateStatus'])->name('updateStatus');
    });
    Route::prefix('{username}/transfer')->name('{username}.transfer.')->group(function () {
        Route::get('', [AdminTransferController::class, 'index'])->name('index');
        Route::get('search', [AdminTransferController::class, 'search'])->name('search');
        Route::get('update-notification/{id}', [AdminTransferController::class, 'updateNotification'])->name('updateNotification');
    });
    Route::prefix('{username}/zalo')->name('{username}.zalo.')->group(function () {
        Route::get('zns', [AdminZaloController::class, 'index'])->name('zns');
        Route::get('/get-active-oa-name', [AdminZaloController::class, 'getActiveOaName'])->name('getActiveOaName');
        Route::post('/update-oa-status/{oaId}', [AdminZaloController::class, 'updateOaStatus'])->name('updateOaStatus');
        Route::post('/refresh-access-token', [AdminZaloController::class, 'refreshAccessToken'])->name('refreshAccessToken');
        Route::post('store', [AdminZaloController::class, 'store'])->name('store');
        Route::post('/check-oa', [AdminZaloController::class, 'checkOa'])->name('checkOa');
    });

    Route::prefix('{username}/article')->name('{username}.articles.')->group(function () {
        Route::get('', [ArticleController::class, 'index'])->name('list');
        Route::get('broadcast', [ArticleController::class, 'broadcast'])->name('broadcast');
    });
    Route::get('/zalo/articles', [ArticleController::class, 'fetchAllArticles']);
    Route::get('{username}/detail/{id}', [AdminController::class, 'getAdminInfor'])->name('{username}.detail');
    Route::post('{username}/update/{id}', [AdminController::class, 'updateAdminInfor'])->name('{username}.update');
    Route::post('{username}/changePassword', [AdminController::class, 'changePassword'])->name('{username}.changePassword');

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('{username}/message-zalo')->name('{username}.message.zalo.')->group(function () {

        Route::get('text', [AdviseController::class, 'text'])->name('text');
        Route::post('text', [AdviseController::class, 'sendMessage'])->name('text');

        Route::get('image', [AdviseController::class, 'images'])->name('image');
        Route::post('image', [AdviseController::class, 'sendImageMessage'])->name('image');

        Route::get('broadcast', [AdviseController::class, 'messageDocument'])->name('broadcast');
        Route::post('broadcast', [AdviseController::class, 'sendZaloBroadcast'])->name('broadcast');

        Route::get('transaction', [\App\Http\Controllers\Message\TransactionController::class, 'transaction'])->name('transaction');
        Route::post('transaction', [\App\Http\Controllers\Message\TransactionController::class, 'sendTransactionMessage'])->name('transaction');

        Route::get('media', [MediaController::class, 'media'])->name('media');
        Route::post('media', [MediaController::class, 'sendMediaMessage'])->name('media');
    });

    Route::prefix('{username}/template')->name('{username}.template.')->controller(TemplateController::class)->group(function () {
        Route::get('', 'index')->name('template');
        Route::post('', 'storeTemplate')->name('template.store');
    });
});

Route::get('/webhook', [WebhookController::class, 'handle']);
Route::get('test/sheet', [SheetController::class, 'index']);
