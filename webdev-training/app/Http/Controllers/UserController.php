<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserRequest;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::paginate(10);
        // compact('users')
        return view('users.index',[
            'users' => $users
        ] );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return to_route('users.index')->with('success','data user berhasil ditambah');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        // $user = User::find($id);
        // $user = User::findOrFail($id);

        // $user = User::where('id',$id)->first();
      
        return view('users.edit',[
            'user' => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user)
    {
        // ambil request hanya name dan email
        $data = $request->only(['name','email']);

        // jika ada password
        if ($request->filled('password')) {
            // masukan password di hash
            $data['password'] = Hash::make($request->password);
        }
        // update data
        $user->update($data);
        return to_route('users.index')->with('success', 'User berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // delete data user
        $user->delete();

        // redirect ke route users index
        return to_route('users.index')->with('success', 'User berhasil Di delete!');
    }
}
