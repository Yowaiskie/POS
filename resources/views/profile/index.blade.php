@extends('layouts.app')

@section('content')
<div class="max-w-[1200px] mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-slate-900 mb-2">Profile Settings</h1>
        <p class="text-slate-600">Manage your account information and preferences</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white border border-slate-200 rounded-3xl p-8 shadow-sm">
                <div class="flex flex-col items-center text-center">
                    <div class="w-32 h-32 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-5xl font-black shadow-xl shadow-indigo-100 mb-6">
                        AU
                    </div>
                    <h2 class="text-2xl font-black text-slate-900 mb-1 tracking-tight">Admin User</h2>
                    <p class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-6">Manager</p>
                    <div class="w-full pt-6 border-t border-slate-100">
                        <div class="flex items-center justify-between text-sm mb-3">
                            <span class="font-bold text-slate-400 uppercase tracking-wider text-[10px]">Employee ID</span>
                            <span class="font-black text-slate-900">#EMP-001</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-bold text-slate-400 uppercase tracking-wider text-[10px]">Member Since</span>
                            <span class="font-black text-slate-900">Jan 2024</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Information -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-slate-200 rounded-3xl p-6 md:p-8 shadow-sm">
                <h3 class="text-xl font-bold text-slate-900 mb-8 flex items-center gap-2">
                    <i data-lucide="user" class="w-5 h-5 text-indigo-600"></i>
                    Account Information
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] px-1">Full Name</label>
                        <input type="text" value="Admin User" class="w-full px-4 py-3 bg-slate-50 border-2 border-transparent rounded-xl focus:bg-white focus:border-indigo-500 focus:outline-none transition-all font-bold text-slate-700">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] px-1">Email Address</label>
                        <input type="email" value="admin@bosston.com" class="w-full px-4 py-3 bg-slate-50 border-2 border-transparent rounded-xl focus:bg-white focus:border-indigo-500 focus:outline-none transition-all font-bold text-slate-700">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] px-1">Phone Number</label>
                        <input type="tel" value="+63 912 345 6789" class="w-full px-4 py-3 bg-slate-50 border-2 border-transparent rounded-xl focus:bg-white focus:border-indigo-500 focus:outline-none transition-all font-bold text-slate-700">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] px-1">Position</label>
                        <input type="text" value="Manager" class="w-full px-4 py-3 bg-slate-50 border-2 border-transparent rounded-xl focus:bg-white focus:border-indigo-500 focus:outline-none transition-all font-bold text-slate-700">
                    </div>
                </div>

                <div class="mt-8 flex gap-3">
                    <button class="px-8 py-3.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 active:scale-95 transition-all font-black shadow-lg shadow-indigo-100">
                        Save Changes
                    </button>
                    <button class="px-8 py-3.5 bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-200 active:scale-95 transition-all font-bold">
                        Cancel
                    </button>
                </div>
            </div>

            <!-- Security Settings -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 md:p-8 shadow-sm">
                <h3 class="text-xl font-bold text-slate-900 mb-8 flex items-center gap-2">
                    <i data-lucide="lock" class="w-5 h-5 text-rose-500"></i>
                    Security Settings
                </h3>

                <div class="space-y-4 max-w-md">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] px-1">Current Password</label>
                        <input type="password" placeholder="••••••••" class="w-full px-4 py-3 bg-slate-50 border-2 border-transparent rounded-xl focus:bg-white focus:border-rose-500 focus:outline-none transition-all font-bold text-slate-700">
                    </div>
                    <button class="px-8 py-3 bg-white border-2 border-rose-100 text-rose-500 rounded-xl hover:bg-rose-50 active:scale-95 transition-all font-black text-sm">
                        Change Password
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
