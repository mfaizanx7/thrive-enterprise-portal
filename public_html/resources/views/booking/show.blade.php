<div class="modal-body">
    <div class="row">
        <div class="col-12 pb-2">
            <b>{{__(' Start Date Time')}}</b> : <span>{{ (!empty($booking->start_date)) ? date('d-M-Y , h:i a', strtotime($booking->start_date)): '-' }}</span>
        </div>
        <div class="col-12 pb-2">
            <b>{{__('End Date Time')}}</b> : <span>{{ (!empty($booking->end_date)) ? date('d-M-Y , h:i a', strtotime($booking->end_date)) : '-' }}</span>
        </div>
        <div class="col-12">
            <b>{{__('Total time')}}</b>  <span>{{ (!empty($booking->total_min)) ? ($booking->total_min).(' minutes') : '-' }}</span>
            <hr/>
        </div>
    </div>

    <div class="row pb-2">
        <div class="col-6">
 
        </div>
        <div class="col-6 pt-2">
            <div class="row text-center">

            </div>
        </div>
    </div>
</div>
