<div class="modal fade" id="ajaxModal" tabindex="-1" aria-labelledby="ajaxModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ajaxModalLabel">Edit Product Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Form Starts Here -->
                {{ Form::open(['url' => '', 'method' => 'PUT', 'id' => 'modalForm']) }}
                @csrf
                <div class="mb-3">
                    {{ Form::label('name', 'Product Name', ['class' => 'form-label']) }}
                    {{ Form::text('name', null, ['class' => 'form-control', 'id' => 'textInput__01', 'required']) }}
                </div>

                <div class="mb-3">
                    {{ Form::label('tax_id', 'Select Tax', ['class' => 'form-label']) }}
                    {{ Form::select(
                        'tax_id',
                        [
                            'gst' => 'GST',
                            'service_gst' => 'Service GST',
                            'rate_20' => '20%',
                            'rate_10' => '10',
                            'test_1' => 'Test 1',
                            'test_sector' => 'Test Sector',
                        ],
                        null,  // Set default selected value here if needed
                        ['class' => 'form-select', 'id' => 'selectInput__01']
                    ) }}
                    
                </div>

                <button type="submit" class="btn btn-primary">Save changes</button>
                {{ Form::close() }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>




{{-- <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="ajaxModalLabel">Edit Product Service</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Form Starts Here -->
          <form id="modalForm" method="POST" action="{{url('product-services/update/2')}}">
            @csrf
            @method('PUT')
  
            <!-- Text Input Field Pre-filled with Existing Data -->
            <div class="mb-3">
              <label for="textInput__01" class="form-label">Product Name</label>
              <input type="text" class="form-control" id="textInput__01" name="name" placeholder="Enter product name">
            </div>
  
            <!-- Select Input Pre-filled with Existing Tax ID -->
            <div class="mb-3">
              <label for="selectInput__01" class="form-label">Select Tax</label>
              <select class="form-select" id="selectInput__01" name="tax_id">
                <option value="1">Option 1</option>
                <option value="2">Option 2</option>
                <option value="3">Option 3</option>
              </select>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" form="modalForm">Save changes</button>
        </div>
      </div>
    </div> --}}
{{-- </div> --}}
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        // When edit button is clicked
        $('.editProductServiceBtn').on('click', function(e) {
            e.preventDefault(); // Prevent default action

            var productId = $(this).data('id'); // Get product ID from the button

            // Make an AJAX request to get product data
            $.ajax({
                url: '/product-services/get/' + productId, // Adjust your route here
                method: 'GET',
                success: function(response) {
                    var productService = response[0]; // First item is ProductService
                    var tax = response[1]; // Second item is Tax

                    // Populate the modal fields with the data
                    $('#textInput__01').val(productService.name); // Set the product name
                    $('#selectInput__01').val(productService
                    .tax_id); // Set the tax_id in select dropdown

                    // Update the form action dynamically
                    $('#modalForm').attr('action', '/product-services/update/' +
                        productService.id);

                    // Show the modal using Bootstrap's modal function
                    $('#ajaxModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.log('AJAX Error:', error); // Handle any errors
                }
            });
        });
    });
</script>
