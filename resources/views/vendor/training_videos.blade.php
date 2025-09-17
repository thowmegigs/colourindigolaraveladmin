@extends('layouts.vendor.app')
@section('content')
<div class="container-xxl flex-grow-1 py-3">

    <!-- Training Videos Card -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
            <h4 class="mb-0 fw-bold">Training Videos</h4>
        </div>

        <div class="card-body">
            <div class="row g-4">
                @forelse ($videos as $video)
                    @php
                        $embed_id = '';
                        $parsed_url = parse_url($video->name);

                        // Extract YouTube ID
                        if (isset($parsed_url['query'])) {
                            parse_str($parsed_url['query'], $query_params);
                            if (isset($query_params['v'])) {
                                $embed_id = $query_params['v'];
                            }
                        }

                        if (isset($parsed_url['path'])) {
                            $path = trim($parsed_url['path'], '/');
                            if (preg_match('#^(?:shorts/|embed/|v/)?([a-zA-Z0-9_-]{11})#', $path, $matches)) {
                                $embed_id = $matches[1];
                            }
                        }

                        if (isset($parsed_url['host']) && $parsed_url['host'] === 'youtu.be') {
                            $embed_id = ltrim($parsed_url['path'], '/');
                        }
                    @endphp

                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden">
                            <div class="ratio ratio-16x9">
                                <iframe
                                    src="https://www.youtube.com/embed/{{ $embed_id }}"
                                    title="YouTube video"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    allowfullscreen>
                                </iframe>
                            </div>
                            <div class="card-body p-3">
                                <h6 class="fw-semibold text-truncate mb-2" title="{{ $video->title ?? 'Training Video' }}">
                                    {{ $video->title ?? 'Training Video' }}
                                </h6>
                                <a href="{{ $video->name }}" target="_blank" class="btn btn-sm btn-outline-primary w-100">
                                    Watch on YouTube
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            No training videos available at the moment.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
