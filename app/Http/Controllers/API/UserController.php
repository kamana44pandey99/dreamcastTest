<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    // Store the user data
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|regex:/^(\+91[\-\s]?)?\(?\d{3}\)?[\-\s]?\d{3}[\-\s]?\d{4}$/',
            'description' => 'nullable|string',
            'role_id' => 'required|exists:roles,id',
            'profile' => 'nullable|image|max:1024',
        ]);

        $data = $request->all();

        if ($request->hasFile('profile')) {
            $file = $request->file('profile');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $data['profile'] = $this->uploadImage($file, $fileName);
        }

        $user = User::create($data);
        $user = User::with('role')->where('id',$user->id)->first();
        // Return the user data as JSON
        return response()->json($user, 200);
    }

    //upload the profile in public/profiles folder then return the full path of the image
    public function uploadImage($file, $fileName)
    {
        $destinationPath = public_path('profiles');
        $file->move($destinationPath, $fileName);
        return url('/').'/public/profiles/' . $fileName;
    }


}

