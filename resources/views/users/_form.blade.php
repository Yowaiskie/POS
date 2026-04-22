<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
        <input type="text" name="name" value="{{ old('name') }}" required
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
               placeholder="Juan dela Cruz">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Username</label>
        <input type="text" name="username" value="{{ old('username') }}" required
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
               placeholder="juandelacruz">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Employee ID</label>
        <div class="w-full px-3 py-2 border border-dashed border-indigo-300 bg-indigo-50 rounded-lg text-sm text-indigo-700 font-mono font-semibold tracking-wide">
            {{ $nextEmployeeId }}
        </div>
        <p class="text-xs text-slate-400 mt-1">Auto-generated</p>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Phone <span class="text-slate-400 text-xs">(11 digits)</span></label>
        <input type="text" name="phone" value="{{ old('phone') }}"
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
            <option value="Staff" {{ old('position') === 'Staff' ? 'selected' : '' }}>Staff</option>
            <option value="Admin" {{ old('position') === 'Admin' ? 'selected' : '' }}>Admin</option>
        </select>
    </div>
    <div class="col-span-2">
        <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
        <input type="password" name="password" required
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
               placeholder="Min. 6 characters">
    </div>
    <div class="col-span-2">
        <label class="block text-sm font-medium text-slate-700 mb-1">Confirm Password</label>
        <input type="password" name="password_confirmation" required
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
               placeholder="Repeat password">
    </div>
</div>

@if($errors->any())
    <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
        <ul class="text-sm text-red-700 space-y-1 list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
