<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use App\Models\Currency;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);


        event(new Registered($user));
        $currencies = Currency::pluck('acronym', 'id'); 
        // Auth::login($user);

        // return redirect(RouteServiceProvider::HOME);
        return view('auth.register-company', compact('currencies'));
    }

    public function createCompany(Request $request)
    {
        // Validate company registration form data
        $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            // Add validation rules for other company details
        ]);

        // Create the company
        $company = new Company();
        $company->name = $request->company_name;
        $company->email = $request->email;
        $company->address = $request->address;
        $company->website = $request->website;
        $company->phone = $request->phone;
        $company->business_type = $request->business_type;
        $company->subscription_type = $request->subscription_type;
        $company->currency = $request->currency;
        // Add other company details as needed
        $company->save();

        // Associate the user with the company
        $user = Auth::user();
        $user->company_id = $company->id;
        $user->save();

        Auth::login($user);
        // Redirect to home or any other desired page
        return redirect(RouteServiceProvider::HOME);
    }

    public function showRegistrationForm()
    {
        // Fetch currency data from the database
        $currencies = Currency::pluck('acronym', 'id'); // Assuming 'acronym' is the column containing currency acronyms

        return view('auth.register', ['currencies' => $currencies]);
    }
}
