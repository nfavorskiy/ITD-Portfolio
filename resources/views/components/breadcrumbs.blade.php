@if(isset($links) && is_array($links) && count($links) > 0)
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb" style="--bs-breadcrumb-divider: '>';">
        @foreach($links as $label => $url)
            @if($loop->last)
                <li class="breadcrumb-item active" aria-current="page">{!! $label !!}</li>
            @elseif(empty($url))
                <li class="breadcrumb-item">{!! $label !!}</li>
            @else
                <li class="breadcrumb-item"><a href="{{ $url }}">{!! $label !!}</a></li>
            @endif
        @endforeach
    </ol>
</nav>
@endif