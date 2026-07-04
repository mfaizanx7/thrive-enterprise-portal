@extends('layouts.admin')

@section('page-title')
    {{__('Space Booking')}}
@endsection

@push('css-page')
    <link rel="stylesheet" href="{{ asset('assets/libs/fullcalendar/dist/fullcalendar.min.css') }}">
@endpush

@php
    $setting = \App\Models\Utility::settings();
@endphp

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Space Calendar')}}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                            <tr>
                                <th> {{ __('#') }}</th>
                                <th> {{ __('Space Name') }}</th>
                                <th> {{ __('Company') }}</th>
                                <th> {{ __('User') }}</th>
                                <th> {{ __('duration/mins') }}</th>
                                <th> {{ __('Start Date / Time') }}</th>
                                <th> {{ __('End Date / Time') }}</th>
                                <th> {{ __('Action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($bookings as $booking)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ @$booking->space->name }}</td>
                                    <td>{{ @$booking->company->name }}</td>
                                    <td>{{ @$booking->user->name }}</td>
                                    <td>{{ $booking->total_min }} <small> Minutes</small></td>
                                    <td>{{ date('Y-m-d H:i A',strtotime($booking->start_date)) }}</td>
                                    <td>{{ date('Y-m-d H:i A',strtotime($booking->end_date)) }}</td>
                                    <td class="Action">
                                        <a href="#" data-size="md" data-url="{{ route('booking.edit',$booking->id) }}" style="mx-3 btn btn-sm align-items-center" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Edit Booking')}}" data-title="{{__('Edit Booking')}}"  id="createBookingLink" class="btn btn-sm btn-primary">
                                            <i class="ti ti-pencil"></i>
                                        </a>
                                        {{-- <a href="{{ route('booking.edit',\Crypt::encrypt($expense->id)) }}" class="mx-3 btn btn-sm align-items-center" data-bs-toggle="tooltip" title="Edit" data-original-title="{{__('Edit')}}">
                                            <i class="ti ti-pencil text-white"></i>
                                        </a> --}}
                                    </td>
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