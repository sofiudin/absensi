<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Http\Controllers\AuthController;

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

Route::get('qrcode', function () {
    return QrCode::size(300)->format('png')->generate('A basic example of QR code!','../public/qrcodes/qrcode.png');
});

Route::get('/laravel', function () {
    return view('welcome');
});

// Auth Controller
Route::get('/', [AuthController::class, 'showFormLogin'])->name('login');
Route::get('login', [AuthController::class, 'showFormLogin'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::get('register', [AuthController::class, 'showFormRegister'])->name('register');
Route::post('register', [AuthController::class, 'register']);

Route::group(['middleware' => 'auth'], function () {

    // Route Dashboard
    Route::get('/dashboard', [ DashboardController::class, 'index' ])->name('home');

    // Route Master Anggota
    Route::resource('anggota', MemberController::class);
    Route::post('anggota/read', [ MemberController::class, 'ReadData' ])->name('anggota.read');
    Route::get('anggota/hapus/{anggotum}', [ MemberController::class, 'HapusData' ])->name('anggota.hapus');

    // Route Kunjungan Laboratorium
    Route::get('/kunjungan', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/kunjungan/filter', [AttendanceController::class, 'FilterDate'])->name('attendance.filter');

    // Route Scan
    Route::post('scanAbsen', [ AttendanceController::class, 'ScanSend' ])->name('attendance.scansend');

    // Route Auth
    Route::get('register', [AuthController::class, 'showFormRegister'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
});


// Route Scan
Route::get('/scan', function () {return view('scanpengunjung');})->name('attendance.scan');
