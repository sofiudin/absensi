<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Scan Pengunjung</title>
        <link href="{{url('WebCodeCamJS/css/bootstrap.min.css')}}" rel="stylesheet">
        <link href="{{url('WebCodeCamJS/css/style.css')}}" rel="stylesheet">
        <link href="{{url('css/toastr/toastr.css')}}" rel="stylesheet">
    </head>
    <body>
        <div class="container" id="QR-Code">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <div class="navbar-form navbar-left">
                        <h4>Scan Absen Laboratorium</h4>
                    </div>
                    <div class="navbar-form navbar-right">
                        {{-- Pilih Kegiatan --}}
                        <select name="kegiatan" id="kegiatan" class="form-control">
                            <option value="" selected>Pilih Kegiatan</option>
                            <option value="Belajar">Belajar</option>
                            <option value="Praktikum">Praktikum</option>
                        </select>
                        <select class="form-control" id="camera-select"></select>
                        <div class="form-group">
                            <button title="Image shoot" class="btn btn-info btn-sm disabled" id="grab-img" type="button" data-toggle="tooltip"><span class="glyphicon glyphicon-picture"></span></button>
                            <button title="Play" class="btn btn-success btn-sm" id="play" type="button" data-toggle="tooltip"><span class="glyphicon glyphicon-play"></span></button>
                            <button title="Pause" class="btn btn-warning btn-sm" id="pause" type="button" data-toggle="tooltip"><span class="glyphicon glyphicon-pause"></span></button>
                            <button title="Stop streams" class="btn btn-danger btn-sm" id="stop" type="button" data-toggle="tooltip"><span class="glyphicon glyphicon-stop"></span></button>
                         </div>
                    </div>
                </div>
                <div class="panel-body text-center">
                    <div class="col-md-6">
                        <div class="well" style="position: relative;display: inline-block;">
                            <canvas width="320" height="240" id="webcodecam-canvas"></canvas>
                            <div class="scanner-laser laser-rightBottom" style="opacity: 0.5;"></div>
                            <div class="scanner-laser laser-rightTop" style="opacity: 0.5;"></div>
                            <div class="scanner-laser laser-leftBottom" style="opacity: 0.5;"></div>
                            <div class="scanner-laser laser-leftTop" style="opacity: 0.5;"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="thumbnail" id="result">
                            <div class="well" style="overflow: hidden;">
                                <img width="320" height="240" id="scanned-img" src="">
                            </div>
                            <div class="caption">
                                {{ csrf_field() }}
                                <h3>Hasil Scan</h3>
                                <p id="scanned-QR"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">

                </div>
            </div>
        </div>
        <script type="text/javascript" src="{{url('WebCodeCamJS/js/filereader.js')}}"></script>
        <!-- Using javascript vanila version: -->
        <!--
            <script type="text/javascript" src="js/qrcodelib.js"></script>
            <script type="text/javascript" src="js/webcodecamjs.js"></script>
            <script type="text/javascript" src="js/main.js"></script>
        -->
        <script type="text/javascript" src="{{url('WebCodeCamJS/js/jquery.js')}}"></script>
        <script type="text/javascript" src="{{url('WebCodeCamJS/js/qrcodelib.js')}}"></script>
        <script type="text/javascript" src="{{url('js/toastr/toastr.min.js')}}"></script>

{{-- Show Notification --}}
{!! Toastr::message() !!}

{{-- SettingToast --}}
<script type="text/javascript">


    // Function Notification Toaster
   function Toast_notification(aksi,title,text) {
    if (aksi == 'success') {
      toastr.success(text, title);
    } else if (aksi == 'warning') {
      toastr.warning(text, title);
    } else if (aksi == 'information') {
      toastr.info(text, title);
    } else if (aksi == 'error') {
      toastr.error(text, title);
    }
  }

  // Setting Toaster Notification
    toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": false,
    "progressBar": false,
    "positionClass": "toast-top-right",
    "preventDuplicates": true,
    "onclick": null,
    "showDuration": "3000",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
    };

</script>

{{-- Script WebCodecamJquery.js --}}
<script type="text/javascript">
APP_URL = {!! json_encode(url('/')) !!};
(function($, window, document, undefined) {
    'use strict';
    var pluginName = 'WebCodeCamJQuery';
    var mediaDevices = window.navigator.mediaDevices;
    mediaDevices.getUserMedia = function(c) {
        return new Promise(function(y, n) {
            (window.navigator.getUserMedia || window.navigator.mozGetUserMedia || window.navigator.webkitGetUserMedia).call(navigator, c, y, n);
        });
    }
    HTMLVideoElement.prototype.streamSrc = ('srcObject' in HTMLVideoElement.prototype) ? function(stream) {
        this.srcObject = !!stream ? stream : null;
    } : function(stream) {
        if (!!stream) {
            this.src = (window.URL || window.webkitURL).createObjectURL(stream);
        } else {
            this.removeAttribute('src');
        }
    };
    var Self, display, videoSelect, lastImageSrc, con, beepSound, w, h, lastCode,
        DecodeWorker = null,
        video = $('<video muted autoplay playsinline></video>')[0],
        sucessLocalDecode = false,
        localImage = false,
        flipMode = [1, 3, 6, 8],
        isStreaming = false,
        delayBool = false,
        initialized = false,
        localStream = null,
        defaults = {
            decodeQRCodeRate: 5,
            decodeBarCodeRate: 3,
            successTimeout: 500,
            codeRepetition: true,
            tryVertical: true,
            frameRate: 15,
            width: 320,
            height: 240,
            constraints: {
                video: {
                    mandatory: {
                        maxWidth: 1280,
                        maxHeight: 720
                    },
                    optional: [{
                        sourceId: true
                    }]
                },
                audio: false
            },
            flipVertical: false,
            flipHorizontal: false,
            zoom: 0,
            beep: '{{url("WebCodeCamJS/audio/beep.mp3")}}',
            decoderWorker: '{{url("WebCodeCamJS/js/DecoderWorker.js")}}',
            brightness: 0,
            autoBrightnessValue: 0,
            grayScale: 0,
            contrast: 0,
            threshold: 0,
            sharpness: [],
            resultFunction: function(res) {
                console.log(res.format + ": " + res.code);
            },
            cameraSuccess: function(stream) {
                console.log('cameraSuccess');
            },
            canPlayFunction: function() {
                console.log('canPlayFunction');
            },
            getDevicesError: function(error) {
                console.log(error);
            },
            getUserMediaError: function(error) {
                console.log(error);
            },
            cameraError: function(error) {
                console.log(error);
            }
        };

    function Plugin(element, options) {
        Self = this;
        this.element = element;
        display = element;
        this.options = $.extend({}, defaults, options);
        this._defaults = defaults;
        this._name = pluginName;
        return this;
    }

    function init() {
        var constraints = changeConstraints();
        try {
            mediaDevices.getUserMedia(constraints).then(cameraSuccess).catch(function(error) {
                Self.options.cameraError(error);
                return false;
            });
        } catch (error) {
            Self.options.getUserMediaError(error);
            return false;
        }
        return true;
    }

    function play() {
        if (!localImage) {
            if (!localStream) {
                init();
            }
            const p = video.play();
            if (p && (typeof Promise !== 'undefined') && (p instanceof Promise)) {
                p.catch(e => null);
            }
            delay();
        }
    }

    function stop() {
        delayBool = true;
        const p = video.pause();
        if (p && (typeof Promise !== 'undefined') && (p instanceof Promise)) {
            p.catch(e => null);
        }
        video.streamSrc(null);
        con.clearRect(0, 0, w, h);
        if (localStream) {
            for (var i = 0; i < localStream.getTracks().length; i++) {
                localStream.getTracks()[i].stop();
            }
        }
        localStream = null;
    }

    function pause() {
        delayBool = true;
        const p = video.pause();
        if (p && (typeof Promise !== 'undefined') && (p instanceof Promise)) {
            p.catch(e => null);
        }
    }

    function delay() {
        delayBool = true;
        if (!localImage) {
            setTimeout(function() {
                delayBool = false;
                if (Self.options.decodeBarCodeRate) {
                    tryParseBarCode();
                }
                if (Self.options.decodeQRCodeRate) {
                    tryParseQRCode();
                }
            }, Self.options.successTimeout);
        }
    }

    function beep() {
        if (Self.options.beep) {
            beepSound.play();
        }
    }

    function cameraSuccess(stream) {
        localStream = stream;
        video.streamSrc(stream);
        Self.options.cameraSuccess(stream);
    }

    function cameraError(error) {
        Self.options.cameraError(error);
    }

    function setEventListeners() {
        $(video).on('canplay', function(e) {
            if (!isStreaming) {
                if (video.videoWidth > 0) {
                    h = video.videoHeight / (video.videoWidth / w);
                }
                $(display).attr('width', w);
                $(display).attr('height', h);
                isStreaming = true;
                if (Self.options.decodeQRCodeRate || Self.options.decodeBarCodeRate) {
                    delay();
                }
            }
        });
        $(video).on('play', function() {
            setInterval(function() {
                if (!video.paused && !video.ended) {
                    var z = Self.options.zoom;
                    if (z === 0) {
                        z = optimalZoom();
                    }
                    con.drawImage(video, (w * z - w) / -2, (h * z - h) / -2, w * z, h * z);
                    var imageData = con.getImageData(0, 0, w, h);
                    if (Self.options.grayScale) {
                        imageData = grayScale(imageData);
                    }
                    if (Self.options.brightness !== 0 || Self.options.autoBrightnessValue) {
                        imageData = brightness(imageData, Self.options.brightness);
                    }
                    if (Self.options.contrast !== 0) {
                        imageData = contrast(imageData, Self.options.contrast);
                    }
                    if (Self.options.threshold !== 0) {
                        imageData = threshold(imageData, Self.options.threshold);
                    }
                    if (Self.options.sharpness.length !== 0) {
                        imageData = convolute(imageData, Self.options.sharpness);
                    }
                    con.putImageData(imageData, 0, 0);
                }
            }, 1E3 / Self.options.frameRate);
        });
    }

    function setCallBack() {
        DecodeWorker.onmessage = function(e) {
            if (localImage || (!delayBool && !video.paused)) {
                if (e.data.success === true && e.data.success != 'localization') {
                    sucessLocalDecode = true;
                    delayBool = true;
                    delay();
                    setTimeout(function() {
                        if (Self.options.codeRepetition || lastCode != e.data.result[0].Value) {
                            beep();
                            lastCode = e.data.result[0].Value;
                            Self.options.resultFunction({
                                format: e.data.result[0].Format,
                                code: e.data.result[0].Value,
                                imgData: lastImageSrc
                            });
                        }
                    }, 0);
                }
                if ((!sucessLocalDecode || !localImage) && e.data.success != 'localization') {
                    if (!localImage) {
                        setTimeout(tryParseBarCode, 1E3 / Self.options.decodeBarCodeRate);
                    }
                }
            }
        };
        qrcode.callback = function(a) {
            if (localImage || (!delayBool && !video.paused)) {
                sucessLocalDecode = true;
                delayBool = true;
                delay();
                setTimeout(function() {
                    if (Self.options.codeRepetition || lastCode != a) {
                        beep();
                        lastCode = a;
                        Self.options.resultFunction({
                            format: 'QR Code',
                            code: a,
                            imgData: lastImageSrc
                        });
                    }
                }, 0);
            }
        };
    }

    function tryParseBarCode() {
        $(display).css({
            'transform': 'scale(' + (Self.options.flipHorizontal ? '-1' : '1') + ', ' + (Self.options.flipVertical ? '-1' : '1') + ')'
        });
        if (Self.options.tryVertical && !localImage) {
            flipMode.push(flipMode[0]);
            flipMode.splice(0, 1);
        } else {
            flipMode = [1, 3, 6, 8];
        }
        lastImageSrc = display.toDataURL();
        DecodeWorker.postMessage({
            scan: con.getImageData(0, 0, w, h).data,
            scanWidth: w,
            scanHeight: h,
            multiple: false,
            decodeFormats: ["Code128", "Code93", "Code39", "EAN-13", "2Of5", "Inter2Of5", "Codabar"],
            rotation: flipMode[0]
        });
    }

    function tryParseQRCode() {
        $(display).css({
            'transform': 'scale(' + (Self.options.flipHorizontal ? '-1' : '1') + ', ' + (Self.options.flipVertical ? '-1' : '1') + ')'
        });
        try {
            lastImageSrc = display.toDataURL();
            qrcode.decode();
        } catch (e) {
            if (!localImage && !delayBool) {
                setTimeout(tryParseQRCode, 1E3 / Self.options.decodeQRCodeRate);
            }
        }
    }

    function optimalZoom() {
        return video.videoHeight / h;
    }

    function getImageLightness() {
        var pixels = con.getImageData(0, 0, w, h),
            d = pixels.data,
            colorSum = 0,
            r, g, b, avg;
        for (var x = 0, len = d.length; x < len; x += 4) {
            r = d[x];
            g = d[x + 1];
            b = d[x + 2];
            avg = Math.floor((r + g + b) / 3);
            colorSum += avg;
        }
        return Math.floor(colorSum / (w * h));
    }

    function brightness(pixels, adjustment) {
        adjustment = adjustment === 0 && Self.options.autoBrightnessValue ? Self.options.autoBrightnessValue - getImageLightness() : adjustment;
        var d = pixels.data;
        for (var i = 0; i < d.length; i += 4) {
            d[i] += adjustment;
            d[i + 1] += adjustment;
            d[i + 2] += adjustment;
        }
        return pixels;
    }

    function grayScale(pixels) {
        var d = pixels.data;
        for (var i = 0; i < d.length; i += 4) {
            var r = d[i],
                g = d[i + 1],
                b = d[i + 2],
                v = 0.2126 * r + 0.7152 * g + 0.0722 * b;
            d[i] = d[i + 1] = d[i + 2] = v;
        }
        return pixels;
    }

    function contrast(pixels, cont) {
        var data = pixels.data;
        var factor = (259 * (cont + 255)) / (255 * (259 - cont));
        for (var i = 0; i < data.length; i += 4) {
            data[i] = factor * (data[i] - 128) + 128;
            data[i + 1] = factor * (data[i + 1] - 128) + 128;
            data[i + 2] = factor * (data[i + 2] - 128) + 128;
        }
        return pixels;
    }

    function threshold(pixels, thres) {
        var average, d = pixels.data;
        for (var i = 0, len = w * h * 4; i < len; i += 4) {
            average = d[i] + d[i + 1] + d[i + 2];
            if (average < thres) {
                d[i] = d[i + 1] = d[i + 2] = 0;
            } else {
                d[i] = d[i + 1] = d[i + 2] = 255;
            }
            d[i + 3] = 255;
        }
        return pixels;
    }

    function convolute(pixels, weights, opaque) {
        var sw = pixels.width,
            sh = pixels.height,
            w = sw,
            h = sh,
            side = Math.round(Math.sqrt(weights.length)),
            halfSide = Math.floor(side / 2),
            src = pixels.data,
            tmpCanvas = document.createElement('canvas'),
            tmpCtx = tmpCanvas.getContext('2d'),
            output = tmpCtx.createImageData(w, h),
            dst = output.data,
            alphaFac = opaque ? 1 : 0;
        for (var y = 0; y < h; y++) {
            for (var x = 0; x < w; x++) {
                var sy = y,
                    sx = x,
                    r = 0,
                    g = 0,
                    b = 0,
                    a = 0,
                    dstOff = (y * w + x) * 4;
                for (var cy = 0; cy < side; cy++) {
                    for (var cx = 0; cx < side; cx++) {
                        var scy = sy + cy - halfSide,
                            scx = sx + cx - halfSide;
                        if (scy >= 0 && scy < sh && scx >= 0 && scx < sw) {
                            var srcOff = (scy * sw + scx) * 4,
                                wt = weights[cy * side + cx];
                            r += src[srcOff] * wt;
                            g += src[srcOff + 1] * wt;
                            b += src[srcOff + 2] * wt;
                            a += src[srcOff + 3] * wt;
                        }
                    }
                }
                dst[dstOff] = r;
                dst[dstOff + 1] = g;
                dst[dstOff + 2] = b;
                dst[dstOff + 3] = a + alphaFac * (255 - a);
            }
        }
        return output;
    }

    function buildSelectMenu(selectorVideo, ind) {
        videoSelect = $(selectorVideo);
        videoSelect.html('');
        try {
            if (mediaDevices && mediaDevices.enumerateDevices) {
                mediaDevices.enumerateDevices().then(function(devices) {
                    devices.forEach(function(device) {
                        gotSources(device);
                    });
                    if (typeof ind === 'string') {
                        Array.prototype.find.call(videoSelect.get(0).children, function(a, i) {
                            if ($(a).text().toLowerCase().match(new RegExp(ind, 'g'))) {
                                videoSelect.prop('selectedIndex', i);
                            }
                        });
                    } else {
                        videoSelect.prop('selectedIndex', videoSelect.children().length <= ind ? 0 : ind);
                    }
                }).catch(function(error) {
                    Self.options.getDevicesError(error);
                });
            } else if (mediaDevices && !mediaDevices.enumerateDevices) {
                $('<option value="true">On</option>').appendTo(videoSelect);
                Self.options.getDevicesError(new NotSupportError('enumerateDevices Or getSources is Not supported'));
            } else {
                throw new NotSupportError('getUserMedia is Not supported');
            }
        } catch (error) {
            Self.options.getDevicesError(error);
        }
    }

    function gotSources(device) {
        if (device.kind === 'video' || device.kind === 'videoinput') {
            var face = (!device.facing || device.facing === '') ? 'unknown' : device.facing;
            var text = device.label || 'Camera '.concat(videoSelect.children().length + 1, ' (facing: ' + face + ')');
            $('<option value="' + (device.id || device.deviceId) + '">' + text + '</option>').appendTo(videoSelect);
        }
    }

    function changeConstraints() {
        var constraints = $.parseJSON(JSON.stringify(Self.options.constraints));
        if (videoSelect && videoSelect.children().length !== 0) {
            switch (videoSelect.val().toString()) {
                case 'true':
                    if (navigator.userAgent.search("Edge") == -1 && navigator.userAgent.search("Chrome") != -1) {
                        constraints.video.optional = [{
                            sourceId: true
                        }];
                    } else {
                        constraints.video.deviceId = undefined;
                    }
                    break;
                case 'false':
                    constraints.video = false;
                    break;
                default:
                    if (navigator.userAgent.search("Edge") == -1 && navigator.userAgent.search("Chrome") != -1) {
                        constraints.video.optional = [{
                            sourceId: videoSelect.val()
                        }];
                    } else if (navigator.userAgent.search("Firefox") != -1) {
                        constraints.video.deviceId = {
                            exact: videoSelect.val()
                        };
                    } else {
                         constraints.video.deviceId = videoSelect.val();
                    }
                    break;
            }
        }
        constraints.audio = false;
        return constraints;
    }

    function decodeLocalImage(url) {
        stop();
        localImage = true;
        sucessLocalDecode = false;
        var img = new Image();
        img.onload = function() {
            con.fillStyle = '#fff';
            con.fillRect(0, 0, w, h);
            con.drawImage(this, 5, 5, w - 10, h - 10);
            tryParseQRCode();
            tryParseBarCode();
        };
        if (url) {
            download("temp", url);
            decodeLocalImage();
        } else {
            if (FileReaderHelper) {
                new FileReaderHelper().Init('jpg|png|jpeg|gif', 'dataURL', function(e) {
                    img.src = e.data;
                }, true);
            } else {
                alert("fileReader class not found!");
            }
        }
    }

    function download(filename, url) {
        var a = $('<a>');
        a.attr('href', url);
        a.attr('download', filename);
        a.css('display', 'none');
        a.appendTo('body');
        a.click();
        a.remove();
    }

    function NotSupportError(message) {
        this.name = 'NotSupportError';
        this.message = (message || '');
    }
    NotSupportError.prototype = Error.prototype;
    $.extend(Plugin.prototype, {
        init: function() {
            if (!initialized) {
                if (!display || display.tagName.toLowerCase() !== 'canvas') {
                    console.log('Element type must be canvas!');
                    alert('Element type must be canvas!');
                    return false;
                }
                con = display.getContext('2d');
                display.width = w = Self.options.width;
                display.height = h = Self.options.height;
                qrcode.sourceCanvas = display;
                initialized = true;
                setEventListeners();
                DecodeWorker = new Worker(this.options.decoderWorker);
                if (this.options.beep) {
                    beepSound = new Audio(this.options.beep);
                }
                if (this.options.decodeQRCodeRate || this.options.decodeBarCodeRate) {
                    setCallBack();
                }
            }
            return this;
        },
        play: function() {
            this.init();
            localImage = false;
            setTimeout(play, 100);
            return this;
        },
        stop: function() {
            stop();
            return this;
        },
        pause: function() {
            pause();
            return this;
        },
        buildSelectMenu: function(selector, ind) {
            buildSelectMenu(selector, ind ? ind : 0);
            return this;
        },
        getOptimalZoom: function() {
            return optimalZoom();
        },
        getLastImageSrc: function() {
            return display.toDataURL();
        },
        decodeLocalImage: function(url) {
            decodeLocalImage(url);
        },
        isInitialized: function() {
            return initialized;
        }
    });
    $.fn[pluginName] = function(options) {
        return this.each(function() {
            if (!$.data(this, 'plugin_' + pluginName)) {
                $.data(this, 'plugin_' + pluginName, new Plugin(this, options));
            }
        });
    };
})(jQuery, window, document);
</script>

{{-- Script mainjquey.js --}}
<script type="text/javascript">

(function(undefined) {
    var scannerLaser = $(".scanner-laser"),
        imageUrl = $("#image-url"),
        decodeLocal = $("#decode-img"),
        play = $("#play"),
        scannedImg = $("#scanned-img"),
        scannedQR = $("#scanned-QR"),
        grabImg = $("#grab-img"),
        pause = $("#pause"),
        stop = $("#stop"),
        contrast = $("#contrast"),
        contrastValue = $("#contrast-value"),
        zoom = $("#zoom"),
        zoomValue = $("#zoom-value"),
        brightness = $("#brightness"),
        brightnessValue = $("#brightness-value"),
        threshold = $("#threshold"),
        thresholdValue = $("#threshold-value"),
        sharpness = $("#sharpness"),
        sharpnessValue = $("#sharpness-value"),
        grayscale = $("#grayscale"),
        grayscaleValue = $("#grayscale-value"),
        flipVertical = $("#flipVertical"),
        flipVerticalValue = $("#flipVertical-value"),
        flipHorizontal = $("#flipHorizontal"),
        flipHorizontalValue = $("#flipHorizontal-value");
    var args = {
        autoBrightnessValue: 100,
        resultFunction: function(res) {
            [].forEach.call(scannerLaser, function(el) {
                $(el).fadeOut(300, function() {
                    $(el).fadeIn(300);
                });
            });
            scannedImg.attr("src", res.imgData);

            if($('#kegiatan').val() == ''){
                Toast_notification('error','Kegiatan','Pilih Jenis Kegiatan Terlebih Dahulu.');
            }else{
                // Create AbsenDB
                var nim = res.code;
                var kegiatan = $('#kegiatan').val();
                var _token = $("input[name='_token']").val();
                $.ajax({
                    url: "{{ route('attendance.scansend') }}",
                    type: 'POST',
                    dataType: 'json',
                    data:{_token:_token,nim:nim,kegiatan:kegiatan},
                    success: function(data) {
                            if(data.success === true){
                                Toast_notification('success','Absen','TerimahKasih Telah Absen.');
                                $('#kegiatan').val('');
                            }else{
                                Toast_notification('error','Absen','Gagal Absen.');
                                $('#kegiatan').val('');
                            }
                    }
                });
                scannedQR.text(res.format + ": Nim " + res.code + " Berhasil Absen.");
            }
        },
        getDevicesError: function(error) {
            var p, message = "Error detected with the following parameters:\n";
            for (p in error) {
                message += (p + ": " + error[p] + "\n");
            }
            alert(message);
        },
        getUserMediaError: function(error) {
            var p, message = "Error detected with the following parameters:\n";
            for (p in error) {
                message += (p + ": " + error[p] + "\n");
            }
            alert(message);
        },
        cameraError: function(error) {
            var p, message = "Error detected with the following parameters:\n";
            if (error.name == "NotSupportedError") {
                var ans = confirm("Your browser does not support getUserMedia via HTTP!\n(see: https://goo.gl/Y0ZkNV).\n You want to see github demo page in a new window?");
                if (ans) {
                    window.open("https://andrastoth.github.io/webcodecamjs/");
                }
            } else {
                for (p in error) {
                    message += p + ": " + error[p] + "\n";
                }
                alert(message);
            }
        },
        cameraSuccess: function() {
            grabImg.removeClass("disabled");
        }
    };
    var decoder = $("#webcodecam-canvas").WebCodeCamJQuery(args).data().plugin_WebCodeCamJQuery;
    decoder.buildSelectMenu("#camera-select", "environment|back").init();
    decodeLocal.on("click", function() {
        Page.decodeLocalImage();
    });
    play.on("click", function() {
        scannedQR.text("Scanning ...");
        grabImg.removeClass("disabled");
        decoder.play();
    });
    grabImg.on("click", function() {
        scannedImg.attr("src", decoder.getLastImageSrc());
    });
    pause.on("click", function(event) {
        scannedQR.text("Paused");
        decoder.pause();
    });
    stop.on("click", function(event) {
        grabImg.addClass("disabled");
        scannedQR.text("Stopped");
        decoder.stop();
    });
    Page.changeZoom = function(a) {
        if (decoder.isInitialized()) {
            var value = typeof a !== "undefined" ? parseFloat(a.toPrecision(2)) : zoom.val() / 10;
            zoomValue.text(zoomValue.text().split(":")[0] + ": " + value.toString());
            decoder.options.zoom = value;
            if (typeof a != "undefined") {
                zoom.val(a * 10);
            }
        }
    };
    Page.changeContrast = function() {
        if (decoder.isInitialized()) {
            var value = contrast.val();
            contrastValue.text(contrastValue.text().split(":")[0] + ": " + value.toString());
            decoder.options.contrast = parseFloat(value);
        }
    };
    Page.changeBrightness = function() {
        if (decoder.isInitialized()) {
            var value = brightness.val();
            brightnessValue.text(brightnessValue.text().split(":")[0] + ": " + value.toString());
            decoder.options.brightness = parseFloat(value);
        }
    };
    Page.changeThreshold = function() {
        if (decoder.isInitialized()) {
            var value = threshold.val();
            thresholdValue.text(thresholdValue.text().split(":")[0] + ": " + value.toString());
            decoder.options.threshold = parseFloat(value);
        }
    };
    Page.changeSharpness = function() {
        if (decoder.isInitialized()) {
            var value = sharpness.prop("checked");
            if (value) {
                sharpnessValue.text(sharpnessValue.text().split(":")[0] + ": on");
                decoder.options.sharpness = [0, -1, 0, -1, 5, -1, 0, -1, 0];
            } else {
                sharpnessValue.text(sharpnessValue.text().split(":")[0] + ": off");
                decoder.options.sharpness = [];
            }
        }
    };
    Page.changeGrayscale = function() {
        if (decoder.isInitialized()) {
            var value = grayscale.prop("checked");
            if (value) {
                grayscaleValue.text(grayscaleValue.text().split(":")[0] + ": on");
                decoder.options.grayScale = true;
            } else {
                grayscaleValue.text(grayscaleValue.text().split(":")[0] + ": off");
                decoder.options.grayScale = false;
            }
        }
    };
    Page.changeVertical = function() {
        if (decoder.isInitialized()) {
            var value = flipVertical.prop("checked");
            if (value) {
                flipVerticalValue.text(flipVerticalValue.text().split(":")[0] + ": on");
                decoder.options.flipVertical = value;
            } else {
                flipVerticalValue.text(flipVerticalValue.text().split(":")[0] + ": off");
                decoder.options.flipVertical = value;
            }
        }
    };
    Page.changeHorizontal = function() {
        if (decoder.isInitialized()) {
            var value = flipHorizontal.prop("checked");
            if (value) {
                flipHorizontalValue.text(flipHorizontalValue.text().split(":")[0] + ": on");
                decoder.options.flipHorizontal = value;
            } else {
                flipHorizontalValue.text(flipHorizontalValue.text().split(":")[0] + ": off");
                decoder.options.flipHorizontal = value;
            }
        }
    };
    Page.decodeLocalImage = function() {
        if (decoder.isInitialized()) {
            decoder.decodeLocalImage(imageUrl.val());
        }
        imageUrl.val(null);
    };
    var getZomm = setInterval(function() {
        var a;
        try {
            a = decoder.getOptimalZoom();
        } catch (e) {
            a = 0;
        }
        if (!!a && a !== 0) {
            Page.changeZoom(a);
            clearInterval(getZomm);
        }
    }, 500);
    $("#camera-select").on("change", function() {
        if (decoder.isInitialized()) {
            decoder.stop().play();
        }
    });
}).call(window.Page = window.Page || {});
</script>
    </body>
</html>
