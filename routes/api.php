<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Member\AccountController;
use App\Http\Controllers\Member\ContactController;
use App\Http\Controllers\Member\MemberController;
use App\Http\Controllers\Member\PlanController;
use App\Http\Controllers\Member\SubscriptionController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\BillingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\PasswordResetLinkController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware("auth:sanctum")->get("/user", function (Request $request) {
    return $request->user();
});

Route::group(
    ["middleware" => config("fortify.middleware", ["web"])],
    function () {
        // setting the throttle limit to null for testing environment
        //$limiter = config('fortify.limiters.login');
        $limiter =
            env("APP_ENV") === "local"
                ? null
                : config("fortify.limiters.login");

        Route::post("/login", [
            AuthenticatedSessionController::class,
            "store",
        ])->middleware(
            array_filter([
                "guest:" . config("fortify.guard"),
                $limiter ? "throttle:" . $limiter : null,
            ])
        );

        Route::post("/logout", [
            AuthenticatedSessionController::class,
            "destroy",
        ])->name("logout");

        // Registration...
        // TODO not using below route at the moment as this is for providing user registration functionality from frontend
        if (Features::enabled(Features::registration())) {
            Route::post("/register", [
                RegisteredUserController::class,
                "store",
            ])->middleware(["guest:" . config("fortify.guard")]);
        }
    }
);

//middleware is owner or admin
Route::middleware(["auth:" . config("fortify.guard"), "owner.admin"])
    ->prefix("admin")
    ->name("admin.")
    ->group(function () {
        Route::get("/members/active", [
            MemberController::class,
            "activeMembersList",
        ])->name("members.active");
        Route::get("/members/inactive", [
            MemberController::class,
            "inactiveMembersList",
        ])->name("members.inactive");
        Route::get("/members/active/upcoming_due_date", [
            MemberController::class,
            "getActiveCustomersWithUpcomingDueDate",
        ])->name("members.active.upcomingduedate");
        Route::get("/members/active/over_due_date", [
            MemberController::class,
            "getActiveCustomersWithOverDueDate",
        ])->name("members.active.overduedate");
        Route::get("/members/suspended", [
            MemberController::class,
            "suspendedCustomers",
        ])->name("members.suspended");
        Route::apiResource("members", MemberController::class);
    });

Route::middleware(["auth:" . config("fortify.guard"), "owner.admin"])
    ->prefix("admin")
    ->name("admin.")
    ->group(function () {
        Route::apiResource("contacts", ContactController::class);
    });

//member controller
Route::middleware(["auth:" . config("fortify.guard"), "owner.admin"])
    ->prefix("admin")
    ->name("admin.")
    ->group(function () {
        Route::get("/members/registration/{registrationnumber}", [
            MemberController::class,
            "searchByRegistration",
        ])->name("members.byaccount");
        Route::get("/members/phonenumber/{phonenumber}", [
            MemberController::class,
            "searchByPhone",
        ])->name("members.byphone");
        Route::get("/members/email/{email}", [
            MemberController::class,
            "searchByEmail",
        ])->name("members.byemail");
    });

// transaction controller
Route::middleware(["auth:" . config("fortify.guard"), "owner.admin"])
    ->prefix("admin")
    ->name("admin.")
    ->group(function () {
        Route::apiResource("transactions", TransactionController::class)->only([
            "index",
            "show",
            "store",
        ]);
        Route::get("/transactions/accountid/{accountID}", [
            TransactionController::class,
            "getTransactionsByAccountID",
        ])->name("transactions.byaccountid");
    });

// Subscription controller
Route::middleware(["auth:" . config("fortify.guard"), "owner.admin"])
    ->prefix("admin")
    ->name("admin.")
    ->group(function () {
        Route::apiResource(
            "subscriptions",
            SubscriptionController::class
        )->only(["update"]);
        Route::post("/subscriptions/queue", [
            SubscriptionController::class,
            "queueSubscriptionChanges",
        ])->name("subscription.queue");
    });

// account controlled
Route::middleware(["auth:" . config("fortify.guard"), "owner.admin"])
    ->prefix("admin")
    ->name("admin.")
    ->group(function () {
        Route::put("/accounts/deactivate/{id}", [
            AccountController::class,
            "inactive",
        ])->name("accounts.inactive");
        Route::put("/accounts/activate/{id}", [
            AccountController::class,
            "active",
        ])->name("accounts.active");
    });

// Billing controller
Route::middleware(["auth:" . config("fortify.guard"), "owner.admin"])
    ->prefix("admin")
    ->name("admin.")
    ->group(function () {
        Route::apiResource("bills", BillingController::class)->only(["show"]);
        Route::get("/bills/accountid/{accountID}", [
            BillingController::class,
            "getInvoicesByAccountID",
        ])->name("bills.byaccountid");
    });

// Plan controller
Route::middleware(["auth:" . config("fortify.guard"), "owner.admin"])
    ->prefix("admin")
    ->name("admin.")
    ->group(function () {
        Route::apiResource("plans", PlanController::class)->only([
            "index",
            "store",
            "update",
        ]);
        Route::get("/plans/active", [
            PlanController::class,
            "getActivePlans",
        ])->name("plans.active");
        Route::put("/plans/activate/{plan}", [
            PlanController::class,
            "activatePlan",
        ])->name("plans.activate");
        Route::put("/plans/deactivate/{plan}", [
            PlanController::class,
            "deactivatePlan",
        ])->name("plans.deactivate");
    });

// attendnace controller
Route::middleware(["auth:" . config("fortify.guard"), "owner.admin"])
    ->prefix("admin")
    ->name("admin.")
    ->group(function () {
        Route::apiResource("attendance", AttendanceController::class)->only([
            "index",
            "store",
        ]);
        Route::put("/attendance/outtime", [
            AttendanceController::class,
            "captureOutTime",
        ])->name("attendance.captureouttime");
        Route::get("/attendance/todays", [
            AttendanceController::class,
            "attendanceForToday",
        ])->name("attendance.attendanceForToday");
    });

// Dashboard controller
Route::middleware(["auth:" . config("fortify.guard"), "owner.admin"])
    ->prefix("admin")
    ->name("admin.")
    ->group(function () {
        Route::get("/dashboard/attendance/sixmonths", [
            \App\Http\Controllers\DashboardController::class,
            "getSixMonthsAttendance",
        ])->name("dashboard.attendanceSixMonths");
        Route::get("/dashboard/attendance/sevendays", [
            \App\Http\Controllers\DashboardController::class,
            "getSevenDaysAttendance",
        ])->name("dashboard.attendanceSevenDays");
        Route::get("/dashboard/newusers/monthly/count", [
            \App\Http\Controllers\DashboardController::class,
            "getNewUsersCount",
        ])->name("dashboard.newusers.monthly.count");
        Route::get("/dashboard/active/users/count", [
            \App\Http\Controllers\DashboardController::class,
            "getActiveUsersCount",
        ])->name("dashboard.active.users.count");
        Route::get("/dashboard/monthly/revenue", [
            \App\Http\Controllers\DashboardController::class,
            "getMonthlyTotalRevenue",
        ])->name("dashboard.monthly.revenue");
        Route::get("/dashboard/currentmonth/paymentdue", [
            \App\Http\Controllers\DashboardController::class,
            "getCurrentMonthPaymentDue",
        ])->name("dashboard.currentmonth.paymentdue");
    });
