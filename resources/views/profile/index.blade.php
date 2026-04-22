@extends('layouts.app')

@section('content')
<div class="p-4 md:p-8 max-w-[1200px] mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-slate-900 mb-2">Profile Settings</h1>
        <p class="text-slate-600">Manage your account information and preferences</p>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-lg flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
            <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
        </div>
    @endif
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white border border-slate-200 rounded-xl p-6" style="box-shadow: var(--shadow-md)">
                <div class="flex flex-col items-center text-center">
                    <div class="w-32 h-32 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-5xl font-bold mb-4">
                        {{ $initials }}
                    </div>
                    <h2 class="text-2xl font-bold text-slate-900 mb-1">{{ $user->name }}</h2>
                    <p class="text-slate-600 mb-4">{{ $user->position }}</p>
                    <div class="w-full pt-4 border-t border-slate-200">
                        <div class="flex items-center justify-between text-sm mb-2">
                            <span class="text-slate-500">Employee ID</span>
                            <span class="font-semibold text-slate-900">{{ $user->employee_id ? '#' . $user->employee_id : '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-500">Member Since</span>
                            <span class="font-semibold text-slate-900">{{ $memberSince }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Information -->
        <div class="lg:col-span-2">
            <div class="bg-white border border-slate-200 rounded-xl p-6 mb-6" style="box-shadow: var(--shadow-md)">
                <h3 class="text-xl font-bold text-slate-900 mb-6">Account Information</h3>

                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">
                                    <i data-lucide="user" class="w-4 h-4 inline mr-2"></i>
                                    Full Name
                                </label>
                                <input type="text" name="name" value="{{ $user->name }}" required class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-lg focus:border-indigo-500 focus:outline-none transition-colors">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">
                                    <i data-lucide="phone" class="w-4 h-4 inline mr-2"></i>
                                    Phone Number
                                </label>
                                <input type="tel" name="phone" value="{{ $user->phone }}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-lg focus:border-indigo-500 focus:outline-none transition-colors">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">
                                    <i data-lucide="briefcase" class="w-4 h-4 inline mr-2"></i>
                                    Position
                                </label>
                                <input type="text" name="position" value="{{ $user->position }}" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-lg focus:border-indigo-500 focus:outline-none transition-colors">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">
                                    <i data-lucide="id-card" class="w-4 h-4 inline mr-2"></i>
                                    Username
                                </label>
                                <input type="text" value="{{ $user->username }}" disabled class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-lg bg-slate-50 text-slate-500 cursor-not-allowed">
                                <p class="text-xs text-slate-400 mt-1">Username cannot be changed.</p>
                            </div>
                        </div>

                    </div>

                    <div class="mt-6 flex gap-3">
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 text-white rounded-lg hover:from-indigo-700 hover:to-indigo-600 active:scale-98 transition-all font-semibold shadow-md">
                            Save Changes
                        </button>
                        <button type="reset" class="px-6 py-3 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 active:scale-98 transition-all font-semibold">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>

            <!-- Security Settings -->
            <div class="bg-white border border-slate-200 rounded-xl p-6" style="box-shadow: var(--shadow-md)">
                <h3 class="text-xl font-bold text-slate-900 mb-6">
                    <i data-lucide="lock" class="w-5 h-5 inline mr-2"></i>
                    Security Settings
                </h3>

                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="space-y-4">
                        @if($errors->has('current_password'))
                            <div class="p-3 bg-red-50 border-l-4 border-red-500 rounded-lg">
                                <p class="text-sm text-red-700">{{ $errors->first('current_password') }}</p>
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Current Password</label>
                            <input type="password" name="current_password" required placeholder="Enter current password" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-lg focus:border-indigo-500 focus:outline-none transition-colors @error('current_password') border-red-400 @enderror">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">New Password</label>
                            <input type="password" name="password" required placeholder="Enter new password (min. 8 characters)" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-lg focus:border-indigo-500 focus:outline-none transition-colors @error('password') border-red-400 @enderror">
                            @error('password')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Confirm New Password</label>
                            <input type="password" name="password_confirmation" required placeholder="Confirm new password" class="w-full px-4 py-2.5 border-2 border-slate-200 rounded-lg focus:border-indigo-500 focus:outline-none transition-colors">
                        </div>
                    </div>

                    <div class="mt-6">
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-500 text-white rounded-lg hover:from-purple-700 hover:to-pink-600 active:scale-98 transition-all font-semibold shadow-md">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
