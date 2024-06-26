<?php

namespace App\Http\Controllers;

use App\Models\Employee;

use App\Models\Branch;
use App\Models\Bank;
use App\Models\Designation;
use App\Models\State;
use App\Models\Lga;
use App\Models\Department;
use App\Models\Nationality;
// use App\Models\Qualification;
use App\Models\Profession;
use App\Models\Position;
use App\Models\PensionManager;

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
        $employees = $employees->paginate(25);
    
        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }
        $companyId = auth()->user()->company_id;
    
        // Define variables for dropdown fields
        $branches = Branch::where('company_id', $companyId)->get();
        $banks = Bank::where('company_id', $companyId)->get();
        $designations = Designation::where('company_id', $companyId)->get();
        $states = State::where('company_id', $companyId)->get();
        $LGAs = LGA::where('company_id', $companyId)->get();
        $departments = Department::where('company_id', $companyId)->get();
        $nationalities = Nationality::where('company_id', $companyId)->get();
        // $qualifications = Qualification::where('company_id', $companyId)->get();
        $professions = Profession::where('company_id', $companyId)->get();
        $positions = Position::where('company_id', $companyId)->get();
        $pension_managers = PensionManager::where('company_id', $companyId)->get();
    
        // Return a view for creating a new employee with dropdown fields
        return view('employees.create', compact('banks','designations','branches', 'states', 'LGAs', 'departments', 'nationalities', 'professions', 'positions', 'pension_managers'));
    }
    
    
    public function store(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }
        $companyId = auth()->user()->company_id;
    
        // Validate the request data
        $validatedData = $request->validate([
            'active_status' => 'required|boolean',
            'staff_number' => 'required|string|max:255|unique:employees,staff_number',
            'sur_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'gender' => 'nullable|string|in:male,female,other',
            'designation_id' => 'nullable', //|exists:designations,id
            'last_promotion' => 'nullable|date',
            'level' => 'nullable|string|max:255',
            'step' => 'nullable|string|max:255',
            'cadre' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'height' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'email' => 'nullable|email|max:255|unique:employees,email',
            'phone' => 'nullable|string|max:255|unique:employees,phone',
            'date_employed' => 'required|date',
            'exit_date' => 'nullable|date',
            'exit_reason' => 'nullable|string|max:255',
            'bank_id' => 'nullable|exists:banks,id',
            'account_number' => 'nullable|string|max:255',
            'blood_group' => 'nullable|string|max:10',
            'genotype' => 'nullable|string|max:10',
            'in_staff_qtrs' => 'nullable|boolean',
            'region' => 'nullable|string|max:255',
            'branch_id' => 'nullable',
            'state_of_origin_id' => 'nullable',
            'LGA_of_origin_id' => 'nullable',
            'department_id' => 'nullable',
            'home_address' => 'nullable|string|max:255',
            'contact_address' => 'nullable|string|max:255',
            'personal_phone' => 'nullable|string|max:255',
            'personal_email' => 'nullable|email|max:255',
            'office_location' => 'nullable|string|max:255',
            'employment_type' => 'nullable|string|max:255',
            'marital_status' => 'nullable|string|in:single,married,divorced,widowed',
            'birth_place' => 'nullable|string|max:255',
            'spouse_name' => 'nullable|string|max:255',
            'marriage_date' => 'nullable|date',
            'nationality_id' => 'nullable',
            // 'qualifications_id' => 'nullable|exists:qualifications,id',
            'profession_id' => 'nullable',
            'confirmation_status' => 'nullable|string|max:255',
            'position_id' => 'nullable',
            'date_confirmed' => 'nullable|date',
            'pension_managers_id' => 'nullable',
            'pension_amount' => 'nullable|numeric',
            'deformity' => 'nullable|string|max:255',
            'salary' => 'nullable|numeric',
            'days_worked' => 'nullable|numeric',
            'tax_id' => 'nullable|string|max:255',
            'pension_pin' => 'nullable|string|max:255',
            'passport_number' => 'nullable|string|max:255',
            'residency_status' => 'nullable|string|max:255',
            'visa_type' => 'nullable|string|max:255',
            'visa_expiry' => 'nullable|date',
        ]);
    
        // Create a new employee record
        $validatedData['company_id'] = $companyId;
        Employee::create($validatedData);
    
        // Redirect to the employee index page with a success message
        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }
    
    public function show(Employee $employee)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }
        $companyId = auth()->user()->company_id;
        $employee = Employee::with(['bank', 'designation', 'branch', 'stateOfOrigin', 'LGAOfOrigin', 'department'])->find($employee->id);
    
    // Define variables for dropdown fields
    $branches = DB::table('branches')->get();
    $banks = DB::table('banks')->get();
    $designations = DB::table('designations')->get();
    $states = DB::table('states')->get();
    $LGAs = DB::table('LGAs')->get();
    $departments = DB::table('departments')->get();
    $nationalities = DB::table('nationalities')->get();
    $professions = DB::table('professions')->get();
    $positions = DB::table('positions')->get();
    $pension_managers = DB::table('pension_managers')->get();

    // Show the details of a specific employee with dropdown fields
    return view('employees.show', compact('employee', 'banks', 'designations', 'branches', 'states', 'LGAs', 'departments', 'nationalities', 'professions', 'positions', 'pension_managers'));

    }
    
    
    public function edit(Employee $employee)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }
        $companyId = auth()->user()->company_id;
    
        // Define variables for dropdown fields
        $branches = Branch::where('company_id', $companyId)->get();
        $banks = Bank::where('company_id', $companyId)->get();
        $designations = Designation::where('company_id', $companyId)->get();
        $states = State::where('company_id', $companyId)->get();
        $LGAs = LGA::where('company_id', $companyId)->get();
        $departments = Department::where('company_id', $companyId)->get();
        $nationalities = Nationality::where('company_id', $companyId)->get();
        // $qualifications = Qualification::where('company_id', $companyId)->get();
        $professions = Profession::where('company_id', $companyId)->get();
        $positions = Position::where('company_id', $companyId)->get();
        $pension_managers = PensionManager::where('company_id', $companyId)->get();
    
        // Return a view for editing a specific employee with dropdown fields
        return view('employees.edit', compact('employee', 'banks', 'designations', 'branches', 'states', 'LGAs', 'departments', 'nationalities', 'professions', 'positions', 'pension_managers'));
    }
    

    public function update(Request $request, Employee $employee)
    {
        // Check if the authenticated user has access to update this employee
        if (!auth()->check() || $employee->company_id !== auth()->user()->company_id) {
            return redirect()->route('employees.index')->with('error', 'Unauthorized access.');
        }
        
        // Validate the request data
        $validatedData = $request->validate([
            'active_status' => 'required|boolean',
            'staff_number' => 'required|string|max:255|unique:employees,staff_number,'.$employee->id,
            'sur_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'gender' => 'nullable|string|in:male,female,other',
            'designation_id' => 'nullable|exists:designations,id',
            'last_promotion' => 'nullable|date',
            'level' => 'nullable|string|max:255',
            'step' => 'nullable|string|max:255',
            'cadre' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'height' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'email' => 'nullable|email|max:255|unique:employees,email,'.$employee->id,
            'phone' => 'nullable|string|max:255|unique:employees,phone,'.$employee->id,
            'date_employed' => 'required|date',
            'exit_date' => 'nullable|date',
            'exit_reason' => 'nullable|string|max:255',
            'bank_id' => 'nullable|exists:banks,id',
            'account_number' => 'nullable|string|max:255',
            'blood_group' => 'nullable|string|max:10',
            'genotype' => 'nullable|string|max:10',
            'in_staff_qtrs' => 'nullable|boolean',
            'region' => 'nullable|string|max:255',
            'branch_id' => 'nullable',
            'state_of_origin_id' => 'nullable',
            'LGA_of_origin_id' => 'nullable',
            'department_id' => 'nullable',
            'home_address' => 'nullable|string|max:255',
            'contact_address' => 'nullable|string|max:255',
            'personal_phone' => 'nullable|string|max:255',
            'personal_email' => 'nullable|email|max:255',
            'office_location' => 'nullable|string|max:255',
            'employment_type' => 'nullable|string|max:255',
            'marital_status' => 'nullable|string|in:single,married,divorced,widowed',
            'birth_place' => 'nullable|string|max:255',
            'spouse_name' => 'nullable|string|max:255',
            'marriage_date' => 'nullable|date',
            'nationality_id' => 'nullable',
            // 'qualifications_id' => 'nullable|exists:qualifications,id',
            'profession_id' => 'nullable',
            'confirmation_status' => 'nullable|string|max:255',
            'position_id' => 'nullable',
            'date_confirmed' => 'nullable|date',
            'pension_managers_id' => 'nullable',
            'pension_amount' => 'nullable|numeric',
            'deformity' => 'nullable|string|max:255',
            'salary' => 'nullable|numeric',
            'days_worked' => 'nullable|numeric',
            'tax_id' => 'nullable|string|max:255',
            'pension_pin' => 'nullable|string|max:255',
            'passport_number' => 'nullable|string|max:255',
            'residency_status' => 'nullable|string|max:255',
            'visa_type' => 'nullable|string|max:255',
            'visa_expiry' => 'nullable|date',
        ]);
    
        // Update the employee record
        $employee->update($validatedData);
    
        // Redirect to the employee index page with a success message
        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }
    

    public function destroy(Employee $employee)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }
        // Ensure the employee belongs to the current user's company
        if ($employee->company_id !== auth()->user()->company_id) {
            return redirect()->route('employees.index')->with('error', 'Unauthorized access to employee.');
        }
    
        // Delete the employee record
        $employee->delete();
    
        // Redirect to the employee index page with a success message
        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }
    
}
