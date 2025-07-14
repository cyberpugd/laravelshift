
@if (session()->has('message'))
     <div class="alert alert-{{ session('message.level') }} flash alert-center">
        {!! session('message.message') !!}
    </div>
@elseif (session()->has('messagestay'))
     <div class="alert alert-{{ session('messagestay.level')}}">
        {!! session('messagestay.message') !!}
    </div>
@endif