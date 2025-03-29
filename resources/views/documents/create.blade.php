{{-- resources/views/documents/create.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Upload New Document</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <div class="upload-area p-5 border rounded text-center" id="dropzone">
                            <i class="bi bi-cloud-upload fs-1 text-primary mb-3"></i>
                            <h5>Drag and drop your file here</h5>
                            <p class="text-muted">or</p>
                            <div class="mb-3 position-relative">
                                <input type="file" name="document_file" id="document_file" class="form-control @error('document_file') is-invalid @enderror"
                                       style="padding-top: 32px; padding-bottom: 32px;">
                                <label for="document_file" class="btn btn-primary position-absolute"
                                       style="top: 50%; left: 50%; transform: translate(-50%, -50%);">
                                    Browse Files
                                </label>
                            </div>
                            <p class="text-muted small">Supported formats: PDF, JPG, PNG, DOC, DOCX (Max 10MB)</p>
                            @error('document_file')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="title" class="form-label">Document Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title') }}" required>
                        @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="document_type" class="form-label">Document Type <span class="text-danger">*</span></label>
                        <select name="document_type" id="document_type" class="form-select @error('document_type') is-invalid @enderror" required>
                            <option value="" disabled selected>Select document type</option>
                            <option value="Medical Record" {{ old('document_type') == 'Medical Record' ? 'selected' : '' }}>Medical Record</option>
                            <option value="Lab Result" {{ old('document_type') == 'Lab Result' ? 'selected' : '' }}>Lab Result</option>
                            <option value="Prescription" {{ old('document_type') == 'Prescription' ? 'selected' : '' }}>Prescription</option>
                            <option value="Imaging" {{ old('document_type') == 'Imaging' ? 'selected' : '' }}>Imaging</option>
                            <option value="Other" {{ old('document_type') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('document_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description (Optional)</label>
                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                  rows="3">{{ old('description') }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Access Permissions</label>
                        <div class="form-check">
                            <input type="checkbox" name="share_with_doctor" id="share_with_doctor" class="form-check-input"
                                {{ old('share_with_doctor') ? 'checked' : '' }}>
                            <label for="share_with_doctor" class="form-check-label">My Doctor</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="share_with_specialists" id="share_with_specialists" class="form-check-input"
                                {{ old('share_with_specialists') ? 'checked' : '' }}>
                            <label for="share_with_specialists" class="form-check-label">Specialists</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="share_with_family" id="share_with_family" class="form-check-input"
                                {{ old('share_with_family') ? 'checked' : '' }}>
                            <label for="share_with_family" class="form-check-label">Family Members</label>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Upload Document</button>
                        <a href="{{ route('documents.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Simple file input display logic
        document.getElementById('document_file').addEventListener('change', function() {
            const fileName = this.files[0]?.name;
            if (fileName) {
                this.nextElementSibling.textContent = fileName;
            } else {
                this.nextElementSibling.textContent = 'Browse Files';
            }
        });

        // Simple drag and drop
        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('document_file');

        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, function(e) {
                e.preventDefault();
                dropzone.classList.add('border-primary');
            });
        });

