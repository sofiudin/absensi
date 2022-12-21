<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Attendance;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get Data Kunjungan
        $kunjungans = DB::table('attendances')
            ->join('members', 'attendances.nim', '=', 'members.nim')
            ->select('members.nim', 'members.nama', 'members.jurusan','members.angkatan','members.email','attendances.kegiatan','attendances.created_at as WaktuScan')
            ->get();

        // Side Activr Navigation
        $sideactive = 'Kunjungan';
        return view('pages.kunjungan',compact('sideactive'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function ScanSend(Request $request)
    {

        $attendance = new Attendance;
        $attendance->nim = $request->nim;
        $attendance->kegiatan = $request->kegiatan;
        $affected = $attendance->save();

        if($affected){
            return response()->json(['success'=>true]);
        }else{
            return response()->json(['success'=>false]);
        }
    }

    public function FilterDate(Request $request)
    {

        $kunjungans = DB::table('attendances')
            ->join('members', 'attendances.nim', '=', 'members.nim')
            ->select('members.nim', 'members.nama', 'members.jurusan','members.angkatan','members.email','attendances.kegiatan','attendances.created_at as WaktuScan')
            ->whereBetween('attendances.created_at', [$request->TglMulai, date('Y-m-d H:i:s', strtotime($request->TglSampai . ' +1 day'))])
            ->get();

            // Create Var
            $tbody ='';
            $i = 1;

            foreach($kunjungans as $kunjungan):
                $row  = '<tr>';
                $row .= '<td>'. $i.'</td>';
                $row .= '<td>'. $kunjungan->nim.'</td>';
                $row .= '<td>'. $kunjungan->nama.'</td>';
                $row .= '<td>'. $kunjungan->jurusan.'</td>';
                $row .= '<td>'. $kunjungan->angkatan.'</td>';
                $row .= '<td>'. $kunjungan->email.'</td>';
                $row .= '<td>'. $kunjungan->kegiatan.'</td>';
                $row .= '<td>'. $kunjungan->WaktuScan.'</td>';
                $row .= '</tr>';

                $tbody .= $row;
                $i++;
            endforeach;

            return response()->json(['success' => true,'html_Tbody'=>$tbody]);
    }
}
