@php
    use Illuminate\Support\Str;
@endphp

@if(isset($timeline) && $timeline->count())
    <ul class="list-unstyled mb-0">
        @foreach($timeline as $item)
            @php
                $kind  = $item['kind'];
                $model = $item['model'];
                $date  = $item['date'];

                $isNote     = $kind === 'note';
                $isActivity = $kind === 'activity';

                // Choose icon + badge by type
                $icon = 'clock';
                $badgeClass = 'secondary';
                $label = '';

                if ($isNote) {
                    $icon = 'file-text';
                    $badgeClass = $model->is_pinned ? 'warning text-dark' : 'secondary';
                    $label = 'Note';
                } elseif ($isActivity) {
                    $label = ucfirst($model->type);

                    switch ($model->type) {
                        case 'call':
                            $icon = 'phone';
                            $badgeClass = 'info';
                            break;
                        case 'meeting':
                            $icon = 'calendar';
                            $badgeClass = 'primary';
                            break;
                        case 'email':
                            $icon = 'mail';
                            $badgeClass = 'success';
                            break;
                        default: // task
                            $icon = 'check-square';
                            $badgeClass = 'secondary';
                            break;
                    }
                }
            @endphp

            <li class="d-flex mb-3">
                {{-- Icon circle --}}
                <div class="me-3">
                    <div class="rounded-circle border d-flex align-items-center justify-content-center"
                         style="width: 32px; height: 32px;">
                        <i data-feather="{{ $icon }}" class="feather-sm"></i>
                    </div>
                </div>

                {{-- Content --}}
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div>
                            <span class="badge bg-{{ $badgeClass }} me-1">
                                {{ $label }}
                            </span>

                            @if($isActivity)
                                <strong>{{ $model->title }}</strong>
                            @elseif($isNote)
                                <strong>{{ Str::limit(strip_tags($model->body), 40) }}</strong>
                            @endif
                        </div>
                        <div class="text-muted small">
                            {{ $date ? $date->format('Y-m-d H:i') : '' }}
                        </div>
                    </div>

                    {{-- Body/notes --}}
                    <div class="small text-muted">
                        @if($isActivity)
                            @if($model->notes)
                                {{ Str::limit($model->notes, 120) }}
                            @else
                                @if($model->type === 'task')
                                    Task scheduled
                                @elseif($model->type === 'call')
                                    Call scheduled
                                @elseif($model->type === 'meeting')
                                    Meeting scheduled
                                @elseif($model->type === 'email')
                                    Email activity
                                @endif
                            @endif
                        @elseif($isNote)
                            {!! nl2br(e($model->body)) !!}
                        @endif
                    </div>

                    {{-- Author line (optional) --}}
                    <div class="small text-muted mt-1">
                        @if($isNote && $model->user)
                            by {{ $model->user->name }}
                        @elseif($isActivity && $model->owner)
                            assigned to {{ $model->owner->name }}
                        @endif
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
@else
    <p class="text-muted mb-0">No timeline events yet.</p>
@endif
