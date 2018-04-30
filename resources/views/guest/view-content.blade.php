<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>{{ $resource->title }} | {{ strtoupper(config('app.name')) }}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
    <style>
        html, body {
            overflow-x: hidden;
        }

        body {
            padding-top: 56px; /* Height of navbar */
            font-family: "Open Sans", sans-serif, Arial;
            color: rgb(73, 80, 87);
        }

        .navbar-brand {
            color: rgb(232, 62, 140) !important;
            font-weight: bold;
        }

        .offcanvas-collapse {
            position: fixed;
            z-index: 10;
            background-color: #343A40;
        }

        .sidebar {
            top: 2rem;
            height: calc(100vh - 2rem);
        }

        .sidebar h3 {
            color: rgb(255, 255, 255);
            font-size: 1.2rem;
        }

        .sidebar a {
            color: rgb(255, 255, 255);
        }

        .sidebar a:hover, .sidebar a:focus, .sidebar a:active {
            color: rgb(232, 62, 140);
        }

        .sidebar li.active a {
            color: rgb(232, 62, 140);
        }

        @media (max-width: 992px) {
            .offcanvas-collapse {
                position: fixed;
                top: 56px; /* Height of navbar */
                bottom: 0;
                left: 100%;
                width: 100%;
                padding-right: 1rem;
                padding-left: 1rem;
                overflow-y: auto;
                visibility: hidden;
                transition-timing-function: ease-in-out;
                transition-duration: .3s;
                transition-property: left, visibility;
            }
            .offcanvas-collapse.open {
                left: 0;
                visibility: visible;
            }
        }

        @media (min-width: 992px) {
            .sidebar {
                overflow-y: auto;
            }

            .main-content {
                margin-left: 24%;
                padding-left: 4rem !important;
            }
        }
    </style>
</head>

<body class="bg-light">

<nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-dark">
    <div class="navbar-brand mr-auto mr-lg-0">
        {{ strtoupper(config('app.name')) }}
    </div>
    <button class="navbar-toggler p-0 border-0" type="button" data-toggle="offcanvas">
        <span class="navbar-toggler-icon"></span>
    </button>
</nav>

<main role="main" class="container-fluid">
    <div class="row">
        <div class="col-12 col-lg-3 offcanvas-collapse">
            <div class="sidebar p-3">
                <h3>{{ strtoupper($resource->title) }}</h3>
                @if ( $menu )
                    <ul class="navbar-nav mr-auto">
                        @foreach ( $menu as $item )
                            <li class="nav-item">{!! $item !!}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
        <div class="col-12 col-lg-9 my-3 p-3 bg-white main-content">
            {!! $content !!}
        </div>
    </div>
</main>

<script src="//code.jquery.com/jquery-3.3.1.min.js" crossorigin="anonymous"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
<script src="//stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
<script>
    jQuery(document).ready(function($) {
        'use strict';

        $('[data-toggle="offcanvas"]').on('click', function () {
            $('.offcanvas-collapse').toggleClass('open')
        });

        let $anchoredLinks = $('.sidebar a');
        let headerHeight = $('.navbar').outerHeight();

        $anchoredLinks.each(function() {
            if ( $(this).attr('href').toLowerCase() === location.hash.toLowerCase() ) {
                $(this).parent().addClass('active');
                return false;
            }
        });

        $anchoredLinks.click(function() {
            if ( location.pathname.replace(/^\//,'').toLowerCase() === this.pathname.replace(/^\//,'').toLowerCase() && location.hostname.toLowerCase() === this.hostname.toLowerCase() ) {
                let target = $(this.hash);
                let $this = $(this);
                let $parent = $this.parent();
                let hash = $this.attr('href').toLowerCase();
                let $htmlBody = $('html,body');

                if (target.length) {
                    let t = target.offset().top - headerHeight;

                    $('.offcanvas-collapse').removeClass('open');

                    $htmlBody.stop().animate({
                        scrollTop: t
                    }, 800, function() {
                        location.hash = hash;
                        $parent.siblings().removeClass('active');
                        $parent.addClass('active');
                        $htmlBody.scrollTop(t);
                    });

                    return false;
                }
            }
        });

        $('.main-content img').each(function() {
            $(this).addClass('img-fluid');
        });
    });
</script>
</body>
</html>
