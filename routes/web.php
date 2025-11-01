<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\LocalityController;
use App\Http\Controllers\TalukaController;
use App\Http\Controllers\VillageController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return redirect()->route('login');
    })->name('home');

Route::get('dashboard', function () {
    $stats = [
        'roles' => \Spatie\Permission\Models\Role::count(),
        'users' => \App\Models\User::count(),
        'states' => \App\Models\State::count(),
        'cities' => \App\Models\City::count(),
    ];
    
    return Inertia::render('Dashboard', [
        'stats' => $stats,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

// Management landing pages
Route::get('management/locations', function () {
    $stats = [
        'states' => \App\Models\State::count(),
        'cities' => \App\Models\City::count(),
        'districts' => \App\Models\District::count(),
        'localities' => \App\Models\Locality::count(),
        'talukas' => \App\Models\Taluka::count(),
        'villages' => \App\Models\Village::count(),
    ];

    return Inertia::render('LocationManagement', [
        'stats' => $stats,
    ]);
})->middleware(['auth', 'verified'])->name('management.locations');

Route::get('management/users', function () {
    $stats = [
        'users' => \App\Models\User::count(),
        'roles' => \Spatie\Permission\Models\Role::count(),
    ];

    return Inertia::render('UserManagement', [
        'stats' => $stats,
    ]);
})->middleware(['auth', 'verified'])->name('management.users');

Route::get('profile', function () {
    $user = \Illuminate\Support\Facades\Auth::user();
    /** @var \App\Models\User|null $user */
    $user?->load(['state', 'city']);
    $states = \App\Models\State::where('is_active', true)->orderBy('name')->get();
    
    return Inertia::render('Profile', [
        'user' => $user,
        'states' => $states,
    ]);
})->middleware(['auth', 'verified'])->name('profile');

Route::get('roles', function () {
    $roles = \Spatie\Permission\Models\Role::with('permissions')->get();
    $permissions = \Spatie\Permission\Models\Permission::all();
    
    return Inertia::render('roles/Roles', [
        'roles' => $roles,
        'permissions' => $permissions,
    ]);
})->middleware(['auth', 'verified', 'permission:role.read'])->name('roles.index');

Route::get('states', function () {
    $states = \App\Models\State::orderBy('name')->get();
    
    return Inertia::render('states/States', [
        'states' => $states,
    ]);
})->middleware(['auth', 'verified', 'permission:role.read'])->name('states.index');

Route::get('cities', function () {
    $cities = \App\Models\City::with('state')->orderBy('name')->get();
    $states = \App\Models\State::where('is_active', true)->orderBy('name')->get();
    
    return Inertia::render('cities/Cities', [
        'cities' => $cities,
        'states' => $states,
    ]);
})->middleware(['auth', 'verified', 'permission:role.read'])->name('cities.index');

Route::get('districts', function () {
    $districts = \App\Models\District::with('state')->orderBy('name')->get();
    $states = \App\Models\State::where('is_active', true)->orderBy('name')->get();
    
    return Inertia::render('districts/Districts', [
        'districts' => $districts,
        'states' => $states,
    ]);
})->middleware(['auth', 'verified', 'permission:role.read'])->name('districts.index');

Route::get('localities', function () {
    $localities = \App\Models\Locality::with(['state', 'city'])->orderBy('name')->get();
    $states = \App\Models\State::where('is_active', true)->orderBy('name')->get();
    
    return Inertia::render('localities/Localities', [
        'localities' => $localities,
        'states' => $states,
    ]);
})->middleware(['auth', 'verified', 'permission:role.read'])->name('localities.index');

Route::get('talukas', function () {
    $talukas = \App\Models\Taluka::with('district')->orderBy('name')->get();
    $districts = \App\Models\District::where('is_active', true)->orderBy('name')->get();
    
    return Inertia::render('talukas/Talukas', [
        'talukas' => $talukas,
        'districts' => $districts,
    ]);
})->middleware(['auth', 'verified', 'permission:role.read'])->name('talukas.index');

Route::get('villages', function () {
    $villages = \App\Models\Village::with(['district', 'taluka'])->orderBy('name')->get();
    $districts = \App\Models\District::where('is_active', true)->orderBy('name')->get();
    
    return Inertia::render('villages/Villages', [
        'villages' => $villages,
        'districts' => $districts,
    ]);
})->middleware(['auth', 'verified', 'permission:role.read'])->name('villages.index');

Route::get('users', function () {
    $users = \App\Models\User::with(['state', 'city', 'roles'])->orderBy('created_at', 'desc')->get();
    $states = \App\Models\State::where('is_active', true)->orderBy('name')->get();
    
    return Inertia::render('users/Users', [
        'users' => $users,
        'states' => $states,
    ]);
})->middleware(['auth', 'verified', 'permission:user.read'])->name('users.index');

Route::middleware(['auth'])->group(function () {
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store')->middleware('permission:role.create');
    Route::get('/roles/{role}', [RoleController::class, 'show'])->name('roles.show')->middleware('permission:role.read');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update')->middleware('permission:role.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy')->middleware('permission:role.delete');
    
    Route::post('/states', [StateController::class, 'store'])->name('states.store')->middleware('permission:role.create');
    Route::get('/states/{state}', [StateController::class, 'show'])->name('states.show')->middleware('permission:role.read');
    Route::put('/states/{state}', [StateController::class, 'update'])->name('states.update')->middleware('permission:role.update');
    Route::delete('/states/{state}', [StateController::class, 'destroy'])->name('states.destroy')->middleware('permission:role.delete');
    
    Route::post('/cities', [CityController::class, 'store'])->name('cities.store')->middleware('permission:role.create');
    Route::get('/cities/{city}', [CityController::class, 'show'])->name('cities.show')->middleware('permission:role.read');
    Route::put('/cities/{city}', [CityController::class, 'update'])->name('cities.update')->middleware('permission:role.update');
    Route::delete('/cities/{city}', [CityController::class, 'destroy'])->name('cities.destroy')->middleware('permission:role.delete');
    Route::get('/states/{state}/cities', [CityController::class, 'getByState'])->name('cities.by-state')->middleware('permission:role.read');
    
    Route::post('/districts', [DistrictController::class, 'store'])->name('districts.store')->middleware('permission:role.create');
    Route::get('/districts/{district}', [DistrictController::class, 'show'])->name('districts.show')->middleware('permission:role.read');
    Route::put('/districts/{district}', [DistrictController::class, 'update'])->name('districts.update')->middleware('permission:role.update');
    Route::delete('/districts/{district}', [DistrictController::class, 'destroy'])->name('districts.destroy')->middleware('permission:role.delete');
    Route::get('/states/{state}/districts', [DistrictController::class, 'getByState'])->name('districts.by-state')->middleware('permission:role.read');
    
    Route::post('/localities', [LocalityController::class, 'store'])->name('localities.store')->middleware('permission:role.create');
    Route::get('/localities/{locality}', [LocalityController::class, 'show'])->name('localities.show')->middleware('permission:role.read');
    Route::put('/localities/{locality}', [LocalityController::class, 'update'])->name('localities.update')->middleware('permission:role.update');
    Route::delete('/localities/{locality}', [LocalityController::class, 'destroy'])->name('localities.destroy')->middleware('permission:role.delete');
    Route::get('/cities/{city}/localities', [LocalityController::class, 'getByCity'])->name('localities.by-city')->middleware('permission:role.read');
    
    Route::post('/talukas', [TalukaController::class, 'store'])->name('talukas.store')->middleware('permission:role.create');
    Route::get('/talukas/{taluka}', [TalukaController::class, 'show'])->name('talukas.show')->middleware('permission:role.read');
    Route::put('/talukas/{taluka}', [TalukaController::class, 'update'])->name('talukas.update')->middleware('permission:role.update');
    Route::delete('/talukas/{taluka}', [TalukaController::class, 'destroy'])->name('talukas.destroy')->middleware('permission:role.delete');
    Route::get('/districts/{district}/talukas', [TalukaController::class, 'getByDistrict'])->name('talukas.by-district')->middleware('permission:role.read');
    
    Route::post('/villages', [VillageController::class, 'store'])->name('villages.store')->middleware('permission:role.create');
    Route::get('/villages/{village}', [VillageController::class, 'show'])->name('villages.show')->middleware('permission:role.read');
    Route::put('/villages/{village}', [VillageController::class, 'update'])->name('villages.update')->middleware('permission:role.update');
    Route::delete('/villages/{village}', [VillageController::class, 'destroy'])->name('villages.destroy')->middleware('permission:role.delete');
    Route::get('/talukas/{taluka}/villages', [VillageController::class, 'getByTaluka'])->name('villages.by-taluka')->middleware('permission:role.read');
    
    Route::post('/users', [UserController::class, 'store'])->name('users.store')->middleware('permission:user.create');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show')->middleware('permission:user.read');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update')->middleware('permission:user.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('permission:user.delete');
    Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active')->middleware('permission:user.update');
    
    // Profile routes (users can update their own profile)
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update.simple');
});

require __DIR__.'/settings.php';
