@extends('layouts.app')



@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-center">
                    <h3>{{ __('Registration') }}</h3>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{route('users.store')}}">
                        @csrf

                        <div class="form-group row">
                            <label for=" firstname" class="col-md-4 col-form-label text-md-right">{{ __('First Name') }}</label>

                            <div class="col-md-6">
                                <input id=" firstname" type="text" class="form-control" name=" firstname" value="{{ old(' firstname') }}" required autocomplete=" firstname" autofocus>
                                <small class="text-danger err">
                                    @error('firstname')
                                    <span>{{$message}}</span>
                                    @enderror
                                </small>
                            </div>
                        </div>

                        <!-- Last Name -->
                        <div class="form-group row">
                            <label for=" lastname" class="col-md-4 col-form-label text-md-right">{{ __('Last Name') }}</label>

                            <div class="col-md-6">
                                <input id=" lastname" type="text" class="form-control" name=" lastname" value="{{ old(' lastname') }}" required autocomplete=" lastname">
                            </div>
                        </div>

                        <!-- Username -->
                        <div class="form-group row">
                            <label for="username" class="col-md-4 col-form-label text-md-right">{{ __('Username') }}</label>

                            <div class="col-md-6">
                                <input id="username" type="text" class="form-control     " name="username" value="{{ old('username') }}" required autocomplete="username">
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('Email') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control     " name="email" value="{{ old('email') }}" required autocomplete="email">
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control     " name="password" required autocomplete="new-password">

                            </div>
                        </div>

                        <!-- Gender -->
                        <div class="form-group row">
                            <label for="gender" class="col-md-4 col-form-label text-md-right">{{ __('Gender') }}</label>

                            <div class="col-md-6">
                                <select id="gender" class="form-control    " name="gender" required>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                        </div>

                        <!-- Phone Number -->
                        <div class="form-group row">
                            <label for="phone_no" class="col-md-4 col-form-label text-md-right">{{ __('Phone Number') }}</label>

                            <div class="col-md-6">
                                <input id="phone_no" type="text" class="form-control    " name="phone_no" value="{{ old('phone_no') }}" required autocomplete="phone_no">
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Register') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection