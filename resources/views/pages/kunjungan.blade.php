{{-- Template Interface --}}
@extends('layouts.app')

{{-- Title --}}
@section('title','Kunjungan Laboratorium')


{{-- Header Content --}}
@section('IconHeaderContent','fa fa-id-badge')
@section('TitleHeaderContent','KUNJUNGAN LABORATORIUM')
@section('InformationHeaderContent','Data Kunjungan Laboratorium.')

{{-- Body Content --}}
@section('content')


{{-- Row 1 --}}
<div class="row">
    <div class="col-md-12 col-xl-12">
        <div class="main-card mb-3 card">
            <div class="card-header">
                <div class="form-inline">
                    {{ csrf_field() }}
                    <div class="mb-2 mr-sm-2 mb-sm-0 position-relative form-group">
                        <label for="TglMulai" class="mr-sm-2">Filter Tanggal : </label>
                        <input name="TglMulai" id="TglMulai" type="date" class="form-control" placeholder="Tgl Mulai">
                    </div>
                    <div class="mb-2 mr-sm-2 mb-sm-0 position-relative form-group">
                        <label for="TglSampai" class="mr-sm-2"></label>
                        <input name="TglSampai" id="TglSampai" type="date" class="form-control" placeholder="Tgl Sampai">
                    </div>
                    <button class="btn btn-primary mr-2" id="FilterDateButton">Filter</button>
                    <button class="btn btn-warning" id="CetakButton">Cetak</button>
                </div>
            </div>
            <div class="card-body">
                <h5 class="card-title">Absen Kunjungan Laboratorium</h5>
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
                        <tbody id='tbody_KA'>
                           <tr>
                               <td colspan="8" class="text-center">Tidak Ada Data</td>
                           </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('Js_Added')
<script type="text/javascript">
    $(document).ready(function(){

        $('#FilterDateButton').on('click',function(e){
            e.preventDefault();

            if($('#TglMulai').val() !== '' || $('#TglSampai').val() !== ''){
                var _token = $("input[name='_token']").val();
                $.ajax({
                    url: "{{ route('attendance.filter') }}",
                    type: 'POST',
                    dataType: 'json',
                    data:{
                        _token:_token,
                        TglMulai: $('#TglMulai').val(),
                        TglSampai: $('#TglSampai').val()
                    },
                    success: function(data) {
                        if(data.success === true){
                            $('#tbody_KA').html(data.html_Tbody);
                        }
                    }
                });
            } else {
                Toast_notification('warning','Filter Data','Tanggal Filter Tidak Lengkap');
            }
        });

        $('#CetakButton').click(function () {
            if($('#TglMulai').val() !== '' || $('#TglSampai').val() !== ''){
                var pageTitle = 'Laporan Kunjungan Laboratorium Tanggal '+ $('#TglMulai').val() + ' Sampai ' + $('#TglSampai').val();
                    win = window.open('', 'Print', 'width=1000,height=1000');
                    win.document.write('<html><head><title>' + pageTitle + '</title>' + '</head><body>' + $('.table')[0].outerHTML + '</body></html>');
                    win.document.close();
                    win.print();
                    win.close();
                return false;
            } else {
                Toast_notification('warning','Cetak Data','Pilih Data Terlebih Dahulu');
            }
        });
    });
</script>
@endsection
