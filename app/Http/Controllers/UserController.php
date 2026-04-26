<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(10);
        $nextEmployeeId = $this->generateNextEmployeeId();
        return view('users.index', compact('users', 'nextEmployeeId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'username'    => ['required', 'string', 'max:255', 'unique:users,username'],
            'phone'       => ['nullable', 'digits:11'],
            'position'    => ['required', 'in:Admin,Staff,Kitchen'],
            'password'    => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['employee_id'] = $this->generateNextEmployeeId();

        User::create($validated);

        return back()->with('success', 'User created successfully.');
    }

    private function generateNextEmployeeId(): string
    {
        $last = User::whereNotNull('employee_id')
            ->orderByRaw("CAST(SUBSTR(employee_id, 5) AS INTEGER) DESC")
            ->value('employee_id');

        $next = $last ? (intval(substr($last, 4)) + 1) : 1;

        return 'EMP-' . str_pad($next, 3, '0', STR_PAD_LEFT);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'username'    => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
            'phone'       => ['nullable', 'digits:11'],
            'position'    => ['required', 'in:Admin,Staff,Kitchen'],
            'password'    => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return back()->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }
}
