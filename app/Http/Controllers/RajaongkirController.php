<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Log;

class RajaongkirController extends Controller
{
    public function index()
    {
        return view('index');
    }

    // mendapatkan kota untuk select2
    public function getCities(Request $request)
    {
        $data = [];
        if ($request->filled('q')) {
            $data = DB::table('cities')->where('city_name', 'LIKE', $request->get('q').'%')->select('id', 'city_name', 'type')->get();
        }
        return response()->json($data);
    }

    // cek ongkir
    public function getCosts(Request $request)
    {
        try {
            $couriers = ['jne', 'pos', 'tiki']; // kode kurir pengiriman: ['jne', 'pos', 'tiki']

            foreach ($couriers as $couriers) { 
                $costs = Http::withHeaders([
                    'key' => env('API_KEY', null),
                ])->post('https://api.rajaongkir.com/starter/cost', [
                    'origin'      => $request->asal, // ID kota/kabupaten asal
                    'destination' => $request->tujuan, // ID kota/kabupaten tujuan
                    'weight'      => $request->berat, // berat barang dalam gram
                    'courier'     => $couriers, // jasa pengiriman
                ]);
                $response = $costs['rajaongkir']['results'][0];
                foreach ($response['costs'] as $perJenis) {
                    $data[] = [
                        'kurir'  => $response['code'],
                        'jenis'  => $perJenis['service'],
                        'ongkir' => $perJenis['cost'][0]['value'],
                        'lama'   => explode(' ', $perJenis['cost'][0]['etd'])[0],
                    ];
                }
            }
            return response()->json($data);
        } catch (\Exception $e) {
            $error = "Terdapat kesalahan";
            return response()->json($error);
        }
    }
}
