<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $role = auth()->user()->role;

        if($role !== 1){
            $data = [
                'code' => 403,
                'message' => 'Not Authorized'
            ];

            return response()->json($data, $data['code']);
        }

        $users = User::paginate(5);

        $data = [
            'code' => 200,
            'users' => $users
        ];

        return response()->json($data, $data['code']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $role = auth()->user()->role;

        if($role !== 1){
            $data = [
                'code' => 403,
                'message' => 'Not Authorized'
            ];

            return response()->json($data, $data['code']);
        }

        $rules = [
            'role' => ['required', 'integer'],
            'name' => ['required'], 'string',
            'lastname' => ['required', 'string'],
            'email' => ['required', 'email'],
            'phone' => ['string'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ];

        $validator =  Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        };

        $validated = $validator->validated();

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        $data = [
            'code' => 201,
            'stored' => $user
        ];

        return response()->json($data, $data['code']);
    }

    public function register(Request $request){
        $rules = [
            'role' => ['required', 'integer'],
            'name' => ['required'], 'string',
            'lastname' => ['required', 'string'],
            'email' => ['required', 'email'],
            'phone' => ['string'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ];

        $validator =  Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        };

        $validated = $validator->validated();

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        $data = [
            'code' => 201,
            'stored' => $user
        ];

        return response()->json($data, $data['code']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = auth()->user()->role;

        if($role !== 1){
            $data = [
                'code' => 403,
                'message' => 'Not Authorized'
            ];

            return response()->json($data, $data['code']);
        }

        $user = User::with('notifies')->find($id);

        if(!$user) {

            $data = [
                'code' => 404,
                'message' => 'Not Found User'
            ];

            return response()->json($data, $data['code']);
        }

        $data = [
            'code' => 200,
            'showed' => $user
        ];

        return response()->json($data, $data['code']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $role = auth()->user()->role;

        if($role !== 1){
            $data = [
                'code' => 403,
                'message' => 'Not Authorized'
            ];

            return response()->json($data, $data['code']);
        }

        $rules = [
            'role' => ['required', 'integer'],
            'name' => ['required', 'string'],
            'lastname' => ['required', 'string'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string']
        ];

        $validator =  Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        };

        $user = User::find($id);

        if(!$user) {
            $data = [
                'code' => 404,
                'message' => 'User Not Found'
            ];

            return response()->json($data, $data['code']);
        }


        $validated = $validator->validated();

        $user->update($validated);

        $data = [
            'code' => 201,
            'updated' => $user
        ];

        return response()->json($data, $data['code']);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = auth()->user()->role;

        if($role !== 1){
            $data = [
                'code' => 403,
                'message' => 'Not Authorized'
            ];

            return response()->json($data, $data['code']);
        }

        $user = User::where('id', $id)->first();

        if(!$user) {
            $data = [
                'code' => 404,
                'message' => 'User Not Found'
            ];

            return response()->json($data, $data['code']);
        }

        $user->notifies()->detach();

        $user->delete();

        $data = [
            'code' => 201,
            'user_id_deleted' => $id
        ];

        return response()->json($data, $data['code']);
    }

    public function login(Request $request){
        $rulesLogin = [
            'email' => ['required','email'],
            'password' => ['required'],
        ];

        $validator =  Validator::make($request->all(), $rulesLogin);

        if ($validator->fails()) {
            $data = [
                'code' => 400,
                'message' => $validator->errors()
            ];
            return response()->json($data, $data['code']);
        };

        $user = User::where('email', $request->email)->first();


        if ( !$user || ! Hash::check($request->password, $user->password) ) {
            $data = [
                'code' => 401,
                'message' => 'Invalid credentials'
            ];
        } else{

            $token = $user->createToken('auth_token')->plainTextToken;

            $data = [
                'code' => 200,
                'token' => $token,
                'user' => $user
            ];
        }

        return response()->json($data, $data['code']);

    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
    }
}
