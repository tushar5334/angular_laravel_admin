@include('front.common.header')
@include('front.common.menu')
<div class="container">
	<div class="card-deck mb-3 text-center">
		@yield('content')
	</div>
	@include('front.common.footer')
</div>
@include('front.include.functions')
@include('front.include.flash_message')