@extends('layouts.default')

@section('content')
    <h1 class="text-xl font-semibold text-emerald-400">Search for a MAC</h1>

    <form action="/identifiers" method="get" class="bg-emerald-950 p-4 rounded-2xl border-2 border-emerald-800 text-emerald-400">
        <label for="mac_address" class="block ">Enter a MAC to search for</label>
        <input id="mac_address" type="text" name="mac_address" class="block border-2 rounded-xl p-2 border-emerald-800 focus:border-emerald-400 text-emerald-400 mb-2" placeholder="e.g. 00-11-22-00-11-A2">
        <p class="pb-4" id="mac-explanation">To search for multiple, separate them with a comma, for example: 123456,789000</p>
        <button type="submit" class="bg-emerald-400 text-black rounded-xl pt-2 pr-4 pb-2 pl-4">Search</button>
    </form>
@endsection
