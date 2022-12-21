<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Attendance;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Collect data
        $CountAnggota = Member::count();
        $CountKunjungan = Attendance::count();

        $kunjungans = DB::table('attendances')
            ->join('members', 'attendances.nim', '=', 'members.nim')
            ->select('members.nim', 'members.nama', 'members.jurusan','members.angkatan','members.email','attendances.kegiatan','attendances.created_at as WaktuScan')
            ->take(5)
            ->orderBy('attendances.created_at', 'desc')
            ->get();
        // Data
        $data = array(
            'CountAnggota' => $CountAnggota,
            'CountKunjungan' => $CountKunjungan,
            'Kunjungans' => $kunjungans
        );
        // Side Activr Navigation
        $sideactive = 'dashboard';
        return view('pages.dashboard',compact('sideactive','data'));
    }
}
