@component('mail::layout')
{{-- Header --}}
@slot('header')
    @if($showEmailHeader)
    @component('mail::header', ['url' => config('app.url')])
        <!-- header here -->
        <img src="{{asset(getSetting('site.light_logo'))}}" class="mail-logo">
    @endcomponent
    @endif
@endslot

# {{$mailTitle}}
{{$mailContent}}

@if(count($button))
@component('mail::button', ['url' => $button['url'], 'color'=> isset($button['color']) ? $button['color'] : 'primary' ])
    {{$button['text']}}
@endcomponent
@endif

{{__('Thanks')}},<br>
{{ getSetting('emails.from_name') }}
@slot('footer')
    @component('mail::footer')
        © {{ date('Y') }} {{getSetting('site.name')}}. {{__('All rights reserved.')}}
    @endcomponent
@endslot
@endcomponent
