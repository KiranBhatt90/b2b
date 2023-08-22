<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonPlanController;

use App\Http\Controllers\Notification;
use App\Http\Controllers\ProgramController;

use App\Http\Controllers\ReportController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WebPage;use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::middleware('isLogin')->get('/', function () {
 return redirect(route('login'));
});

Route::get('home', [AuthController::class, 'dashboard']);
Route::get('signout', [AuthController::class, 'signout'])->name('signout');

//Student Routes//

Route::get('manage-student', [StudentController::class, 'index'])->name('student.list');
Route::get('create-student', [StudentController::class, 'createStudent'])->name('student.add');
Route::post('add-student', [StudentController::class, 'addStudent'])->name('student.store');
Route::get('edit-student', [StudentController::class, 'editStudent'])->name('student.edit');
Route::post('update-student', [StudentController::class, 'updateStudent'])->name('student.update');
Route::post('remove-student', [StudentController::class, 'destroy'])->name('student.remove');
Route::post('reset-password', [StudentController::class,'resetPassword'])->name('student.password');
Route::post('student-status', [StudentController::class,'change_user_status'])->name('student.status');
Route::get('student-upload', [StudentController::class,'uploadStudent'])->name('student.bulkUploadForm');
Route::post('students/process-bulk-upload', [StudentController::class, 'import'])->name('student.import');

Route::middleware('isLogin')->controller(AuthController::class)->group(function () {
 Route::get('login', 'index')->name('login');
 Route::post('process-login', 'authuser')->name('login.process');
});

Route::middleware(['auth', 'isAdmin'])->get('master-dashboard', [AuthController::class, 'AdminDash'])->name('admin-dashboard');
Route::middleware('auth')->get('dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

Route::middleware(['auth', 'isAdmin'])->prefix('school')->controller(SchoolController::class)->group(function () {
 Route::get('manage-school', 'index')->name('school.list');
 Route::get('add-new-school', 'addschool')->name('school.add');
 Route::get('update-school', 'editschool')->name('school.edit');
 Route::post('remove-school', 'destroy')->name('school.remove');
 Route::post('preview-school', 'previewSchool')->name('school.preview');
 Route::post('school-add', 'store')->name('school.store');
 Route::post('school-edit', 'edit')->name('school.update');
 Route::post('school-status', 'change_status')->name('school.status');
 Route::post('school-demo-status-update', 'change_school_demo_status')->name('school.demo.status');
 Route::post('fetch-cities', 'CityList')->name('city.json');
});

Route::middleware(['auth', 'isAdmin'])->prefix('course')->controller(CourseController::class)->group(function () {
 Route::get('manage-course', 'index')->name('course.list');
 Route::get('add-course', 'addcourse')->name('course.add');
 Route::get('update-course', 'editcourse')->name('course.edit');
 Route::post('remove-course', 'destroy')->name('course.remove');
 Route::post('course-add', 'store')->name('course.store');
 Route::post('course-edit', 'edit')->name('course.update');
 Route::post('course-status', 'change_status')->name('course.status');
});

Route::middleware(['auth', 'isAdmin'])->prefix('lesson-plan')->controller(LessonPlanController::class)->group(function () {
 Route::get('manage-lesson-plan', 'index')->name('lesson.plan.list');
 Route::get('add-lesson-plan', 'addlessonplan')->name('lesson.plan.add');
 Route::get('update-lesson-plan', 'editlessonplan')->name('lesson.plan.edit');
 Route::post('remove-lesson-plan', 'destroy')->name('lesson.plan.remove');
 Route::post('lesson-plan-add', 'store')->name('lesson.plan.store');
 Route::post('lesson-plan-edit', 'edit')->name('lesson.plan.update');

 Route::get('sort-instruction-module', 'sortLessonPlan')->name('lesson.plan.sorting');
 Route::post('sort-update-lesson-module', 'updateSortingNumber')->name('lesson.plan.sorting.update');
 Route::post('lesson-plan-demo-status', 'change_demo_status')->name('lesson.demo.status');
 Route::post('lesson-status', 'change_status')->name('lesson.status');
});

Route::middleware(['auth', 'isAdmin'])->prefix('grade')->controller(ProgramController::class)->group(function () {
 Route::get('manage-grade', 'index')->name('program.list');
 Route::get('add-grade', 'addprogram')->name('program.add');
 Route::get('update-grade', 'editprogram')->name('program.edit');
 Route::post('remove-grade', 'destroy')->name('program.remove');
 Route::post('grade-add', 'store')->name('program.store');
 Route::post('grade-edit', 'edit')->name('program.update');
 Route::post('grade-status', 'change_status')->name('grade.status');
});

Route::middleware('auth')->prefix('school')->controller(AuthController::class)->group(function () {
    Route::get('manage-teacher', 'userlist')->name('teacher.list');
    Route::get('add-teacher', 'addUser')->name('teacher.add');
    Route::get('update-teacher', 'updateUser')->name('teacher.edit');
    Route::post('teacher-remove', 'destroy')->name('teacher.remove');
    Route::post('teacher-add', 'createuser')->name('teacher.store');
    Route::post('teacher-edit', 'edituser')->name('teacher.update');
    Route::get('teacher-list', 'teacherList')->name('school.teacher.list');
    Route::post('reset-password', 'resetPassword')->name('user.password');
    Route::get('manage-school-admin', 'SchoolAdmin')->name('school.admin');
    Route::get('update-school-admin/{userid}', 'updateAdminUser')->name('school.admin.edit');
});

Route::middleware(['auth', 'isAdmin'])->prefix('whats-new')->controller(Notification::class)->group(function () {
 Route::get('manage-notification', 'index')->name('notify.list');
 Route::get('add-notification', 'addnewNotification')->name('notify.add');
 Route::post('add-update-notify', 'addUpdateNotify')->name('notify.store');
 Route::post('remove-notify', 'destroy')->name('notify.remove');
 Route::get('view-whats-new', 'viewNotify')->name('notify.schoolview')->withoutMiddleware('isAdmin');
});

Route::middleware(['auth', 'isAdmin'])->prefix('users')->controller(UserController::class)->group(function () {
 Route::get('manage-users', 'index')->name('users.admin.list');
 Route::get('add-master-user', 'AddAdminUser')->name('users.admin.add');
 Route::get('update-master-user', 'editAdminUser')->name('users.admin.edit');
 Route::post('store-master-user', 'AddUpdateAdminUser')->name('users.admin.store');
 Route::post('master-user-remove', 'destroy')->name('users.admin.remove');
});

/** ============= SCHOOL ROUTES ============= */

/** ============= TEACHER ROUTES ============= */
Route::middleware(['auth', 'isTeacher'])->prefix('teacher')->group(function () {
 Route::get('class-list', [ProgramController::class, 'TeacherClasslist'])->name('teacher.class.list');
 Route::get('course-list/{class}', [WebPage::class, 'courselist'])->name('teacher.course.list');
 Route::get('lesson-plan-list/{classid}/{course}', [WebPage::class, 'lessonPlan'])->name('teacher.lesson.list');
 Route::post('save-plan-report', [WebPage::class, 'setUserReport'])->name('report.save.plan');
});

Route::middleware(['auth', 'isTeacher'])->prefix('reports')->controller(ReportController::class)->group(function () {
 Route::get('view-engagement', 'index')->name('report.school.view');
 Route::get('view-history', 'viewTeacherSummary')->name('teacher.class.history');
 Route::post('view-grade-wise-history', 'viewTeacherGradeSummary')->name('teacher.grade.course.history');
});

/** ============= COMMON ROUTES ============= */
Route::middleware(['auth'])->prefix('school')->controller(SchoolController::class)->group(function () {
 Route::post('teacher-status', 'change_user_status')->name('teacher.status');
 Route::get('users-logs-list/{userid}', 'viewLogs')->name('user.logs.list');
});

Route::middleware('auth')->get('ajax-notify-list', [Notification::class, 'viewNotifyList'])->name('ajax.notify.list');

Route::get('get-demo', [WebPage::class, 'getDemo'])->name('get.demo.page');
Route::get('send-mail', function () {
 $details = [
  'view'    => 'emails.test',
  'subject' => 'Test Mail form LMS',
  'title'   => 'Mail from Valuez School',
  'body'    => 'This is for testing email using smtp',
 ];
 \Mail::to('itrahul.com@gmail.com')->send(new \App\Mail\TestMail($details));
 dd("Email is Sent.");
});
