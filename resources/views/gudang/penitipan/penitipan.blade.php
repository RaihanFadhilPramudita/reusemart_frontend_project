@extends('layouts.app')

@section('title', 'Preview Nota')

@section('content')
<div class="container mx-auto py-6">
  <h2 class="text-xl font-semibold mb-4">Preview Nota Penitipan #{{ $id }}</h2>

  <iframe
        src="http://localhost:8000/api/gudang/penitipan/{{ $id }}/nota-preview"
        width="100%"
        height="750"
        style="border: 1px solid #ccc;"
    ></iframe>


  <div class="mt-6">
   <a href="http://localhost:8000/api/gudang/penitipan/{{ $id }}/nota-download"
        class="bg-green-600 text-white px-4 py-2 rounded"
        target="_blank">
        Download PDF
    </a>
  </div>
</div>
@endsection
