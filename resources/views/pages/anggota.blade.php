{{-- Template Interface --}}
@extends('layouts.app')

{{-- Title --}}
@section('title','Master Anggota')


{{-- Header Content --}}
@section('IconHeaderContent','fa fa-users')
@section('TitleHeaderContent','MASTER ANGGOTA')
@section('InformationHeaderContent','Data Anggota Laboratorium.')

{{-- Body Content --}}
@section('content')


{{-- Row 1 --}}
<div class="row">
    <div class="col-md-12 col-xl-12">
        <div class="main-card mb-3 card">
            <div class="card-header">
                {{-- Button Collapse --}}
                <button class="mb-2 mr-2 btn btn-primary add-anggota-disabled" data-toggle="collapse" href="#collapseForm"><i class="fa fa-plus-circle"></i> Tambah Anggota</button>
            </div>
            <div class="card-body">
                {{-- Content Collapse --}}
                <div class="main-card mb-4 card collapse"  id="collapseForm">
                    <form>
                        <div class="card-body">
                            {{ csrf_field() }}
                            <div class="position-relative row form-group"><label for="Nama" class="col-sm-2 col-form-label">Nama</label>
                                <div class="col-sm-10"><input name="nama" id="nama" placeholder="Nama Anggota" type="text" class="form-control"></div>
                            </div>
                            <div class="position-relative row form-group"><label for="Email" class="col-sm-2 col-form-label">Email</label>
                                <div class="col-sm-10"><input name="email" id="email" placeholder="Email" type="email" class="form-control"></div>
                            </div>
                            <div class="position-relative row form-group"><label for="NIM" class="col-sm-2 col-form-label">NIM</label>
                                <div class="col-sm-10"><input name="nim" id="nim" placeholder="NIM Anggota" type="number" class="form-control"></div>
                            </div>
                            <div class="position-relative row form-group">
                                <label for="Jurusan" class="col-sm-2 col-form-label">Jurusan</label>
                                <div class="col-sm-10">
                                    <select name="jurusan" id="jurusan" class="form-control">
                                        <option value="" selected>-- Pilih Jurusan --</option>
                                        <option value="Teknik Informatika">Teknik Informatika</option>
                                        <option value="Teknik Industri">Teknik Industri</option>
                                        <option value="Teknik Mesin">Teknik Mesin</option>
                                        <option value="Teknik Elektro">Teknik Elektro</option>
                                    </select>
                                </div>
                            </div>
                            <div class="position-relative row form-group"><label for="Angkatan" class="col-sm-2 col-form-label">Angkatan</label>
                                <div class="col-sm-10"><input name="angkatan" id="angkatan" placeholder="Angkatan" type="number" class="form-control"></div>
                            </div>
                        </div>
                        <div class="card-footer"><button class="mb-2 mr-2 btn btn-success" id="SimpanDataAnggota"><i class="fa fa-database"></i> Simpan</button></div>
                    </form>
                </div>

                <h5 class="card-title">Data Master Anggota</h5>
                <div class="table-responsive">
                    <table class="mb-0 table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Anggota</th>
                                <th>NIM</th>
                                <th>Jurusan</th>
                                <th>Angkatan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id='tbody_MA'>
                            @foreach($Members as $Member)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $Member->nama }}</td>
                                <td>{{ $Member->nim }}</td>
                                <td>{{ $Member->jurusan }}</td>
                                <td>{{ $Member->angkatan }}</td>
                                <td>
                                    <a href="{{url('qrcodes')}}/{{$Member->nim}}.png" class="mb-2 btn btn-success" download><i class="fa fa-qrcode"></i></a>
                                    <a href="#" class="mb-2 btn btn-info" onclick="PrintImage({{$Member->nim}})"><i class="fa fa-print"></i></a>
                                    <a href="{{ route('anggota.edit',$Member->id_member) }}" class="mb-2 btn btn-warning"><i class="fa fa-edit"></i></a>
                                    <a href="{{ route('anggota.hapus',$Member->id_member) }}" class="mb-2 btn btn-danger"><i class="fa fa-trash"></i></a>
                                </td>
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

@section('Modals')

@endsection

@section('Js_Added')
<script type="text/javascript">

    $(document).ready(function(){

        function clearField(){
            $('#nama').val('');
            $('#email').val('');
            $('#nim').val('');
            $('#jurusan').val('');
            $('#angkatan').val('');
        }

        function RefreshTable()
        {
            var _token = $("input[name='_token']").val();
            $.ajax({
                url: "{{ route('anggota.read') }}",
                type: 'POST',
                dataType: 'json',
                data:{_token:_token},
                success: function(data) {
                        if(data.success === true){
                            $('#tbody_MA').html(data.html_Tbody);
                        }
                        // alert(data.success);
                }
            });
        }

    // Print Barcode
    function ImagetoPrint(source)
    {
        return "<html><head><scri"+"pt>function step1(){\n" +
                "setTimeout('step2()', 10);}\n" +
                "function step2(){window.print();window.close()}\n" +
                "</scri" + "pt></head><body onload='step1()'>\n" +
                "<img src='" + source + "' /></body></html>";
    }

    window.PrintImage = function(source){
        var dataPrint = APP_URL + '/qrcodes/' + source + '.png';
        var Pagelink = "about:blank";
        var pwa = window.open(Pagelink, "_new");
        pwa.document.open();
        pwa.document.write(ImagetoPrint(dataPrint));
        pwa.document.close();
    }


        // CreateDB Data Master Anggota
        $('#SimpanDataAnggota').on('click',function(e){
            e.preventDefault();
            // Toast_notification('success','Test','Test Notif');
            var _token = $("input[name='_token']").val();
            $.ajax({
                url: "{{ route('anggota.store') }}",
                type: 'POST',
                dataType: 'json',
                data:{
                    _token:_token,
                    nama: $('#nama').val(),
                    email: $('#email').val(),
                    nim: $('#nim').val(),
                    jurusan: $('#jurusan').val(),
                    angkatan: $('#angkatan').val(),
                },
                success: function(data) {
                        // alert(data.success);
                    if($.isEmptyObject(data.error)){
                        if(data.success === true){
                            Toast_notification('success','Create Data Anggota','Berhasil Menambahkan Data Anggota');
                            clearField();
                            RefreshTable();
                        }else{
                            Toast_notification('warning','Create Data Anggota',data.message);
                        }
                        // alert(data.success);
                    }else{
                        alert(data.error);
                    }
                }
            });

        });
    });
    </script>
@endsection
