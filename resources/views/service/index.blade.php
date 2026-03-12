@extends('layouts.base')
@section('body')
    <div id="services" class="container">
        @include('layouts.flash-messages')
        <a class="btn btn-primary" href="{{ route('services.create') }}" role="button">add service</a>

        <form method="POST" enctype="multipart/form-data" action="{{ route('service.import') }}" class="mt-3 mb-3">
            @csrf
            <input type="file" id="serviceUploadName" name="service_upload" required>
            <button type="submit" class="btn btn-info btn-primary">Import Service Excel File</button>
        </form>

        {{ $dataTable->table() }}
    </div>

    @push('scripts')
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.0.3/css/buttons.dataTables.min.css">
        <script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
        <script src="/vendor/datatables/buttons.server-side.js"></script>
        {!! $dataTable->scripts() !!}
    @endpush
@endsection
