<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use DB, Validator;

class AddressController extends Controller
{
    public function continents()
    {
        $continents = ['Africa', 'Antarctica', 'Asia', 'Europe', 'North America', 'Oceania', 'South America'];
        return response()->json([
            'status' => count($continents) > 0 ? true : false,
            'message' => count($continents) > 0 ? '' : 'No continent found!',
            'data' => $continents
        ]);
    }

    public function countries($continent)
    {
        $response = \Http::get("https://restcountries.com/v3.1/region/{$continent}");
        if ($response->successful()) {
            $countries = $response->json();
            $countryNames = array_map(function ($country) {
                return $country['name']['common'];
            }, $countries);
            return response()->json(['status' => true, 'message' => '', 'data' => $countryNames]);
        }
        return response()->json(['status' => false, 'message' => 'No Country found!']);
    }
}