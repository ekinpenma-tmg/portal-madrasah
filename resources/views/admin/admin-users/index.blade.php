@extends('layouts.admin')
@section('title', 'Kelola Admin')
@section('content')

{{-- Header --}}
<div class="flex items-center justify-between mb-4 fade-in">
    <div>
        <h1 class="text-sm font-semibold text-zinc-900">Kelola Akun Admin</h1>
        <p class="text-xs text-zinc-400 mt-0.5">Daftar akun yang dapat mengakses dashboard</p>
    </div>
    <a href="{{ route('admin.admin-users.create') }}" class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg bg-zinc-900 text-white hover:bg-zinc-700 transition">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Admin
    </a>
</div>

{{-- Info box --}}
<div class="bg-blue-50 border border-blue-100 rounded-xl px-4 py-3 mb-4 flex items-start gap-2.5 fade-in">
    <svg class="w-4 h-4 text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <div>
        <p class="text-xs font-medium text-blue-800">Pendaftaran akun baru hanya bisa dilakukan dari halaman ini</p>
        <p class="text-2xs text-blue-500 mt-0.5">Halaman register publik telah dinonaktifkan untuk keamanan sistem.</p>
    </div>
</div>

{{-- Tabel --}}
<div class="bg-white rounded-xl border border-zinc-100 overflow-hidden fade-in">
    <table class="w-full">
        <thead>
            <tr class="tbl-head">
                <th class="px-4 py-2.5 text-left text-2xs font-semibold text-zinc-600 uppercase tracking-wider">Admin</th>
                <th class="px-4 py-2.5 text-left text-2xs font-semibold text-zinc-600 uppercase tracking-wider hidden sm:table-cell">Email</th>
                <th class="px-4 py-2.5 text-center text-2xs font-semibold text-zinc-600 uppercase tracking-wider hidden md:table-cell">Bergabung</th>
                <th class="px-4 py-2.5 text-center text-2xs font-semibold text-zinc-600 uppercase tracking-wider">Peran</th>
                <th class="px-4 py-2.5 text-center text-2xs font-semibold text-zinc-600 uppercase tracking-wider">Status</th>
                <th class="px-4 py-2.5 text-center text-2xs font-semibold text-zinc-600 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($admins as $admin)
            <tr class="tbl-row" style="border-bottom:1px solid #fafafa">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 text-white font-semibold text-2xs bg-zinc-800">
                            {{ strtoupper(substr($admin->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-zinc-800 truncate">{{ $admin->name }}</p>
                            @if($admin->id === Auth::id())
                                <p class="text-2xs text-green-600 font-medium">Akun Anda</p>
                            @endif
                            <p class="text-2xs text-zinc-400 sm:hidden truncate">{{ $admin->email }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 hidden sm:table-cell"><span class="text-xs text-zinc-500">{{ $admin->email }}</span></td>
                <td class="px-4 py-3 hidden md:table-cell"><span class="text-2xs text-zinc-400">{{ $admin->created_at->format('d M Y') }}</span></td>
                <td class="px-4 py-3 text-center">
                    <span class="badge {{ $admin->role === 'super_admin' ? 'badge-blue' : 'badge-gray' }}">
                        {{ $admin->role === 'super_admin' ? 'Super Admin' : 'Staff' }}
                    </span>
                </td>
                <td class="px-4 py-3 text-center">
                    <span class="badge badge-green">Aktif</span>
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="flex items-center justify-center gap-1">
                        <a href="{{ route('admin.admin-users.edit', $admin->id) }}" class="btn-icon blue" title="Edit">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>

                        @if($admin->id !== Auth::id() && $admins->total() > 1)
                        <form action="{{ route('admin.admin-users.destroy', $admin->id) }}" method="POST"
                              data-confirm="Hapus akun admin {{ addslashes($admin->name) }}? Akun tidak bisa dipulihkan." data-confirm-btn="Ya, Hapus" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-icon danger" title="Hapus">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                        @else
                        <span class="btn-icon" style="cursor:not-allowed; opacity:0.4" title="{{ $admin->id === Auth::id() ? 'Tidak bisa hapus akun sendiri' : 'Minimal harus ada 1 admin' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </span>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="py-14 text-center">
                    <svg class="w-10 h-10 mx-auto text-zinc-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-xs text-zinc-400">Belum ada akun admin</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 flex items-center justify-between" style="border-top:1px solid #f4f4f5">
        <p class="text-2xs text-zinc-400">Total: {{ $admins->total() }} akun admin</p>
        {{ $admins->links() }}
    </div>
</div>

@endsection