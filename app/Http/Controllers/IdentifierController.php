<?php

namespace App\Http\Controllers;

use App\Helpers\Mac;
use App\Models\Identifier;
use Illuminate\Http\Request;

class IdentifierController extends Controller
{
    public function show(Request $request)
    {
        $searchString = (new Mac)->convertToOui($request->input('mac_address'));
        $identifier = Identifier::where('assignment', $searchString)->first();

        if (!$identifier) {
            return response()->json(['message' => 'MAC address not found'], 404);
        }

        return response()->json([
            'data' => [
                'mac_address' => $request->input('mac_address'),
                'assignment' => $identifier->assignment,
                'vendors' => $identifier->organisations->pluck('name')->toArray()
            ]
        ]);
    }

    public function find(Request $request)
    {
        $data = collect();
        foreach ($request->input('mac_addresses') as $address) {
            $searchString = (new Mac)->convertToOui($address);
            $identifier = Identifier::where('assignment', $searchString)->first();

            if ($identifier) {
                $data->push([
                    'mac_address' => $address,
                    'assignment' => $identifier->assignment,
                    'vendors' => $identifier->organisations->pluck('name')->toArray()
                ]);
            }
        }

        return response()->json([
            'data' => $data
        ]);
    }
}
