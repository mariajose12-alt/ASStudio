@extends('layouts.landing')

@section('title', 'Términos y Condiciones — AStudio')

@section('content')

    <header class="tc-hero">
        <p class="section-eyebrow">AStudio</p>
        <h1>Términos y Condiciones</h1>
        <p class="tc-hero-meta">Última actualización: {{ date('d \d\e F \d\e Y') }}</p>
    </header>

    <main class="tc-layout">
        @include('partials.terms-content')
    </main>

@endsection
