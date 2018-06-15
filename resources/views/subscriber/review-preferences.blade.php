@extends('_layouts.main-template')

@section('title', 'Review Preferences')

@section('body')
    <div class="main-content">
        <div class="am-signin-wrapper">
            <div class="am-signin-box">
                <div class="row no-gutters">
                    <div class="col-lg-5">
                        <div>
                            <h1>{{ strtoupper(config('newsletter.client_name')) }}</h1>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        @if ( $subscriber && $id )
                            <h5 class="tx-gray-800 mg-b-25">Please review your details below:</h5>
                            @if ( $errors->any() )
                                <div class="alert alert-info">
                                    {{ $errors->first() }}
                                </div>
                            @elseif ( session()->has('success') )
                                <div class="alert alert-warning">
                                    {{ session()->get('success') }}
                                </div>
                            @endif
                            <form method="POST" action="{{ route('subscriber.update_preferences') }}">
                                {{ csrf_field() }}
                                <input type="hidden" name="unique" value="{{ $id }}" />

                                <div class="form-group">
                                    <label for="first_name" class="form-control-label">First Name</label>
                                    <input id="first_name" type="text" class="form-control{{ $errors->has('first_name') ? ' is-invalid' : '' }}"
                                           name="first_name" value="{{ old('first_name', $subscriber->first_name) }}" >

                                    @if ($errors->has('first_name'))
                                        <small class="invalid-feedback">
                                            {{ $errors->first('first_name') }}
                                        </small>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label for="last_name" class="form-control-label">Last Name</label>
                                    <input id="last_name" type="text" class="form-control{{ $errors->has('last_name') ? ' is-invalid' : '' }}"
                                           name="last_name" value="{{ old('last_name', $subscriber->last_name) }}" >

                                    @if ($errors->has('last_name'))
                                        <small class="invalid-feedback">
                                            {{ $errors->first('last_name') }}
                                        </small>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label for="email" class="form-control-label">Email</label>
                                    <input id="email" type="text" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                           name="email" value="{{ old('email', $subscriber->email) }}">

                                    @if ($errors->has('email'))
                                        <small class="invalid-feedback">
                                            {{ $errors->first('email') }}
                                        </small>
                                    @endif
                                </div>

                                <div class="form-check tx-gray-600 mt-4 mb-3">
                                    <input class="form-check-input" type="radio" name="consent" id="iConsent" value="1" @if (old('consent', $subscriber->consent) == '1') checked @endif>
                                    <label class="form-check-label" for="iConsent">
                                        I consent to continuing to receive information from {{ config('newsletter.client_name') }}.
                                    </label>
                                </div>
                                <div class="form-check tx-gray-600">
                                    <input class="form-check-input" type="radio" name="consent" id="iDontConsent" value="0" @if (old('consent') == '0') checked @endif>
                                    <label class="form-check-label" for="iDontConsent">
                                        I withdraw my consent from {{ config('newsletter.client_name') }}.
                                    </label>
                                </div>

                                <button type="submit" class="btn btn-block">Update</button>
                            </form>
                        @else
                            <h5 class="tx-gray-800 mg-b-25">Subscriber not found.</h5>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
