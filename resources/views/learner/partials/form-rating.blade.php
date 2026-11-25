{{-- Formulir rating bintang 1–5 (learner). Variabel: $kelas, $ratingSaya (int|null) --}}
<form method="POST" action="{{ route('kelas.rating', $kelas) }}" class="form-rating" data-testid="form-rating-{{ $kelas->id }}">
    @csrf
    <div class="input-bintang" role="radiogroup" aria-label="Beri rating kelas">
        @for ($i = 5; $i >= 1; $i--)
            <input type="radio" id="bintang-{{ $kelas->id }}-{{ $i }}" name="bintang" value="{{ $i }}"
                   @checked((int) $ratingSaya === $i) required data-testid="rating-bintang-{{ $kelas->id }}-{{ $i }}">
            <label for="bintang-{{ $kelas->id }}-{{ $i }}" title="{{ $i }} bintang">★</label>
        @endfor
    </div>
    <button type="submit" class="btn btn-utama btn-sm" data-testid="kirim-rating-button-{{ $kelas->id }}">
        {{ $ratingSaya ? 'Perbarui Rating' : 'Kirim Rating' }}
    </button>
</form>
