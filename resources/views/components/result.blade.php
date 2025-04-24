<div class="bg-emerald-950 p-4 rounded-2xl border-2 border-emerald-800 text-emerald-400 mb-4">
    <h2 class="text-xl font-semibold">Results for: {{ $result->mac_address }}</h2>
    <p class=""><span class="font-bold">Matched OUI assignment: </span>{{ $result->assignment }}</p>
    @if (count($result->vendors))
        <p class="font-bold">Matched vendor{{ count($result->vendors) > 1 ? 's' : '' }}:</p>
        <ul class="list-disc ml-4">
            @foreach ($result->vendors as $vendor)
                <li class="text-emerald-400">{{ $vendor }}</li>
            @endforeach
        </ul>
    @endif
    @if ($result->is_potentially_randomised)
        <p class="italic">The searched-for MAC address is potentially randomised/obfuscated. This usually occurs when the second alphanumeric character is one of the following: 2, 6, A, or E.</p>
    @endif
    <p><a class="underline hover:no-underline" href="/">Search for another?</a></p>
</div>
