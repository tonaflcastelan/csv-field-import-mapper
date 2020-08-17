<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Maatwebsite\Excel\Facades\Excel;

class CsvService
{
    public function parseData($request)
    {
        $path = $request->file('csv_file')->getRealPath();

        $data = array_map('str_getcsv', file($path));
        $headers = collect($data[0]);

        $keys = array_shift($data);  
        
        $newArray = array_map(function($values) use ($keys){
            return array_combine($keys, $values);
        }, $data);
        return [$newArray, $headers];
    }

    public function getHeaders($csvHeaders)
    {
        $headers = Config::get('constants.contacts_headers');
        $customHeaders = $csvHeaders->filter(function($item, $key) use (&$headers) {
            if (!in_array($item, $headers)) {
                return $item;
            }            
        });
        return [$headers, $customHeaders];
    }
}
