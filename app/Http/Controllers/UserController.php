<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return User::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'username' => 'required|string|unique:users,username|max:255',
                'email' => 'required|string|email|unique:users,email|max:255',
                'password' => 'required|string|min:8',
                'gender' => ['required', Rule::in(['male', 'female'])],
                'phone_no' => 'required|string|max:10',
            ], [
                'firstname.required' => 'First Name is required',
                'firstname.string' => 'First Name should not be contain any numbers.',
                'lastname.required' => 'Last Name is required',
                'lastname.string' => 'Last Name should not be contain any numbers.',
                'username.required' => "Username is required.",
                'username.unique' => "Username already exists.",
                'email.required' => "Email is required.",
                'email.unique' => "Email already registered.",
                'password.required' => 'Password is required.',
                'gender.required' => 'Gender is required',
                'phone_no.required' => 'Phone Number is required.'
            ]);


            $user = new User;
            $user->firstname = $request['firstname'];
            $user->lastname = $request['lastname'];
            $user->username = $request['username'];
            $user->email = $request['email'];
            $user->password = Hash::make($request['password']);
            $user->gender = $request['gender'];
            $user->phone_no = $request['phone_no'];
            $user->save();
            $userResponse = new UserResource($user);
            return response()->json($userResponse, 201);
        } catch (ValidationException $e) {
            $error = $e->validator->errors();
            // Handle database or other errors
            return response()->json(["errors" => $error]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'gender' => ['required', Rule::in(['male', 'female'])],
            'phone_no' => 'required|string|max:15',
        ]);

        $user->update($validatedData);

        return response()->json($user, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        $user = User::findOrFail($id);
        if (!$user) {
            throw new Exception('User not found');
        }
        $user->delete();

        return response()->json('User Deleted Successfully', 204);
    }

    public function userLogin(Request $request)
    {
        try {
            $request->validate([
                'userEmail' => 'required | email',
                'userPassword' => ['required', Password::min(8)->numbers()]
            ]);
            $user = User::where("email", $request['userEmail'])->get();
            // dd($user[0]->roll);
            if ($user->isNotEmpty()) {
                if (Hash::check($request['userPassword'], $user[0]->password)) {
                    // $request->session()->put('user', ['user' => $user[0]->name, 'roll' => $user[0]->roll, 'email' => $user[0]->email]);
                    return response()->json(['message' => 'user logged in successfully']);
                    // return redirect('/employee');
                } else {
                    // return back()->with('error', 'Wrong password. Enter correct password.');
                    return response()->json(['error' => 'Wrong password. Enter correct password.']);
                }
            } else {
                // return back()->with('userError', 'This email is not found or might be wrong.');
                return response()->json(['error' => 'This email is not found or might be wrong.']);
            }
        } catch (ValidationException $err) {
            $error = $err->validator->errors();
            return response()->json(['error' => $error]);
            // return back()->withErrors($error)->withInput();
        }
    }
}
