<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::when($request->query('name'), function($query, $value) { $query->where('name', 'LIKE', "%{$value}%"); })
            ->when($request->query('email'), function($query, $value) { $query->where('email', 'LIKE', "%{$value}%"); })
            ->paginate();

        return response()->json([
            'message'   => 'OK',
            'status'    => 200,
            'data'      => $users,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'                  => 'required|max:255',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|min:8',
            'password_confirmation' => 'required|same:password',
        ]);

        $request->merge([
            'password'              => Hash::make($request->post('password')),
            'password_confirmation' => Hash::make($request->post('password_confirmation')),
            'type'                  => User::CLIENT,
        ]);
        
        $user = User::create($request->all());
        $user->refresh();

        return response()->json([
            'message'   => 'User Created',
            'status'    => 201,
            'date'      => $user,
        ],201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        // $user->load(['roles', 'country',  'profile.ratings']);

        return response()->json([
            'messsage'  => 'OK',
            'status'    => 200,
            'data'      => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrfail($id);

        $request->validate([
            'name'                  => 'required|max:255',
            'email'                 => 'required|email',
            'password'              => 'nullable|min:8',
            'password_confirmation' => 'nullable|same:password',
        ]);

        if ($request->post('password')) {
            $request->merge([
            'password'              => Hash::make($request->post('password')),
            'password_confirmation' => Hash::make($request->post('password_confirmation')),
            ]);
        }else 
            $request->merge(['password' => $user->password,]);
        
        $user->update($request->all());
        $user->refresh();

        return response()->json([
            'message'   => 'User Updated',
            'status'    => 201,
            'data'      => $user,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'message'   => 'User Deleted',
            'status'    => 200,
        ]);
    }
}
