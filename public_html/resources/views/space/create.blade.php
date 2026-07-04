{{ Form::open(['url' => 'space']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-6">
            {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}
            {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter Space Name'), 'required' => 'required']) }}
        </div>
        <div class="form-group col-6">
            {{ Form::label('type_id', __('Space_type'), ['class' => 'form-label']) }}
            {{ Form::select('type_id', $spacetype, null, ['class' => 'form-control', 'placeholder' => __('Select Space Type'), 'required' => 'required']) }}
        </div>
        <div class="form-group col-6">
            {{ Form::label('capacity', __('Capacity'), ['class' => 'form-label']) }}
            {{ Form::number('capacity', null, ['class' => 'form-control', 'placeholder' => __('Enter Capacity'), 'required' => 'required', 'id' => 'space_capacity']) }}
        </div>
        <div class="form-group col-6">
            {{ Form::label('pricing_type', __('Pricing Type'), ['class' => 'form-label']) }}
            {{ Form::select('pricing_type', ['hour' => 'Hour', 'day' => 'Day', 'month' => 'Month'], 'day', ['class' => 'form-control', 'required' => 'required', 'id' => 'pricing_type']) }}
        </div>
        <div class="form-group col-6">
            {{ Form::label('base_price', __('Base Price'), ['class' => 'form-label']) }}
            {{ Form::number('base_price', null, ['class' => 'form-control', 'placeholder' => __('Enter Base Price'), 'required' => 'required', 'step' => '0.01', 'id' => 'base_price']) }}
        </div>
        <div class="form-group col-6">
            {{ Form::label('price', __('Total Price (Auto Calculated)'), ['class' => 'form-label']) }}
            {{ Form::number('price', null, ['class' => 'form-control', 'placeholder' => __('Total Price'), 'required' => 'required', 'readonly' => 'readonly', 'id' => 'total_price']) }}
        </div>
        <div class="form-group col-6" id="per_seat_container">
            <div class="form-check form-switch mt-4">
                <input type="hidden" name="per_seat_pricing" value="0">
                <input type="checkbox" class="form-check-input" name="per_seat_pricing" id="per_seat_pricing"
                    value="1">
                <label class="form-check-label" for="per_seat_pricing">{{ __('Enable Per Seat Pricing') }}</label>
            </div>
        </div>
        <div class="form-group col-6">
            {{ Form::label('meeting', __('Meeting'), ['class' => 'form-label']) }}
            {{ Form::select('meeting', ['no' => 'No', 'yes' => 'Yes'], null, ['class' => 'form-control', 'required' => 'required']) }}
        </div>
        <div class="form-group col-6">
            {{ Form::label('window', __('Window'), ['class' => 'form-label']) }}
            {{ Form::select('window', ['no' => 'No', 'yes' => 'Yes'], null, ['class' => 'form-control', 'required' => 'required']) }}
        </div>
        <div class="form-group col-12">
            {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
            {{ Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => __('Enter Description'), 'rows' => 3, 'required' => 'required']) }}
        </div>

        @if (!$customFields->isEmpty())
            @include('custom_fields.formBuilder')
        @endif

    </div>
</div>

<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
</div>

{{ Form::close() }}

<script>
    $(document).ready(function() {
        const capacityInput = $('#space_capacity');
        const pricingTypeSelect = $('#pricing_type');
        const basePriceInput = $('#base_price');
        const totalPriceInput = $('#total_price');
        const perSeatContainer = $('#per_seat_container');
        const perSeatCheckbox = $('#per_seat_pricing');

        function updatePricing() {
            let capacity = parseFloat(capacityInput.val()) || 0;
            let basePrice = parseFloat(basePriceInput.val()) || 0;
            let pricingType = pricingTypeSelect.val();
            let isPerSeat = perSeatCheckbox.is(':checked');

            // Handle visibility
            if (pricingType === 'hour') {
                perSeatContainer.hide();
                perSeatCheckbox.prop('checked', false);
                isPerSeat = false;
            } else {
                perSeatContainer.show();
            }

            // Calculate price
            let finalPrice = basePrice;
            if (isPerSeat && capacity > 0) {
                finalPrice = basePrice * capacity;
            }

            totalPriceInput.val(finalPrice.toFixed(2));
        }

        capacityInput.on('input', updatePricing);
        pricingTypeSelect.on('change', updatePricing);
        basePriceInput.on('input', updatePricing);
        perSeatCheckbox.on('change', updatePricing);

        // Initial setup
        updatePricing();
    });
</script>
