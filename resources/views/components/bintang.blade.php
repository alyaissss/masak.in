@props(['nilai' => null, 'jumlah' => 0])

{{-- Tampilan rating bintang (read-only). --}}
@if ($nilai)
    <span {{ $attributes->merge(['class' => 'bintang-tampil']) }} aria-label="Rating {{ number_format($nilai, 1, ',', '.') }} dari 5">
        <span class="bintang-baris" aria-hidden="true">
            @for ($i = 1; $i <= 5; $i++)
                <span class="{{ $i <= round($nilai) ? 'isi' : '' }}">★</span>
            @endfor
        </span>
        <strong>{{ number_format($nilai, 1, ',', '.') }}</strong>
        @if ($jumlah) <span>({{ $jumlah }} rating)</span> @endif
    </span>
@else
    <span {{ $attributes->merge(['class' => 'bintang-tampil kosong-rating']) }}>Belum ada rating</span>
@endif
