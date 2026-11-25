@props([
    'id',
    'label',
    'current' => null,
    'testid' => null,
])

<div class="field image-upload">
    <label for="{{ $id }}">{{ $label }}</label>
    <input
        id="{{ $id }}"
        name="foto"
        type="file"
        accept="image/jpeg,image/png,image/webp"
        class="input"
        data-image-crop
        @if ($testid) data-testid="{{ $testid }}" @endif
    >

    @if ($current)
        <img class="image-upload-current" src="{{ $current }}" alt="Foto yang digunakan saat ini">
    @endif

    <div class="image-crop-editor" data-crop-editor hidden>
        <div class="image-crop-frame" data-crop-frame>
            <img data-crop-preview alt="Pratinjau foto yang dipotong">
        </div>
        <div class="image-crop-controls">
            <div class="field">
                <label for="{{ $id }}-zoom">Perbesar <output data-crop-zoom-value>100%</output></label>
                <input id="{{ $id }}-zoom" type="range" min="1" max="3" step="0.01" value="1" data-crop-zoom>
            </div>
            <div class="field">
                <label for="{{ $id }}-horizontal">Geser horizontal</label>
                <input id="{{ $id }}-horizontal" type="range" min="0" max="100" step="1" value="50" data-crop-horizontal>
            </div>
            <div class="field">
                <label for="{{ $id }}-vertical">Geser vertikal</label>
                <input id="{{ $id }}-vertical" type="range" min="0" max="100" step="1" value="50" data-crop-vertical>
            </div>
        </div>
    </div>

    <p class="bantuan" data-crop-status aria-live="polite">JPG, PNG, atau WEBP · maksimal 2 MB · ukuran potong 16:9.</p>
    <p class="galat image-crop-error" data-crop-error role="alert" hidden></p>
    @error('foto') <span class="galat">{{ $message }}</span> @enderror
</div>
