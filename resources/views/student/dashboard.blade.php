@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Student Dashboard') }}</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <h4>{{ __('Welcome, Student!') }}</h4>
                        <p>{{ __('Here you can access your courses and assignments.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection