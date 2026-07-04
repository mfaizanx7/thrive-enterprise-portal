@extends('layouts.admin')

@section('page-title')
    {{ __('Import Leads from Excel') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('leads.index') }}">{{ __('Leads') }}</a></li>
    <li class="breadcrumb-item">{{ __('Import Excel') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <strong>{{ __('Success!') }}</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <strong>{{ __('Error!') }}</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Import Leads from Excel') }}</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6>{{ __('Excel Column Headers Required:') }}</h6>
                        <ul class="mb-0">
                            <li><strong>full_name</strong> or <strong>Full Name</strong> (Required)</li>
                            <li><strong>email</strong> or <strong>Email</strong> (Required)</li>
                            <li><strong>Business/Startup</strong> or <strong>business_startup</strong> (Optional)</li>
                            <li><strong>phone</strong> or <strong>Phone</strong> (Optional)</li>
                            <li><strong>inbox_url</strong> or <strong>Inbox URL</strong> (Optional)</li>
                            <li><strong>team_members</strong> or <strong>Team Members</strong> (Optional)</li>
                            <li><strong>Sr.No</strong> or <strong>sr_no</strong> (Optional)</li>
                            <li><strong>Update</strong> or <strong>update</strong> (Optional)</li>
                            <li><strong>Follow Up 1</strong> or <strong>follow_up_1</strong> (Optional)</li>
                            <li><strong>Update 2.0</strong> or <strong>update_2_0</strong> (Optional)</li>
                            <li><strong>Follow Up 2</strong> or <strong>follow_up_2</strong> (Optional)</li>
                            <li><strong>Follow Up 3</strong> or <strong>follow_up_3</strong> (Optional)</li>
                        </ul>
                    </div>

                    <form action="{{ route('leads.import_excel') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="excel_file" class="form-label">{{ __('Select Excel File') }} (.xlsx, .xls)</label>
                            <input type="file" class="form-control" name="file" id="excel_file" accept=".xlsx,.xls" required>
                        </div>
                        <div class="mb-3">
                            <p class="text-muted">{{ __('Note: Leads will be automatically assigned to the MindStir pipeline and "new inquiry" stage.') }}</p>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('leads.index') }}" class="btn btn-light">{{ __('Cancel') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('Import Leads') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
