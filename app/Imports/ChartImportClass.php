<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\ChartOfAccount;
use Illuminate\Support\Facades\Validator;


class ChartImportClass implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return null;
        }      
        $companyId = auth()->user()->company_id;
        
        // Define validation rules
        $rules = [
            'code' => 'required',
            'category' => 'required',
            'type' => 'required',
        ];

        if (!empty($row['description'])) {
            $rules['description'] = 'required';
        }
        // Validate the row data
        $validator = Validator::make($row, $rules);

        // Check if validation fails
        if ($validator->fails()) {
            // Get the validation errors
            $errors = $validator->errors()->all();

            // Flash the errors to the session
            session()->flash('import_errors', $errors);

            // Return null to skip importing this row
            return null;
        }        
        // Define how to create a model from the Excel row data
        return new ChartOfAccount([
            'code' => $row['code'],
            'category' => $row['category'],
            'type' => $row['type'],
            'description' => $row['description'],
            'company_id' => $companyId,
        ]);
    }

    // public function collection(Collection $rows)
    // {
    //     foreach ($rows as $row) {
    //         // Process each row and save data to the database
    //         ChartOfAccount::create([
    //             'description' => $row['description'],
    //             'code' => $row['code'],
    //             'type' => $row['type'],
    //             'category' => $row['category'],
    //             // Map other columns as needed
    //         ]);
    //     }
    // }
}
