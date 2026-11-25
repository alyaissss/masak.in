@extends('layouts.app')

@section('judul', 'Forum Diskusi')
@section('sub-judul')
    <p class="sub-judul">Pertanyaan peserta dari seluruh kelas Anda — balas agar belajar mereka tetap mengalir.</p>
@endsection

@section('konten')
    @include('chef.partials.forum', ['pertanyaan' => $pertanyaan])
@endsection
