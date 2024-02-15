@extends('front.auth.auth_layout')
@section('content')
<div class="login-box mt-3 d-flex justify-content-center">
  <div class="login-logo">
  </div>

  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Register a new membership</p>

      <form name="frontRegisterForm" id="frontRegisterForm" method="post" action="{{ route('front.post.register') }}">
        @csrf
        <div class="input-group mb-3" {{ $errors->has('name') ? ' has-error' : '' }}>
          <input type="text" class="form-control" placeholder="Full name" name="name" id="name" type="text"
            value="{{ old('name') }}">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
          @if ($errors->has('name'))
          <span class="help-block">
            <strong>{{ $errors->first('name') }}</strong>
          </span>
          @endif
        </div>
        <div class="input-group mb-3 {{ $errors->has('email') ? ' has-error' : '' }}">
          <input type="email" class="form-control" placeholder="Email" name="email" id="email" type="text"
            value="{{ old('email') }}">
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
          <input type="password" class="form-control" placeholder="Password" name="password" id="password"
            type="password" value="{{ old('password') }}">
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
        <div class="input-group mb-3 {{ $errors->has('confirm_password') ? ' has-error' : '' }}">
          <input type="password" class="form-control" placeholder="Retype password" name="confirm_password"
            id="confirm_password" type="password" value="{{ old('confirm_password') }}">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
          @if ($errors->has('confirm_password'))
          <span class="help-block">
            <strong>{{ $errors->first('confirm_password') }}</strong>
          </span>
          @endif
        </div>
        <div class="row">
          <!-- /.col -->
          <div class="col-6">
            <button type="submit" class="btn btn-primary btn-block">Register</button>
          </div>
          <!-- /.col -->
        </div>
      </form>
      <a href="{{ route('front.get.login') }}" class="text-center">I already have a membership</a>
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
@push('custom_scripts')
<script type="text/javascript">
  $(document).ready(function () {		
		$("#frontRegisterForm").validate({
			rules: {
				name: {
					required: true,
				},
				/* last_name: {
					required: true,
				},
				phone: {
					required: true,
				}, */
				email: {
					required: true,
				},
				password: {
					required: true,		
				},
				confirm_password: {
					required: true,		
					equalTo : "#password"
				}
			},
			messages: {
				name: {
					required: 'Full name field is required',
				},
			/* 	last_name: {
					required: 'Last name field is required',
				},
				phone: {
					required: 'Phone number field is required',
				}, */
				email: {
					required: 'Email field is required',
				},
				password: {
					required: 'The password field is required.',
					minlength: 'The password must be at least 5 characters.'
				},
				confirm_password: {
					required: 'The confirm password field is required.',
					minlength: 'The confirm password must be at least 5 characters.',
					equalTo: 'Your password and confirmation password do not match.',
				}
			},
		});	
	});
</script>
@endpush
@stop