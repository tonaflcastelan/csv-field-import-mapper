<?php

namespace App\Http\Controllers;

use App\Contact;
use App\CsvData;
use App\CustomAttribute;
use App\Http\Requests\CsvRequestPost;
use App\Services\CsvService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class ImportController extends Controller
{
    protected $csvService;

    public function __construct(CsvService $service)
    {
        $this->csvService = $service;
    }

    public function index()
    {
        return view('import');
    }

    public function store(CsvRequestPost $request)
    {   
        list($data, $csvHeaders) = $this->csvService->parseData($request);
        list($headers, $customHeaders) = $this->csvService->getHeaders($csvHeaders);
        
        if (count($data) > 0) {
            $response = [
                'csv_filename' => $request->file('csv_file')->getClientOriginalName(),
                'csv_headers' => json_encode($headers),
                'csv_custom_headers' => json_encode(array_values($customHeaders->toArray())),
                'csv_data' => json_encode($data)
            ];
        
            $csv = CsvData::create($response);
            unset($data[0]);
            $csvData = [
                'csv_id' => $csv->id,
                'csv_filename' => $request->file('csv_file')->getClientOriginalName(),
                'csv_headers' => $headers,
                'csv_custom_headers' => array_values($customHeaders->toArray()),
                'csv_data' => $data
            ];
            return response()->json($csvData, 200);
        }
    }

    public function import(Request $request)
    {
        $csv = CsvData::find($request->input('csv_id'));
        $customHeaders = $request->except(['csv_id']);
        $headers = Config::get('constants.contacts_headers');

        foreach (collect(json_decode($csv->csv_data)) as $row) {
            $contact = new Contact();
            
            foreach ($headers as $index => $field) {
                $contact->$field = $row->{$field};
            }
            $contact->save();
            foreach ($customHeaders as $index => $field) {
                $customAttr = new CustomAttribute();
                $customAttr->contact_id = $contact->id;
                $customAttr->key = $field;
                $customAttr->value = $row->{$index};
                $customAttr->save();
            }
        }
        return response()->json([
            'message' => 'Import success!!'
        ], 201);
    }
}
