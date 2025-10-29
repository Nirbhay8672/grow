<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\CityController;
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

Route::get('profile', function () {
    $user = auth()->user()->load(['state', 'city']);
    $states = \App\Models\State::where('is_active', true)->orderBy('name')->get();
    
    return Inertia::render('Profile', [
        'user' => $user,
        'states' => $states,
    ]);
})->middleware(['auth', 'verified'])->name('profile');

Route::get('roles', function () {
    $roles = \Spatie\Permission\Models\Role::with('permissions')->get();
    $permissions = \Spatie\Permission\Models\Permission::all();
    
    return Inertia::render('Roles', [
        'roles' => $roles,
        'permissions' => $permissions,
    ]);
})->middleware(['auth', 'verified', 'permission:role.read'])->name('roles.index');

Route::get('states', function () {
    $states = \App\Models\State::orderBy('name')->get();
    
    return Inertia::render('States', [
        'states' => $states,
    ]);
})->middleware(['auth', 'verified', 'permission:role.read'])->name('states.index');

Route::get('cities', function () {
    $cities = \App\Models\City::with('state')->orderBy('name')->get();
    $states = \App\Models\State::where('is_active', true)->orderBy('name')->get();
    
    return Inertia::render('Cities', [
        'cities' => $cities,
        'states' => $states,
    ]);
})->middleware(['auth', 'verified', 'permission:role.read'])->name('cities.index');

Route::get('users', function () {
    $users = \App\Models\User::with(['state', 'city', 'roles'])->orderBy('created_at', 'desc')->get();
    $states = \App\Models\State::where('is_active', true)->orderBy('name')->get();
    
    return Inertia::render('Users', [
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
    
    Route::post('/users', [UserController::class, 'store'])->name('users.store')->middleware('permission:user.create');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show')->middleware('permission:user.read');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update')->middleware('permission:user.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('permission:user.delete');
    Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active')->middleware('permission:user.update');
    
    // Profile routes (users can update their own profile)
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update.simple');
});

require __DIR__.'/settings.php';
