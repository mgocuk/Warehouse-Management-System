@extends('layouts.app')

@section('title', 'Kullanıcılar ve Roller')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Yeni Kullanıcı Ekleme Formu -->
    <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm h-fit">
        <h2 class="text-base font-bold text-gray-800 pb-2 mb-3 border-b border-gray-200">Yeni Kullanıcı Ekle</h2>
        
        <form method="POST" action="{{ route('users.store') }}" class="space-y-3">
            @csrf
            
            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Ad Soyad *</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Örn: Ahmet Yılmaz" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">E-Posta Adresi *</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="ahmet@example.com" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Şifre *</label>
                <input type="password" name="password" required minlength="6" placeholder="En az 6 karakter" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Kullanıcı Rolü *</label>
                <select name="role_id" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
                    <option value="">Rol Seçiniz...</option>
                    @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded">
                Kullanıcı Oluştur
            </button>
        </form>
    </div>

    <!-- Kullanıcılar Tablosu -->
    <div class="lg:col-span-2 bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-sm font-bold text-gray-800">Sistem Kullanıcıları</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead class="bg-gray-50 text-gray-700 border-b border-gray-200 text-xs font-semibold uppercase">
                    <tr>
                        <th class="p-3">Ad Soyad</th>
                        <th class="p-3">E-Posta</th>
                        <th class="p-3">Rolü</th>
                        <th class="p-3 text-center">Durum</th>
                        <th class="p-3 text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-xs">
                    @forelse($users as $u)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 font-bold text-gray-800">{{ $u->name }}</td>
                        <td class="p-3 text-gray-600">{{ $u->email }}</td>
                        <td class="p-3 whitespace-nowrap">
                            <span class="bg-blue-100 text-blue-800 font-semibold px-2 py-0.5 rounded text-[11px]">
                                {{ $u->role->name ?? 'Rol Yok' }}
                            </span>
                        </td>
                        <td class="p-3 text-center whitespace-nowrap">
                            @if($u->is_active)
                            <span class="bg-green-100 text-green-800 font-bold px-2 py-0.5 rounded text-[11px]">AKTİF</span>
                            @else
                            <span class="bg-red-100 text-red-800 font-bold px-2 py-0.5 rounded text-[11px]">PASİF</span>
                            @endif
                        </td>
                        <td class="p-3 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <button onclick="openEditModal({{ $u->id }}, '{{ addslashes($u->name) }}', '{{ $u->email }}', {{ $u->role_id ?? 'null' }}, {{ $u->is_active ? 'true' : 'false' }})"
                                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded text-xs font-medium">
                                    Düzenle
                                </button>

                                @if($u->id !== Auth::id())
                                <form method="POST" action="{{ route('users.destroy', $u->id) }}" class="inline" onsubmit="return confirm('Bu kullanıcıyı kalıcı olarak silmek istediğinize emin misiniz?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded text-xs font-medium">
                                        Sil
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-gray-500">Henüz kullanıcı bulunmuyor.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3 border-t border-gray-200 bg-gray-50">
            {{ $users->links() }}
        </div>
    </div>

</div>

<!-- Kullanıcı Düzenleme Modalı -->
<div id="editUserModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden">
    <div class="w-full max-w-md p-6 rounded-lg bg-white border border-gray-200 shadow-xl space-y-4 relative">
        <button onclick="closeEditModal()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-700 text-xl font-bold">&times;</button>

        <div class="border-b pb-2">
            <h3 class="font-bold text-gray-800 text-base">Kullanıcı Bilgilerini Düzenle</h3>
        </div>

        <form id="editUserForm" method="POST" action="" class="space-y-3">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Ad Soyad *</label>
                <input type="text" id="edit_name" name="name" required class="w-full border border-gray-300 rounded px-3 py-1.5 text-sm focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">E-Posta *</label>
                <input type="email" id="edit_email" name="email" required class="w-full border border-gray-300 rounded px-3 py-1.5 text-sm focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Yeni Şifre (boş bırakılırsa değişmez)</label>
                <input type="password" name="password" minlength="6" placeholder="Değiştirmek istemiyorsanız boş bırakın" class="w-full border border-gray-300 rounded px-3 py-1.5 text-sm focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Kullanıcı Rolü *</label>
                <select id="edit_role_id" name="role_id" required class="w-full border border-gray-300 rounded px-3 py-1.5 text-sm focus:outline-none focus:border-blue-500">
                    @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" id="edit_is_active" name="is_active" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <label for="edit_is_active" class="text-xs font-semibold text-gray-700">Hesap Aktif</label>
            </div>

            <div class="pt-3 border-t border-gray-200 flex items-center justify-end gap-2">
                <button type="button" onclick="closeEditModal()" class="px-3 py-1.5 rounded border border-gray-300 text-gray-700 text-xs font-medium hover:bg-gray-100">İptal</button>
                <button type="submit" class="px-4 py-1.5 rounded bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold">
                    Kaydet
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(id, name, email, roleId, isActive) {
    document.getElementById('editUserForm').action = '/users/' + id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_role_id').value = roleId;
    document.getElementById('edit_is_active').checked = isActive;
    document.getElementById('editUserModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editUserModal').classList.add('hidden');
}

document.getElementById('editUserModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});
</script>
@endsection
