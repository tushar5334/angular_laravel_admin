@php
use Illuminate\Support\Str;
$content = file_get_contents(public_path('/app/index.html'));
@endphp
{!! $content !!}