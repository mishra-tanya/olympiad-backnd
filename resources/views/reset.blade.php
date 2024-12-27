<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
    .custom-card {
        max-width: 400px;
        margin: auto;
        margin-top: 5%;
    }
</style>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg" style="">
                <div class="text-dark" style="text-align:center;font-weight:bold;
                font-size:21px;
                padding:20px;">{{ __('Reset Password') }}</div>
                <span style="text-align:center; 
                font-size:12px;
                padding:2px;">** Enter New Password ** </span>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-left">{{ __('Your Email') }}</label>

                            <div class="col-md-8">
                                <input id="email" type="email" class="form-control " name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                           
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-left">{{ __('New Password') }}</label>

                            <div class="col-md-8">
                                <input id="password" type="password" class="form-control" name="password" required autocomplete="new-password" minlength="6">

                              
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-left">{{ __('Confirm Password') }}</label>

                            <div class="col-md-8">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password"minlength="6">
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary"style="background-color: #1976d2;
                               
                                ">
                                    {{ __('Reset Password') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>