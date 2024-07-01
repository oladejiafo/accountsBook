<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payroll;
use App\Models\Employee;
use App\Models\PayrollDetail;
use App\Models\Transaction;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }
    
        $companyId = auth()->user()->company_id;
        $query = Payroll::where('company_id', $companyId)->orderBy('id', 'desc');
    
        // Handle search functionality
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('employee', function ($employeeQuery) use ($search) {
                    $employeeQuery->where('first_name', 'like', "%$search%")
                                  ->orWhere('last_name', 'like', "%$search%");
                });
            });
        }
    
        $perPage = $request->input('per_page', 25);
        $payrolls = $query->paginate($perPage);
    
        return view('payroll.index', compact('payrolls'));
    }
    
    public function create()
    {
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }
    
        $companyId = auth()->user()->company_id;

        $employees = Employee::where('company_id', auth()->user()->company_id)->get();
        return view('payroll.create', compact('employees'));
    }

    public function store(Request $request)
    {
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }
    
        $companyId = auth()->user()->company_id;

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'year' => 'required|integer',
            'month' => 'required|integer|between:1,12',
        ]);


        try {
            // Fetch active employee IDs (replace with your logic to fetch active employees)
            $activeEmployeeIds = $this->fetchActiveEmployeeIds();

            // Ensure the selected employee is active
            if (!in_array($request->employee_id, $activeEmployeeIds)) {
                return redirect()->back()->withErrors(['employee_id' => 'Selected employee is not active or eligible for payroll.']);
            }

            // Fetch employee details from external source (example)
            $employeeDetails = $this->fetchEmployeeDetails($request->employee_id); // Replace with your logic

            // Calculate payroll components based on fetched data
            $basicSalary = $employeeDetails['basic_salary'] ?? 0.00;
            $allowances = $employeeDetails['allowances'] ?? 0.00;
            $deductions = $employeeDetails['deductions'] ?? 0.00;
            // Additional calculations for overtime, taxes, etc.

            // Create the payroll entry
            $payroll = new Payroll();
            $payroll->company_id = $companyId;
            $payroll->employee_id = $request->employee_id;
            $payroll->basic_salary = $basicSalary;
            $payroll->allowances = $allowances;
            $payroll->deductions = $deductions;
            $payroll->total_pay = $basicSalary + $allowances - $deductions; 
            $payroll->payment_date = now();
            $payroll->period = "{$request->month}-{$request->year}";
            $payroll->save();

            if ($allowances > 0) {
                $allowancesDetail = new PayrollDetail([
                    'company_id' => $payroll->company_id,
                    'payroll_id' => $payroll->id,
                    'component' => 'Allowance',
                    'amount' => $allowances,
                ]);
                $allowancesDetail->save();
            }

            if ($deductions > 0) {
                $deductionsDetail = new PayrollDetail([
                    'company_id' => $payroll->company_id,
                    'payroll_id' => $payroll->id,
                    'component' => 'Deduction',
                    'amount' => $deductions,
                ]);
                $deductionsDetail->save();
            }

            // Example: Creating a transaction entry for payroll
            $transaction = new Transaction([
                'company_id' => $payroll->company_id,
                'type' => 'Payroll',
                'amount' => $payroll->total_pay,
                'description' => 'Payroll for employee: ' . $payroll->employee->full_name,
                // Add more fields as needed
            ]);
            $transaction->save();

            return redirect()->route('payrolls.index')->with('success', 'Payroll generated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to generate payroll: ' . $e->getMessage()]);
        }
    }

    private function fetchActiveEmployeeIds()
    {
        return Employee::where('status', 'active')->pluck('id')->toArray();
    }

    private function fetchEmployeeDetails($employeeId)
    {
        // Replace with your logic to fetch employee details from another source
        // Example: DB query or API call to retrieve detailed payment information
        return [
            'basic_salary' => 5000.00,
            'allowances' => 1000.00,
            'deductions' => 500.00,
            // Add other details as needed
        ];
    }

    public function rollback($id)
    {
        // Find the original payroll record to be rolled back
        $originalPayroll = Payroll::findOrFail($id);

        // Create a new payroll entry based on the original payroll data
        $newPayroll = new Payroll();
        $newPayroll->company_id = $originalPayroll->company_id;
        $newPayroll->employee_id = $originalPayroll->employee_id;
        $newPayroll->basic_salary = $originalPayroll->basic_salary;
        $newPayroll->allowances = $originalPayroll->allowances;
        $newPayroll->deductions = $originalPayroll->deductions;
        $newPayroll->total_pay = $originalPayroll->total_pay;
        $newPayroll->payment_date = now(); // or set a specific date for rollback
        $newPayroll->payment_method = $originalPayroll->payment_method;
        $newPayroll->period = $originalPayroll->period;

        // Save the new payroll entry
        $newPayroll->save();

        // Redirect or return response after successful rollback
    }
}
