<div class="bg-emerald-950 p-4 rounded-2xl border-2 border-emerald-800 text-emerald-400 mb-4">
    <h2 class="text-xl font-semibold">Results for: {{ $result->mac_address }}</h2>
    <p class=""><span class="font-bold">Matched OUI assignment: </span>{{ $result->assignment }}</p>
    <p class="font-bold">Matched vendor{{ count($result->vendors) > 1 ? 's' : '' }}:</p>
    <ul class="list-disc ml-4">
        @foreach ($result->vendors as $vendor)
            <li class="text-emerald-400">{{ $vendor }}</li>
        @endforeach
    </ul>
    <p><a class="underline hover:no-underline" href="/">Search for another?</a></p>
</div>
