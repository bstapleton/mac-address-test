<?php

namespace App\Http\Controllers;

use App\Helpers\Mac;
use App\Models\Identifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IdentifierController extends Controller
{
    public function index(): View
    {
        return view('identifiers.index');
    }

    public function get(Request $request): JsonResponse
    {
        $searchString = (new Mac)->convertToOui($request->input('mac_address'));
        $identifier = Identifier::where('assignment', $searchString)->first();

        if (!$identifier) {
            return response()->json(['data' => [
                'message' => 'MAC address not found'
            ]], 404);
        }

        return response()->json([
            'data' => [
                'mac_address' => $request->input('mac_address'),
                'assignment' => $identifier->assignment,
                'vendors' => $identifier->organisations->pluck('name')->toArray()
            ]
        ]);
    }

    public function find(Request $request): JsonResponse
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

        if ($data->isEmpty()) {
            return response()->json(['data' => [
                'message' => 'No MAC addresses found'
            ]], 404);
        }

        return response()->json([
            'data' => $data->toArray()
        ]);
    }

    public function results(Request $request): View
    {
        $array = explode(',', $request->input('mac_address'));
        if (count($array) > 1) {
            $request->merge(['mac_addresses' => $array]);
            $data = $this->find($request)->getData()->data;
        } else {
            $data = $this->get($request)->getData()->data;
        }

        // We got an error from the API, so let the UI know to show a friendly message to retry
        if (!is_array($data) && property_exists($data, 'message')) {
            return view('identifiers.show', ['data' => null]);
        }

        return view('identifiers.show', ['data' => $data]);
    }
}
