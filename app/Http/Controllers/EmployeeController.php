<?php

namespace App\Http\Controllers;

use App\Models\Employee;

// use App\Models\Branch;
// use App\Models\State;
// use App\Models\LGA;
// use App\Models\Department;
// use App\Models\Nationality;
// use App\Models\Qualification;
// use App\Models\Profession;
// use App\Models\Position;
// use App\Models\PensionManager;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class EmployeeController extends Controller
{

    public function index(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }
        $companyId = auth()->user()->company_id;
    
        $query = $request->input('search');
        $employees = Employee::query();
    
        if ($query) {
            $employees->where('company_id', $companyId)
                ->where(function ($queryBuilder) use ($query) {
                    $queryBuilder->where('first_name', 'like', '%' . $query . '%')
                        ->orWhere('middle_name', 'like', '%' . $query . '%')
                        ->orWhere('sur_name', 'like', '%' . $query . '%')
                        ->orWhere('email', 'like', '%' . $query . '%')
                        ->orWhere('phone', 'like', '%' . $query . '%')
                        ->orWhere('department_id', function ($deptQuery) use ($query) {
                            $deptQuery->where('name', 'like', '%' . $query . '%');
                        })
                        ->orWhere('designation_id', function ($userQuery) use ($query) {
                            $userQuery->where('name', 'like', '%' . $query . '%');
                        });
                });
        } else {
            $employees->where('company_id', $companyId);
        }
    
        // Execute the query and fetch the results
        $employees = $employees->paginate(15);
    
        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        // Define variables for dropdown fields
        // $branches = Branch::all();
        // $states = State::all();
        // $LGAs = LGA::all();
        // $departments = Department::all();
        // $nationalities = Nationality::all();
        // $qualifications = Qualification::all();
        // $professions = Profession::all();
        // $positions = Position::all();
        // $pension_managers = PensionManager::all();

    $branches = DB::table('branches')->get();
    $banks = DB::table('banks')->get();
    $designations = DB::table('designations')->get();
    $states = DB::table('states')->get();
    $LGAs = DB::table('LGAs')->get();
    $departments = DB::table('departments')->get();
    $nationalities = DB::table('nationalities')->get();
    // $qualifications = DB::table('qualifications')->get();
    $professions = DB::table('professions')->get();
    $positions = DB::table('positions')->get();
    $pension_managers = DB::table('pension_managers')->get();

    
        // Return a view for creating a new employee with dropdown fields
        return view('employees.create', compact('banks','designations','branches', 'states', 'LGAs', 'departments', 'nationalities', 'professions', 'positions', 'pension_managers'));
    }
    
    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            // Define validation rules here based on your requirements
        ]);

        // Create a new employee record
        Employee::create($validatedData);

        // Redirect to the employee index page with a success message
        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    public function show(Employee $employee)
    {
        // Show the details of a specific employee
        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        // Return a view for editing a specific employee
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        // Validate the request data
        $validatedData = $request->validate([
            // Define validation rules here based on your requirements
        ]);

        // Update the employee record
        $employee->update($validatedData);

        // Redirect to the employee index page with a success message
        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        // Delete the employee record
        $employee->delete();

        // Redirect to the employee index page with a success message
        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }
}
