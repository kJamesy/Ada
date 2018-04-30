@extends('_layouts.main-template')

@section('title', 'Opt Out')

@section('body')
    <div class="main-content">
        <div class="am-signin-wrapper">
            <div class="am-signin-box">
                <div class="row no-gutters">
                    <div class="col-lg-5">
                        <div>
                            <h1>{{ strtoupper(config('app.name')) }}</h1>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <h5 class="tx-gray-800 mg-b-25">You have successfully opted out of our emails.</h5>
                        <h6 class="tx-gray-600 mg-b-25">
                            @if ( $name = request()->get('name') )
                                {{ "Sorry to see you go, $name" }}.
                            @else
                                {{ "Sorry to see you go." }}
                            @endif
                        </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
