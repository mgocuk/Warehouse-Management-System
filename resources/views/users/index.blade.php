@extends('layouts.app')

@section('title', 'Kullanıcılar ve Roller Yönetimi')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pt-4">

    <!-- Add User Form Column -->
    <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl h-fit space-y-4">
        <h3 class="font-bold text-white text-base">Yeni Kullanıcı Hesabı Ekle</h3>
        
        <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Ad Soyad *</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Örn: Ahmet Yılmaz" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-purple-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">E-Posta Adresi *</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="ahmet@bilimmerkezi.org" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-purple-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Şifre *</label>
                <input type="password" name="password" required minlength="6" placeholder="******" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-purple-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Kullanıcı Rolü *</label>
                <select name="role_id" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-purple-500">
                    <option value="">Rol Seçiniz...</option>
                    @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="w-full py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs shadow-lg shadow-purple-600/30 transition-all">
                Kullanıcı Oluştur
            </button>
        </form>
    </div>

    <!-- Users Table Column -->
    <div class="lg:col-span-2 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl overflow-hidden">
        <div class="p-4 border-b border-slate-800">
            <h3 class="font-bold text-white text-base">Sistem Kullanıcıları</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/80 text-slate-400 border-b border-slate-800 uppercase tracking-wider">
                    <tr>
                        <th class="p-4 font-semibold">Ad Soyad</th>
                        <th class="p-4 font-semibold">E-Posta</th>
                        <th class="p-4 font-semibold">Rolü</th>
                        <th class="p-4 font-semibold text-center">Durum</th>
                        <th class="p-4 font-semibold text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($users as $u)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="p-4 font-semibold text-white">{{ $u->name }}</td>
                        <td class="p-4 text-slate-400 font-mono">{{ $u->email }}</td>
                        <td class="p-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 rounded-md text-[11px] font-bold uppercase whitespace-nowrap inline-block
                                {{ strtolower($u->role->name ?? '') === 'admin' ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : (strtolower($u->role->name ?? '') === 'depo sorumlusu' ? 'bg-sky-500/20 text-sky-300 border border-sky-500/30' : 'bg-slate-800 text-slate-300') }}">
                                {{ $u->role->name ?? 'Rol Yok' }}
                            </span>
                        </td>
                        <td class="p-4 text-center whitespace-nowrap">
                            @if($u->is_active)
                            <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 whitespace-nowrap inline-block">AKTİF</span>
                            @else
                            <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30 whitespace-nowrap inline-block">PASİF</span>
                            @endif
                        </td>
                        <td class="p-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-2 whitespace-nowrap">
                                {{-- Edit button opens inline modal --}}
                                <button onclick="openEditModal({{ $u->id }}, '{{ addslashes($u->name) }}', '{{ $u->email }}', {{ $u->role_id ?? 'null' }}, {{ $u->is_active ? 'true' : 'false' }})"
                                    class="px-2.5 py-1.5 rounded-lg bg-sky-600/20 hover:bg-sky-600/30 text-sky-300 border border-sky-500/30 transition-colors">
                                    Düzenle
                                </button>

                                @if($u->id !== Auth::id())
                                <form method="POST" action="{{ route('users.destroy', $u->id) }}" class="inline" onsubmit="return confirm('DİKKAT! Bu kullanıcı hesabını veritabanından kalıcı olarak silmek istediğinize emin misiniz?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-rose-600/20 hover:bg-rose-600/30 text-rose-300 border border-rose-500/30 transition-colors">
                                        Sil
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-slate-500">Henüz kullanıcı bulunmuyor.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-800">
            {{ $users->links() }}
        </div>
    </div>

</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm hidden">
    <div class="w-full max-w-md p-8 rounded-2xl bg-slate-900 border border-slate-700 shadow-2xl space-y-5 relative">
        <button onclick="closeEditModal()" class="absolute top-4 right-4 text-slate-400 hover:text-white">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <div>
            <h3 class="font-bold text-white text-base">Kullanıcıyı Düzenle</h3>
            <p class="text-xs text-slate-400 mt-1">Bilgileri değiştirip kaydet düğmesine basın.</p>
        </div>

        <form id="editUserForm" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Ad Soyad *</label>
                <input type="text" id="edit_name" name="name" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-sky-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">E-Posta *</label>
                <input type="email" id="edit_email" name="email" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-sky-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Yeni Şifre (boş bırakılırsa değişmez)</label>
                <input type="password" name="password" minlength="6" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-sky-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Kullanıcı Rolü *</label>
                <select id="edit_role_id" name="role_id" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-sky-500">
                    @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-3">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="edit_is_active" name="is_active" value="1" class="sr-only peer">
                    <div class="w-10 h-5 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:bg-emerald-500 transition-all"></div>
                    <span class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full transition-all peer-checked:translate-x-5"></span>
                </label>
                <span class="text-xs font-semibold text-slate-300">Hesap Aktif</span>
            </div>

            <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3">
                <button type="button" onclick="closeEditModal()" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold">İptal</button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold shadow-lg shadow-sky-600/30">
                    Değişiklikleri Kaydet
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(id, name, email, roleId, isActive) {
    document.getElementById('editUserForm').action = `/users/${id}`;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_role_id').value = roleId;
    document.getElementById('edit_is_active').checked = isActive;
    document.getElementById('editUserModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editUserModal').classList.add('hidden');
}

// Close modal on backdrop click
document.getElementById('editUserModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});
</script>
@endsection
