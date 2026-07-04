@extends('layouts.admin')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('clientuser.dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Client User') }}</li>
@endsection
@push('css-page')
<style>
    .date-slider-container {
        padding: 10px 0;
        position: relative;
    }
    
    .date-slider {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        position: relative;
    }
    
    .date-slider-track {
        display: flex;
        overflow-x: hidden;
        scroll-behavior: smooth;
        width: 100%;
        padding: 10px 0;
    }
    
    .date-item {
        min-width: 30px;
        text-align: center;
        cursor: pointer;
        padding: 10px 0;
        flex: 1;
        transition: all 0.3s ease;
        border-radius: 50px;
        margin:0px 5px;
        color:#6c757d;
    }
    
    .date-item:hover {
        border:1px solid var(--used-color);
    }
    
    .date-item.active {
        border:1px solid var(--used-color);
    }
    
    .day-name {
        color: var(--used-color);
        font-size: 14px;
        margin-bottom: 5px;
    }
    
    .day-number {
        font-size: 18px;
        font-weight: 600;
        color: var(--bs-btn-color);
        transition: all 0.3s ease;
    }
    
    .day-number.active-date {
        color: var(--used-color);
    }
    
    .slider-nav {
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        color: #6c757d;
        padding: 0 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1;
    }
    
    .slider-nav:hover {
        color:var(--used-color);
    }
    
    .slider-nav:disabled {
        color: #d1d1d1;
        cursor: not-allowed;
    }
</style>
@endpush
@push('script-page')
<!-- Add this JavaScript to initialize the date slider -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateSlider = document.getElementById('dateSlider');
    const prevBtn = document.getElementById('prevDate');
    const nextBtn = document.getElementById('nextDate');
    const dateItems = document.querySelectorAll('.date-item');
    const scrollAmount = 200; // Adjust this value based on your design
    
    // Initialize the tab content based on the active date
    function showActiveTab() {
        const activeItem = document.querySelector('.date-item.active');
        if (activeItem) {
            const targetId = activeItem.getAttribute('data-target');
            const tabContent = document.querySelectorAll('.tab-pane');
            
            tabContent.forEach(tab => {
                tab.classList.remove('active', 'show');
            });
            
            const activeTab = document.querySelector(targetId);
            if (activeTab) {
                activeTab.classList.add('active', 'show');
            }
            
            // Update the date display
            const dateStr = activeItem.getAttribute('data-date');
            const selectedDate = new Date(dateStr);
            const formattedDate = selectedDate.getDate() + ' ' +
                selectedDate.toLocaleString('default', { month: 'long' }) + ' ' +
                selectedDate.getFullYear() + ', ' +
                selectedDate.toLocaleString('default', { weekday: 'long' });
            
            const dateDisplay = activeTab.querySelector('.mb-4');
            if (dateDisplay) {
                dateDisplay.textContent = formattedDate;
            }
        }
    }
    
    // Event listeners for the date items
    dateItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remove active class from all items
            dateItems.forEach(di => {
                di.classList.remove('active');
                di.querySelector('.day-number').classList.remove('active-date');
            });
            
            // Add active class to clicked item
            this.classList.add('active');
            this.querySelector('.day-number').classList.add('active-date');
            
            // Show the corresponding tab
            showActiveTab();
        });
    });
    
    // Event listeners for navigation buttons
    prevBtn.addEventListener('click', function() {
        dateSlider.scrollLeft -= scrollAmount;
    });
    
    nextBtn.addEventListener('click', function() {
        dateSlider.scrollLeft += scrollAmount;
    });
    
    // Check scroll position to enable/disable navigation buttons
    dateSlider.addEventListener('scroll', function() {
        prevBtn.disabled = (dateSlider.scrollLeft <= 0);
        nextBtn.disabled = (dateSlider.scrollLeft + dateSlider.clientWidth >= dateSlider.scrollWidth);
    });
    
    // Initialize
    showActiveTab();
    prevBtn.disabled = true; // Initially disable the previous button
});
</script>
@endpush 
@php
    $setting = \App\Models\Utility::settings();

    $stat = 'Welcome';
    date_default_timezone_set('Asia/Karachi'); // Set the timezone to Pakistan
    $currentTime = date('H:i'); // Get the current time in 24-hour format
    if ($currentTime >= '05:00' && $currentTime < '11:30') {
        $stat = 'Good Morning';
    } elseif ($currentTime >= '11:30' && $currentTime < '17:30') {
        $stat = 'Good Afternoon';
    } elseif ($currentTime >= '17:30' && $currentTime < '20:00') {
        $stat = 'Good Evening';
    } else {
        $stat = 'Good Night';
    }
@endphp
@section('content')
    <div class=" row">
        <!-- Title and Top Buttons Start -->
        <div class="page-title-container">
            <div class="row">
                <!-- Title Start -->
                <div class="col-12 col-md-7">
                    {{-- <span class="align-middle text-muted d-inline-block lh-1 pb-2 pt-2 text-small">Home</span> --}}
                    <h1 class="mb-0 pb-0 display-6" id="title">{{ $stat }}, {{ $user->name }}</h1>
                </div>
                <!-- Title End -->
            </div>
        </div>
        <!-- Title and Top Buttons End -->

        <div class="row">
            <div class="col-xl-12">
                <!-- Stats Start -->
                <h2 class="small-title">Statistics</h2>
                    <div class="row " style="row-gap: 20px;">
        <div class="col-lg-3 col-md-12 dashboard-card">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mb-3 mb-sm-0">
                            <div class="d-flex align-items-center">
                                <div class="theme-avtar bg-primary">
                                    <i class="ti ti-layout-2"></i>
                                </div>
                                <div class="ms-3">
                                    <small class="text-muted">{{ __('Meeting') }}</small>
                                    <h6 class="m-0">{{ __('Hours') }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto text-end">
                            <h4 class="m-0">{{ $hours }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-12 dashboard-card">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mb-3 mb-sm-0">
                            <div class="d-flex align-items-center">
                                <div class="theme-avtar bg-primary">
                                    <i class="ti ti-notebook"></i>
                                </div>
                                <div class="ms-1">
                                    <small class="text-muted">{{ __('Total') }}</small>
                                    <h6 class="m-0">{{ __('Employees') }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto text-end">
                            <h4 class="m-0">{{ $employees }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-12 dashboard-card">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mb-3 mb-sm-0">
                            <div class="d-flex align-items-center">
                                <div class="theme-avtar bg-primary">
                                    <i class="ti ti-layout-2"></i>
                                </div>
                                <div class="ms-3">
                                    <small class="text-muted">{{ __('Total') }}</small>
                                    <h6 class="m-0">{{ __('Visitors') }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto text-end">
                            <h4 class="m-0">{{ $visit }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-12 dashboard-card">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto mb-3 mb-sm-0">
                            <div class="d-flex align-items-center">
                                <div class="theme-avtar bg-primary">
                                    <i class="ti ti-notebook"></i>
                                </div>
                                <div class="ms-3">
                                    <small class="text-muted">{{ __('Total') }}</small>
                                    <h6 class="m-0">{{ __('Mails') }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto text-end">
                            <h4 class="m-0">{{ $mail }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
            </div>
            <!-- Stats End -->
<div class="row pb-5">
    <!-- Appointments Start -->
    <div class="col-xl-6 mb-5">
        <div class="d-flex justify-content-between">
            <h2 class="small-title">Bookings</h2>
            <button class="btn btn-icon btn-icon-end btn-xs btn-background-alternate p-0 text-small" type="button">
                <a href="{{ route('booking.calendar', ['all']) }}"><span class="align-bottom">Add New</span></a>
                <i data-acorn-icon="chevron-right" class="align-middle" data-acorn-size="12"></i>
            </button>
        </div>
        <div class="card h-100 hover-border-primary">
            <div class="card-header border-0 pb-0 d-flex justify-content-center">
                <!-- Date slider starts here -->
                <div class="date-slider-container w-100">
                    <div class="date-slider">
                        <button class="slider-nav prev-btn" id="prevDate">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        
                        <div class="date-slider-track" id="dateSlider">
                            @for ($i = 0; $i < 15; $i++)
                                <div class="date-item @if ($i == 0) active @endif" 
                                     data-date="{{ \Carbon\Carbon::now()->addDays($i)->format('Y-m-d') }}"
                                     data-target="#day{{ $i + 1 }}">
                                    <div class="day-name">{{ substr(\Carbon\Carbon::now()->addDays($i)->format('l'), 0, 2) }}</div>
                                    <div class="day-number @if ($i == 0) active-date @endif">{{ \Carbon\Carbon::now()->addDays($i)->format('j') }}</div>
                                </div>
                            @endfor
                        </div>
                        
                        <button class="slider-nav next-btn" id="nextDate">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
                <!-- Date slider ends here -->
            </div>
            <div class="card-body pt-3">
                <div class="tab-content">
                    @for ($i = 0; $i < 12; $i++)
                        <div class="tab-pane fade @if ($i == 0) active @endif show mb-n3"
                            id="day{{ $i + 1 }}" role="tabpanel">
                            <div class="mb-4 text-primary text-center">
                                {{ \Carbon\Carbon::now()->addDays($i)->format('d F Y, l') }}</div>
                            @php
                                $bookings = \App\Models\Booking::with('space')
                                    ->where('owned_by', \Auth::user()->ownedId())
                                    ->whereDate(
                                        'start_date',
                                        \Carbon\Carbon::now()->addDays($i)->format('Y-m-d'),
                                    )
                                    ->get();
                            @endphp
                            @if (count($bookings) > 0)
                                @for ($j = 0; $j < count($bookings); $j++)
                                    <div class="row g-0 mb-3">
                                        <div class="col-auto">
                                            <div class="sw-5 d-inline-block d-flex align-items-center pt-1">
                                                <i data-acorn-icon="calendar"></i>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div
                                                class="card-body d-flex flex-column ps-0 pt-0 pb-0 h-100 justify-content-center">
                                                <div class="d-flex flex-column">
                                                    <div class="text-body">{{ $bookings[$j]->space->name }}
                                                    </div>
                                                    <div class="text-muted">
                                                        {{ \Auth::user()->dateFormat($bookings[$j]->start_date) }}
                                                        {{ \Auth::user()->timeFormat($bookings[$j]->start_date) }}
                                                    </div>
                                                    <div class="text-muted">
                                                        {{ \Auth::user()->dateFormat($bookings[$j]->end_date) }}
                                                        {{ \Auth::user()->timeFormat($bookings[$j]->end_date) }}
                                                    </div>
                                                    <div class="text-muted">{{ $bookings[$j]->total_min }}
                                                        mints</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            @else
                                <div class="text-center mb-3">
                                    <img src="img/illustration/icon-appointment.webp" class="theme-filter"
                                        alt="Bookings" />
                                    <p>No Bookings for the day!</p>
                                    <button class="btn btn-icon btn-icon-start btn-primary"
                                        type="button">
                                        <a href="{{ route('booking.calendar', ['all']) }}"><i
                                                data-acorn-icon="calendar"></i></a>
                                        <span>New Booking</span>
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
     <div class="col-xl-6 mb-5">
                    <div class="d-flex justify-content-between">
                        <h2 class="small-title"></h2>
                        <button class="btn btn-icon btn-icon-end btn-xs btn-background-alternate p-0 text-small"
                            type="button">
                            <a href="{{ route('isvisitor.index') }}"><span class="align-bottom">Add New</span></a>
                            <i data-acorn-icon="chevron-right" class="align-middle" data-acorn-size="12"></i>
                        </button>
                    </div>

                    <div class="card h-100 mt-3 hover-border-primary">
                        <div class="card-header">
                            <h5>Your Vistors</h5>
                        </div>
                        <div class="card-body" style="min-height: 250px;">
                            <div class="table-responsive">
                                @if (count($visitors) > 0)
                                    <table class="table align-items-center">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Name') }}</th>
                                                <th>{{ __('Date') }}</th>
                                                <th>{{ __('Time') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="list">
                                            @foreach ($visitors as $visitor)
                                                <tr>
                                                    <td>{{ $visitor->name }}</td>
                                                    <td>{{ \Auth::user()->dateFormat($visitor->date_time) }}</td>
                                                    <td>{{ \Auth::user()->timeFormat($visitor->date_time) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div class="p-2 text-primary">
                                        {{ __('No visitors yet.') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>


                    {{-- <h2 class="small-title">Your Vistors</h2>
                        <div class="card">
                            <div class="card-body mb-n3 border-last-none">
                                <div class="mb-3 pb-3 border-bottom border-separator-light">
                                    <div class="row g-0 sh-6">
                                        <div class="col-auto">
                                            <a href="Doctors.Detail.html">
                                                <img src="img/profile/profile-14.webp" class="card-img rounded-xl sh-6 sw-6"
                                                    alt="thumb" />
                                            </a>
                                        </div>
                                        <div class="col">
                                            <div
                                                class="card-body d-flex flex-row pt-0 pb-0 ps-3 pe-0 h-100 align-items-center justify-content-between">
                                                <div class="d-flex flex-column">
                                                    <a href="Doctors.Detail.html" class="body-link">Karter Kidd, M.D.</a>
                                                    <div class="text-small text-muted">Neurologist</div>
                                                </div>
                                                <div class="d-flex">
                                                    <button class="btn btn-outline-secondary btn-sm ms-1"
                                                        type="button">Schedule</button>
                                                    <button
                                                        class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary ms-1"
                                                        type="button">
                                                        <i data-acorn-icon="more-vertical"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3 pb-3 border-bottom border-separator-light">
                                    <div class="row g-0 sh-6">
                                        <div class="col-auto">
                                            <a href="Doctors.Detail.html">
                                                <img src="img/profile/profile-12.webp" class="card-img rounded-xl sh-6 sw-6"
                                                    alt="thumb" />
                                            </a>
                                        </div>
                                        <div class="col">
                                            <div
                                                class="card-body d-flex flex-row pt-0 pb-0 ps-3 pe-0 h-100 align-items-center justify-content-between">
                                                <div class="d-flex flex-column">
                                                    <a href="Doctors.Detail.html" class="body-link">Carmelo Avril,
                                                        M.B.B.S.</a>
                                                    <div class="text-small text-muted">Rheumatologist</div>
                                                </div>
                                                <div class="d-flex">
                                                    <button class="btn btn-outline-secondary btn-sm ms-1"
                                                        type="button">Schedule</button>
                                                    <button
                                                        class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary ms-1"
                                                        type="button">
                                                        <i data-acorn-icon="more-vertical"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3 pb-3 border-bottom border-separator-light">
                                    <div class="row g-0 sh-6">
                                        <div class="col-auto">
                                            <a href="Doctors.Detail.html">
                                                <img src="img/profile/profile-13.webp" class="card-img rounded-xl sh-6 sw-6"
                                                    alt="thumb" />
                                            </a>
                                        </div>
                                        <div class="col">
                                            <div
                                                class="card-body d-flex flex-row pt-0 pb-0 ps-3 pe-0 h-100 align-items-center justify-content-between">
                                                <div class="d-flex flex-column">
                                                    <a href="Doctors.Detail.html" class="body-link">Wiebe Rodolfo, M.D.</a>
                                                    <div class="text-small text-muted">Psychiatrist</div>
                                                </div>
                                                <div class="d-flex">
                                                    <button class="btn btn-outline-secondary btn-sm ms-1"
                                                        type="button">Schedule</button>
                                                    <button
                                                        class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary ms-1"
                                                        type="button">
                                                        <i data-acorn-icon="more-vertical"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3 pb-3 border-bottom border-separator-light">
                                    <div class="row g-0 sh-6">
                                        <div class="col-auto">
                                            <a href="Doctors.Detail.html">
                                                <img src="img/profile/profile-15.webp" class="card-img rounded-xl sh-6 sw-6"
                                                    alt="thumb" />
                                            </a>
                                        </div>
                                        <div class="col">
                                            <div
                                                class="card-body d-flex flex-row pt-0 pb-0 ps-3 pe-0 h-100 align-items-center justify-content-between">
                                                <div class="d-flex flex-column">
                                                    <a href="Doctors.Detail.html" class="body-link">Alma Holder, D.M.S.</a>
                                                    <div class="text-small text-muted">Ophthalmologist</div>
                                                </div>
                                                <div class="d-flex">
                                                    <button class="btn btn-outline-secondary btn-sm ms-1"
                                                        type="button">Schedule</button>
                                                    <button
                                                        class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary ms-1"
                                                        type="button">
                                                        <i data-acorn-icon="more-vertical"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3 border-bottom border-separator-light">
                                    <div class="row g-0 sh-6">
                                        <div class="col-auto">
                                            <a href="Doctors.Detail.html">
                                                <img src="img/profile/profile-16.webp" class="card-img rounded-xl sh-6 sw-6"
                                                    alt="thumb" />
                                            </a>
                                        </div>
                                        <div class="col">
                                            <div
                                                class="card-body d-flex flex-row pt-0 pb-0 ps-3 pe-0 h-100 align-items-center justify-content-between">
                                                <div class="d-flex flex-column">
                                                    <a href="Doctors.Detail.html" class="body-link">Isaac Mckee, D.O.</a>
                                                    <div class="text-small text-muted">Neurologist</div>
                                                </div>
                                                <div class="d-flex">
                                                    <button class="btn btn-outline-secondary btn-sm ms-1"
                                                        type="button">Schedule</button>
                                                    <button
                                                        class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary ms-1"
                                                        type="button">
                                                        <i data-acorn-icon="more-vertical"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                </div>
    <!-- Appointments End -->
    
    <!-- Rest of the code for visitors section -->
    <!-- Your Visitors section code remains the same -->
</div>
            <!--<div class="row">
                <!-- Appointments Start
                <div class="col-xl-6 mb-5">
                    <div class="d-flex justify-content-between">
                        <h2 class="small-title">Bookings</h2>
                        <button class="btn btn-icon btn-icon-end btn-xs btn-background-alternate p-0 text-small"
                            type="button">
                            <a href="{{ route('booking.calendar', ['all']) }}"><span class="align-bottom">Add New</span></a>
                            <i data-acorn-icon="chevron-right" class="align-middle" data-acorn-size="12"></i>
                        </button>
                    </div>
                    <div class="card h-xl-100-card hover-border-primary">
                        <div class="card-header border-0 pb-0 d-flex justify-content-center">
                            <div class="glide-tab-container">
                                <div class="glide glide-tab" id="appointmentsCarousel">
                                    <div id="calendar" class="compact h-100"></div>
                                    <div class="glide__track" data-glide-el="track">
                                        <div class="glide__slides nav nav-pills" role="tablist">
                                            @for ($i = 0; $i < 15; $i++)
                                                <div class="glide__slide @if ($i == 0) active @endif"
                                                    data-bs-toggle="tab" data-bs-target="#day{{ $i + 1 }}"
                                                    role="tab" aria-selected="{{ $i === 0 ? 'true' : 'false' }}">
                                                    <button
                                                        class="btn btn-foreground hover-outline px-1 py-3 rounded-xl sw-4"
                                                        type="button">
                                                        <div class="text-alternate mb-2">
                                                            {{ substr(\Carbon\Carbon::now()->addDays($i)->format('l'), 0, 2) }}
                                                        </div>
                                                        <div class="text-primary">
                                                            {{ \Carbon\Carbon::now()->addDays($i)->format('j') }}</div>
                                                    </button>
                                                </div>
                                            @endfor

                                        </div>
                                    </div>
                                    <div class="glide__arrows" data-glide-el="controls">
                                        <button class="btn btn-icon btn-icon-only btn-link left-arrow btn-sm mt-3"
                                            data-glide-dir="<">
                                            <i data-acorn-icon="chevron-left"></i>
                                        </button>
                                        <button class="btn btn-icon btn-icon-only btn-link right-arrow btn-sm mt-3"
                                            data-glide-dir=">">
                                            <i data-acorn-icon="chevron-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-3">
                            <div class="tab-content">
                                @for ($i = 0; $i < 12; $i++)
                                    {{-- <div class="glide__slide @if ($i == 0) active @endif" data-bs-toggle="tab" data-bs-target="#day{{ $i + 1 }}" role="tab" aria-selected="{{ $i === 0 ? 'true' : 'false' }}"> --}}
                                    <div class="tab-pane fade @if ($i == 0) active @endif show mb-n3"
                                        id="day{{ $i + 1 }}" role="tabpanel">
                                        <div class="mb-4 text-primary text-center">
                                            {{ \Carbon\Carbon::now()->addDays($i)->format('d F Y, l') }}</div>
                                        @php
                                            $bookings = \App\Models\Booking::with('space')
                                                ->where('owned_by', \Auth::user()->ownedId())
                                                ->whereDate(
                                                    'start_date',
                                                    \Carbon\Carbon::now()->addDays($i)->format('Y-m-d'),
                                                )
                                                ->get();
                                        @endphp
                                        @if (count($bookings) > 0)
                                            @for ($j = 0; $j < count($bookings); $j++)
                                                <div class="row g-0 mb-3">
                                                    <div class="col-auto">
                                                        <div class="sw-5 d-inline-block d-flex align-items-center pt-1">
                                                            <i data-acorn-icon="calendar"></i>
                                                            {{-- <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-notebook-1 undefined"><path d="M3 5.5C3 4.09554 3 3.39331 3.33706 2.88886C3.48298 2.67048 3.67048 2.48298 3.88886 2.33706C4.39331 2 5.09554 2 6.5 2H13.5C14.9045 2 15.6067 2 16.1111 2.33706C16.3295 2.48298 16.517 2.67048 16.6629 2.88886C17 3.39331 17 4.09554 17 5.5V14.5C17 15.9045 17 16.6067 16.6629 17.1111C16.517 17.3295 16.3295 17.517 16.1111 17.6629C15.6067 18 14.9045 18 13.5 18H6.5C5.09554 18 4.39331 18 3.88886 17.6629C3.67048 17.517 3.48298 17.3295 3.33706 17.1111C3 16.6067 3 15.9045 3 14.5V5.5Z"></path><path d="M8 6H12M8 10H12M8 14H12M2 8H4M2 12H4"></path></svg> --}}
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div
                                                            class="card-body d-flex flex-column ps-0 pt-0 pb-0 h-100 justify-content-center">
                                                            <div class="d-flex flex-column">
                                                                <div class="text-body">{{ $bookings[$j]->space->name }}
                                                                </div>
                                                                <div class="text-muted">
                                                                    {{ \Auth::user()->dateFormat($bookings[$j]->start_date) }}
                                                                    {{ \Auth::user()->timeFormat($bookings[$j]->start_date) }}
                                                                </div>
                                                                <div class="text-muted">
                                                                    {{ \Auth::user()->dateFormat($bookings[$j]->end_date) }}
                                                                    {{ \Auth::user()->timeFormat($bookings[$j]->end_date) }}
                                                                </div>
                                                                <div class="text-muted">{{ $bookings[$j]->total_min }}
                                                                    mints</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endfor
                                        @else
                                            <div class="tab-pane" id="dayNone" role="tabpanel">

                                                <div class="text-center">
                                                    <img src="img/illustration/icon-appointment.webp" class="theme-filter"
                                                        alt="Bookings" />
                                                    <p>No Bookings for the day!</p>
                                                    <button class="btn btn-icon btn-icon-start btn-primary"
                                                        type="button">
                                                        <a href="{{ route('booking.calendar', ['all']) }}"><i
                                                                data-acorn-icon="calendar"></i></a>
                                                        <span>New Booking</span>
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endfor

                            </div>

                        </div>
                    </div>
                </div>

                
               
                
            </div>-->
        </div>
    </div>

    <div class="col-sm-12">
        <div class="row">
            <div class="col-lg-6">
                <div class="card hover-border-primary">
                    <div class="card-header">

                        <h5>{{ __('Announcement List') }}</h5>
                    </div>
                    <div class="card-body" style="min-height: 250px;">
                        <div class="table-responsive">
                            @if (count($announcements) > 0)
                                <table class="table align-items-center">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Title') }}</th>
                                            <th>{{ __('Start Date') }}</th>
                                            <th>{{ __('End Date') }}</th>

                                        </tr>
                                    </thead>
                                    <tbody class="list">
                                        @foreach ($announcements as $announcement)
                                            <tr>
                                                <td>{{ $announcement->title }}</td>
                                                <td>{{ \Auth::user()->dateFormat($announcement->start_date) }}</td>
                                                <td>{{ \Auth::user()->dateFormat($announcement->end_date) }}</td>

                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="p-2 text-primary">
                                    {{ __('No accouncement present yet.') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card hover-border-primary">
                    <div class="card-header">
                        <h5>{{ __('Meeting schedule') }}</h5>
                    </div>
                    <div class="card-body" style="min-height: 250px;">
                        <div class="table-responsive">
                            @if (count($meetings) > 0)
                                <table class="table align-items-center">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Title') }}</th>
                                            <th>{{ __('Date') }}</th>
                                            <th>{{ __('Time') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list">
                                        @foreach ($meetings as $meeting)
                                            <tr>
                                                <td>{{ $meeting->title }}</td>
                                                <td>{{ \Auth::user()->dateFormat($meeting->date) }}</td>
                                                <td>{{ \Auth::user()->timeFormat($meeting->time) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="p-2 text-primary">
                                    {{ __('No meeting scheduled yet.') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
