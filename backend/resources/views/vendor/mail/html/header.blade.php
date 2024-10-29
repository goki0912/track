@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'TrackWave')
<img src="{{ $url ."/api/images/logo.png" }}" class="logo" alt="TrackWave Logo">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
