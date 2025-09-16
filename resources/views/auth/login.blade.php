@extends('layouts.auth')

@section('content')
<!-- Login Container -->
<div id="login-container">
    <!-- Login Header -->
    <h1 class="h2 text-dark text-center push-top-bottom animation-slideDown" style="margin-left: -29px; margin-bottom: 31px;">
            <img src="{{!empty(app(App\Settings\StoreSettings::class)->store_logo) ? asset('storage/store/'.app(App\Settings\StoreSettings::class)->store_logo):asset('logo.png')}}"style="width:100px;"> {{app(App\Settings\StoreSettings::class)->store_name ? : 'Storeify'}}
    </h1>
    <!-- END Login Header -->

    <!-- Login Block -->
    <div class="block animation-fadeInQuickInv">
        <!-- Login Title -->
        <div class="block-title">
            <h2>Please Login</h2>
        </div>
        <!-- END Login Title -->

        <!-- Login Form -->
        <form id="form-login" action="{{route('login')}}" method="post" class="form-horizontal">
            @csrf
            <div class="form-group">
                <div class="col-xs-12">
                    <input type="text" name="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}"  autocomplete="email" placeholder="Your Username..">
                  @error('email')
                      <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                      </span>
                  @enderror
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-12">
                    <input type="password" class="form-control @error('password') is-invalid @enderror" name="password"  autocomplete="current-password" placeholder="Your password..">
                    @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                </div>
            </div>
            <div class="form-group form-actions">
                <div class="col-xs-8">
                    <label class="csscheckbox csscheckbox-primary">
                        <input type="checkbox" id="login-remember-me" name="login-remember-me">
                        <span></span>
                    </label>
                    Remember Me?
                </div>
                <div class="col-xs-4 text-right">
                    <button type="submit" name="login-submit" class="btn btn-effect-ripple btn-sm btn-primary"><i class="fa fa-check"></i> Let's Go</button>
                </div>
            </div>
        </form>
        <!-- END Login Form -->
    </div>
    <!-- END Login Block -->

    <!-- Footer -->
    <footer class="text-muted text-center animation-pullUp">
        <small><span id="year-copy">{{date('Y')}}</span> &copy; <a href="" target="_blank">{{app(App\Settings\StoreSettings::class)->store_name ? : 'Storeify'}}</a></small>
    </footer>
    <!-- END Footer -->
</div>
@endsection
