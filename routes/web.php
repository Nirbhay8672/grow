<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ManagementController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\LocalityController;
use App\Http\Controllers\TalukaController;
use App\Http\Controllers\VillageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MeasurementUnitController;
use App\Http\Controllers\BuilderController;
use App\Http\Controllers\ConstructionTypeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\PriorityTypeController;
use App\Http\Controllers\PropertySourceController;
use App\Http\Controllers\OwnerTypeController;
use App\Http\Controllers\FurnitureTypeController;
use App\Http\Controllers\PropertyZoneController;
use App\Http\Controllers\AmenityController;
use App\Http\Controllers\PropertyConstructionDocumentController;
use App\Http\Controllers\ProjectController;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::get('dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::get('management/locations', [ManagementController::class, 'locations'])->middleware(['auth', 'verified'])->name('management.locations');
Route::get('management/users', [ManagementController::class, 'users'])->middleware(['auth', 'verified'])->name('management.users');
Route::get('management/configuration', [ManagementController::class, 'configuration'])->middleware(['auth', 'verified'])->name('management.configuration');
Route::get('management/property-configuration', [ManagementController::class, 'propertyConfiguration'])->middleware(['auth', 'verified'])->name('management.property-configuration');

Route::get('profile', [UserController::class, 'showProfile'])->middleware(['auth', 'verified'])->name('profile');

Route::get('roles', [RoleController::class, 'showPage'])->middleware(['auth', 'verified', 'permission:role.read'])->name('roles.index');
Route::get('states', [StateController::class, 'showPage'])->middleware(['auth', 'verified', 'permission:role.read'])->name('states.index');
Route::get('cities', [CityController::class, 'showPage'])->middleware(['auth', 'verified', 'permission:role.read'])->name('cities.index');
Route::get('districts', [DistrictController::class, 'showPage'])->middleware(['auth', 'verified', 'permission:role.read'])->name('districts.index');
Route::get('localities', [LocalityController::class, 'showPage'])->middleware(['auth', 'verified', 'permission:role.read'])->name('localities.index');
Route::get('talukas', [TalukaController::class, 'showPage'])->middleware(['auth', 'verified', 'permission:role.read'])->name('talukas.index');
Route::get('villages', [VillageController::class, 'showPage'])->middleware(['auth', 'verified', 'permission:role.read'])->name('villages.index');
Route::get('users', [UserController::class, 'showPage'])->middleware(['auth', 'verified', 'permission:user.read'])->name('users.index');
Route::get('measurement-units', [MeasurementUnitController::class, 'showPage'])->middleware(['auth', 'verified', 'permission:role.read'])->name('measurement-units.index');
Route::get('builders', [BuilderController::class, 'showPage'])->middleware(['auth', 'verified', 'permission:role.read'])->name('builders.index');
Route::get('construction-types', [ConstructionTypeController::class, 'showPage'])->middleware(['auth', 'verified', 'permission:role.read'])->name('construction-types.index');
Route::get('categories', [CategoryController::class, 'showPage'])->middleware(['auth', 'verified', 'permission:role.read'])->name('categories.index');
Route::get('sub-categories', [SubCategoryController::class, 'showPage'])->middleware(['auth', 'verified', 'permission:role.read'])->name('sub-categories.index');
Route::get('priority-types', [PriorityTypeController::class, 'showPage'])->middleware(['auth', 'verified', 'permission:role.read'])->name('priority-types.index');
Route::get('property-sources', [PropertySourceController::class, 'showPage'])->middleware(['auth', 'verified', 'permission:role.read'])->name('property-sources.index');
Route::get('owner-types', [OwnerTypeController::class, 'showPage'])->middleware(['auth', 'verified', 'permission:role.read'])->name('owner-types.index');
Route::get('furniture-types', [FurnitureTypeController::class, 'showPage'])->middleware(['auth', 'verified', 'permission:role.read'])->name('furniture-types.index');
Route::get('property-zones', [PropertyZoneController::class, 'showPage'])->middleware(['auth', 'verified', 'permission:role.read'])->name('property-zones.index');
Route::get('amenities', [AmenityController::class, 'showPage'])->middleware(['auth', 'verified', 'permission:role.read'])->name('amenities.index');
Route::get('property-construction-documents', [PropertyConstructionDocumentController::class, 'showPage'])->middleware(['auth', 'verified', 'permission:role.read'])->name('property-construction-documents.index');
Route::get('projects', [ProjectController::class, 'index'])->middleware(['auth', 'verified'])->name('projects.index');
Route::get('projects/create', [ProjectController::class, 'create'])->middleware(['auth', 'verified'])->name('projects.create');
Route::post('projects', [ProjectController::class, 'store'])->middleware(['auth', 'verified'])->name('projects.store');
Route::get('projects/{project}/edit', [ProjectController::class, 'edit'])->middleware(['auth', 'verified'])->name('projects.edit');
Route::put('projects/{project}', [ProjectController::class, 'update'])->middleware(['auth', 'verified'])->name('projects.update');
Route::delete('projects/documents/{document}', [ProjectController::class, 'deleteDocument'])->middleware(['auth', 'verified'])->name('projects.documents.delete');

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
    
    Route::post('/measurement-units', [MeasurementUnitController::class, 'store'])->name('measurement-units.store')->middleware('permission:role.create');
    Route::get('/measurement-units/{measurementUnit}', [MeasurementUnitController::class, 'show'])->name('measurement-units.show')->middleware('permission:role.read');
    Route::put('/measurement-units/{measurementUnit}', [MeasurementUnitController::class, 'update'])->name('measurement-units.update')->middleware('permission:role.update');
    Route::delete('/measurement-units/{measurementUnit}', [MeasurementUnitController::class, 'destroy'])->name('measurement-units.destroy')->middleware('permission:role.delete');
    
    Route::post('/builders', [BuilderController::class, 'store'])->name('builders.store')->middleware('permission:role.create');
    Route::get('/builders/{builder}', [BuilderController::class, 'show'])->name('builders.show')->middleware('permission:role.read');
    Route::put('/builders/{builder}', [BuilderController::class, 'update'])->name('builders.update')->middleware('permission:role.update');
    Route::delete('/builders/{builder}', [BuilderController::class, 'destroy'])->name('builders.destroy')->middleware('permission:role.delete');
    
    Route::post('/construction-types', [ConstructionTypeController::class, 'store'])->name('construction-types.store')->middleware('permission:role.create');
    Route::get('/construction-types/{constructionType}', [ConstructionTypeController::class, 'show'])->name('construction-types.show')->middleware('permission:role.read');
    Route::put('/construction-types/{constructionType}', [ConstructionTypeController::class, 'update'])->name('construction-types.update')->middleware('permission:role.update');
    Route::delete('/construction-types/{constructionType}', [ConstructionTypeController::class, 'destroy'])->name('construction-types.destroy')->middleware('permission:role.delete');
    
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store')->middleware('permission:role.create');
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show')->middleware('permission:role.read');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update')->middleware('permission:role.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy')->middleware('permission:role.delete');
    
    Route::post('/sub-categories', [SubCategoryController::class, 'store'])->name('sub-categories.store')->middleware('permission:role.create');
    Route::get('/sub-categories/{subCategory}', [SubCategoryController::class, 'show'])->name('sub-categories.show')->middleware('permission:role.read');
    Route::put('/sub-categories/{subCategory}', [SubCategoryController::class, 'update'])->name('sub-categories.update')->middleware('permission:role.update');
    Route::delete('/sub-categories/{subCategory}', [SubCategoryController::class, 'destroy'])->name('sub-categories.destroy')->middleware('permission:role.delete');
    Route::get('/categories/{category}/sub-categories', [SubCategoryController::class, 'getByCategory'])->name('sub-categories.by-category')->middleware('permission:role.read');
    
    Route::post('/priority-types', [PriorityTypeController::class, 'store'])->name('priority-types.store')->middleware('permission:role.create');
    Route::get('/priority-types/{priorityType}', [PriorityTypeController::class, 'show'])->name('priority-types.show')->middleware('permission:role.read');
    Route::put('/priority-types/{priorityType}', [PriorityTypeController::class, 'update'])->name('priority-types.update')->middleware('permission:role.update');
    Route::delete('/priority-types/{priorityType}', [PriorityTypeController::class, 'destroy'])->name('priority-types.destroy')->middleware('permission:role.delete');
    
    Route::post('/property-sources', [PropertySourceController::class, 'store'])->name('property-sources.store')->middleware('permission:role.create');
    Route::get('/property-sources/{propertySource}', [PropertySourceController::class, 'show'])->name('property-sources.show')->middleware('permission:role.read');
    Route::put('/property-sources/{propertySource}', [PropertySourceController::class, 'update'])->name('property-sources.update')->middleware('permission:role.update');
    Route::delete('/property-sources/{propertySource}', [PropertySourceController::class, 'destroy'])->name('property-sources.destroy')->middleware('permission:role.delete');
    
    Route::post('/owner-types', [OwnerTypeController::class, 'store'])->name('owner-types.store')->middleware('permission:role.create');
    Route::get('/owner-types/{ownerType}', [OwnerTypeController::class, 'show'])->name('owner-types.show')->middleware('permission:role.read');
    Route::put('/owner-types/{ownerType}', [OwnerTypeController::class, 'update'])->name('owner-types.update')->middleware('permission:role.update');
    Route::delete('/owner-types/{ownerType}', [OwnerTypeController::class, 'destroy'])->name('owner-types.destroy')->middleware('permission:role.delete');
    
    Route::post('/furniture-types', [FurnitureTypeController::class, 'store'])->name('furniture-types.store')->middleware('permission:role.create');
    Route::get('/furniture-types/{furnitureType}', [FurnitureTypeController::class, 'show'])->name('furniture-types.show')->middleware('permission:role.read');
    Route::put('/furniture-types/{furnitureType}', [FurnitureTypeController::class, 'update'])->name('furniture-types.update')->middleware('permission:role.update');
    Route::delete('/furniture-types/{furnitureType}', [FurnitureTypeController::class, 'destroy'])->name('furniture-types.destroy')->middleware('permission:role.delete');
    
    Route::post('/property-zones', [PropertyZoneController::class, 'store'])->name('property-zones.store')->middleware('permission:role.create');
    Route::get('/property-zones/{propertyZone}', [PropertyZoneController::class, 'show'])->name('property-zones.show')->middleware('permission:role.read');
    Route::put('/property-zones/{propertyZone}', [PropertyZoneController::class, 'update'])->name('property-zones.update')->middleware('permission:role.update');
    Route::delete('/property-zones/{propertyZone}', [PropertyZoneController::class, 'destroy'])->name('property-zones.destroy')->middleware('permission:role.delete');
    
    Route::post('/amenities', [AmenityController::class, 'store'])->name('amenities.store')->middleware('permission:role.create');
    Route::get('/amenities/{amenity}', [AmenityController::class, 'show'])->name('amenities.show')->middleware('permission:role.read');
    Route::put('/amenities/{amenity}', [AmenityController::class, 'update'])->name('amenities.update')->middleware('permission:role.update');
    Route::delete('/amenities/{amenity}', [AmenityController::class, 'destroy'])->name('amenities.destroy')->middleware('permission:role.delete');
    
    Route::post('/property-construction-documents', [PropertyConstructionDocumentController::class, 'store'])->name('property-construction-documents.store')->middleware('permission:role.create');
    Route::get('/property-construction-documents/{propertyConstructionDocument}', [PropertyConstructionDocumentController::class, 'show'])->name('property-construction-documents.show')->middleware('permission:role.read');
    Route::put('/property-construction-documents/{propertyConstructionDocument}', [PropertyConstructionDocumentController::class, 'update'])->name('property-construction-documents.update')->middleware('permission:role.update');
    Route::delete('/property-construction-documents/{propertyConstructionDocument}', [PropertyConstructionDocumentController::class, 'destroy'])->name('property-construction-documents.destroy')->middleware('permission:role.delete');
    
    Route::get('/states/{state}/cities', [ProjectController::class, 'getCitiesByState'])->name('projects.cities-by-state')->middleware('permission:role.read');
    Route::get('/cities/{city}/localities', [ProjectController::class, 'getLocalitiesByCity'])->name('projects.localities-by-city')->middleware('permission:role.read');
    
    // Profile routes (users can update their own profile)
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update.simple');
});

require __DIR__.'/settings.php';