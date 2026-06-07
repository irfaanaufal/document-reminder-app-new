@php
    $selectedValue = (string) ($selected ?? old('reminder_bulan', ''));
    $labelSuffix = $labelSuffix ?? ' sebelum expired';
@endphp
@foreach (\App\Support\DocumentReminderIntervals::ALLOWED_MONTHS as $month)
    <option value="{{ $month }}" @selected($selectedValue === (string) $month)>{{ $month }} bulan{{ $labelSuffix }}</option>
@endforeach
