<div class="d-flex flex-column flex-md-row align-items-center p-3 px-md-4 mb-3 bg-white border-bottom shadow-sm">
  {{-- Check if user is logged in start --}}
  @auth
  <h5 class="my-0 mr-md-auto font-weight-normal">Hi, {{ Auth::user()->name }}</h5>
  @endauth
  {{-- Check if user is logged in end --}}
  <nav class="my-2 my-md-0 mr-md-3">
    {{-- Check if user is logged in start --}}
    @auth
    <a class="p-2 text-dark" href="javascript:;">
      @if(Auth::user()->display_picture!="")
      <img src="{{ Auth::user()->display_picture}}" width="24" height="24" class="img-circle elevation-2"
        alt="User Image">
      @else
      <img src="{{ asset('/images/avatar5.png')}}" width="24" height="24" class="img-circle elevation-2"
        alt="User Image">
      @endif
    </a>
    @endauth
    {{-- Check if user is logged in end --}}

    {{-- Check if user is not logged in start --}}
    @guest
    <a class="p-2 text-dark" href="{{route('front.get.login')}}">Login</a>
    <a class="p-2 text-dark" href="{{route('front.get.register')}}">Register</a>
    @endguest
    {{-- Check if user is not logged in end --}}
  </nav>

  {{-- Check if user is logged in start --}}
  @auth
  <a class="btn btn-outline-primary" href="javascript:void(0);"
    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
  <form id="logout-form" action="{{ route('front.post.logout') }}" method="POST" style="display: none;">
    {{ csrf_field() }}
  </form>
  @endauth
  {{-- Check if user is logged in end --}}
</div>