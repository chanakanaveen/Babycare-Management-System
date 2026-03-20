{{-- AI Growth Prediction Card --}}
@if(isset($prediction) && !empty($prediction))
@php
    $status = $prediction['growth_status'] ?? ($prediction['growth_trend'] ?? 'Normal');
    $isEnhanced = isset($prediction['overall_summary']);

    // Color coding
    $statusColors = [
        'Normal' => ['bg' => '#f0fdf4', 'border' => '#16a34a', 'badge' => 'badge-success'],
        'Above Average' => ['bg' => '#eff6ff', 'border' => '#2563eb', 'badge' => 'badge-primary'],
        'Below Average' => ['bg' => '#fef3c7', 'border' => '#d97706', 'badge' => 'badge-warning'],
        'At Risk' => ['bg' => '#fef2f2', 'border' => '#dc2626', 'badge' => 'badge-danger'],
        'Critical' => ['bg' => '#fef2f2', 'border' => '#b91c1c', 'badge' => 'badge-danger'],
    ];
    $colors = $statusColors[$status] ?? $statusColors['Normal'];

    $urgencyColors = [
        'Routine' => 'badge-success',
        'Soon' => 'badge-warning',
        'Urgent' => 'badge-danger',
    ];
@endphp

<div class="card-box" style="background: {{ $colors['bg'] }}; border-left: 4px solid {{ $colors['border'] }}; border-radius: 8px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 style="margin: 0; font-size: 16px;">
            <i class="fa fa-brain" style="color: {{ $colors['border'] }};"></i>
            AI Growth Analysis
        </h5>
        <div>
            <span class="badge {{ $colors['badge'] }}">{{ $status }}</span>
            @if($isEnhanced && isset($prediction['follow_up_urgency']))
                <span class="badge {{ $urgencyColors[$prediction['follow_up_urgency']] ?? 'badge-secondary' }} ml-1">
                    Follow-up: {{ $prediction['follow_up_urgency'] }}
                </span>
            @endif
        </div>
    </div>

    {{-- Summary --}}
    @if($isEnhanced && isset($prediction['overall_summary']))
    <p style="font-size: 14px; color: #374151; line-height: 1.6; margin-bottom: 16px;">
        {{ $prediction['overall_summary'] }}
    </p>
    @else
    <p style="font-size: 14px; color: #374151; line-height: 1.6; margin-bottom: 16px;">
        <strong>Trend:</strong> {{ $prediction['growth_trend'] ?? 'N/A' }}<br>
        <strong>Percentile:</strong> {{ $prediction['percentile_estimate'] ?? 'N/A' }}<br>
        <strong>BMI:</strong> {{ $prediction['bmi_category'] ?? 'N/A' }}
    </p>
    @endif

    @if($isEnhanced)
    {{-- Weight & Height Analysis --}}
    <div class="row mb-3">
        @if(isset($prediction['weight_analysis']))
        <div class="col-md-6 mb-2">
            <div style="background: rgba(255,255,255,0.7); border-radius: 8px; padding: 12px;">
                <strong style="font-size: 13px; color: #6b7280;">Weight</strong>
                <div style="font-size: 14px; font-weight: 600; margin: 4px 0;">
                    {{ $prediction['weight_analysis']['status'] ?? 'N/A' }}
                    <small style="font-weight: normal; color: #9ca3af;">({{ $prediction['weight_analysis']['percentile'] ?? '' }})</small>
                </div>
                <p style="font-size: 12px; color: #6b7280; margin: 0;">{{ $prediction['weight_analysis']['detail'] ?? '' }}</p>
            </div>
        </div>
        @endif
        @if(isset($prediction['height_analysis']))
        <div class="col-md-6 mb-2">
            <div style="background: rgba(255,255,255,0.7); border-radius: 8px; padding: 12px;">
                <strong style="font-size: 13px; color: #6b7280;">Height</strong>
                <div style="font-size: 14px; font-weight: 600; margin: 4px 0;">
                    {{ $prediction['height_analysis']['status'] ?? 'N/A' }}
                    <small style="font-weight: normal; color: #9ca3af;">({{ $prediction['height_analysis']['percentile'] ?? '' }})</small>
                </div>
                <p style="font-size: 12px; color: #6b7280; margin: 0;">{{ $prediction['height_analysis']['detail'] ?? '' }}</p>
            </div>
        </div>
        @endif
    </div>

    {{-- Predictions --}}
    @if(isset($prediction['predictions']) && !empty($prediction['predictions']))
    <div class="mb-3" style="background: rgba(255,255,255,0.7); border-radius: 8px; padding: 12px;">
        <strong style="font-size: 13px; color: #6b7280;">📈 Next Month Predictions</strong>
        <div class="d-flex mt-2" style="gap: 16px;">
            @if(isset($prediction['predictions']['next_month_weight']))
            <div><span style="font-size: 12px; color: #9ca3af;">Weight:</span> <strong>{{ $prediction['predictions']['next_month_weight'] }} kg</strong></div>
            @endif
            @if(isset($prediction['predictions']['next_month_height']))
            <div><span style="font-size: 12px; color: #9ca3af;">Height:</span> <strong>{{ $prediction['predictions']['next_month_height'] }} cm</strong></div>
            @endif
        </div>
        @if(isset($prediction['predictions']['growth_velocity']))
        <p style="font-size: 12px; color: #6b7280; margin: 4px 0 0;">{{ $prediction['predictions']['growth_velocity'] }}</p>
        @endif
    </div>
    @endif
    @endif

    {{-- Recommendations --}}
    @if(!empty($prediction['recommendations']))
    <div class="mb-3">
        <strong style="font-size: 13px; color: #6b7280;">💡 Recommendations</strong>
        <ul style="margin: 8px 0 0; padding-left: 20px; font-size: 13px; color: #374151;">
            @foreach($prediction['recommendations'] as $rec)
            <li style="margin-bottom: 4px;">{{ $rec }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Red Flags --}}
    @if(!empty($prediction['red_flags']))
    <div class="mb-3" style="background: #fef2f2; border-radius: 8px; padding: 12px;">
        <strong style="font-size: 13px; color: #dc2626;">⚠️ Red Flags</strong>
        <ul style="margin: 8px 0 0; padding-left: 20px; font-size: 13px; color: #991b1b;">
            @foreach($prediction['red_flags'] as $flag)
            <li style="margin-bottom: 4px;">{{ $flag }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Health Risks (legacy format) --}}
    @if(!$isEnhanced && !empty($prediction['health_risks']))
    <div class="mb-3">
        <strong style="font-size: 13px; color: #6b7280;">⚠️ Health Risks</strong>
        <ul style="margin: 8px 0 0; padding-left: 20px; font-size: 13px;">
            @foreach($prediction['health_risks'] as $risk)
            <li>{{ $risk }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Source + Disclaimer --}}
    <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid rgba(0,0,0,0.08); font-size: 11px; color: #9ca3af;">
        <i class="fa fa-info-circle"></i>
        This AI-generated analysis is for informational purposes only and should not replace professional medical advice.
        @if(isset($prediction['source']))
            <span class="ml-2">(Source: {{ $prediction['source'] }})</span>
        @endif
        @if(isset($prediction['generated_at']))
            <span class="ml-2">| Generated: {{ \Carbon\Carbon::parse($prediction['generated_at'])->format('M d, Y h:i A') }}</span>
        @endif
    </div>
</div>
@endif
