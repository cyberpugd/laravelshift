@if ( $errors->any() )
     <div class="alert alert-danger fade in alert-dismissable alert-center">
     <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
             <ul>
                 @foreach ( $errors->all() as $error )
                 <li>{!! $error !!}</li>
                 @endforeach
             </ul>
     </div>
@endif