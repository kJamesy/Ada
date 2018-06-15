@extends('_layouts.main-template')

@section('title', 'Search Results')

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
                    <h1 class="mt-4 mb-3">
                        Search Results
                    </h1>
                    @if ( isset($originalSearch) && strlen($originalSearch) )
                        <p class="font-italic mb-5">"{!! $originalSearch !!}"</p>
                    @endif

                    @if ( count($results) )
                        @foreach ( $results as $result )
                            <article class="result mb-4">
                                <h4><a href="{{ route('guest-developer-guides.show', ['slug' => $result->slug]) }}">{{ $result->title }}</a></h4>
                                <div class="mt-3">
                                    {!! str_limit(strip_tags($result->content, '<br>'), 128) !!}
                                    <a href="{{ route('guest-developer-guides.show', ['slug' => $result->slug]) }}" class="link-arrow">&rarr;</a>
                                </div>
                            </article>
                        @endforeach

                        <div class="pagination mt-5">
                            @if ( isset($originalSearch) && strlen($originalSearch) )
                                {!! $results->appends(['search' => $originalSearch])->links() !!}
                            @else
                                {!! $results->links() !!}
                            @endif
                        </div>
                    @else
                        <div class="no-results">
                            Nothing found. Please try again with different key words.
                        </div>
                    @endif
                </div>
            </div>
        </main>
        @include('guest.includes.documentation-footer')
    </div>
@endsection

@section('footer-scripts')
    @include('guest.includes.documentation-js')
@endsection

