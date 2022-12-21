{{-- Template Interface --}}
@extends('layouts.app')

{{-- Title --}}
@section('title','Dashboard')


{{-- Header Content --}}
@section('IconHeaderContent','fa fa-home')
@section('TitleHeaderContent','DASHBOARD')
@section('InformationHeaderContent','Selamat Datang Di Aplikasi Absensi Laboratorium.')

{{-- Body Content --}}
@section('content')

{{-- Row 1 --}}
<div class="row">
    <div class="col-md-6 col-xl-6">
        <div class="card mb-3 widget-content bg-arielle-smile">
            <div class="widget-content-wrapper text-white">
                <div class="widget-content-left">
                    <div class="widget-heading">Anggota Laboratorium</div>
                    <div class="widget-subheading">Total Anggota</div>
                </div>
                <div class="widget-content-right">
                    <div class="widget-numbers text-white"><span>{{$data['CountAnggota']}}</span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-6">
        <div class="card mb-3 widget-content bg-grow-early">
            <div class="widget-content-wrapper text-white">
                <div class="widget-content-left">
                    <div class="widget-heading">Pengunjung Laboratorium</div>
                    <div class="widget-subheading">Total Pengunjung Hari Ini</div>
                </div>
                <div class="widget-content-right">
                    <div class="widget-numbers text-white"><span>{{$data['CountKunjungan']}}</span></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Row 2 --}}
<div class="row">
    <div class="col-lg-12">
        <div class="main-card mb-3 card">
            <div class="card-body"><h5 class="card-title">Pengunjung Laboratorium (5 Terakhir)</h5>
                <div class="table-responsive">
                    <table class="mb-0 table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>NIM</th>
                                <th>Nama Anggota</th>
                                <th>Jurusan</th>
                                <th>Angkatan</th>
                                <th>Email</th>
                                <th>kegiatan</th>
                                <th>Waktu Scan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['Kunjungans'] as $kunjungan)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $kunjungan->nim }}</td>
                                <td>{{ $kunjungan->nama }}</td>
                                <td>{{ $kunjungan->jurusan }}</td>
                                <td>{{ $kunjungan->angkatan }}</td>
                                <td>{{ $kunjungan->email }}</td>
                                <td>{{ $kunjungan->kegiatan }}</td>
                                <td>{{ $kunjungan->WaktuScan }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
