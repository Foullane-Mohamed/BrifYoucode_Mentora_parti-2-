<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\SubcategoryController;
use App\Http\Controllers\API\TagController;
use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\VideoController;
use App\Http\Controllers\API\EnrollmentController;
use App\Http\Controllers\API\StatisticsController;

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

Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::get('/categories', [CategoryController::class, 'getActiveCategories']);
    Route::get('/categories/{slug}', [CategoryController::class, 'getBySlug']);
    Route::get('/categories/with-subcategories', [CategoryController::class, 'getWithSubcategories']);
    
    Route::get('/subcategories', [SubcategoryController::class, 'getActiveSubcategories']);
    Route::get('/subcategories/{slug}', [SubcategoryController::class, 'getBySlug']);
    Route::get('/categories/{categoryId}/subcategories', [SubcategoryController::class, 'getByCategory']);
    
    Route::get('/tags', [TagController::class, 'index']);
    Route::get('/tags/{slug}', [TagController::class, 'getBySlug']);
    Route::get('/tags/popular', [TagController::class, 'getPopular']);
    
    Route::get('/courses', [CourseController::class, 'getPublished']);
    Route::get('/courses/{slug}', [CourseController::class, 'getBySlug']);
    Route::get('/courses/search', [CourseController::class, 'search']);
    Route::get('/mentors/{mentorId}/courses', [CourseController::class, 'getByMentor']);
});

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    Route::get('/users/role/{role}', [UserController::class, 'getUsersByRole']);
    Route::put('/profile', [UserController::class, 'updateProfile']);
    
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
    Route::get('/categories/{id}/courses', [CategoryController::class, 'getWithCourses']);
    
    Route::post('/subcategories', [SubcategoryController::class, 'store']);
    Route::get('/subcategories/{id}', [SubcategoryController::class, 'show']);
    Route::put('/subcategories/{id}', [SubcategoryController::class, 'update']);
    Route::delete('/subcategories/{id}', [SubcategoryController::class, 'destroy']);
    Route::get('/subcategories/{id}/courses', [SubcategoryController::class, 'getWithCourses']);
    
    Route::post('/tags', [TagController::class, 'store']);
    Route::get('/tags/{id}', [TagController::class, 'show']);
    Route::put('/tags/{id}', [TagController::class, 'update']);
    Route::delete('/tags/{id}', [TagController::class, 'destroy']);
    Route::get('/tags/{id}/courses', [TagController::class, 'getWithCourses']);
    Route::post('/courses/{courseId}/tags', [TagController::class, 'syncWithCourse']);
    
    Route::get('/courses-all', [CourseController::class, 'index']);
    Route::post('/courses', [CourseController::class, 'store']);
    Route::get('/courses/{id}', [CourseController::class, 'show']);
    Route::put('/courses/{id}', [CourseController::class, 'update']);
    Route::delete('/courses/{id}', [CourseController::class, 'destroy']);
    Route::get('/courses/{id}/videos', [CourseController::class, 'getWithVideos']);
    Route::get('/courses/{id}/tags', [CourseController::class, 'getWithTags']);
    Route::get('/courses/{id}/enrollments', [CourseController::class, 'getWithEnrollments']);
    Route::put('/courses/{id}/publish', [CourseController::class, 'publish']);
    Route::put('/courses/{id}/unpublish', [CourseController::class, 'unpublish']);
    
    Route::get('/videos', [VideoController::class, 'index']);
    Route::post('/videos', [VideoController::class, 'store']);
    Route::get('/videos/{id}', [VideoController::class, 'show']);
    Route::put('/videos/{id}', [VideoController::class, 'update']);
    Route::delete('/videos/{id}', [VideoController::class, 'destroy']);
    Route::get('/courses/{courseId}/videos', [VideoController::class, 'getByCourse']);
    Route::put('/videos/{id}/order', [VideoController::class, 'updateOrder']);
    Route::put('/courses/{courseId}/videos/reorder', [VideoController::class, 'reorder']);
    
    Route::get('/enrollments', [EnrollmentController::class, 'index']);
    Route::post('/enrollments', [EnrollmentController::class, 'store']);
    Route::get('/enrollments/{id}', [EnrollmentController::class, 'show']);
    Route::put('/enrollments/{id}', [EnrollmentController::class, 'update']);
    Route::delete('/enrollments/{id}', [EnrollmentController::class, 'destroy']);
    Route::get('/users/{userId}/enrollments', [EnrollmentController::class, 'getByUser']);
    Route::get('/courses/{courseId}/enrollments', [EnrollmentController::class, 'getByCourse']);
    Route::get('/enrollments/status/{status}', [EnrollmentController::class, 'getByStatus']);
    Route::put('/enrollments/{id}/approve', [EnrollmentController::class, 'approve']);
    Route::put('/enrollments/{id}/reject', [EnrollmentController::class, 'reject']);
    Route::put('/enrollments/{id}/progress', [EnrollmentController::class, 'updateProgress']);
    
    Route::get('/statistics/overall', [StatisticsController::class, 'getOverallStatistics']);
    Route::get('/statistics/categories', [StatisticsController::class, 'getCategoryStatistics']);
    Route::get('/statistics/tags', [StatisticsController::class, 'getTagStatistics']);
    Route::get('/statistics/enrollments', [StatisticsController::class, 'getEnrollmentStatistics']);
    Route::get('/statistics/enrollments/monthly', [StatisticsController::class, 'getMonthlyEnrollmentStatistics']);
    Route::get('/statistics/courses/by-level', [StatisticsController::class, 'getCourseStatisticsByLevel']);
});