<?php
$final_encoded = json_encode($final);

if(!isset($final['players_list']) OR !isset($final['players_list'][0]['player_name'])) {
    die("Players list empty. Send data to see the spinner. ");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="https://noorgames.net/images/logochangecolor.gif">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
    <meta name="description" content="Noor-games">
    <meta name="author" content="Noor-games">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="keywords" content="Noor-games">
    <meta content="" name="Noor-games"><!-- Favicons -->
    <link href="{{ URL::to('/images/logochangecolor.gif') }}" rel="icon">
    <link href="{{ URL::to('/images/logochangecolor.gif') }}" rel="apple-touch-icon">
    <!-- Title Page-->
    <title>Noor-games</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <!-- Icons font CSS-->
    <!-- Font special for pages-->
    
    <link rel="stylesheet" href="assets/main.css" type="text/css" />
    <script type="text/javascript" src="assets/winwheel.js"></script>
    <script src="assets/tweenmax.js"></script>

    <!-- Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" >

    <!-- Main CSS-->
    <!--<script src="{{ asset('js/app.js') }}" defer></script>-->

    <link href="{{ asset('css/main.css') }}" rel="stylesheet" media="all">
    <link href="{{ asset('css/my.css') }}" rel="stylesheet" media="all">
    
    <!---->
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <!-- Nucleo Icons -->
    <link href="{{asset('public/newAdmin/css/nucleo-icons.css')}}" rel="stylesheet" />
    <link href="{{asset('public/newAdmin/css/nucleo-svg.css')}}" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <!-- CSS Files -->
    <link id="pagestyle" href="{{asset('public/newAdmin/css/argon-dashboard.css')}}" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.13/css/dataTables.jqueryui.css" /> 
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.2.4/css/buttons.jqueryui.css" /> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" >
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/digital-7-mono" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" >
<link href="{{asset('public/css/jquery.mCustomScrollbar.css')}}" rel="stylesheet" />
    <style>
        .hidden{
            display:none;
        }
        @media screen and (max-width: 576px) {
            #myVideo {
                position: fixed;
                right: -5%;
                top: 0;
                height: 100vh;
            }
        }
        .input-group.border-custom {
            border:0;
        }
        .input-group.border-custom::after { 
            content:'';
            height: 2px;
            width: 106px;
            background: #006aff;
        }
        #captcha_image {
            border: 2px solid #d36d77;
            animation: glowing 1300ms infinite;
        }

        #main-container{
            display:flex ;
            justify-content:center;
            /*position:relative;*/
            position:fixed;
            width:100vw !important;
        }
 
        .aside{
            width:100%;
            background:rgba(0,0,0,0.6) ;
        }

        .canvas-wrap{
            background: rgba(0,0,0,0.6);
            border-radius: 50%;
            height: 50%;
            width: 100%;
        }

        #canvas{
            position: relative;
            width: 32vw;
        }

        .player-list{
            position: absolute;
            height:100% !important;
            color: red;

            /* top: 0px; */
            z-index: 1;
            font-size: 18px;
            font-family: 'neon_planetdisplay', Arial, sans-serif;
            overflow:scroll;
            /*background:rgba(0,0,0,0.6) !important;*/
            width:100%;
        }

        @media screen and (max-width: 750px) {
            #main-container{
                display:flex;
                flex-direction:column;
                width:100vw;
            }
            #main-container :nth-child(1) { order: 2; }
            #main-container :nth-child(2) { order: 1; }
            #main-container :nth-child(3) { order: 3; }

            #canvas{
                width:50vw;
            }

            .player-list,.player-list2,.player-list>table,.player-list2>table{
                width:100% !important;
                left:70px;
            }
        }
        .player-list2{
            position: absolute;
            right: 11%;
            z-index: 1;
            color: blue;
            font-size: 18px;
            height:100vh !important;
            overflow:scroll;
            /*background:rgba(0,0,0,0.6) !important;*/
        }
        .player-list,
        .player-list2 {
            -ms-overflow-style: none;  /* Internet Explorer 10+ */
            scrollbar-width: none;  /* Firefox */
        }
        .player-list::-webkit-scrollbar,
        .player-list2::-webkit-scrollbar { 
            display: none;  /* Safari and Chrome */
        }
        .mCSB_container_wrapper{
            margin: 0px;
        }
    </style>
</head>

<body>
    <div class="page-wrapper font-robo">
        <video autoplay muted loop id="myVideo">
            <source src="{{url('images/fin.mp4')}}" type="video/mp4">
            Your browser does not support HTML5 video.
        </video>
        <div id="main-container">
            <div class="aside gradient-border">
                <div class="text-center mt-4" style="height:100vh">
                    <div id="image-carousel" class="carousel slide" data-ride="carousel" style="margin-left:85px">
                        <div class="player-list neon-text" style="height:100vh !important;font-family:cursive;background:rgba(0,0,0,0.6) ">
                            <div style="width:22vw;">
                                <strong>
                                    <h3 style="margin-left:25px">
                                        <b>
                                            <span class="neon-text font-weight-bold">Playing Players</span>
                                        </b>
                                    </h3>
                                </strong>
                                <ul>
                                    @foreach($final['players_list'] as $key=>$row)
                                    <li id="demo" style="background:rgba(0,0,0,0.6);border:1px solid red;border-radius:5px;padding:5px;margin-bottom:10px;padding:10px;list-style:none;display:flex;justify-content:space-between"><span style="font-size:25px">{{$key+1}}</span><span class="neon-text">{{$row['player_name']}}</span></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    <!--<ol class="carousel-indicators">-->
                    <!--    <li data-target="#image-carousel" data-slide-to="0" class="active"></li>-->
                    <!--    <li data-target="#image-carousel" data-slide-to="1"></li>-->
                    <!--    <li data-target="#image-carousel" data-slide-to="2"></li>-->
                    <!--    <li data-target="#image-carousel" data-slide-to="3"></li>-->
                    <!--    <li data-target="#image-carousel" data-slide-to="4"></li>-->
                    <!--    <li data-target="#image-carousel" data-slide-to="5"></li>-->
                    <!--</ol>-->
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img class="d-block m-auto" src="{{ URL::to('/images/icon.gif') }}" width="400" height="500" alt="First slide">
                            </div>
                            <div class="carousel-item">
                                <img class="d-block m-auto" src="{{ URL::to('/images/icon2.gif') }}" width="400" height="500" alt="Second slide">
                            </div>
                            <div class="carousel-item">
                                <img class="d-block m-auto" src="{{ URL::to('/images/iconss3.gif') }}" width="400" height="500" alt="Third slide">
                            </div>
                            <div class="carousel-item">
                                <img class="d-block m-auto" src="{{ URL::to('/images/icon4.gif') }}" width="400" height="500" alt="Fourth slide">
                            </div>
                            <div class="carousel-item">
                                <img class="d-block m-auto" src="{{ URL::to('/images/mainicons.gif') }}" width="400" height="500" alt="Fifth slide">
                            </div>
                            <div class="carousel-item">
                                <img class="d-block m-auto" src="{{ URL::to('/images/pureicons.gif') }}" width="400" height="500" alt="Sixth slide">
                            </div>
                        </div>
                        <!--<a class="carousel-control-prev" href="#image-carousel" role="button" data-slide="prev">-->
                        <!--    <span class="carousel-control-prev-icon" aria-hidden="true"></span>-->
                        <!--    <span class="sr-only">Previous</span>-->
                        <!--</a>-->
                        <!--<a class="carousel-control-next" href="#image-carousel" role="button" data-slide="next">-->
                        <!--    <span class="carousel-control-next-icon" aria-hidden="true"></span>-->
                        <!--    <span class="sr-only">Next</span>-->
                        <!--</a>-->
                    </div>
                    
                    <!--<img src="{{ URL::to('/images/popular.gif') }}" width="400" height="500">-->
                </div>
            </div>
            <div class="canvas-wrap2" style="z-index:1 !important;background:rgba(0,0,0,0.6);align-self:center;height:105vh">
                <table style="position:relative;top:10%">
                    <tr>
                        <td style="text-align: center;">
                            <button id="bigButton" class="bigButton" onclick="calculatePrize(); this.disabled=true;" style="color:red;border: 2px solid red; border-radius:5px; padding: 10px;">Spin the Wheel</button>
                            <div id="winnerinfo"></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center;">
                            <div class="canvas-wrap">
                                <canvas id="canvas" width="600" height="600">
                                </canvas>
                            </div>
                            <div class="text-center pt-3">
                                <h4>
                                    <b>
                                        <span class="neon-text font-weight-bold">Copyright Noorgames</span>
                                        <span class="just-neon">© 2021</span> <span class="neon-text"> All Rights Reserved</span>
                                    </b>
                                </h4>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="aside gradient-border">
               <div class="text-center mt-4" style="height:100vh">
                    <div id="image-carousel" class="carousel slide" data-ride="carousel">
                       <div class="player-list2 neon-text" style="height:-webkit-fill-available;background:rgba(0,0,0,0.6) !important;font-family:cursive;left:0px !important; width:30vw ">
                            <div style="width:22vw">
                                <strong><h3 style="margin-left:25px"><b><span class="neon-text font-weight-bold">Past Winners</span></b></h3></strong>
                                <ul>
                                    <!-- <li id="demo" style="background:rgba(0,0,0,0.6);border:1px solid red;border-radius:5px;padding:5px;margin-bottom:10px;padding:10px;list-style:none;display:flex;justify-content:space-between">
                                        <span style="font-size:25px">1</span><span class="neon-text">{{$final['winner_info']['player_name']}}</span>
                                    </li> -->
                                </ul>
                            </div>
                        </div>
                        <!--<ol class="carousel-indicators">-->
                        <!--    <li data-target="#image-carousel" data-slide-to="0" class="active"></li>-->
                        <!--    <li data-target="#image-carousel" data-slide-to="1"></li>-->
                        <!--    <li data-target="#image-carousel" data-slide-to="2"></li>-->
                        <!--    <li data-target="#image-carousel" data-slide-to="3"></li>-->
                        <!--    <li data-target="#image-carousel" data-slide-to="4"></li>-->
                        <!--    <li data-target="#image-carousel" data-slide-to="5"></li>-->
                        <!--</ol>-->
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img class="d-block m-auto" src="{{ URL::to('/images/icon.gif') }}" width="400" height="500" alt="First slide">
                            </div>
                            <div class="carousel-item">
                                <img class="d-block m-auto" src="{{ URL::to('/images/icon2.gif') }}" width="400" height="500" alt="Second slide">
                            </div>
                            <div class="carousel-item">
                                <img class="d-block m-auto" src="{{ URL::to('/images/iconss3.gif') }}" width="400" height="500" alt="Third slide">
                            </div>
                            <div class="carousel-item">
                                <img class="d-block m-auto" src="{{ URL::to('/images/icon4.gif') }}" width="400" height="500" alt="Fourth slide">
                            </div>
                            <div class="carousel-item">
                                <img class="d-block m-auto" src="{{ URL::to('/images/mainicons.gif') }}" width="400" height="500" alt="Fifth slide">
                            </div>
                            <div class="carousel-item">
                                <img class="d-block m-auto" src="{{ URL::to('/images/pureicons.gif') }}" width="400" height="500" alt="Sixth slide">
                            </div>
                        </div>
                        <!--<a class="carousel-control-prev" href="#image-carousel" role="button" data-slide="prev">-->
                        <!--    <span class="carousel-control-prev-icon" aria-hidden="true"></span>-->
                        <!--    <span class="sr-only">Previous</span>-->
                        <!--</a>-->
                        <!--<a class="carousel-control-next" href="#image-carousel" role="button" data-slide="next">-->
                        <!--    <span class="carousel-control-next-icon" aria-hidden="true"></span>-->
                        <!--    <span class="sr-only">Next</span>-->
                        <!--</a>-->
                    </div>
                        
                    <!--<img src="{{ URL::to('/images/popular.gif') }}" width="400" height="500">-->
                </div>
            </div>
        </div>
            <!--<div class="page-wrapper font-robo">-->
                <!--    <div class="wrapper wrapper--w680" style="max-width:100%">-->
                    <!--        <div class="card card-1 py-5">-->
                        <!--<div class="card-heading">-->
                            
                            <!--</div>-->
                            <!--            <div>-->
                                <!--                <h2 class="font-weight-bold text-center main-header-text">-->
                                    <!--                    WELCOME TO THE NOOR GAMES-->
                                    <!--                </h2>-->
                                    <!--            </div>-->
                                    <!--            <div class="mt-5 mx-5 text-center">-->
                                        <!--                <h3 style="line-height:2rem;">-->
                                            <!--                    <span class="neon-text font-weight-bold blink">Complete the registration process and start getting your bonus and rewards</span>. -->
                                            <!--                </h3>-->
                                            <!--                <h4 class="mt-4">-->
                                                <!--                    <span class="font-weight-bold neon-text neon-text-danger blink-danger">You are only a few steps away</span>. -->
                                                <!--                </h4>-->
                                                <!--                <h3 class="mt-4">    -->
                                                    <!--                    <span class="font-weight-bold neon-text blink">Be the owner of your luck</span>.-->
                                                    <!--                </h3>-->
                                                    
                                                    <!--            </div> -->
                                                    <!--            <div><h1>-->
                                                        <!--            </div>-->
                                                        <!--       <div class="count-div p-3 text-center neon-text" style="font-family:s!important">-->
                                                            <!--        <p class="date-div" style="height:7%;font-size:40px;"></p>-->
                                                            <!--        <p class="date-countdown " style="height:7%;font-size:59px;font-family:s!important"></p>-->
                                                            <!--      </div>-->
                                                            
                                                            <!--            <div class = "text-center logo">-->
                                                                <!--                  <img src="{{ URL::to('/images/dragonnn.gif') }}" width="220" height="250" class="w-auto">-->
                                                                <!--            </div>-->
                                                                

                                                                <!--            <div class="card-body p-5">-->
                                                                    <!--<h1 style="color:yellow; text-align:center" class="title">Welcome to Noor Games! :-D </br>Fill out the following form to get registered into our room. We will send you the <b>Monthly Match</b> based on the date you joined us as a loyal customer. </br> All the best!!!</h1>-->
                                                                    
                                                                    
                                                                    <!--                    @if ($errors->any())-->
                                                                    <!--                    <div class="alert alert-danger neon-text-danger mt-3">-->
                                                                        <!--                        <ul>-->
                                                                            <!--                            @foreach ($errors->all() as $error)-->
                                                                            
                                                                            <!--                                <h3><li>{{ $error }}</li></h3>-->
                                                                            <!--                            @endforeach-->
                                                                            <!--                        </ul>-->
                                                                            <!--                    </div>-->
                                                                            <!--                    </br>-->
                                                                            <!--                    @endif-->

                                                                            <!--                </br>-->
                                                                            <!-- </div> -->
                        


        <div class="text-center pt-3">
            <h4>
                <b>
                    <span class="neon-text font-weight-bold">Copyright Noorgames</span>
                    <span class="just-neon">© 2021</span> <span class="neon-text"> All Rights Reserved</span>
                </b>
            </h4>
        </div>
    </div>
                    <!-- </div>
                </div>
            </div>
        </div> -->
    <script>
        // document.getElementById("captcha_image").src="https://test.noorgames.net/captcha_image.php?"+Math.random();
        // var captchaImage = document.getElementById("captcha_image");
        
        // var refreshButton = document.getElementById("refresh-captcha");
        // refreshButton.onclick = function(event) {
        //     event.preventDefault();
        //     captchaImage.src = "https://noorgames.net/captcha_image.php?"+Math.random();
        // }
    </script>

    <!-- Jquery JS-->
    
    <script src="js/jquery.min.js"></script>
    <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>

    <!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>-->
    <!-- Vendor JS-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.3/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <!-- Main JS-->
    <script src="js/global.js"></script>
    <!--<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/a26d9146a0.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!--   Core JS Files   -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="{{asset('public/js/jquery-input-mask-phone-number.min.js')}}"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.js"></script>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.js"></script>
    <script type="text/javascript" src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.13/js/dataTables.jqueryui.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.2.4/js/dataTables.buttons.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.jqueryui.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.colVis.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.flash.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.html5.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.print.js"></script> 

    <script src="{{asset('public/js/core/popper.min.js')}}"></script>
    <script src="{{asset('public/js/core/bootstrap.min.js')}}"></script>
    <script src="{{asset('public/newAdmin/js/plugins/perfect-scrollbar.min.js')}}"></script>
    <script src="{{asset('public/newAdmin/js/plugins/smooth-scrollbar.min.js')}}"></script>
    <script src="{{asset('public/newAdmin/js/plugins/chartjs.min.js')}}"></script>
    <script src="{{asset('public/js/core/bootstrap.min.js')}}"></script>
    <!--<script src="{{asset('public/newAdmin/js/argon-dashboard.min.js')}}"></script>-->

    <script src="{{asset('js/editable.js')}}"></script>

    {{-- <script src="../../public/js/core/popper.min.js" type="text/javascript"></script>
    <script src="../../public/js/core/bootstrap.min.js" type="text/javascript"></script>
    <script src="../../public/js/plugins/bootstrap-notify.js"></script>
    <script src="../../public/js/light-bootstrap-dashboard790f.js" type="text/javascript"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

    <script src="{{asset('public/js/demo.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="{{asset('public/js/table.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="{{asset('public/js/jquery-input-mask-phone-number.min.js')}}"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.js"></script>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.js"></script>
    <script type="text/javascript" src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.13/js/dataTables.jqueryui.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.2.4/js/dataTables.buttons.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.jqueryui.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.colVis.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.flash.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.html5.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.print.js"></script> 

    <script src="{{asset('public/js/core/popper.min.js')}}"></script>
    <script src="{{asset('public/js/core/bootstrap.min.js')}}"></script>
    <script src="{{asset('public/newAdmin/js/plugins/perfect-scrollbar.min.js')}}"></script>
    <script src="{{asset('public/newAdmin/js/plugins/smooth-scrollbar.min.js')}}"></script>
    <script src="{{asset('public/newAdmin/js/plugins/chartjs.min.js')}}"></script>
    <script src="{{asset('public/js/core/bootstrap.min.js')}}"></script>
    <!--<script src="{{asset('public/newAdmin/js/argon-dashboard.min.js')}}"></script>-->

    <script src="{{asset('js/editable.js')}}"></script>

    {{-- <script src="../../public/js/core/popper.min.js" type="text/javascript"></script>
    <script src="../../public/js/core/bootstrap.min.js" type="text/javascript"></script>
    <script src="../../public/js/plugins/bootstrap-notify.js"></script>
    <script src="../../public/js/light-bootstrap-dashboard790f.js" type="text/javascript"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

    <script src="{{asset('public/js/demo.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="{{asset('public/js/table.js')}}"></script><script src="{{asset('public/js/jquery.mCustomScrollbar.js')}}"></script>
    <script>

    $(document).ready( function () {
        $('.captcha-input').on('keypress',function(e) {
            if(!($('.captcha-error').hasClass('hidden'))){
                $('.captcha-error').addClass('hidden');
            }
        });


        $('.submit-btn').on('click',function(e) {
            console.log('clicked');
            e.preventDefault();
            var form = $('#regForm');

            if($('input[name="full_name"]').val() == '') {
                toastr.error('Error','Enter Full Name');
                return;
            }
            if($('input[name="number"]').val() == '') {
                toastr.error('Error','Enter your number');
                return;
            }
            // if($('input[name="r_id"]')){
            //     toastr.error('Error','Enter Full Name');
            //     return;
            // }
            if($('input[name="email"]').val() == ''){
                toastr.error('Error','Enter Email');
                return;
            }
            if($('input[name="facebook_name"]').val() == ''){
                toastr.error('Error','Enter your Facebook Name');
                return;
            }
            if($('input[name="game_id"]').val() == ''){
                toastr.error('Error','Enter your Game Id');
                return;
            }
            // if($('input[name="full_name"]')){
            //     toastr.error('Error','Enter Full Name');
            //     return;
            // }

            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
            });
            $.ajax({
                type: "POST",
                url: '/checkCaptcha',
                data: {
                    "captcha": $('.captcha-input').val(),
                },
                dataType: 'json',
                success: function (data) {
                    if(data == 'true'){
                        form.submit();
                    }else{
                        toastr.error('Error','Captcha Incorrect');
                    }
                },
                error: function (data) {
                    toastr.error('Error','Something went wrong. Please Try again.');
                }
            });
        });
    });

    var x, i, j, l, ll, selElmnt, a, b, c;
    /* Look for any elements with the class "custom-select-neon": */
    x = document.getElementsByClassName("custom-select-neon");
    l = x.length;
    for (i = 0; i < l; i++) {
        selElmnt = x[i].getElementsByTagName("select")[0];
        ll = selElmnt.length;
        /* For each element, create a new DIV that will act as the selected item: */
        a = document.createElement("DIV");
        a.setAttribute("class", "select-selected");
        a.innerHTML = selElmnt.options[selElmnt.selectedIndex].innerHTML;
        x[i].appendChild(a);
        /* For each element, create a new DIV that will contain the option list: */
        b = document.createElement("DIV");
        b.setAttribute("class", "select-items select-hide");
        for (j = 1; j < ll; j++) {
            /* For each option in the original select element,
            create a new DIV that will act as an option item: */
            c = document.createElement("DIV");
            c.innerHTML = selElmnt.options[j].innerHTML;
            c.addEventListener("click", function(e) {
                /* When an item is clicked, update the original select box,
                and the selected item: */
                var y, i, k, s, h, sl, yl;
                s = this.parentNode.parentNode.getElementsByTagName("select")[0];
                sl = s.length;
                h = this.parentNode.previousSibling;
                for (i = 0; i < sl; i++) {
                    if (s.options[i].innerHTML == this.innerHTML) {
                        s.selectedIndex = i;
                        h.innerHTML = this.innerHTML;
                        y = this.parentNode.getElementsByClassName("same-as-selected");
                        yl = y.length;
                        for (k = 0; k < yl; k++) {
                            y[k].removeAttribute("class");
                        }
                        this.setAttribute("class", "same-as-selected");
                        break;
                    }
                }
                h.click();
            });
            b.appendChild(c);
        }
        x[i].appendChild(b);
        a.addEventListener("click", function(e) {
            /* When the select box is clicked, close any other select boxes,
            and open/close the current select box: */
            e.stopPropagation();
            closeAllSelect(this);
            this.nextSibling.classList.toggle("select-hide");
            this.classList.toggle("select-arrow-active");
        });
    }

    function closeAllSelect(elmnt) {
        /* A function that will close all select boxes in the document,
        except the current select box: */
        var x, y, i, xl, yl, arrNo = [];
        x = document.getElementsByClassName("select-items");
        y = document.getElementsByClassName("select-selected");
        xl = x.length;
        yl = y.length;
        for (i = 0; i < yl; i++) {
            if (elmnt == y[i]) {
                arrNo.push(i)
            } else {
                y[i].classList.remove("select-arrow-active");
            }
        }
        for (i = 0; i < xl; i++) {
            if (arrNo.indexOf(i)) {
                x[i].classList.add("select-hide");
            }
        }
    }

    /* If the user clicks anywhere outside the select box,
    then close all select boxes: */
    document.addEventListener("click", closeAllSelect);

    // 

    playerArray=[];

    playerArray['player_info']=[{
            id:"1",
            name:"Prasil"
        },
        {  
            id:"2",
            name:"Gaurav"
        },
        {
            id:"3",
            name:"Prasun"
        }
    ]

    playerArray['winner_info'] = [{
        id:"2",
        name:"Prasil",
        }
    ]

    var jsonData = JSON.parse('<?= $final_encoded; ?>');
    var jsonArray=<?= $final_encoded; ?>;

    console.log(jsonArray);

    // console.log(jsonData);

    var res = [];
    var res_winner = [];


    //build array for segments for wheel
    var segments = [];


    //console.log(res);
    jsonData.players_list.forEach(element => {
    //console.log(element);
    segments.push({
        fillStyle: generateRandomColor(),
        text: element.player_name
        });
    });

    //get the array length of player_info
    var player_info_length = jsonData.players_list.length;

    //get id from winner info and compare with player info
    var winner_info_id = jsonData.winner_info.player_id;

    document.getElementById("winnerinfo").innerHTML = "<br>winner will be "+jsonData.winner_info.player_name;

    //get the index of winner info id
    var winner_info_index = jsonData.players_list.findIndex(x => x.player_id == winner_info_id);

    //console.log(jsonData.winner_info);


    // //convert the player array into a json string
    // playerArray = JSON.stringify(playerArray);

    // //convert the json string into an object
    // playerArray = JSON.parse(playerArray);


    //170 default radi

    textSize=10

    if(player_info_length<20) textFontSize= 20
    else if(player_info_length<80) textFontSize=15
    else textFontSize=10

    spin_duration=randomIntFromInterval(10,20); //make spin time random
    spin_times=randomIntFromInterval(3,6);

    let theWheel = new Winwheel({
        'numSegments'    : player_info_length,
        'outerRadius'    : 270,
        'innerRadius'   : 50,
        'segments'       : segments,
        'textFontSize' : 10,
        'textAlignment'  : 'outer',
        'clearTheCanvas' : false,
        'animation' : {
            'type'          : 'spinToStop',
            'duration'      : spin_duration,
            'spins'         : spin_times,
            'callbackAfter' : 'drawTriangle()',
            'callbackSound' : playSound,
            'callbackFinished' : 'winAnimation()',
            }
    });

    let audio = new Audio('assets/tick.mp3');  // Create audio object and load desired file.

    function winAnimation() {
        // // Get the audio with the sound it in, then play.
        // let winsound = document.getElementById('winsound');
        // winsound.play();

        // Get the number of the winning segment.
        let winningSegmentNumber = theWheel.getIndicatedSegmentNumber();

        // Loop and set fillStyle of all segments to gray.
        for (let x = 1; x < theWheel.segments.length; x ++) {
            theWheel.segments[x].fillStyle = 'gray';
        }

        // Make the winning one yellow.
        theWheel.segments[winningSegmentNumber].fillStyle = 'yellow';

        // Call draw function to render changes.
        theWheel.draw();

        // Also re-draw the pointer, otherwise it disappears.
        drawTriangle();
    }

    function generateRandomColor(){
        // let color = "#";
        // for (let i = 0; i < 3; i++){
        //     color += ("0" + Math.floor(((1 + Math.random()) * Math.pow(16, 2)) / 2).toString(16)).slice(-2);
        // }
        let color = "#"+Math.floor(Math.random()*16777215).toString(16);
        return color;
    }

    function randomIntFromInterval(min, max) { // min and max included 
        return Math.floor(Math.random() * (max - min + 1) + min)
    }

    function playSound() {
        // Stop and rewind the sound (stops it if already playing).
        audio.pause();
        audio.currentTime = 0;

        // Play the sound.
        audio.play();
    }

    // Function with formula to work out stopAngle before spinning animation.
    // Called from Click of the Spin button.
    function calculatePrize(winner_info=winner_info_index) {
        // console.log("Winner is"+winner_info);
        // Get random angle inside specified segment of the wheel.
        let stopAt = theWheel.getRandomForSegment(winner_info_index+1);

        // Important thing is to set the stopAngle of the animation before stating the spin.
        theWheel.animation.stopAngle = stopAt;

        // Start the spin animation here.
        theWheel.startAnimation();
    }

    // Usual pointer drawing code.
    drawTriangle();

    function drawTriangle() {
        // Get the canvas context the wheel uses.
        let ctx = theWheel.ctx;

        ctx.strokeStyle = 'navy';     // Set line colour.
        ctx.fillStyle   = 'aqua';     // Set fill colour.
        ctx.lineWidth   = 2;
        ctx.beginPath();              // Begin path.
        ctx.moveTo(290, 5);           // Move to initial position.
        ctx.lineTo(290, 5);           // Draw lines to make the shape.
        ctx.lineTo(300, 40);
        ctx.lineTo(310, 5);
        ctx.stroke();                 // Complete the path by stroking (draw lines).
        ctx.fill();                   // Then fill.
    }

    // $(document).ready( function () {
    //     $('.datatable').DataTable({
    //         pageLength: 100,
    //         dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
    //         buttons: [
    //         {
    //            extend: 'copy',
    //            className: 'btn-sm btn-info',
    //            title: 'Gamers',
    //            header: false,
    //            footer: true,
    //            exportOptions: {
    //               // columns: ':visible'
    //            }
    //         },
    //         {
    //            extend: 'csv',
    //            className: 'btn-sm btn-success',
    //            title: 'Gamers',
    //            header: false,
    //            footer: true,
    //            exportOptions: {
    //               // columns: ':visible'
    //            }
    //         },
    //         {
    //            extend: 'excel',
    //            className: 'btn-sm btn-warning',
    //            title: 'Gamers',
    //            header: false,
    //            footer: true,
    //            exportOptions: {
    //               // columns: ':visible',
    //            }
    //         },
    //         {
    //            extend: 'pdf',
    //            className: 'btn-sm btn-primary',
    //            title: 'Gamers',
    //            pageSize: 'A2',
    //            header: false,
    //            footer: true,
    //            exportOptions: {
    //               // columns: ':visible'
    //            }
    //         },
    //         {
    //            extend: 'print',
    //            className: 'btn-sm btn-success',
    //            title: 'Gamers',
    //            // orientation:'landscape',
    //            pageSize: 'A2',
    //            header: true,
    //            footer: false,
    //            orientation: 'landscape',
    //            exportOptions: {
    //               // columns: ':visible',
    //               stripHtml: false
    //            }
    //         }
    //      ],
    //     });
    // } );

    jQuery(document).ready( function () {

        $(".jquery-width").css("width","100%");

        var link = $('.delete-form').attr("href");
        // var link = $('.delete-form');
        $('.datatable tbody').on('click', '.delete-form', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure you want to delete this?',
                showDenyButton: true,
                showCancelButton: true,
                showConfirmButton: false,
            // confirmButtonText: 'Save',
            denyButtonText: `Delete`,
        }).then((result) => {
            if (result.isConfirmed) {
            } 
            else if (result.isDenied) {
                var cid = $(this).data('id');
                $.ajax({
                    type: 'get',
                    url: "/gamers/destroy/"+cid,
                    data: {
                        "cid": $(this).data('id'),
                    },
                    dataType: 'json',
                    success: function (data) {
                        console.log(data);
                        $('.tr-'+cid).remove();
                        $( ".count-row" ).each(function( index ) {
                            $(this).text((index+1));
                                // console.log( index + ": " + $( this ).text() );
                            })
                        toastr.success('Success',"Deleted"); 
                    },
                    error: function (data) {
                        console.log(data);
                        toastr.error('Error',data.responseText);
                    }
                });
                    // window.location = link;
                }
            });
        });

        $('.datatable tbody').on('click', '.edit-form', function () {
            $('.editFormModal').modal('show');
            var cid = $(this).data('id');
            $.ajax({
                type: 'get',
                url: "/gamers/edit/"+cid,
                data: {
                    "cid": $(this).data('id'),
                },
                dataType: 'json',
                success: function (data) {
                    // console.log(data);
                    $('.appendHere').remove();
                    $('.editFormModalBody').append('<div class="appendHere"></div>');
                    $('.appendHere').append(data);
                    // $('#summernote').summernote();
                    // $( ".count-row" ).each(function( index ) {
                        // $(this).text((index+1));
                        // console.log( index + ": " + $( this ).text() );
                    // })
                    // toastr.success('Success',"Deleted");
                    
                },
                error: function (data) {
                    console.log(data);
                    toastr.error('Error',data.responseText);
                }
            });
        });
        $(".player-list, .player-list2").mCustomScrollbar({
          axis:"yx"
        });
    });

    x = '<?php echo Carbon\Carbon::now().'   ('.config('app.timezone').')' ?>';
    console.log(x);
    //   var dateTime = new Date();
    var weekday=new Array(7);
    weekday[0]="Sunday";
    weekday[1]="Monday";
    weekday[2]="Tuesday";
    weekday[3]="Wednesday";
    weekday[4]="Thursday";
    weekday[5]="Friday";
    weekday[6]="Saturday";

    var monthNames=new Array(7);
    monthNames[0]="January";
    monthNames[1]="February";
    monthNames[2]="March";
    monthNames[3]="April";
    monthNames[4]="May";
    monthNames[5]="June";
    monthNames[6]="July";
    monthNames[7]="August";
    monthNames[8]="September";
    monthNames[9]="October";
    monthNames[10]="November";
    monthNames[11]="December";

    var  dateTime = new Date(x);
    var dayName = weekday[dateTime.getDay()];
    var monthName2 = monthNames[dateTime.getMonth()];
    //   console.log(dateTime.getFullYear());
    //   console.log(monthNames[dateTime.getMonth()]);
    var hour, hourTemp, minute, minuteTemp, second, secondTemp, monthnumber, monthnumberTemp, monthday, monthdayTemp, year, ap;
    function timefunction() {
        dateTime.setSeconds(dateTime.getSeconds() + 1, 0);
        hourTemp = hour = dateTime.getHours();

        minuteTemp = minute = dateTime.getMinutes();
        if (minute.toString().length == 1)
            minuteTemp = "0" + minute.toString();

        secondTemp = second = dateTime.getSeconds();
        if (second.toString().length == 1)
            secondTemp = "0" + second.toString();

        monthnumberTemp = monthnumber = dateTime.getMonth();
        if ((monthnumber + 1).toString().length == 1)
            monthnumberTemp = "0" + (monthnumber + 1).toString();

        monthdayTemp = monthday = dateTime.getDate();
        if (monthday.toString().length == 1)
            monthdayTemp = "0" + monthday.toString();
        year = dateTime.getFullYear();
        // console.log(dateTime.getYear());
        ap = "AM";
        if (hour > 11) { ap = "PM"; }
        if (hour > 12) { hour = hour - 12; }
        if (hour == 0) { hour = 12; }
        if (hour.toString().length == 1)
            hourTemp = "0" + hour.toString();
        $('.date-div').text(dayName + ", "+monthdayTemp+" " + monthName2 + "," + year );
           
        $('.date-countdown').text(hourTemp + " : " + minuteTemp + " : " + secondTemp + " " + ap);
            // document.getElementById('time').innerHTML = monthnumberTemp + "/" + monthdayTemp + "/" + year + " " + hourTemp + ":" + minuteTemp + ":" + secondTemp + " " + ap;
        }
        timefunction();
        setInterval("timefunction()", 1000);
    </script>

    @if (isset($activeGame) && ($activeGame['image'] != ''))
    <script>
        $('.back-image-game').css('background-image',"url('public/uploads/{{$activeGame['image']}}')");
    </script>
    @else
    <script>
        $('.back-image-game').css('background-color','#ffb342');
    </script>
    @endif
    <script>
        $(function(){
            $('#phone-number').usPhoneFormat({
                format:'xxx-xxx-xxxx'
            });
            $('#summernote').summernote();
            $('.select2').select2({
            // dropdownParent: $('#popup3')
        });
        });
    </script>
    @if (session('success'))
    <script>
        toastr.success('{{ session('success') }}');
    </script>
    @endif
    @if (session('error'))
    @if(is_array(session('error')))
    @foreach(session('error') as $error)
    <script>
        toastr.error(' {{ $error }}');
    </script>
    @endforeach
    @else    
    <script>
        toastr.error(' {{session('error') }}');
    </script>
    @endif
    @endif


</body>
<?php
//check if current date is equal to sample date
$sample_date="2022-06-01 00:00:00";
$current_date=date("Y-m-d H:i:s");

//check if current date is equal to sample date
if($current_date==$sample_date)
{
    ?>
    <script>
        calculatePrize();
    </script>
    <?php
}
else
{
   //show javascript countdown
    echo "<div id='datebox'></div>";
}
?>

<script>
    //countown from sample date to current date
    var sample_date = new Date("<?php echo $sample_date; ?>");
    var current_date = new Date("<?php echo $current_date; ?>");
    var diff = sample_date.getTime() - current_date.getTime();
    var days = Math.floor(diff / (1000 * 60 * 60 * 24));
    var hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    var minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((diff % (1000 * 60)) / 1000);

    //show live countdown 
    document.getElementById("datebox").innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds + "s ";

    //countdown timer
    setInterval(function () {
        seconds--;
        if (seconds < 0) {
            minutes--;
            seconds = 59;
        }
        if (minutes < 0) {
            hours--;
            minutes = 59;
        }
        if (hours < 0) {
            days--;
            hours = 23;
        }
        if (days < 0) {
            days = 0;
        }
        document.getElementById("datebox").innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds + "s ";
    }, 1000);
</script>