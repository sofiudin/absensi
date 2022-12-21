<?php

namespace App\Http\Controllers;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use DB;
use App\Models\Member;
use Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Mengambil Keseluruhan data anggota
        $Members = Member::orderBy('created_at', 'DESC')->get();

        // Side Activr Navigation
        $sideactive = 'MasterAnggota';
        return view('pages.anggota', compact('Members', 'sideactive'));
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
        $rules = [
            'nama' => 'required',
            'email' => 'required|email',
            'nim' => 'required|max:10',
            'jurusan' => 'required',
            'angkatan' => 'required',
        ];

        $messages = [
            'nama.required'          => 'Nama wajib diisi.',
            'email.required'         => 'Email wajib diisi.',
            'email.email'            => 'Email tidak valid.',
            'nim.required'           => 'NIM wajib diisi.',
            'nim.max'                => 'Panjang NIM Maksimal 10.',
            'jurusan.required'       => 'Jurusan wajib diisi.',
            'angkatan.required'      => 'Angkatan wajib diisi.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }

        // Cek apakah terdapat NIM yg sama Pada DB
        $CekNIM = Member::where('nim', '=', $request->nim)->get();
        $NIMCount = $CekNIM->count();

        if ($NIMCount > 0) {
            return response()->json(['success' => false, 'message' => 'NIM Anggota Sudah Terdaftar']);
        }

        $member           = new Member;
        $member->nama     = $request->nama;
        $member->email    = $request->email;
        $member->nim      = $request->nim;
        $member->jurusan  = $request->jurusan;
        $member->angkatan = $request->angkatan;
        $member->save();

        // Create QR Code
        $pathFile = '../public/qrcodes/' . $request->nim . '.png';
        QrCode::size(300)->format('png')->generate($request->nim, $pathFile);

        return response()->json(['success' => true]);
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
        $Member = Member::find($id);
        $sideactive = 'MasterAnggota';
        return view('pages.edit_anggota', compact('Member', 'sideactive'));
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
        $Member = Member::find($id);

        // Cek apakah terdapat NIM yg sama Pada DB
        if ($request->nim_edit != $request->nim_lama) {
            $CekNIM = Member::where('nim', '=', $request->nim_edit)->get();
            $NIMCount = $CekNIM->count();

            if ($NIMCount > 0) {
                Toastr::warning('NIM Anggota Sudah Terdaftar, Gagal Edit Data !', 'Edit Data Anggota');
                return redirect()->route('anggota.index');
            }
        }

        $Member->nama = $request->nama_edit;
        $Member->email = $request->email_edit;
        $Member->nim = $request->nim_edit;
        $Member->jurusan = $request->jurusan_edit;
        $Member->angkatan = $request->angkatan_edit;

        $Member->save();

        if ($Member->wasChanged() == true) {
            Toastr::success('Data Berhasil Dirubah !', 'Edit Data Anggota');
        } else {
            Toastr::warning('Data Tidak Dirubah !', 'Edit Data Anggota');
        }
        return redirect()->route('anggota.index');
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

    public function ReadData()
    {
        $Members = Member::all();
        $tbody = '';
        $i = 1;

        foreach ($Members as $member) :

            // Create Href Action Edit
            $urlEdit = '/anggota/' . $member->id_member . '/edit';
            $edit_action = url($urlEdit);

            // Create Href Action Delete
            $urlDelete = '/anggota/hapus/' . $member->id_member;
            $delete_action = url($urlDelete);

            // Create Href Download QrCode
            $QrCode = 'qrcodes/' . $member->nim . '.png';
            $UrlQrCode = url($QrCode);

            $row  = '<tr>';
            $row .= '<td>' . $i . '</td>';
            $row .= '<td>' . $member->nama . '</td>';
            $row .= '<td>' . $member->nim . '</td>';
            $row .= '<td>' . $member->jurusan . '</td>';
            $row .= '<td>' . $member->angkatan . '</td>';
            $row .= '<td>
            <a href="' . $UrlQrCode . '" class="mb-2 btn btn-success" download><i class="fa fa-qrcode"></i></a>
            <a href="#" class="mb-2 btn btn-info" onclick="PrintImage(' . $member->nim . ')"><i class="fa fa-qrcode"></i></a>
            <a href="' . $edit_action . '" class="mb-2 btn btn-warning"><i class="fa fa-edit"></i></a>
            <a href="' . $delete_action . '" class="mb-2 btn btn-danger"><i class="fa fa-trash"></i></a>
            </td>';
            $row .= '</tr>';

            $tbody .= $row;
            $i++;
        endforeach;

        return response()->json(['success' => true, 'html_Tbody' => $tbody]);
    }

    public function HapusData($id)
    {
        $member = Member::findOrFail($id);
        if ($member->delete() == true) {
            Toastr::error('Data Berhasil Dihapus !', 'Hapus Data Anggota');
        } else {
            Toastr::warning('Data Gagal Dihapus !', 'Hapus Data Anggota');
        }
        return redirect()->route('anggota.index');
    }
}
