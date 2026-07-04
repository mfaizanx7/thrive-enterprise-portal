@extends('layouts.admin')
@section('page-title')
    {{ __('Bookings') }}
@endsection
@push('script-page')
    <script src="{{ asset('js/jquery-searchbox.js') }}"></script>
@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Taxes') }}</li>
@endsection
@section('action-btn')
    <div class="float-end">
        {{-- @can('create constant tax')
            <a href="#" data-url="{{ route('taxes.create') }}" data-ajax-popup="true" data-title="{{__('Create Tax Rate')}}" data-bs-toggle="tooltip" title="{{__('Create')}}"  class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
        @endcan --}}
    </div>
@endsection

@section('content')
    <div class="row">

        <div class="col-10">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    {{-- <th> {{ __('Image') }}</th> --}}
                                    <th> {{ __('Space Name') }}</th>
                                    <th> {{ __('User') }}</th>
                                    <th> {{ __('duration/mins') }}</th>
                                    <th> {{ __('start date') }}</th>
                                    <th> {{ __('end date') }}</th>
                                    
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($bookings as $booking)
                                    <tr class="font-style">
                                        {{-- <td><img src="{{ asset('storage/' . $project->project_image) }}" alt="project image"></td> --}}
                                        <td>{{ $booking->space_name }}</td>
                                        <td>{{ $booking->user_name }}</td>
                                        <td>{{ $booking->total_min }}</td>
                                        <td>{{ $booking->start_date }}</td>
                                        <td>{{ $booking->end_date }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
