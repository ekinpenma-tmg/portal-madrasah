@php
    $jBadge = ['RA'=>'badge-ra','MI'=>'badge-mi','MTs'=>'badge-mts','MA'=>'badge-ma'];
    $aBadge = ['A'=>'badge-a','B'=>'badge-b','C'=>'badge-c'];
@endphp

@if($madrasah->count() > 0)
<div class="overflow-x-auto">
    <table class="tbl-madrasah">
        <thead>
            <tr>
                <th class="text-left" style="width:40px">No</th>
                <th class="text-left">NSM</th>
                <th class="text-left">Nama Madrasah</th>
                <th class="text-center">Jenjang</th>
                <th class="text-center">Status</th>
                <th class="text-left">Kecamatan</th>
                <th class="text-center">Akreditasi</th>
                @if(!empty($rekapSiswa))
                <th class="text-right">Siswa</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($madrasah as $i => $m)
            <tr>
                <td class="text-gray-300 text-xs font-mono">{{ $madrasah->firstItem() + $i }}</td>
                <td class="font-mono text-xs text-gray-400">{{ $m->nsm }}</td>
                <td><span class="font-semibold text-gray-800 text-sm">{{ $m->nama_madrasah }}</span></td>
                <td class="text-center">
                    <span class="badge-jenjang {{ $jBadge[$m->jenjang] ?? '' }}">{{ $m->jenjang }}</span>
                </td>
                <td class="text-center">
                    <span class="badge-status {{ $m->status === 'Negeri' ? 'badge-negeri' : 'badge-swasta' }}">
                        {{ $m->status }}
                    </span>
                </td>
                <td class="text-xs text-gray-500">{{ $m->kecamatan }}</td>
                <td class="text-center">
                    @if($m->akreditasi)
                    <span class="badge-akr {{ $aBadge[$m->akreditasi] ?? 'badge-tt' }}">{{ $m->akreditasi }}</span>
                    @else
                    <span class="text-gray-300 text-xs">—</span>
                    @endif
                </td>
                @if(!empty($rekapSiswa))
                <td class="text-right font-bold text-gray-700 text-sm">
                    {{ $m->siswaLatest ? number_format($m->siswaLatest->total_siswa) : '—' }}
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
{{-- Pagination --}}
<div class="pagination-wrap">
    <p class="text-xs text-gray-400">
        Menampilkan <strong class="text-gray-600">{{ $madrasah->firstItem() }}–{{ $madrasah->lastItem() }}</strong>
        dari <strong class="text-gray-600">{{ number_format($madrasah->total()) }}</strong> madrasah
    </p>
    {{ $madrasah->links() }}
</div>

@else
<div class="empty-state">
    <div class="empty-icon">🏫</div>
    <p class="font-bold text-gray-500 text-lg mb-1">
        {{ request()->hasAny(['search','jenjang','kecamatan','status']) ? 'Tidak ada data yang cocok' : 'Belum ada data madrasah' }}
    </p>
    <p class="text-sm text-gray-400">
        {{ request()->hasAny(['search','jenjang','kecamatan','status']) ? 'Coba ubah filter pencarian.' : 'Data akan tersedia setelah admin melakukan import dari EMIS.' }}
    </p>
</div>
@endif
