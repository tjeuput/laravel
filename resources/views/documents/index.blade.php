{{-- resources/views/documents/index.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">My Documents</h4>
                <a href="{{ route('documents.create') }}" class="btn btn-primary">Upload New Document</a>
            </div>
            <div class="card-body">
                <!-- Tabs for document types -->
                <ul class="nav nav-tabs mb-4">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->type == null ? 'active' : '' }}" href="{{ route('documents.index') }}">
                            All Documents
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->type == 'Medical Record' ? 'active' : '' }}"
                           href="{{ route('documents.index', ['type' => 'Medical Record']) }}">
                            Medical Records
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->type == 'Lab Result' ? 'active' : '' }}"
                           href="{{ route('documents.index', ['type' => 'Lab Result']) }}">
                            Lab Results
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->type == 'Prescription' ? 'active' : '' }}"
                           href="{{ route('documents.index', ['type' => 'Prescription']) }}">
                            Prescriptions
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->type == 'Imaging' ? 'active' : '' }}"
                           href="{{ route('documents.index', ['type' => 'Imaging']) }}">
                            Imaging
                        </a>
                    </li>
                </ul>

                <!-- Search and filters -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <form action="{{ route('documents.index') }}" method="GET" class="mb-3">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control"
                                       placeholder="Search documents..." value="{{ request()->search }}">
                                <button type="submit" class="btn btn-outline-secondary">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-end">
                            <select class="form-select w-auto" onchange="window.location = this.value;">
                                <option value="{{ route('documents.index', ['sort' => 'newest']) }}"
                                    {{ request()->sort == 'newest' ? 'selected' : '' }}>
                                    Sort by: Newest
                                </option>
                                <option value="{{ route('documents.index', ['sort' => 'oldest']) }}"
                                    {{ request()->sort == 'oldest' ? 'selected' : '' }}>
                                    Sort by: Oldest
                                </option>
                                <option value="{{ route('documents.index', ['sort' => 'name-asc']) }}"
                                    {{ request()->sort == 'name-asc' ? 'selected' : '' }}>
                                    Sort by: Name A-Z
                                </option>
                                <option value="{{ route('documents.index', ['sort' => 'name-desc']) }}"
                                    {{ request()->sort == 'name-desc' ? 'selected' : '' }}>
                                    Sort by: Name Z-A
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Flash message -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Documents grid -->
                <div class="row">
                    @forelse ($documents as $document)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 document-card">
                                <div class="card-body d-flex">
                                    <!-- Document icon based on file type -->
                                    <div class="document-icon me-3">
                                        @if (in_array($document->file_type, ['pdf']))
                                            <i class="bi bi-file-earmark-pdf fs-1 text-danger"></i>
                                        @elseif (in_array($document->file_type, ['jpg', 'jpeg', 'png']))
                                            <i class="bi bi-file-earmark-image fs-1 text-primary"></i>
                                        @elseif (in_array($document->file_type, ['doc', 'docx']))
                                            <i class="bi bi-file-earmark-word fs-1 text-primary"></i>
                                        @else
                                            <i class="bi bi-file-earmark fs-1 text-secondary"></i>
                                        @endif
                                    </div>

                                    <div class="document-details">
                                        <h5 class="card-title">{{ $document->title }}</h5>
                                        <p class="card-text text-muted fs-6">
                                            {{ $document->document_type }} •
                                            {{ strtoupper($document->file_type) }} •
                                            {{ number_format($document->file_size / 1024, 0) }} KB
                                        </p>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                Uploaded {{ $document->created_at->diffForHumans() }}
                                            </small>
                                        </p>

                                        <div class="d-flex mt-2">
                                            <a href="{{ route('documents.show', $document) }}" class="btn btn-sm btn-primary me-2">
                                                View
                                            </a>
                                            <a href="{{ route('documents.download', $document) }}" class="btn btn-sm btn-success me-2">
                                                Download
                                            </a>
                                            <a href="{{ route('documents.edit', $document) }}" class="btn btn-sm btn-secondary me-2">
                                                Edit
                                            </a>
                                            <form action="{{ route('documents.destroy', $document) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Are you sure you want to delete this document?')">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info">
                                No documents found. <a href="{{ route('documents.create') }}">Upload your first document</a>.
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $documents->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .document-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .document-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .document-icon {
            width: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .document-details {
            flex: 1;
        }
    </style>
@endsection
