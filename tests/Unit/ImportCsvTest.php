<?php

namespace Tests\Unit;

use App\Services\CsvService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ImportCsvTest extends TestCase
{
    use RefreshDatabase;

    private function fakeFile()
    {
        $header = 'Header 1,Header 2,Header 3';
        $row1 = 'value 1,value 2,value 3';
        $row2 = 'value 1,value 2,value 3';
    
        $content = implode("\n", [$header, $row1, $row2]);
    
        return [
            'csv_file' =>
                UploadedFile::
                    fake()->
                    createWithContent(
                        'test.csv',
                        $content
                    )
        ];
    }

    /** @test */
    public function it_should_import_from_csv_file()
    {
        $this->withoutExceptionHandling();
        $header = 'Header 1,Header 2,Header 3';
        $row1 = 'value 1,value 2,value 3';
        $row2 = 'value 1,value 2,value 3';
    
        $response = $this->postJson('/api/imports', $this->fakeFile());
        $responseData = json_decode($response->getContent());
        $response->assertStatus(200);
        $this->assertEquals($responseData->csv_filename, 'test.csv');
    }
}
