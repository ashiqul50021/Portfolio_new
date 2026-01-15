@extends('admin.layouts.admin')

@section('content')
<div class="col-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Edit Portfolio</h4>
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
         @endif
        <form class="forms-sample" method="POST" action="{{ route('admin.portfolio.update', $portfolio->id) }}" enctype="multipart/form-data">
          @csrf
          @method('PUT')
            <div class="form-group">
          <p class="card-description"> Portfolio Details</p>
          <div class="row">
            <div class="col-md-5">
              <div class="form-group row">
                <label class="col-sm-3 col-form-label">Title</label>
                <div class="col-sm-9">
                  <input type="text" name="title" class="form-control" placeholder="Enter Project title" value="{{$portfolio->title}}" required/>
                </div>
              </div>
            </div>
            <div class="col-md-7">
              <div class="form-group row">
                <label class="col-sm-3 col-form-label">Url</label>
                <div class="col-sm-9">
                  <input type="text" name="project_url" class="form-control" placeholder="enter project url" value="{{$portfolio->project_url}}" required/>
                </div>
              </div>
            </div>

            <!-- Existing Images -->
            <div class="col-12 mb-4">
              <label class="mb-2">Current Images</label>
              <div class="row">
                @forelse($portfolio->images as $image)
                <div class="col-md-3 mb-3">
                  <div class="position-relative border rounded p-2">
                    <img src="{{ asset('storage/' . $image->image) }}" class="img-fluid rounded" style="height: 150px; width: 100%; object-fit: cover;">
                    @if($image->is_primary)
                    <span class="badge bg-primary position-absolute top-0 start-0 m-2">Primary</span>
                    @endif
                    <div class="mt-2 d-flex justify-content-between align-items-center">
                      <div class="form-check">
                        <input class="form-check-input" type="radio" name="primary_image" value="{{ $image->id }}" {{ $image->is_primary ? 'checked' : '' }}>
                        <label class="form-check-label small">Primary</label>
                      </div>
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="delete_images[]" value="{{ $image->id }}">
                        <label class="form-check-label small text-danger">Delete</label>
                      </div>
                    </div>
                  </div>
                </div>
                @empty
                @if($portfolio->image)
                <div class="col-md-3 mb-3">
                  <div class="position-relative border rounded p-2">
                    <img src="{{ asset('storage/' . $portfolio->image) }}" class="img-fluid rounded" style="height: 150px; width: 100%; object-fit: cover;">
                    <span class="badge bg-secondary position-absolute top-0 start-0 m-2">Legacy</span>
                  </div>
                </div>
                @else
                <p class="text-muted">No images uploaded yet.</p>
                @endif
                @endforelse
              </div>
            </div>

            <!-- Add New Images -->
            <div class="col-12">
              <div class="form-group">
                <label>Add More Images</label>
                <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                <small class="text-muted">Select multiple images to add to this portfolio.</small>
              </div>
              <div id="image-preview" class="row mt-3"></div>
            </div>

            <div class="col-md-5">
              <div class="form-group row">
                <label class="col-sm-4 col-form-label">Category</label>
                <div class="col-sm-9">
                  <select class="form-control text-black" id="selectCat" name="cat_id">
                    @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ $portfolio->cat_id == $category->id ? 'selected' : '' }} >{{ $category->name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>
          </div>
          <button type="submit" class="btn btn-gradient-primary me-2">Update</button>
        </form>
      </div>
    </div>
  </div>

@endsection

@push('scripts')
<script>
document.querySelector('input[name="images[]"]').addEventListener('change', function(e) {
    const preview = document.getElementById('image-preview');
    preview.innerHTML = '';

    if(e.target.files.length > 0) {
        const label = document.createElement('div');
        label.className = 'col-12 mb-2';
        label.innerHTML = '<small class="text-muted">New images to add:</small>';
        preview.appendChild(label);
    }

    Array.from(e.target.files).forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(event) {
            const col = document.createElement('div');
            col.className = 'col-md-3 mb-3';
            col.innerHTML = `
                <div class="position-relative border rounded p-2">
                    <img src="${event.target.result}" class="img-fluid rounded" style="height: 150px; width: 100%; object-fit: cover;">
                    <span class="badge bg-success position-absolute top-0 start-0 m-2">New</span>
                </div>
            `;
            preview.appendChild(col);
        };
        reader.readAsDataURL(file);
    });
});
</script>
@endpush
