

<div class="container-fluid">

   
    <!-- end page title -->
   <div class="row">
        <div class="col-12">
             <form id="facetAttributeForm">
                @csrf

                <div class="mb-3">
                    <label for="category_id" class="form-label">Select Category</label>
                    <select name="category_id" id="category_id" class="form-select" required>
                        <option value="">Choose Category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="attributeRepeater">
                    <label class="form-label">Attribute Names</label>
                    <div class="repeater-item mb-2 d-flex">
                        <input type="text" name="attributes[]" class="form-control me-2" placeholder="Attribute Name" required>
                        <button type="button" class="btn btn-danger remove-btn">Remove</button>
                    </div>
                </div>

                <button type="button" id="addAttributeBtn" class="btn btn-secondary mb-3">Add Attribute</button>

                <div>
                    <button type="submit" class="btn btn-primary">Save Attributes</button>
                </div>
            </form>
        </div>
</div>


</div>

