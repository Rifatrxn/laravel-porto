<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman publik
|--------------------------------------------------------------------------
*/
Route::get('/', [\App\Http\Controllers\PortfolioController::class, 'index'])->name('portfolio.home');
Route::get('/projects', [\App\Http\Controllers\PortfolioController::class, 'projects'])->name('portfolio.projects');

/*
 * Model portofolio bukan Eloquent (datanya di KroomBase), jadi parameter rute
 * di-resolve manual. Satu resolver untuk dua kebutuhan:
 *  - halaman publik memakai slug  (/projects/sistem-informasi-akademik)
 *  - panel admin memakai id       (/admin/projects/3/edit)
 */
Route::bind('project', function (string $value) {
    return is_numeric($value)
        ? (\App\Models\Project::find($value) ?? abort(404))
        : (\App\Models\Project::findBySlug($value) ?? abort(404));
});

Route::get('/projects/{project}', [\App\Http\Controllers\PortfolioController::class, 'project'])
    ->name('portfolio.project');
Route::post('/contact', [\App\Http\Controllers\PortfolioController::class, 'contact'])
    ->middleware('throttle:10,1')
    ->name('portfolio.contact');

/*
|--------------------------------------------------------------------------
| Autentikasi — diakses lewat "jalur tikus"
|--------------------------------------------------------------------------
| Halaman masuk TIDAK lagi di /login. Alamatnya diambil dari
| PORTFOLIO_LOGIN_PATH di .env, jadi pengunjung yang mencoba /login biasa
| akan mendapat 404. Nama rute tetap 'login' supaya middleware auth dan
| redirect()->route('login') di seluruh aplikasi tidak perlu diubah.
*/
$loginPath = config('portfolio.login_path');

Route::get($loginPath, [AuthController::class, 'showLogin'])->middleware('guest')->name('login');
Route::post($loginPath, [AuthController::class, 'login'])->middleware('guest')->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Panel admin — juga lewat jalur tersembunyi (PORTFOLIO_ADMIN_PATH)
|--------------------------------------------------------------------------
| Model portofolio bukan Eloquent, jadi setiap parameter rute di-resolve
| manual lewat Route::bind() dengan kunci utama (id) dari KroomBase.
*/
Route::prefix(config('portfolio.admin_path'))
    ->middleware('auth')
    ->name('admin.')
    ->group(function () {

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Profil (satu halaman form)
        Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');

        // Pengaturan tampilan
        Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
        Route::delete('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'reset'])->name('settings.reset');

        // Menyalakan/mematikan satu saklar saja, langsung tersimpan.
        // Dipakai tombol di grup "Bagian Halaman" dan "Animasi" supaya satu
        // klik tidak bisa mengubah saklar lain.
        Route::post('/settings/toggle/{key}', [\App\Http\Controllers\Admin\SettingController::class, 'toggle'])
            ->name('settings.toggle');

        Route::resource('projects', \App\Http\Controllers\Admin\ProjectController::class)
            ->except('show')
            ->parameters(['projects' => 'project']);

        Route::resource('skills', \App\Http\Controllers\Admin\SkillController::class)
            ->except('show');
        Route::bind('skill', fn (string $id) => \App\Models\Skill::find($id) ?? abort(404));

        Route::resource('experiences', \App\Http\Controllers\Admin\ExperienceController::class)
            ->except('show')
            ->parameters(['experiences' => 'experience']);
        Route::bind('experience', fn (string $id) => \App\Models\Experience::find($id) ?? abort(404));

        Route::resource('educations', \App\Http\Controllers\Admin\EducationController::class)
            ->except('show')
            ->parameters(['educations' => 'education']);
        Route::bind('education', fn (string $id) => \App\Models\Education::find($id) ?? abort(404));

        Route::resource('services', \App\Http\Controllers\Admin\ServiceController::class)
            ->except('show');
        Route::bind('service', fn (string $id) => \App\Models\Service::find($id) ?? abort(404));

        Route::resource('testimonials', \App\Http\Controllers\Admin\TestimonialController::class)
            ->except('show');
        Route::bind('testimonial', fn (string $id) => \App\Models\Testimonial::find($id) ?? abort(404));

        Route::resource('messages', \App\Http\Controllers\Admin\MessageController::class)
            ->only(['index', 'show', 'update', 'destroy'])
            ->parameters(['messages' => 'message']);
        Route::bind('message', fn (string $id) => \App\Models\Message::find($id) ?? abort(404));

        /*
         * Kelola akun panel — hanya untuk peran 'admin'.
         * Editor tidak melihat menunya dan tidak bisa membukanya (403).
         */
        Route::middleware('role:admin')->group(function () {
            Route::resource('users', \App\Http\Controllers\Admin\UserController::class)
                ->except('show');
        });

        Route::bind('user', fn (string $id) => \App\Models\User::find($id) ?? abort(404));
    });
