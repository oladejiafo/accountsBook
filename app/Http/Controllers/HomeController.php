<?php
namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Company;
use App\Models\SaleBill;
use App\Models\PurchaseBill;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\Designation;
use App\Models\ModelHasRole;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
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

}
