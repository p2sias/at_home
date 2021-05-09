<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{UserController, ChallengeController, FileController, PostController, SessionController, ScoreController, TutorialStepController, LoginController, ValidationController};
use App\Models\ChallengeFile;

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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::post('/login', [LoginController::class, 'authenticate'])->name('login');
Route::middleware('auth:api')->post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth:api')->post('user/checkUnique', [UserController::class, 'checkUnique']);
Route::middleware('auth:api')->post('user/{id}/changePassword', [UserController::class, 'changePassword']);

Route::middleware('auth:api')->get('/users', [UserController::class, 'index'])->name('api.users.list');
Route::middleware('auth:api')->get('/user/{id}', [UserController::class, 'show'])->name('api.user.show');
Route::post('/user/create', [UserController::class, 'store'])->name('api.users.create');
Route::middleware('auth:api')->post('/user/{id}/update', [UserController::class, 'update'])->name('api.user.update');
Route::middleware('auth:api')->get('/user/{id}/delete', [UserController::class, 'destroy'])->name('api.user.delete');

Route::middleware('auth:api')->get('/sessions', [SessionController::class, 'index'])->name('api.sessions.list');
Route::middleware('auth:api')->get('/session/{id}', [SessionController::class, 'show'])->name('api.session.show');
Route::middleware('auth:api')->post('/session/create', [SessionController::class, 'store'])->name('api.session.create');
Route::middleware('auth:api')->post('/session/{id}/update', [SessionController::class, 'update'])->name('api.session.update');
Route::middleware('auth:api')->get('/session/{id}/delete', [SessionController::class, 'destroy'])->name('api.session.delete');
Route::middleware('auth:api')->get('/session/{id}/validations', [SessionController::class, 'getValidations'])->name('api.session.validations');

Route::middleware('auth:api')->post('/session/{id}/join', [UserController::class, 'joinSession'])->name('api.session.join');

Route::middleware('auth:api')->get('/challenges', [ChallengeController::class, 'index'])->name('api.challenges.list');
Route::middleware('auth:api')->get('/challenge/{id}', [ChallengeController::class, 'show'])->name('api.challenge.show');
Route::middleware('auth:api')->post('/challenge/create', [ChallengeController::class, 'store'])->name('api.challenge.create');
Route::middleware('auth:api')->post('/challenge/{id}/update', [ChallengeController::class, 'update'])->name('api.challenge.update');
Route::middleware('auth:api')->get('/challenge/{id}/delete', [ChallengeController::class, 'destroy'])->name('api.challenge.delete');

Route::middleware('auth:api')->post('/challenge/{id}/join', [UserController::class, 'joinChallenge'])->name('api.challenge.join');
Route::middleware('auth:api')->post('/challenge/{id}/leave', [UserController::class, 'leaveChallenge'])->name('api.challenge.leave');

Route::middleware('auth:api')->post('/challenge/{id}/file/create', [FileController::class, 'postFile'])->name('api.challenge.file');
Route::middleware('auth:api')->get('/challenge/file/{user_id}', [FileController::class, 'getFilesByUser'])->name('api.challenge.file.get');

Route::middleware('auth:api')->post('/challenge/file/{file_id}/delete', [FileController::class, 'deleteFile'])->name('api.challenge.file.delete');



Route::middleware('auth:api')->get('/posts', [PostController::class, 'index'])->name('api.post.list');
Route::middleware('auth:api')->get('/post/{id}', [PostController::class, 'show'])->name('api.post.show');
Route::middleware('auth:api')->post('/post/create', [PostController::class, 'store'])->name('api.post.create');
Route::middleware('auth:api')->post('/post/{id}/update', [PostController::class, 'update'])->name('api.post.update');
Route::middleware('auth:api')->get('/post/{id}/delete', [PostController::class, 'destroy'])->name('api.post.delete');

Route::get('/posts/latest', [PostController::class, 'getLatestPosts']);

Route::middleware('auth:api')->post('/score/create', [ScoreController::class, 'create'])->name('api.score.create');

Route::middleware('auth:api')->get('/tutorials', [TutorialStepController::class, 'index'])->name('api.tutorial.list');
Route::middleware('auth:api')->get('/tutorial/{id}', [TutorialStepController::class, 'show'])->name('api.tutorial.show');
Route::middleware('auth:api')->post('/tutorial/create', [TutorialStepController::class, 'store'])->name('api.tutorial.create');
Route::middleware('auth:api')->post('/tutorial/{id}/update', [TutorialStepController::class, 'update'])->name('api.tutorial.update');
Route::middleware('auth:api')->get('/tutorial/{id}/delete', [TutorialStepController::class, 'destroy'])->name('api.tutorial.delete');


Route::middleware('auth:api')->get('/validations', [ValidationController::class, 'index'])->name('api.validation.list');
Route::middleware('auth:api')->get('/validation/{id}', [ValidationController::class, 'show'])->name('api.validation.show');
Route::middleware('auth:api')->post('/challenge/{id}/validate', [ValidationController::class, 'store'])->name('api.challenge.validate');
Route::middleware('auth:api')->post('/validation/{id}/update', [ValidationController::class, 'update'])->name('api.validation.update');
Route::middleware('auth:api')->get('/validation/{id}/delete', [ValidationController::class, 'destroy'])->name('api.validation.delete');


Route::middleware('auth:api')->post('/validation/{id}/validate', [ValidationController::class, 'validateValidation'])->name('api.validation.validate');

Route::middleware('auth:api')->post('/validation/getByUser', [ValidationController::class, 'getValidationByUser'])->name('api.validation.getByUser');
Route::middleware('auth:api')->post('/validation/getValidatingChallenges', [ValidationController::class, 'getValidatingChallengesByUser'])->name('api.validation.getValidatingChallenge');