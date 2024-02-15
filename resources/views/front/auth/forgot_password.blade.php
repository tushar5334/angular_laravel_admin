@extends('front.auth.auth_layout')
@section('content')
<div class="login-box mt-3 d-flex justify-content-center">
  <div class="login-logo">
  </div>

  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">You forgot your password? Here you can easily retrieve a new password.</p>

      <form id="frontForgotPasswordForm" name="frontForgotPasswordForm" method="post"
        action="{{ route('front.post.forgot-password') }}">
        @csrf
        <div class="input-group mb-3 {{ $errors->has('email') ? ' has-error' : '' }}">
          <input type="email" class="form-control" placeholder="Email" name="email" type="text"
            value="{{ old('email') }}" id="email">
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
        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block">Request new password</button>
          </div>
          <!-- /.col -->
        </div>
      </form>

      <p class="mt-3 mb-1">
        <a href="{{route('front.get.login')}}">Login</a>
      </p>
      <p class="mb-0">
        {{-- <a href="{{route('front.register')}}" class="text-center">Register a new membership</a> --}}
      </p>
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
@push('custom_scripts')
<script type="text/javascript">
  $(document).ready(function () {		
		$("#frontForgotPasswordForm").validate({
			rules: {
				email: {
					required: true,
				}
			},
			messages: {
				email: {
					required: 'Email field is required',
				}
			},
		});	
	});
</script>
@endpush
@stop