{{ Form::open(['url' => 'contract', 'onsubmit' => 'disableButton()', 'id' => 'contractForm']) }}
<input type="hidden" name="create_type" id="con_type" value="office" required>

<style>
    /* ── Wizard Shell ── */
    .contract-wizard {
        font-family: 'Segoe UI', sans-serif;
        padding: 0 20px 10px 20px;
    }

    /* ── Step Indicator ── */
    .wizard-steps {
        display: flex;
        align-items: center;
        margin-bottom: 2.5rem;
        padding: 0 1rem;
    }

    .wizard-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        flex: 1;
    }

    .wizard-step:not(:last-child)::after {
        content: '';
        position: absolute;
        top: 18px;
        left: calc(50% + 20px);
        right: calc(-50% + 20px);
        height: 2px;
        background: #e2e8f0;
        z-index: 0;
        transition: background 0.3s ease;
    }

    .wizard-step.completed:not(:last-child)::after {
        background: #48494b;
    }

    .step-circle {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #f1f5f9;
        border: 2px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.85rem;
        color: #94a3b8;
        position: relative;
        z-index: 1;
        transition: all 0.3s ease;
    }

    .wizard-step.active .step-circle {
        background: #48494b;
        border-color: #48494b;
        color: #fff;
        box-shadow: 0 0 0 4px rgba(var(--bs-primary-rgb), 0.15);
    }

    .wizard-step.completed .step-circle {
        background: #48494b;
        border-color: #48494b;
        color: #fff;
    }

    .step-label {
        margin-top: 6px;
        font-size: 0.72rem;
        font-weight: 600;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        white-space: nowrap;
    }

    .wizard-step.active .step-label,
    .wizard-step.completed .step-label {
        color: #48494b;
    }

    /* ── Tab Panes ── */
    .wizard-pane {
        display: none;
        animation: fadeSlide 0.25s ease;
    }

    .wizard-pane.active {
        display: block;
    }

    @keyframes fadeSlide {
        from {
            opacity: 0;
            transform: translateY(8px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ── Section Headers ── */
    .pane-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 1.25rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #f1f5f9;
    }

    .pane-header-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: rgba(var(--bs-primary-rgb), 0.1);
        color: #48494b;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
    }

    .pane-header h6 {
        margin: 0;
        font-size: 0.95rem;
        font-weight: 700;
        color: #1e293b;
    }

    .pane-header p {
        margin: 0;
        font-size: 0.75rem;
        color: #94a3b8;
    }

    /* ── Form Fields ── */
    .form-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: #475569;
        margin-bottom: 4px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .form-control {
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        color: #1e293b;
        transition: border-color 0.2s, box-shadow 0.2s;
        background: #fff;
    }

    .form-control:focus {
        border-color: #48494b;
        box-shadow: 0 0 0 3px rgba(var(--bs-primary-rgb), 0.1);
        outline: none;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    /* ── Service/Deposit Rows ── */
    .charge-row {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 14px;
        margin-bottom: 8px;
    }

    .charge-row-label {
        font-size: 0.82rem;
        font-weight: 600;
        color: #475569;
        min-width: 140px;
    }

    .charge-row .form-control {
        background: #fff;
    }

    .charge-unit {
        font-size: 0.78rem;
        color: #94a3b8;
        white-space: nowrap;
    }

    /* ── Meeting Hours ── */
    .meeting-row {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 14px;
        margin-bottom: 8px;
    }

    .meeting-row .row-title {
        font-size: 0.82rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 8px;
    }

    /* ── Toggle Company ── */
    .new-toggle-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .new-toggle-wrap label {
        font-size: 0.78rem;
        font-weight: 600;
        color: #64748b;
        cursor: pointer;
        margin: 0;
    }

    .form-check-input {
        cursor: pointer;
        width: 16px;
        height: 16px;
        accent-color: #4f46e5;
    }

    /* ── Price Preview Badge ── */
    #price-preview {
        display: none;
        background: rgba(var(--bs-primary-rgb), 0.05);
        border: 1.5px solid rgba(var(--bs-primary-rgb), 0.2);
        border-radius: 10px;
        padding: 10px 14px;
        margin-top: 6px;
        font-size: 0.82rem;
        color: #48494b;
        font-weight: 600;
    }

    /* ── Wizard Actions ── */
    .wizard-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 1.5px solid #f1f5f9;
    }

    .btn-wiz-prev {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 18px;
        border-radius: 8px;
        border: 1.5px solid #e2e8f0;
        background: #fff;
        color: #64748b;
        font-size: 0.83rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-wiz-prev:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    .btn-wiz-next {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 22px;
        border-radius: 8px;
        border: none;
        background: #48494b;
        color: #fff;
        font-size: 0.83rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-wiz-next:hover {
        opacity: 0.9;
    }

    .btn-wiz-submit {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 22px;
        border-radius: 8px;
        border: none;
        background: #48494b;
        color: #fff;
        font-size: 0.83rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-wiz-submit:hover {
        opacity: 0.9;
    }

    .btn-wiz-submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .btn-wiz-cancel {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 8px;
        border: 1.5px solid var(--bs-danger);
        background: #fff;
        color: var(--bs-danger);
        font-size: 0.83rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-wiz-cancel:hover {
        background: rgba(var(--bs-danger-rgb), 0.05);
    }

    /* ── Chair Optional Tag ── */
    .optional-tag {
        font-size: 0.72rem;
        background: rgba(var(--bs-success-rgb), 0.1);
        color: var(--bs-success);
        border: 1px solid rgba(var(--bs-success-rgb), 0.2);
        border-radius: 20px;
        padding: 2px 8px;
        font-weight: 600;
        margin-left: 6px;
        vertical-align: middle;
    }
</style>

<div class="contract-wizard">

    <!-- ── Step Indicator ── -->
    <div class="wizard-steps" id="wizardSteps">
        <div class="wizard-step active" data-step="1">
            <div class="step-circle">1</div>
            <div class="step-label">Details</div>
        </div>
        <div class="wizard-step" data-step="2">
            <div class="step-circle">2</div>
            <div class="step-label">Financial</div>
        </div>
        <div class="wizard-step" data-step="3">
            <div class="step-circle">3</div>
            <div class="step-label">Hours & Deposit</div>
        </div>
    </div>

    <!-- ══════════════════════════════════
         PANE 1 — DETAILS
    ══════════════════════════════════ -->
    <div class="wizard-pane active" id="pane-1">
        <div class="pane-header">
            <div class="pane-header-icon">
                <i class="ti ti-clipboard"></i>
            </div>
            <div>
                <h6>Contract Details</h6>
                <p>Basic information about the contract and space</p>
            </div>
        </div>

        <div class="row">

            {{-- Subject --}}
            <div class="form-group col-md-6">
                {{ Form::label('subject', __('Subject'), ['class' => 'form-label']) }}
                <span style="color:red"> *</span>
                {{ Form::text('subject', '', ['class' => 'form-control', 'placeholder' => 'e.g. Office Space Agreement', 'required' => 'required']) }}
            </div>

            {{-- Company --}}
            <div class="form-group col-md-6">
                <div class="d-flex align-items-center justify-content-between">
                    {{ Form::label('company', __('Company'), ['class' => 'form-label mb-0']) }}
                    <span style="color:red" class="me-auto ms-1"> *</span>
                    <div class="new-toggle-wrap">
                        {{ Form::checkbox('new', '1', false, ['class' => 'form-check-input', 'id' => 'addpropcheck']) }}
                        {{ Form::label('addpropcheck', __('+ New Company'), ['class' => 'form-check-label']) }}
                    </div>
                </div>
                {{ Form::text('newcompany', '', ['class' => 'form-control d-none companyText req mt-1', 'placeholder' => 'Enter company name']) }}
                {{ Form::select('company', $company, null, ['class' => 'form-control mt-1', 'placeholder' => __('Select Company'), 'id' => 'companySelect']) }}
            </div>

            {{-- New Company Fields --}}
            <div class="form-group col-md-6 d-none companyText">
                {{ Form::label('phone_no', __('Phone No'), ['class' => 'form-label']) }}<span style="color:red">
                    *</span>
                {{ Form::number('phone_no', '', ['class' => 'form-control req', 'placeholder' => '+92 300 0000000']) }}
            </div>
            <div class="form-group col-md-6 d-none companyText">
                {{ Form::label('ntn', __('NTN'), ['class' => 'form-label']) }}
                {{ Form::text('ntn', '', ['class' => 'form-control', 'placeholder' => 'Tax Number (optional)']) }}
            </div>
            <div class="form-group col-md-12 d-none companyText">
                {{ Form::label('email', __('Email'), ['class' => 'form-label']) }}<span style="color:red"> *</span>
                {{ Form::email('email', '', ['class' => 'form-control req', 'placeholder' => 'company@email.com']) }}
            </div>

            {{-- Space --}}
            <div class="form-group col-md-6">
                {{ Form::label('space', __('Space'), ['class' => 'form-label']) }}<span style="color:red"> *</span>
                <select name="space" class="form-control space_select" id="space" required
                    onchange="getchairs(this.value)">
                    <option value="" disabled selected>Select a Space</option>
                    @foreach ($spaces as $space)
                        <option value="{{ $space->id }}">{{ $space->name }} ({{ @$space->type->name }})</option>
                    @endforeach
                </select>
            </div>

            {{-- Chair --}}
            <div class="form-group col-md-6" id="ch">
                <label class="form-label">
                    {{ __('Chair') }}
                    <span class="optional-tag">Optional</span>
                </label>
                <select name="chair[]" class="form-control chair_select" id="chair" multiple="multiple">
                    <option value="" disabled>Select Chairs</option>
                </select>
            </div>

            {{-- Price Preview --}}
            <div class="col-12">
                <div id="price-preview"></div>
            </div>

            {{-- Description --}}
            <div class="form-group col-md-12 mt-1">
                {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
                {!! Form::textarea('description', null, [
                    'class' => 'form-control',
                    'rows' => '3',
                    'placeholder' => 'Optional notes about this contract...',
                ]) !!}
            </div>
        </div>

        <div class="wizard-actions">
            <div></div>
            <button type="button" class="btn-wiz-next" onclick="goToStep(2)">
                Financial Info <i class="ti ti-arrow-right"></i>
            </button>
        </div>
    </div>

    <!-- ══════════════════════════════════
         PANE 2 — FINANCIAL
    ══════════════════════════════════ -->
    <div class="wizard-pane" id="pane-2">
        <div class="pane-header">
            <div class="pane-header-icon">
                <i class="ti ti-currency-dollar"></i>
            </div>
            <div>
                <h6>Financial Details</h6>
                <p>Contract type, value and service charges</p>
            </div>
        </div>

        <div class="row">

            {{-- Contract Type --}}
            <div class="form-group col-md-12">
                {{ Form::label('type', __('Contract Type'), ['class' => 'form-label']) }}<span style="color:red">
                    *</span>
                {{ Form::select('type', $contractTypes, null, ['class' => 'form-control', 'required' => 'required']) }}
            </div>

            {{-- Start & End Dates --}}
            <div class="form-group col-md-6">
                {{ Form::label('start_date', __('Start Date'), ['class' => 'form-label']) }}<span style="color:red">
                    *</span>
                {{ Form::date('start_date', '', ['class' => 'form-control', 'required' => 'required']) }}
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('end_date', __('End Date'), ['class' => 'form-label']) }}<span style="color:red">
                    *</span>
                {{ Form::date('end_date', '', ['class' => 'form-control', 'required' => 'required']) }}
            </div>

            {{-- Contract Value --}}
            <div class="form-group col-md-12">
                {{ Form::label('value', __('Contract Value'), ['class' => 'form-label']) }}<span style="color:red">
                    *</span>
                {{ Form::number('value', '', ['class' => 'form-control', 'id' => 'value', 'required' => 'required', 'step' => '0.01', 'min' => '0', 'placeholder' => '0.00']) }}
            </div>

        </div>

        <div class="wizard-actions">
            <button type="button" class="btn-wiz-prev" onclick="goToStep(1)">
                <i class="ti ti-arrow-left"></i> Details
            </button>
            <button type="button" class="btn-wiz-next" onclick="goToStep(3)">
                Hours & Deposit <i class="ti ti-arrow-right"></i>
            </button>
        </div>
    </div>

    <!-- ══════════════════════════════════
         PANE 3 — HOURS & DEPOSIT
    ══════════════════════════════════ -->
    <div class="wizard-pane" id="pane-3">
        <div class="pane-header">
            <div class="pane-header-icon">
                <i class="ti ti-clock"></i>
            </div>
            <div>
                <h6>Hours & Deposit</h6>
                <p>Security deposit and meeting room allocations</p>
            </div>
        </div>

        <div class="row">

            {{-- Security Deposit --}}
            <div class="form-group col-md-12">
                {{ Form::label('security', 'Security Deposit', ['class' => 'form-label']) }}<span style="color:red">
                    *</span>
                <div class="charge-row">
                    <input type="hidden" name="security_deposit_id" value="{{ @$security->id }}">
                    <span class="charge-row-label">{{ ucfirst(@$security->name) }}</span>
                    <input type="number" name="security_deposit_price" id="{{ @$security->id }}"
                        class="form-control" style="max-width:180px" min="0" required placeholder="0.00">
                    <span class="charge-unit">PKR (one-time)</span>
                </div>
            </div>

            {{-- Meeting Room Hours --}}
            <div class="form-group col-md-12">
                {{ Form::label('meeting_hours', 'Meeting Room & Board Room Hours', ['class' => 'form-label']) }}<span
                    style="color:red"> *</span>

                @foreach ($ismeeting as $meeting)
                    <div class="meeting-row">
                        <div class="row-title">{{ ucfirst($meeting->name) }}</div>
                        <input type="hidden" name="room_hours_ids[]" value="{{ $meeting->id }}">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label" style="text-transform:none;font-size:0.75rem">Included
                                    Hours</label>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="number" name="room_hours[]" id="{{ $meeting->id }}"
                                        class="form-control" min="0" required placeholder="0">
                                    <span class="charge-unit">Hrs</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="text-transform:none;font-size:0.75rem">Hourly Rate
                                    (overtime)
                                </label>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="number" name="hourly_rate[]" id="hour{{ $meeting->id }}"
                                        class="form-control" min="0" required placeholder="0.00">
                                    <span class="charge-unit">PKR/hr</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>

        <div class="wizard-actions">
            <button type="button" class="btn-wiz-prev" onclick="goToStep(2)">
                <i class="ti ti-arrow-left"></i> Financial
            </button>
            <div class="d-flex gap-2">
                <button type="button" class="btn-wiz-cancel" data-bs-dismiss="modal">
                    <i class="ti ti-x"></i> Cancel
                </button>
                <button type="submit" id="myButton" class="btn-wiz-submit">
                    <i class="ti ti-check"></i> Create Contract
                </button>
            </div>
        </div>
    </div>

</div><!-- /.contract-wizard -->

{{ Form::close() }}

<script src="{{ asset('assets/js/plugins/choices.min.js') }}"></script>
<script>
    // ── Step Navigation ──────────────────────────────
    var currentStep = 1;
    var totalSteps = 3;

    function goToStep(step) {
        // Validate before advancing
        if (step > currentStep && !validateStep(currentStep)) return;

        // Hide all panes
        document.querySelectorAll('.wizard-pane').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.wizard-step').forEach(s => {
            s.classList.remove('active');
            var n = parseInt(s.getAttribute('data-step'));
            if (n < step) s.classList.add('completed');
            else s.classList.remove('completed');
        });

        // Show target pane
        document.getElementById('pane-' + step).classList.add('active');
        document.querySelector('.wizard-step[data-step="' + step + '"]').classList.add('active');

        currentStep = step;
    }

    function validateStep(step) {
        var pane = document.getElementById('pane-' + step);
        var inputs = pane.querySelectorAll('[required]');
        var valid = true;

        inputs.forEach(function(input) {
            if (!input.value.trim()) {
                input.style.borderColor = '#ef4444';
                input.style.boxShadow = '0 0 0 3px rgba(239,68,68,0.1)';
                valid = false;
                setTimeout(function() {
                    input.style.borderColor = '';
                    input.style.boxShadow = '';
                }, 2500);
            }
        });

        if (!valid && typeof show_toastr === 'function') {
            show_toastr('error', 'Please fill all required fields before proceeding.', 'error');
        }
        return valid;
    }

    // ── Submit Guard ──────────────────────────────────
    document.getElementById('contractForm').addEventListener('submit', function(e) {
        var btn = document.getElementById('myButton');
        btn.disabled = true;
        setTimeout(function() {
            btn.disabled = false;
        }, 5000);
    });

    // ── Company Toggle ────────────────────────────────
    document.getElementById('addpropcheck').addEventListener('change', function() {
        if (this.checked) {
            document.getElementById('companySelect').style.display = 'none';
            document.getElementById('companySelect').required = false;
            document.querySelectorAll('.companyText').forEach(el => {
                el.classList.remove('d-none');
            });
            document.querySelectorAll('.req').forEach(el => el.required = true);
        } else {
            document.getElementById('companySelect').style.display = 'block';
            document.getElementById('companySelect').required = true;
            document.querySelectorAll('.companyText').forEach(el => el.classList.add('d-none'));
            document.querySelectorAll('.req').forEach(el => el.required = false);
        }
    });

    // ── Pricing Logic ─────────────────────────────────
    var currentSpacePricing = {
        base_price: 0,
        per_seat_pricing: 0,
        charge_type: ''
    };

    function calculateContractValue() {
        var valInput = document.getElementById('value');
        var preview = document.getElementById('price-preview');
        if (!valInput) return;

        var basePrice = parseFloat(currentSpacePricing.base_price) || 0;
        var perSeat = currentSpacePricing.per_seat_pricing == 1;
        var chargeType = currentSpacePricing.charge_type || '';

        if (perSeat) {
            var chairSel = document.getElementById('chair');
            var numChairs = 0;
            if (chairSel) {
                for (var i = 0; i < chairSel.options.length; i++) {
                    if (chairSel.options[i].selected) numChairs++;
                }
            }
            var total = numChairs > 0 ? (basePrice * numChairs) : basePrice;
            valInput.value = total.toFixed(2);

            // Preview
            if (numChairs > 0) {
                preview.style.display = 'block';
                preview.innerHTML = '💺 ' + numChairs + ' chairs × ' + basePrice.toLocaleString() + ' (' + chargeType +
                    ') = <strong>' + total.toLocaleString() + '</strong>';
            } else {
                preview.style.display = 'block';
                preview.innerHTML = '💡 Per-seat pricing active — select chairs to calculate total';
            }
        } else {
            valInput.value = basePrice.toFixed(2);
            if (basePrice > 0) {
                preview.style.display = 'block';
                preview.innerHTML = '📋 Flat rate: <strong>' + basePrice.toLocaleString() + '</strong> / ' + (
                    chargeType || 'period');
            } else {
                preview.style.display = 'none';
            }
        }
    }

    // ── Load Chairs via AJAX ──────────────────────────
    function getchairs(ids) {
        $.ajax({
            url: `{{ url('space_chair') }}/${ids}`,
            type: 'GET',
            success: function(data) {
                if (data.success == 'true') {
                    document.getElementById('con_type').value = data.type;

                    if (data.pricing) {
                        currentSpacePricing.base_price = data.pricing.base_price;
                        currentSpacePricing.per_seat_pricing = data.pricing.per_seat_pricing;
                        currentSpacePricing.charge_type = data.pricing.charge_type || '';
                    } else {
                        currentSpacePricing = {
                            base_price: 0,
                            per_seat_pricing: 0,
                            charge_type: ''
                        };
                    }

                    var chDiv = document.getElementById('ch');

                    if (data.type == 'virtual') {
                        chDiv.classList.add('d-none');
                        calculateContractValue();
                    } else {
                        chDiv.classList.remove('d-none');

                        var html = `<label class="form-label">Chair <span class="optional-tag">Optional</span></label>
                        <select name="chair[]" class="form-control chair_select" id="chair" multiple="multiple">
                        <option value="" disabled>Select Chairs</option>`;

                        for (var i = 0; i < data.data.length; i++) {
                            var disabled = data.assignchair.indexOf(data.data[i]['id']) !== -1 ?
                                'disabled' : '';
                            html +=
                                `<option value="${data.data[i]['id']}" ${disabled}>${data.data[i]['name']}</option>`;
                        }
                        html += `</select>`;
                        chDiv.innerHTML = html;

                        // Init Choices.js
                        if (typeof Choices !== 'undefined') {
                            document.querySelectorAll('.chair_select').forEach(function(el) {
                                new Choices('#' + el.id, {
                                    removeItemButton: true
                                });
                                el.addEventListener('change', calculateContractValue);
                            });
                        }

                        calculateContractValue();
                    }
                }
            }
        });
    }

    // ── Init Choices on existing selects ─────────────
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Choices !== 'undefined') {
            document.querySelectorAll('.multi-select').forEach(function(el) {
                new Choices('#' + el.id, {
                    removeItemButton: true
                });
            });
        }
    });
</script>
