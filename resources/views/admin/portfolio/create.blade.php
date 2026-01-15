@extends('admin.layouts.admin')

@section('content')
<div class="col-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Add New</h4>
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
         @endif
        <form class="forms-sample" method="POST" action="{{ route('admin.portfolio.store') }}" enctype="multipart/form-data">
          @csrf
            <div class="form-group">
          <p class="card-description"> Portfolio Details</p>
          <div class="row">
            <div class="col-md-5">
              <div class="form-group row">
                <label class="col-sm-3 col-form-label">Title</label>
                <div class="col-sm-9">
                  <input type="text" name="title" class="form-control" placeholder="Enter Project title" value="{{old('title')}}" required/>
                </div>
              </div>
            </div>
            <div class="col-md-7">
              <div class="form-group row">
                <label class="col-sm-3 col-form-label">Url</label>
                <div class="col-sm-9">
                  <input type="text" name="project_url" class="form-control" placeholder="enter project url" value="{{old('project_url')}}" required/>
                </div>
              </div>
            </div>
            <div class="col-12">
              <div class="form-group">
                <label>Upload Images (Multiple)</label>
                <input type="file" name="images[]" class="form-control" multiple accept="image/*" required>
                <small class="text-muted">You can select multiple images. First image will be the primary/thumbnail.</small>
              </div>
              <div id="image-preview" class="row mt-3"></div>
            </div>
            <div class="row">
              <div class="col-md-5">
                <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Category</label>
                  <div class="col-sm-9">
                    <select class="form-control text-black" id="selectCat" name="cat_id">
                      @foreach ($categories as $category)
                      <option value="{{ $category->id }}" {{ old('cat_id') =="$category->name" ? 'selected' : '' }} >{{ $category->name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
          <button type="submit" class="btn btn-gradient-primary me-2">Submit</button>
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

    Array.from(e.target.files).forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(event) {
            const col = document.createElement('div');
            col.className = 'col-md-3 mb-3';
            col.innerHTML = `
                <div class="position-relative">
                    <img src="${event.target.result}" class="img-fluid rounded" style="height: 150px; width: 100%; object-fit: cover;">
                    ${index === 0 ? '<span class="badge bg-primary position-absolute top-0 start-0 m-1">Primary</span>' : ''}
                </div>
            `;
            preview.appendChild(col);
        };
        reader.readAsDataURL(file);
    });
});
</script>
@endpush
