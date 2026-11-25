@props(['nama'])

@php
    // Ikon garis sederhana (viewBox 24) — geometri stabil, tanpa dependensi.
    $ikon = [
        'dasbor' => '<rect x="3" y="3" width="8" height="8" rx="2"/><rect x="13" y="3" width="8" height="8" rx="2"/><rect x="3" y="13" width="8" height="8" rx="2"/><rect x="13" y="13" width="8" height="8" rx="2"/>',
        'chef' => '<path d="M6 13a4 4 0 0 1-1-7.9A5 5 0 0 1 12 3a5 5 0 0 1 7 2.1A4 4 0 0 1 18 13v5a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2z"/><path d="M9 15h6"/>',
        'pengguna' => '<circle cx="9" cy="8" r="3.5"/><path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6"/><circle cx="17.5" cy="9.5" r="2.5"/><path d="M21 20c0-2.5-1.6-4.6-4-5.4"/>',
        'profil' => '<circle cx="12" cy="8" r="3.5"/><path d="M5 21c0-3.9 3.1-7 7-7s7 3.1 7 7"/>',
        'kelas' => '<path d="M4 19V5a2 2 0 0 1 2-2h13v16H6a2 2 0 0 0-2 2Zm0 0a2 2 0 0 0 2 2h13"/><path d="M9 7h6"/>',
        'dompet' => '<rect x="3" y="6" width="18" height="14" rx="2"/><path d="M3 10h18"/><path d="M16 15h2"/>',
        'kalender' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 10h18"/>',
        'jam' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'obrolan' => '<path d="M21 15a3 3 0 0 1-3 3H8l-5 4V6a3 3 0 0 1 3-3h12a3 3 0 0 1 3 3z"/>',
        'balas' => '<path d="M9 14 4 9l5-5"/><path d="M4 9h10a6 6 0 0 1 6 6v4"/>',
        'menu' => '<path d="M6 2v7a3 3 0 0 0 6 0V2"/><path d="M9 2v20"/><path d="M19 2a4 4 0 0 0-3 4v7h3z"/><path d="M19 13v9"/>',
        'video' => '<rect x="3" y="6" width="12" height="12" rx="2.5"/><path d="m15 12 6-4v8z"/>',
        'tambah' => '<path d="M12 5v14M5 12h14"/>',
        'edit' => '<path d="M4 20h4L20.5 7.5a2.1 2.1 0 0 0-3-3L5 17z"/><path d="m13.5 6.5 3 3"/>',
        'hapus' => '<path d="M4 7h16M10 11v6M14 11v6M6 7l1 13a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2l1-13"/><path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/>',
        'keluar' => '<path d="M14 4h-6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h6"/><path d="m16 8 4 4-4 4M20 12H9"/>',
        'carikan' => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
        'berkas' => '<path d="M12 3v11"/><path d="m8 10 4 4 4-4"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2"/>',
        'gambar' => '<rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="1.6"/><path d="m5 19 6-6 3 3 3-3 2 2"/>',
        'centang' => '<path d="m5 13 4 4L19 7"/>',
        'silang' => '<path d="M6 6l12 12M18 6 6 18"/>',
        'kunci' => '<rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/>',
        'panah' => '<path d="M5 12h14"/><path d="m13 6 6 6-6 6"/>',
        'tatapan' => '<path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6-10-6-10-6Z"/><circle cx="12" cy="12" r="2.6"/>',
        'tanda-seru' => '<circle cx="12" cy="12" r="9"/><path d="M12 8v4M12 16h.01"/>',
        'lampiran' => '<path d="M20 11.5 12.5 19a5 5 0 0 1-7-7l8-8a3.5 3.5 0 0 1 5 5l-8 8a2 2 0 0 1-3-3l7-7"/>',
    ];
@endphp

<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" {!! $attributes->merge(['class' => 'ikon']) !!}>
    {!! $ikon[$nama] ?? $ikon['tanda-seru'] !!}
</svg>
