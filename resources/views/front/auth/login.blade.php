@extends('front.auth.auth_layout')
@section('content')

<div class="login-box mt-3 d-flex justify-content-center">
  <div class="login-logo">
  </div>
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Sign in to start your session</p>
      <form id="frontLoginForm" method="post" action="{{ route('front.post.login') }}">
        @csrf
        <div class="input-group mb-3 {{ $errors->has('email') ? ' has-error' : '' }}">
          <input type="email" class="form-control " placeholder="Email" name="email" value="{{ old('email') }}" required
            autofocus id="email">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
          @if ($errors->has('email'))
          <span class="help-block">
            <strong>{{ $errors->first('email') }}</strong>
          </span>
          @endif
        </div>

        <div class="input-group mb-3 {{ $errors->has('password') ? ' has-error' : '' }}">
          <input type="password" class="form-control" name="password" id="password" required placeholder="Password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
          @if ($errors->has('password'))
          <span class="help-block">
            <strong>{{ $errors->first('password') }}</strong>
          </span>
          @endif
        </div>
        <div class="row">
          <div class="col-7">
            <div class="icheck-primary">
              <input type="checkbox" id="remember_me" name="remember_me" value="1" {{ old('remember_me') ? 'checked'
                : '' }}>
              <label for="remember_me">
                Remember Me
              </label>
            </div>
          </div>
          <!-- /.col -->
          <div class="col-5">
            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
          </div>
          <!-- /.col -->
        </div>
        <p class="mb-1">
          <a href="{{route('front.get.forgot-password')}}">I forgot my password</a>
        </p>
        <p class="mb-0">
          <a href="{{route('front.get.register')}}" class="text-center">Register a new membership</a>
        </p>
      </form>
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
@push('custom_scripts')
<script type="text/javascript">
  $(document).ready(function () {
		$("#frontLoginForm").validate({
			rules: {
				email: {
					required: true,
				},
				password: {
					required: true,
					minlength: 5
				}
			},
			messages: {
				email: {
					required: 'Email field is required',
				},
				password: {
					required: 'The password field is required.',
					minlength: 'The password must be at least 5 characters!'
				}
			},
		});	
	});
</script>
@endpush
@stop