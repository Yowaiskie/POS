import { User, Mail, Phone, MapPin, Briefcase, Calendar, Lock } from 'lucide-react';

export function ProfilePage() {
  return (
    <div className="p-4 md:p-8 max-w-[1200px] mx-auto">
      <div className="mb-8">
        <h1 className="text-3xl md:text-4xl font-bold text-slate-900 mb-2">Profile Settings</h1>
        <p className="text-slate-600">Manage your account information and preferences</p>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Profile Card */}
        <div className="lg:col-span-1">
          <div className="bg-white border border-slate-200 rounded-xl p-6" style={{ boxShadow: 'var(--shadow-md)' }}>
            <div className="flex flex-col items-center text-center">
              <div className="w-32 h-32 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-5xl font-bold mb-4">
                AU
              </div>
              <h2 className="text-2xl font-bold text-slate-900 mb-1">Admin User</h2>
              <p className="text-slate-600 mb-4">Manager</p>
              <div className="w-full pt-4 border-t border-slate-200">
                <div className="flex items-center justify-between text-sm mb-2">
                  <span className="text-slate-500">Employee ID</span>
                  <span className="font-semibold text-slate-900">#EMP-001</span>
                </div>
                <div className="flex items-center justify-between text-sm">
                  <span className="text-slate-500">Member Since</span>
                  <span className="font-semibold text-slate-900">Jan 2024</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* Account Information */}
        <div className="lg:col-span-2">
          <div className="bg-white border border-slate-200 rounded-xl p-6 mb-6" style={{ boxShadow: 'var(--shadow-md)' }}>
            <h3 className="text-xl font-bold text-slate-900 mb-6">Account Information</h3>

            <div className="space-y-4">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-slate-700 mb-2">
                    <User className="w-4 h-4 inline mr-2" />
                    Full Name
                  </label>
                  <input
                    type="text"
                    defaultValue="Admin User"
                    className="w-full px-4 py-2.5 border-2 border-slate-200 rounded-lg focus:border-indigo-500 focus:outline-none transition-colors"
                  />
                </div>

                <div>
                  <label className="block text-sm font-medium text-slate-700 mb-2">
                    <Mail className="w-4 h-4 inline mr-2" />
                    Email Address
                  </label>
                  <input
                    type="email"
                    defaultValue="admin@bosston.com"
                    className="w-full px-4 py-2.5 border-2 border-slate-200 rounded-lg focus:border-indigo-500 focus:outline-none transition-colors"
                  />
                </div>

                <div>
                  <label className="block text-sm font-medium text-slate-700 mb-2">
                    <Phone className="w-4 h-4 inline mr-2" />
                    Phone Number
                  </label>
                  <input
                    type="tel"
                    defaultValue="+63 912 345 6789"
                    className="w-full px-4 py-2.5 border-2 border-slate-200 rounded-lg focus:border-indigo-500 focus:outline-none transition-colors"
                  />
                </div>

                <div>
                  <label className="block text-sm font-medium text-slate-700 mb-2">
                    <Briefcase className="w-4 h-4 inline mr-2" />
                    Position
                  </label>
                  <input
                    type="text"
                    defaultValue="Manager"
                    className="w-full px-4 py-2.5 border-2 border-slate-200 rounded-lg focus:border-indigo-500 focus:outline-none transition-colors"
                  />
                </div>
              </div>

              <div>
                <label className="block text-sm font-medium text-slate-700 mb-2">
                  <MapPin className="w-4 h-4 inline mr-2" />
                  Address
                </label>
                <input
                  type="text"
                  defaultValue="123 KTV Street, Music City"
                  className="w-full px-4 py-2.5 border-2 border-slate-200 rounded-lg focus:border-indigo-500 focus:outline-none transition-colors"
                />
              </div>

              <div>
                <label className="block text-sm font-medium text-slate-700 mb-2">
                  <Calendar className="w-4 h-4 inline mr-2" />
                  Date of Birth
                </label>
                <input
                  type="date"
                  defaultValue="1990-01-01"
                  className="w-full px-4 py-2.5 border-2 border-slate-200 rounded-lg focus:border-indigo-500 focus:outline-none transition-colors"
                />
              </div>
            </div>

            <div className="mt-6 flex gap-3">
              <button className="px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 text-white rounded-lg hover:from-indigo-700 hover:to-indigo-600 active:scale-98 transition-all font-semibold shadow-md">
                Save Changes
              </button>
              <button className="px-6 py-3 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 active:scale-98 transition-all font-semibold">
                Cancel
              </button>
            </div>
          </div>

          {/* Security Settings */}
          <div className="bg-white border border-slate-200 rounded-xl p-6" style={{ boxShadow: 'var(--shadow-md)' }}>
            <h3 className="text-xl font-bold text-slate-900 mb-6">
              <Lock className="w-5 h-5 inline mr-2" />
              Security Settings
            </h3>

            <div className="space-y-4">
              <div>
                <label className="block text-sm font-medium text-slate-700 mb-2">Current Password</label>
                <input
                  type="password"
                  placeholder="Enter current password"
                  className="w-full px-4 py-2.5 border-2 border-slate-200 rounded-lg focus:border-indigo-500 focus:outline-none transition-colors"
                />
              </div>

              <div>
                <label className="block text-sm font-medium text-slate-700 mb-2">New Password</label>
                <input
                  type="password"
                  placeholder="Enter new password"
                  className="w-full px-4 py-2.5 border-2 border-slate-200 rounded-lg focus:border-indigo-500 focus:outline-none transition-colors"
                />
              </div>

              <div>
                <label className="block text-sm font-medium text-slate-700 mb-2">Confirm New Password</label>
                <input
                  type="password"
                  placeholder="Confirm new password"
                  className="w-full px-4 py-2.5 border-2 border-slate-200 rounded-lg focus:border-indigo-500 focus:outline-none transition-colors"
                />
              </div>
            </div>

            <div className="mt-6">
              <button className="px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-500 text-white rounded-lg hover:from-purple-700 hover:to-pink-600 active:scale-98 transition-all font-semibold shadow-md">
                Update Password
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
