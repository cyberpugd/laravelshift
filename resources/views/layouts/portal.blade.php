
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf_token" content="{{ Crypt::encrypt(csrf_token()) }}" />
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>IFS EnR Help Desk</title>


   

    <!-- Bootstrap core CSS -->
     <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css"> -->
    <link href="/css/app.css" rel="stylesheet"> 

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">

    <!-- Dropzone CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.3.0/dropzone.css">
     
    <!-- Custom styles for this template -->
    <link href="/css/dashboard.css" rel="stylesheet">
     <link rel="stylesheet" type="text/css" href="/css/datetimepicker.css"/ >
    <script src="/js/lync-presence.js"></script>
    <!-- <script type="text/javascript" src="https://portal.p2es.com/_layouts/1033/init.js"></script> -->
<!-- <script type="text/javascript" src="https://portal.p2es.com/_layouts/1033/core.js"></script> -->


    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <nav class="navbar navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <span class="navbar-brand"><a href="/helpdesk/dashboard"><img src='/images/ifslogo-topnav.png' width="45"></a></span>
          {{-- <a class="navbar-brand" href="#">Help Desk</a> --}}
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
          @can('agent_portal')
          <li><a href="/dashboard">Agent Portal</a></li>
          @endcan
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">{{Auth::user()->first_name}} <span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="/helpdesk/user-settings/profile">Profile</a></li>
               <li><a href="https://p2energysolutions.sharepoint.com/sites/thehub/it" target="_blank">User Guides</a></li>
              </ul>
            </li>
            <li><a href="/logout">Logout</a></li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container-fluid">
      <div class="row">
        <div class="col-md-10 col-md-offset-1 main">
        <!--=== End Header v3 ===-->
          <div class="messages">
              <!-- Session Flash messaging -->
               @include('app.partials.flash')
               <!-- Ajax Flash -->
               <div class="ajax-flash alert-center" style="display: none;"></div>
              <!-- Show Error Messages if they exist -->
              @include('app.partials.errors')
          </div>
          @yield('content')
        </div>
      </div>
    </div>


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script> -->
   
    <script src="https://code.jquery.com/jquery-2.2.3.min.js"></script>
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <script src="/js/bootstrap.js"></script>
    <script src="/js/sweetalert.js"></script>
    <script src="/js/bootstrap-select.js"></script>
    <script src="/js/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/vue.resource/0.9.3/vue-resource.min.js"></script>
     <script src="/js/moment.js"></script>
     <script src="/js/datetimepicker.min.js"></script>

    <script type="text/javascript">
         $(document).ready(function(){   
              $('.flash').delay(4000).fadeOut(500);
              $('.selectpicker').selectpicker({
                  iconBase: 'fontawesome',
                  tickIcon: 'fa fa-check'
               });
         });
         $.ajaxPrefilter(function(options, originalOptions, xhr) {
                var token = $('meta[name="csrf_token"]').attr('content');
                if (token) {
                      return xhr.setRequestHeader('X-XSRF-TOKEN', token);
                }
          });
    </script>
    @yield('footer')
    @include('app.partials.sweet-alert')
  </body>
</html>
