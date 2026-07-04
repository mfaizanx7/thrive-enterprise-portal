{{ Form::model($booking, ['route' => ['booking.update', $booking->id], 'method' => 'PUT']) }}
<div class="modal-body">
    <div class="row">
        <input type="hidden" name="booking_id" value="{{$booking->id}}">
        <div class="col-12 form-group">       
            <label for="space" class="form-label">{{ __('Space') }}</label>
            {{ Form::select('space_id', $space, $booking->space_id, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Select Space'),]) }}
        </div>
        <div class="col-12 form-group">       
            <label for="datetimeInput" class="form-label">{{ __('Start Time') }}</label>
            <input type="datetime-local" class="form-control" value="{{$booking->start_date}}" id="datetimeInput" required name="start_time">
        </div>
        <div class="col-12 form-group">
            <label for="datetimeInputend" class="form-label">{{__('End Time')}}</label>
            <input type="datetime-local" class="form-control" value="{{$booking->end_date}}" id="datetimeInputend" required name="end_time">
        </div>

    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn  btn-primary">
</div>

{{Form::close()}}

