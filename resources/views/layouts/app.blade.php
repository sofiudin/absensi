<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Language" content="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" />
    <meta name="description" content="This is an example dashboard created using build-in elements and components.">
    <meta name="msapplication-tap-highlight" content="no">

    <link href="{{url('architect/css/main.css')}}" rel="stylesheet">
    <link href="{{url('css/toastr/toastr.css')}}" rel="stylesheet">
</head>
<body>
    <div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header">
        {{-- Header --}}
        @include('layouts.header')

        <div class="app-main">
                {{-- SideBar --}}
                @include('layouts.sidebar')

                <div class="app-main__outer">
                    <div class="app-main__inner">

                        {{-- Content Header --}}
                        <div class="app-page-title">
                            <div class="page-title-wrapper">
                                <div class="page-title-heading">
                                    <div class="page-title-icon">
                                        <i class="@yield('IconHeaderContent') icon-gradient bg-mean-fruit">
                                        </i>
                                    </div>
                                    <div>@yield('TitleHeaderContent')
                                        <div class="page-title-subheading">@yield('InformationHeaderContent')
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Content Body--}}
                        @yield('content')

                    </div>

                    {{-- Footer --}}
                    @include('layouts.footer')
                </div>
                <script src="http://maps.google.com/maps/api/js?sensor=true"></script>
        </div>
    </div>
<script type="text/javascript" src="{{url('architect/js/main.js')}}"></script>
<script type="text/javascript" src="{{url('js/JQuery.js')}}"></script>
<script type="text/javascript" src="{{url('js/toastr/toastr.min.js')}}"></script>

{{-- Show Notification --}}
{!! Toastr::message() !!}

{{-- Base Url --}}
<script type="text/javascript">var APP_URL = {!! json_encode(url('/')) !!}</script>

</body>
@yield('Js_Added')
</html>

<script type="text/javascript">function Toast_notification(aksi,title,text){if(aksi=="success"){toastr.success(text,title)}else if(aksi=="warning"){toastr.warning(text,title)}else if(aksi=="information"){toastr.info(text,title)}else if(aksi=="error"){toastr.error(text,title)}}toastr.options={closeButton:true,debug:false,newestOnTop:false,progressBar:false,positionClass:"toast-top-right",preventDuplicates:true,onclick:null,showDuration:"3000",hideDuration:"1000",timeOut:"5000",extendedTimeOut:"1000",showEasing:"swing",hideEasing:"linear",showMethod:"fadeIn",hideMethod:"fadeOut"};</script>
