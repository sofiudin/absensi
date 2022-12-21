{{-- Template Interface --}}
@extends('layouts.app')

{{-- Title --}}
@section('title','Edit Data Anggota')

{{-- Header Content --}}
@section('IconHeaderContent','fa fa-edit')
@section('TitleHeaderContent','EDIT DATA ANGGOTA')
@section('InformationHeaderContent','Edit Data Anggota.')

{{-- Body Content --}}
@section('content')

{{-- Row 1 --}}
<div class="row">
    <div class="col-md-12 col-xl-12">
        <div class="main-card mb-3 card">
            <form action="{{ route('anggota.update',$Member->id_member) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-header"><h5>Data Anggota</h5></div>
                <div class="card-body">
                    <div class="position-relative row form-group"><label for="Nama" class="col-sm-2 col-form-label">Nama</label>
                        <div class="col-sm-10"><input required name="nama_edit" id="nama_edit" placeholder="Nama Anggota" type="text" class="form-control" value="{{ $Member->nama}}"></div>
                    </div>
                    <div class="position-relative row form-group"><label for="Email" class="col-sm-2 col-form-label">Email</label>
                        <div class="col-sm-10"><input required name="email_edit" id="email_edit" placeholder="Email" type="email" class="form-control" value="{{ $Member->email}}"></div>
                    </div>
                    <div class="position-relative row form-group"><label for="NIM" class="col-sm-2 col-form-label">NIM</label>
                        <div class="col-sm-10">
                            <input required name="nim_edit" id="nim_edit" placeholder="NIM Anggota" type="number" class="form-control" value="{{ $Member->nim}}">
                            <input name="nim_lama" id="nim_lama" placeholder="NIM Anggota" type="hidden" class="form-control" value="{{ $Member->nim}}">
                        </div>
                    </div>
                    <div class="position-relative row form-group">
                        <label for="Jurusan" class="col-sm-2 col-form-label">Jurusan</label>
                        <div class="col-sm-10">
                            <select required name="jurusan_edit" id="jurusan_edit" class="form-control">
                                <option value="" >-- Pilih Jurusan --</option>
                                <option value="Teknik Informatika" <?php echo ($Member->jurusan == 'Teknik Informatika' ? 'selected' : '');?>>Teknik Informatika</option>
                                <option value="Teknik Industri" <?php echo ($Member->jurusan == 'Teknik Industri' ? 'selected' : '');?>>Teknik Industri</option>
                                <option value="Teknik Mesin" <?php echo ($Member->jurusan == 'Teknik Mesin' ? 'selected' : '');?>>Teknik Mesin</option>
                                <option value="Teknik Elektro" <?php echo ($Member->jurusan == 'Teknik Elektro' ? 'selected' : '');?>>Teknik Elektro</option>
                            </select>
                        </div>
                    </div>
                    <div class="position-relative row form-group"><label for="Angkatan" class="col-sm-2 col-form-label">Angkatan</label>
                        <div class="col-sm-10"><input required name="angkatan_edit" id="angkatan_edit" placeholder="Angkatan" type="number" class="form-control" value="{{ $Member->angkatan}}"></div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="mb-2 mr-2 btn btn-success"><i class="fa fa-edit"></i> Update</button>
                    <a href="{{ route('anggota.index') }}" class="mb-2 btn btn-danger"><i class="fa fa-arrow-circle-left"></i> Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('Js_Added')
<script type="text/javascript">
    $(document).ready(function(){});
</script>
@endsection
