@extends('layouts.app')

@section('title', 'Preview Nota Penjualan')

@section('content')
<div class="container mx-auto py-6">
    <h2 class="text-xl font-semibold mb-4">Preview Nota Penjualan #{{ $id }}</h2>

    <iframe
        src="{{ url('/api/nota/' . $id . '/preview') }}"
        width="100%"
        height="750"
        style="border: 1px solid #ccc;"></iframe>

    <div class="mt-6">
        <a href="{{ url('/api/nota/' . $id) }}"
            class="bg-green-600 text-white px-4 py-2 rounded"
            target="_blank">
            Download PDF
        </a>
    </div>
</div>
@endsection