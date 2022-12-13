<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // mendapatkan data kota dari api rajaongkir
        $getCities = Http::withHeaders([
                        'key' => env('API_KEY', null),
                    ])->get('https://api.rajaongkir.com/starter/city');

        // memilih response dari rajaongkir
        $response  = $getCities['rajaongkir']['results'];

        // looping
        foreach ($response as $response) {
            // insert kota ke database
            DB::table('cities')->insert([
                'id'          => $response['city_id'],
                'province_id' => $response['province_id'],
                'province'    => $response['province'],
                'type'        => $response['type'],
                'city_name'   => $response['city_name'],
                'postal_code' => $response['postal_code'],
            ]);
        }
    }
}
