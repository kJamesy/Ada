@extends('_layouts.main-template')

@section('title', $page->title)

@section('header-styles-scripts')
    @include('guest.includes.documentation-header-styles-scripts')
@endsection

@section('body')
    <div id="documentation" class="developer-guide">
        <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
            <a class="navbar-brand" href="{{ route('guest-developer-guides.index') }}">{{ strtolower(config('app.name')) }} developer guide</a>
            <button class="navbar-toggler p-0 border-0" type="button" data-toggle="offcanvas">
                <span class="navbar-toggler-icon"></span>
            </button>

            @if ( config('newsletter.show_admin_link') )
                <div class="collapse navbar-collapse" id="mainNav">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.home') }}">admin</a>
                        </li>
                    </ul>
                </div>
            @endif
        </nav>

        <main role="main" class="container">
            <div class="row">
                <div class="col-12 col-lg-3 pt-4 offcanvas-collapse">
                    <form method="get" action="{{ route('guest-developer-guides.search') }}">
                        <label class="form-control-label">&nbsp;</label>
                        <div class="form-group">
                            <input placeholder="Search" class="form-control" type="text" name="search" value="{{ isset($originalSearch) && strlen($originalSearch) ? $originalSearch : '' }}">
                        </div>
                    </form>

                    @if ( $menu )
                        {!! $menu !!}
                    @endif
                </div>
                <div class="col-12 col-lg-9 pt-4 main-content">
                    <h1 class="mt-4">
                        @if ( $isHome )
                            Developer Guide
                        @else
                            {{ $page->title }}
                        @endif
                    </h1>
                    @if ( $anchorMenu )
                        <div class="anchor-menu">
                            <ul class="">
                                @foreach ( $anchorMenu as $item )
                                    <li class="">#{!! $item !!}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {!! $content !!}
                </div>
            </div>
        </main>
        @include('guest.includes.documentation-footer')
    </div>
@endsection

@section('footer-scripts')
    @include('guest.includes.documentation-js')
@endsection

