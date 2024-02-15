@extends('front.auth.auth_layout')
@section('content')
<div class="login-box">
  <div class="login-logo">
  </div>

  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Reset Password</p>

      <form name="frontResetPasswordForm" id="frontResetPasswordForm" method="post"
        action="{{ route('front.post.reset-password', collect(request()->segments())->last()) }}">
        <input type="hidden" class="form-login" name="token" value="{{ collect(request()->segments())->last() }}">
        @csrf
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
            <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
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
		$("#frontResetPasswordForm").validate({
			rules: {
				password: {
					required: true,
                    minlength: 5,
				},
				confirm_password: {
					required: true,
                    minlength: 5,		
					equalTo : "#password"
				}
			},
			messages: {
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