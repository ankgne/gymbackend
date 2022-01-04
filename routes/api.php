<?php

use App\Http\Controllers\Member\AccountController;
use App\Http\Controllers\Member\ContactController;
use App\Http\Controllers\Member\MemberController;
use App\Http\Controllers\TransactionController;
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
    });
