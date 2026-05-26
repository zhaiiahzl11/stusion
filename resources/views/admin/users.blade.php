@extends('layouts.dashboard', ['role' => 'admin', 'activeModule' => 'users', 'title' => 'Manage Users', 'subtitle' => 'Add, edit or delete users'])

@section('dashboard_content')
<div class="p-6 space-y-4">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 w-full sm:w-auto">
            <input type="text" placeholder="Search users..." class="h-10 w-full sm:w-64 bg-white border border-gray-200 rounded-lg px-3 text-sm text-gray-900 placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-[#f48545]" />
            <select onchange="window.location.href='?role='+this.value" class="h-10 w-full sm:w-32 bg-white border border-gray-200 rounded-lg px-3 text-sm text-gray-500 focus:outline-none focus:ring-2 focus:ring-[#f48545]">
                <option value="All" {{ ($filter ?? 'All') == 'All' ? 'selected' : '' }}>All Roles</option>
                <option value="Student" {{ ($filter ?? '') == 'Student' ? 'selected' : '' }}>Student</option>
                <option value="Counselor" {{ ($filter ?? '') == 'Counselor' ? 'selected' : '' }}>Counselor</option>
                <option value="Admin" {{ ($filter ?? '') == 'Admin' ? 'selected' : '' }}>Admin</option>
            </select>
        </div>
        <button onclick="toggleModal('addUserModal')" class="inline-flex w-full sm:w-auto items-center justify-center rounded-md text-sm font-medium bg-[#f48545] text-white hover:bg-[#e67a3b] h-10 px-4 py-2 transition-colors shadow-sm">
            <i data-lucide="plus" class="w-4 h-4 mr-1"></i> Add User
        </button>
    </div>

    @if(session('success'))
    <div class="p-4 rounded-lg bg-emerald-50 text-sm font-medium text-emerald-800 border border-emerald-100">
        {{ session('success') }}
    </div>
    @endif
    
    @if ($errors->any())
    <div class="p-4 rounded-lg bg-red-50 text-sm font-medium text-red-800 border border-red-100">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Users Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[600px]">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50">
                    <th class="p-4 text-sm font-medium text-gray-500">Name</th>
                    <th class="p-4 text-sm font-medium text-gray-500">Username</th>
                    <th class="p-4 text-sm font-medium text-gray-500">Email</th>
                    <th class="p-4 text-sm font-medium text-gray-500">Role</th>
                    <th class="p-4 text-sm font-medium text-gray-500">Status</th>
                    <th class="p-4 text-sm font-medium text-gray-500 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-[#fef4ee] rounded-full flex items-center justify-center font-bold text-[#f48545] text-xs shadow-sm shadow-[#f48545]/10">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <span class="font-medium text-gray-900">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td class="p-4 text-sm font-semibold text-gray-700">{{ $user->username }}</td>
                    <td class="p-4 text-sm text-gray-500">{{ $user->email }}</td>
                    <td class="p-4">
                        <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ $user->role == 'Admin' ? 'bg-purple-50 text-purple-700 border border-purple-100' : ($user->role == 'Counselor' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-orange-50 text-[#d9733a] border border-orange-100') }}">{{ $user->role }}</span>
                    </td>
                    <td class="p-4">
                        <span class="text-xs {{ ($user->status ?? 'Active') == 'Active' ? 'text-emerald-600' : 'text-gray-500' }}">● {{ $user->status ?? 'Active' }}</span>
                    </td>
                    <td class="p-4 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <button onclick="openEditModal('{{ $user->id }}', '{{ addslashes($user->name) }}', '{{ addslashes($user->username) }}', '{{ addslashes($user->email) }}', '{{ $user->role }}')" class="p-2 text-gray-400 hover:text-gray-900 hover:bg-gray-100 rounded-md">
                                <i data-lucide="edit" class="w-4 h-4"></i>
                            </button>
                            <form action="/admin/users/{{ $user->role }}/{{ $user->id }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-md" onclick="return confirm('Are you sure you want to delete this user?');">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-8 text-center text-gray-500">
                        No users found in the system.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $users->appends(request()->query())->links() }}
    </div>
</div>

<!-- Add User Modal Overlay -->
<div id="addUserModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm hidden flex items-center justify-center z-50 transition-opacity">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform transition-all translate-y-4 sm:translate-y-0 sm:scale-95">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <h3 class="text-base font-bold text-gray-900">Add New User</h3>
            <button onclick="toggleModal('addUserModal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form action="/admin/users" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                <input type="text" name="name" required class="w-full h-10 px-3 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#f48545]">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" name="username" required class="w-full h-10 px-3 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#f48545]">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input type="email" name="email" required class="w-full h-10 px-3 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#f48545]">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select name="role" required class="w-full h-10 px-3 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#f48545]">
                    <option value="" disabled selected>Select Role</option>
                    <option value="Student">Student</option>
                    <option value="Counselor">Counselor</option>
                    <option value="Admin">Admin</option>
                </select>
                <p class="text-xs text-amber-600 mt-2 font-medium"><i data-lucide="info" class="w-3 h-3 inline"></i> User will be created with default password: password123</p>
            </div>

            <div class="pt-4 flex items-center justify-end gap-2">
                <button type="button" onclick="toggleModal('addUserModal')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">Cancel</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-[#f48545] rounded-lg hover:bg-[#e67a3b] shadow-sm transition-colors">Create User</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal Overlay -->
<div id="editUserModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm hidden flex items-center justify-center z-50 transition-opacity">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform transition-all translate-y-4 sm:translate-y-0 sm:scale-95">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <h3 class="text-base font-bold text-gray-900">Edit User Details</h3>
            <button onclick="toggleModal('editUserModal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form id="edit_form" action="" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                <input type="text" name="name" id="edit_name" required class="w-full h-10 px-3 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#f48545]">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" name="username" id="edit_username" required class="w-full h-10 px-3 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#f48545]">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input type="email" name="email" id="edit_email" required class="w-full h-10 px-3 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#f48545]">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Change Password (Optional)</label>
                <input type="password" name="password" placeholder="Leave blank to keep current password" class="w-full h-10 px-3 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#f48545]">
            </div>

            <div class="pt-4 flex items-center justify-end gap-2">
                <button type="button" onclick="toggleModal('editUserModal')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">Cancel</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-[#f48545] rounded-lg hover:bg-[#e67a3b] shadow-sm transition-colors">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleModal(modalId) {
        const modal = document.getElementById(modalId);
        const modalContent = modal.querySelector('div.transform');
        
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            void modal.offsetWidth;
            modalContent.classList.remove('translate-y-4', 'sm:scale-95');
            modalContent.classList.add('translate-y-0', 'sm:scale-100');
        } else {
            modalContent.classList.remove('translate-y-0', 'sm:scale-100');
            modalContent.classList.add('translate-y-4', 'sm:scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 200);
        }
    }

    function openEditModal(id, name, username, email, role) {
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_username').value = username;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_form').action = '/admin/users/' + role + '/' + id;
        toggleModal('editUserModal');
    }
</script>
@endsection
