<?php
namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Company;
use App\Models\Customer;
use App\Models\SaleBill;
use App\Models\PurchaseBill;
use App\Models\SaleBillDetails;
use App\Models\PurchaseBillDetails;
use App\Models\Payment;
use App\Models\User;
use App\Models\Role;
use App\Models\Transaction;
use App\Models\Department;
use App\Models\Designation;
use App\Models\ModelHasRole;
use App\Models\Permission;
use App\Models\RolePermission;
use App\Models\Module;
use App\Models\SubModule;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class HomeController extends Controller
{

    public function index(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }

        // Define filter options
        $filterOptions = [
            '7_days' => 'Last 7 days',
            '30_days' => 'Last 30 days',
            'last_month' => 'Last month',
        ];

        // Get insights
        $insights = [
            [
                'icon' => 'fas fa-users text-primary',
                'title' => 'Number of Customers',
                'value' => Customer::count(),
                'description' => 'Total number of registered customers',
            ],
            [
                'icon' => 'fas fa-chart-line text-success',
                'title' => 'Total Sales',
                'value' => '$' . SaleBillDetails::sum('total'),
                'description' => 'Total amount of sales made',
            ],
            [
                'icon' => 'fas fa-money-bill-wave text-info',
                'title' => 'Total Payments',
                'value' => '$' . Payment::sum('paid_amount'),
                'description' => 'Total amount of payments received',
            ],
            [
                'icon' => 'fas fa-cubes text-warning',
                'title' => 'Restock Level',
                'value' => Stock::where('quantity', '<=', 10)->count(),
                'description' => 'Number of products with low stock levels',
            ],
            [
                'icon' => 'fas fa-shopping-cart text-danger',
                'title' => 'Total Purchases',
                'value' => '$' . PurchaseBillDetails::sum('total'),
                'description' => 'Total amount spent on purchases',
            ],
            // Add any additional insights here
        ];

        return view('home.index', compact('insights', 'filterOptions'));
    }


    public function insightDashboard(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }

        $companyId = null;
        $companyName = null;

        if (auth()->user()) {
            $companyId = auth()->user()->company_id;
            $company = Company::find($companyId);

            if ($company) {
                $companyName = $company->name;
            } else {
                $companyId =1;
                $companyName = "Demo";
            }
        } else {
            $companyId =1;
            $companyName = "Demo";
        }
        // dd($companyName);
        $labels = [];
        $data = [];
        $catt = [];
        // $datas = Stock::pluck('quantity', 'name')->toArray();
        $stockQuery = Stock::where('company_id', $companyId)
            ->where('is_deleted', false)
            ->orderByDesc('quantity')
            ->take(10)
            ->get();

        foreach ($stockQuery as $item) {
            $labels[] = $item->name;
            $data[] = $item->quantity;
            $catt[] = $item->category->name;
        }
        
        $sales = SaleBill::where('company_id', $companyId)
            ->orderByDesc('created_at')
            ->take(3)
            ->get();

        $purchases = PurchaseBill::where('company_id', $companyId)
            ->orderByDesc('created_at')
            ->take(3)
            ->get();
        // dd($purchases,$sales);
        return view('home.inventoryInsights', compact('labels', 'data','catt', 'sales', 'purchases', 'companyName'));
    }

    public function globalSearch(Request $request)
    {
        $query = $request->input('query');

        // Search Users
        $users = User::where('name', 'like', "%$query%")->limit(5)->get();
        
        // Search Transactions
        $transactions = Transaction::where('description', 'like', "%$query%")->limit(5)->get();

        // // Search Sales
        // $sales = Sale::where('product_name', 'like', "%$query%")->limit(5)->get();

        // // Search Supplier Purchases
        // $supplierPurchases = Purchase::where('product_name', 'like', "%$query%")->limit(5)->get();

        // Combine search results from different models into a single array
        $searchResults = [];
        
        // Users
        foreach ($users as $user) {
            $searchResults[] = [
                'type' => 'User',
                'name' => $user->name,
                'link' => route('users.show', $user->id), // Example link to user profile
            ];
        }

        // Transactions
        foreach ($transactions as $transaction) {
            $searchResults[] = [
                'type' => 'Transaction',
                'description' => $transaction->description,
                'link' => route('transactions.index', $transaction->id), // Example link to transaction detail
            ];
        }

        // // Sales
        // foreach ($sales as $sale) {
        //     $searchResults[] = [
        //         'type' => 'Sale',
        //         'product_name' => $sale->product_name,
        //         'link' => route('sale.detail', $sale->id), // Example link to sale detail
        //     ];
        // }

        // // Supplier Purchases
        // foreach ($supplierPurchases as $purchase) {
        //     $searchResults[] = [
        //         'type' => 'Supplier Purchase',
        //         'product_name' => $purchase->product_name,
        //         'link' => route('supplier.purchase.detail', $purchase->id), // Example link to supplier purchase detail
        //     ];
        // }

        return response()->json($searchResults);
    }

    public function about()
    {
        return view('home.about');
    }

    // User Index
    public function usersIndex(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;

        $query = $request->input('search');
        $users = User::query();

        if ($query) {
            $users->where('company_id', $companyId)
                ->where(function ($queryBuilder) use ($query) {
                    $queryBuilder->where('name', 'like', '%' . $query . '%')
                        ->orWhere('email', 'like', '%' . $query . '%')
                        ->orWhereHas('department', function ($deptQuery) use ($query) {
                            $deptQuery->where('name', 'like', '%' . $query . '%');
                        })
                        ->orWhereHas('designation', function ($userQuery) use ($query) {
                            $userQuery->where('name', 'like', '%' . $query . '%');
                        });
                });
        } else {
            $users->where('company_id', $companyId);
        }

        // Execute the query and fetch the results
        $users = $users->paginate(15);

        return view('users.index', compact('users'));
    }

    // User Create Form
    public function usersCreate()
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->get();

        $roles = Role::where('company_id', $companyId)->get();
        $departments = Department::where('company_id', $companyId)->get();
        $designations = Designation::where('company_id', $companyId)->get();
        return view('users.create', compact('roles', 'departments', 'designations'));
    }

    // Store User
    public function usersStore(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->get();

        // Validate request data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'department_id' => 'nullable|exists:departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            // Add more validation rules as needed
        ]);

        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        $user->company_id = auth()->user()->company_id; // Set company_id

        // Save department and designation
        $user->department_id = $request->input('department_id');
        $user->designation_id = $request->input('designation_id');

        $user->save();


        // Sync roles associated with the current company
        $companyRoles = Role::where('company_id', auth()->user()->company_id)->pluck('id');
        $roles = $request->input('roles', []);
        foreach ($roles as $roleId) {
            // Create entry in model_has_roles table only if the role is associated with the current company
            if ($companyRoles->contains($roleId)) {
                ModelHasRole::create([
                    'model_id' => $user->id,
                    'model_type' => get_class($user),
                    'role_id' => $roleId,
                    'company_id' => auth()->user()->company_id,
                ]);
            }
        }        
        // // Update roles
        // $user->roles()->sync($request->input('roles', []));

        // // Update model_has_roles table
        // foreach ($request->input('roles', []) as $roleId) {
        //     ModelHasRole::create([
        //         'model_id' => $user->id,
        //         'model_type' => get_class($user),
        //         'role_id' => $roleId,
        //         'company_id' => auth()->user()->company_id,
        //     ]);
        // }

        return redirect()->route('users.index')->with('success', 'User created successfully');
    }

    // View User
    public function usersShow(User $user)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->get();
        $departments = Department::where('company_id', $companyId)->get();
        $designations = Designation::where('company_id', $companyId)->get();
        return view('users.view', compact('user', 'departments', 'designations'));
    }

    // Edit User Form
    public function usersEdit(User $user)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->get();
        $roles = Role::where('company_id', $companyId)->get();
        $departments = Department::where('company_id', $companyId)->get();
        $designations = Designation::where('company_id', $companyId)->get();
        return view('users.edit', compact('user', 'roles', 'departments', 'designations'));
    }

    // Update User
    public function usersUpdate(Request $request, User $user)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->get();

        // Validate request data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'department_id' => 'nullable|exists:departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            // Add more validation rules as needed
        ]);
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        if ($request->has('password')) {
            $user->password = bcrypt($request->input('password'));
        }
        $user->company_id = auth()->user()->company_id;

        // Save department and designation
        $user->department_id = $request->input('department_id');
        $user->designation_id = $request->input('designation_id');

        $user->save();

        // Get roles associated with the current company
        $companyRoles = Role::where('company_id', auth()->user()->company_id)->pluck('id');

        // Filter roles from the request to include only roles associated with the current company
        $roles = $request->input('roles', []);
        $roles = array_intersect($roles, $companyRoles->toArray());
        // $user->roles()->sync($roles);
        $user->roles()->detach();

        // Loop through the selected roles
        foreach ($roles as $roleId) {
            // Check if the entry already exists
            $existingEntry = ModelHasRole::where([
                'model_id' => $user->id,
                'model_type' => get_class($user),
                'role_id' => $roleId,
            ])->first();

            if ($existingEntry) {
                // Update the existing entry
                $existingEntry->update([
                    'company_id' => auth()->user()->company_id,
                ]);
            } else {
                // Create a new entry if it doesn't exist
                ModelHasRole::create([
                    'model_id' => $user->id,
                    'model_type' => get_class($user),
                    'role_id' => $roleId,
                    'company_id' => auth()->user()->company_id,
                ]);
            }
        }
        // $user->roles()->sync($request->input('roles', []));

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    public function usersDestroy($id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
    
        // Load the transaction and ensure it belongs to the authenticated user's company
        $user = User::where('company_id', auth()->user()->company_id)->findOrFail($id);
    
        // Delete the transaction
        $user->delete();
    
        // Redirect back to the index page with a success message
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    public function rolesIndex(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;

        $query = $request->input('search');
        $roles = Role::query();

        if ($query) {
            $roles->where('company_id', $companyId)
                ->where(function ($queryBuilder) use ($query) {
                    $queryBuilder->where('name', 'like', '%' . $query . '%');
                });
        } else {
            $roles->where('company_id', $companyId);
        }

        // Execute the query and fetch the results
        $roles = $roles->paginate(15);

        return view('roles.index', compact('roles'));
    }
    
    public function rolesCreate()
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->get();

        // $roles = Role::where('company_id', $companyId)->get();
        return view('roles.create');
    }
    
    public function rolesStore(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->get();

        // Validate request data
        $request->validate([
            'name' => 'required|string|max:255',
            'guard_name' => 'nullable',
        ]);

        $role = new Role();
        $role->name = $request->input('name');
        $role->guard_name = $request->input('guard_name');
        $role->company_id = auth()->user()->company_id;

        $role->save();

        return redirect()->route('roles.index')->with('success', 'User Role created successfully');
    }
    
    public function rolesEdit($id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;

        $role = Role::where('company_id', $companyId)->findOrFail($id);
        return view('roles.edit', compact('role'));
    }
    
    public function rolesUpdate(Request $request, $id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;

        // Validate request data
        $request->validate([
            'name' => 'required|string|max:255',
            'guard_name' => 'nullable',
        ]);
        // Find the role by its ID
        $role = Role::where('company_id', $companyId)->findOrFail($id);
        
        // Update role attributes
        $role->name = $request->input('name');
        $role->guard_name = $request->input('guard_name');
        $role->company_id = auth()->user()->company_id;
        $role->save();

        return redirect()->route('roles.index')->with('success', 'User Role updated successfully');
    }
    
    public function rolesDestroy($id)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
    
        // Load the transaction and ensure it belongs to the authenticated user's company
        $role = Role::where('company_id', auth()->user()->company_id)->findOrFail($id);
    
        // Delete the transaction
        $role->delete();
    
        // Redirect back to the index page with a success message
        return redirect()->route('roles.index')->with('success', 'User Role deleted successfully.');
    }    

    ///Roles Permissions
    public function rolePermissionsIndex(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->get();
            // Fetch roles and permissions
        $roles = Role::where('company_id', $companyId)->get();
        $permissions = Permission::all();
        // Create a query builder for RolePermission model
        $rolePermissions = RolePermission::query()->where('company_id', $companyId);

        // Handle search query if provided
        $query = $request->input('search');
        if ($query) {
            $rolePermissions->where(function ($queryBuilder) use ($query) {
                $queryBuilder
                    ->orWhereHas('role', function ($roleQuery) use ($query) {
                        $roleQuery->where('name', 'like', '%' . $query . '%');
                    })
                    ->orWhereHas('permission', function ($permissionQuery) use ($query) {
                        $permissionQuery->where('name', 'like', '%' . $query . '%');
                    });
            });
        }

        // Execute the query and fetch the results
        $rolePermissions = $rolePermissions->get();
        // Define the $editing variable
        $editing = false;
        // $rolePermissions = RolePermission::where('company_id', $companyId)->get();
        return view('role_permissions.index', compact('rolePermissions', 'roles', 'permissions','editing'));
    }
    
    public function rolePermissionCreate()
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        
        // Load roles and permissions
        $roles = Role::where('company_id', auth()->user()->company_id)->get();
        $permissions = Permission::all();
        
        // Load modules and sub-modules
        $modules = Module::all();
        $subModules = SubModule::all();
        
        return view('role_permissions.create', compact('roles', 'permissions', 'modules', 'subModules'));
    }
    
    public function rolePermissionStore(Request $request)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }
    
        // Validate the request
        $request->validate([
            'role_id' => 'required',
            'permission_id' => 'required|array',
            // 'module_id' => 'required',
            // 'sub_module_id' => 'required',
        ]);
    
        // Retrieve the selected permissions, module, and sub-module IDs from the request
        $permissions = $request->input('permission_id', []);
        $moduleId = $request->module_id;
        $subModuleId = $request->sub_module_id;
    
        // Loop through each combination of permission_id and create a new role permission
        foreach ($permissions as $permissionId) {
            RolePermission::create([
                'role_id' => $request->role_id,
                'permission_id' => $permissionId,
                // 'module_id' => $moduleId,
                // 'sub_module_id' => $subModuleId,
                'company_id' => auth()->user()->company_id,
            ]);
        }
    
        return redirect()->route('role-permissions.index')->with('success', 'Role permissions created successfully!');
    }
    
    
    
    
    
    public function getSubModules($moduleId)
    {
        // Retrieve sub-modules for the selected module
        $subModules = SubModule::where('module_id', $moduleId)->pluck('name', 'id')->toArray();
    
        // Return the sub-modules as JSON response
        return response()->json($subModules);
    }

    public function rolePermissionEdit(RolePermission $rolePermission)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->get();

        $roles = Role::where('company_id', $companyId)->get();
        $permissions = Permission::all();
        return view('role_permissions.edit', compact('rolePermission', 'roles', 'permissions'));
    }

    public function rolePermissionUpdate(Request $request, RolePermission $rolePermission)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;
        //where('company_id', $companyId)->get();
        
        // Validate the request
        $request->validate([
            'role_id' => 'required',
            'permission_id' => 'required',
        ]);

        // Update the role permission
        $rolePermission->update([
            'role_id' => $request->role_id,
            'permission_id' => $request->permission_id,
            'company_id' => auth()->user()->company_id,
        ]);

        return redirect()->route('role-permissions.index')->with('success', 'Role permission updated successfully!');
    }

    public function rolePermissionDestroy(RolePermission $rolePermission)
    {
        // Check authentication and company identification
        if (!auth()->check() || !auth()->user()->company_id) {
            return redirect()->route('login')->with('error', 'Unauthorized access.');
        }      
        $companyId = auth()->user()->company_id;

        // Check if the role permission belongs to the authenticated user's company
        if ($rolePermission->company_id !== auth()->user()->company_id) {
            return redirect()->route('role-permissions.index')->with('error', 'Unauthorized access.');
        }

        // Delete the role permission
        $rolePermission->delete();

        // Redirect back with success message
        return redirect()->route('role-permissions.index')->with('success', 'Role permission deleted successfully');
    }
}
