<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\BankTransaction;
use Illuminate\Support\Facades\Validator;


class BankImportClass implements ToModel, WithHeadingRow
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
            'bank_name' => 'required',
            'type' => 'required',
            'date' => 'required',
            'amount' => 'required',
            'description' => 'required',
        ];

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
        return new BankTransaction([
            'bank_name' => $row['bank_name'],
            'date' => $row['date'],
            'type' => $row['type'],
            'amount' => $row['amount'],
            'description' => $row['description'],
            'company_id' => $companyId,
        ]);
    }

}
