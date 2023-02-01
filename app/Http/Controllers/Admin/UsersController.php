<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $this->authorize('viewAny', User::class);

        // for search
        $request = request();
        $query = User::query();

        if ($name = $request->query('name')) {
            $query->where('name', 'LIKE', "%{$name}%");
        }
        if ($email = $request->query('email')) {
            $query->where('email', 'LIKE', "%{$email}%");
        }

        $users = $query->orderBy('type')->paginate();
        // dd($users);

        return view('admin.users.index', [
            'title' => __("Users List"),
            'users' => $users,
        ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.users.create', [
            'user' => new User(),
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
        $request->validate(User::validateRules());

        // sheck if image in request
        // if ($request->hasFile('image')) {
        //     $file = $request->file('image'); // UplodedFile Object

        //     $image_path = $file->storeAs('uploads',
        //         time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName()),
        //         'public');
            
        //     // merge image to the request
        //     $request->merge([
        //         'profile_photo_path' => $image_path,
        //     ]);
        // }

        $request->merge([
            'password' => Hash::make($request->post('password')),
            'password_confirmation' => Hash::make($request->post('password_confirmation')),
            // 'country_id' => $request->post('country'),
        ]);
        
        $user = User::create($request->all());

        return redirect()->route('users.index')
            ->with('success', __('app.users_store'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        dd('show user '.$user->name.' is work');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', [
            'title'     => __('Edit User'),
            'user'      => $user,
            // 'countries' => $countries
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $request->validate($user->validateRules());

        if ($request->post('password')) {
            $request->merge([
                'password'              => Hash::make($request->post('password')),
                'password_confirmation' => Hash::make($request->post('password_confirmation')),
                'country_id'            => $request->post('country'),
            ]);
        } else
            $request->merge(['password' => $user->password,]);

        $user->update($request->all());

        return redirect()->route('users.index')
        ->with('success', __('app.users_update'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', __('app.users_delete', ['name' => $user->name]));
    }
}
