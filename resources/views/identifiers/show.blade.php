@extends('layouts.default')

@section('content')
    <h1 class="text-xl font-semibold text-emerald-400">Search results</h1>

    @if(isset($data))
        @if (is_array($data))
            @foreach ($data as $result)
                @include('components.result', ['result' => $result])
            @endforeach
        @else
            @include('components.result', ['result' => $data])
        @endif
    @else
        <div class="bg-emerald-950 p-4 rounded-2xl border-2 border-emerald-800 text-emerald-400">
            <p>We didn't find anything. <a class="underline hover:no-underline" href="/">Try again?</a></p>
        </div>
    @endif

@endsection
