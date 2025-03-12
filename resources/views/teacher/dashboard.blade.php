@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Teacher Dashboard') }}</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <h4>{{ __('Welcome, Teacher!') }}</h4>
                        <p>{{ __('You can manage your students and courses here.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection