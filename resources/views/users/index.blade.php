@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="space-y-6" x-data="userManager()" x-init="init()">

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="flex items-center gap-3 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-xl text-emerald-800 text-sm font-medium">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-500 flex-shrink-0"></i>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 p-4 bg-red-50 border-l-4 border-red-500 rounded-xl text-red-800 text-sm font-medium">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 flex-shrink-0"></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl" style="box-shadow: var(--shadow-sm)">
        <div>
            <h1 class="text-2xl tracking-tight font-bold text-slate-900">User Management</h1>
            <p class="text-sm text-[--muted-foreground] mt-1">Manage system access and roles</p>
        </div>
        <button @click="openAddModal()"
                class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-xl hover:shadow-lg transition-all active:scale-95 flex items-center justify-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Add User
        </button>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl overflow-hidden" style="box-shadow: var(--shadow-sm)">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-[--border]">
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Employee ID</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Username</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[--border]">
                    @foreach($users as $user)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ $user->employee_id ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center text-indigo-700 font-bold text-xs">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-slate-900">{{ $user->name }}</div>
                                        @if($user->phone)
                                            <div class="text-xs text-slate-400">{{ $user->phone }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ $user->username }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ strtolower($user->position) === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-emerald-100 text-emerald-700' }}">
                                    {{ $user->position }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button @click="openEditModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->username }}', '{{ $user->employee_id }}', '{{ $user->phone }}', '{{ $user->position }}')"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 transition-colors mx-1">
                                    <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                                    Edit
                                </button>
                                @if($user->id !== auth()->id())
                                    <button @click="openDeleteModal({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 transition-colors mx-1">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        Delete
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach

                    @if($users->isEmpty())
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                                <div class="flex flex-col items-center">
                                    <i data-lucide="users" class="w-12 h-12 text-slate-300 mb-3"></i>
                                    <p>No users found.</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-[--border]">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    {{-- ADD USER MODAL --}}
    <div x-show="showAddModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
         @click.self="showAddModal = false"
         style="display:none">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md" @click.stop>
            <div class="flex items-center justify-between p-6 border-b border-[--border]">
                <h2 class="text-lg font-bold text-slate-900">Add New User</h2>
                <button @click="showAddModal = false" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                    <i data-lucide="x" class="w-5 h-5 text-slate-500"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('users.store') }}" class="p-6 space-y-4">
                @csrf
                @include('users._form', ['user' => null])
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showAddModal = false"
                            class="px-4 py-2 text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:shadow-lg rounded-xl transition-all active:scale-95">
                        Create User
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- EDIT USER MODAL --}}
    <div x-show="showEditModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
         @click.self="showEditModal = false"
         style="display:none">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md" @click.stop>
            <div class="flex items-center justify-between p-6 border-b border-[--border]">
                <h2 class="text-lg font-bold text-slate-900">Edit User</h2>
                <button @click="showEditModal = false" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                    <i data-lucide="x" class="w-5 h-5 text-slate-500"></i>
                </button>
            </div>
            <form method="POST" :action="`/users/${editUser.id}`" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
                        <input type="text" name="name" :value="editUser.name" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Username</label>
                        <input type="text" name="username" :value="editUser.username" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Employee ID</label>
                        <div class="w-full px-3 py-2 border border-dashed border-indigo-300 bg-indigo-50 rounded-lg text-sm text-indigo-700 font-mono font-semibold tracking-wide" x-text="editUser.employee_id || '—'"></div>
                        <p class="text-xs text-slate-400 mt-1">Auto-generated, cannot be changed</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Phone <span class="text-slate-400 text-xs">(11 digits)</span></label>
                        <input type="text" name="phone" :value="editUser.phone"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="09XXXXXXXXX"
                               maxlength="11"
                               pattern="[0-9]{11}"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)"
                               title="Phone number must be exactly 11 digits">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Role</label>
                        <select name="position" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="Admin" :selected="editUser.position === 'Admin'">Admin</option>
                            <option value="Staff" :selected="editUser.position === 'Staff'">Staff</option>
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">New Password <span class="text-slate-400 text-xs">(leave blank to keep current)</span></label>
                        <input type="password" name="password"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="••••••">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Confirm Password</label>
                        <input type="password" name="password_confirmation"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="••••••">
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showEditModal = false"
                            class="px-4 py-2 text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:shadow-lg rounded-xl transition-all active:scale-95">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- DELETE CONFIRM MODAL --}}
    <div x-show="showDeleteModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
         @click.self="showDeleteModal = false"
         style="display:none">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center" @click.stop>
            <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                <i data-lucide="trash-2" class="w-7 h-7 text-red-600"></i>
            </div>
            <h2 class="text-lg font-bold text-slate-900 mb-1">Delete User?</h2>
            <p class="text-sm text-slate-500 mb-6">Are you sure you want to delete <span class="font-semibold text-slate-700" x-text="deleteUser.name"></span>? This action cannot be undone.</p>
            <form method="POST" :action="`/users/${deleteUser.id}`">
                @csrf
                @method('DELETE')
                <div class="flex gap-3 justify-center">
                    <button type="button" @click="showDeleteModal = false"
                            class="px-5 py-2 text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-5 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-xl transition-colors">
                        Yes, Delete
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
function userManager() {
    return {
        showAddModal: false,
        showEditModal: false,
        showDeleteModal: false,
        editUser: { id: null, name: '', username: '', employee_id: '', phone: '', position: 'Staff' },
        deleteUser: { id: null, name: '' },
        hasErrors: {{ $errors->any() ? 'true' : 'false' }},

        init() {
            if (this.hasErrors) {
                this.showAddModal = true;
            }
        },

        openAddModal() {
            this.showAddModal = true;
        },

        openEditModal(id, name, username, employee_id, phone, position) {
            this.editUser = { id, name, username, employee_id: employee_id || '', phone: phone || '', position };
            this.showEditModal = true;
        },

        openDeleteModal(id, name) {
            this.deleteUser = { id, name };
            this.showDeleteModal = true;
        }
    }
}
</script>
@endpush

@endsection
