
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
    <link href="{{ elixir('css/app.css') }}" rel="stylesheet"> 

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">

    <!-- Dropzone CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.3.0/dropzone.css">
     
    <!-- Custom styles for this template -->
    <link href="/css/dashboard.css" rel="stylesheet">
     <link rel="stylesheet" type="text/css" href="/css/datetimepicker.css">
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
          <a class="navbar-brand" href="#">IFS EnR Help Desk</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
          @can('help_desk_portal')
          <li><a href="/helpdesk/dashboard">Help Desk Portal</a></li>
          @endcan
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">{{Auth::user()->first_name}} <span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="/user-settings/profile">Profile</a></li>
                @can('create_view')
                    <li><a href="/user-settings/query-builder">Query Builder</a></li>
               @endcan
               @can('build_forms')
                    <li><a href="/admin/forms">Custom Forms</a></li>
               @endcan
               @can('create_work_order_templates')
                    <li><a href="/user-settings/wo-template">Work Order Templates</a></li>
               @endcan
               @can('create_cc_ticket')
                    <li><a href="/user-settings/cc-templates">Change Control Templates</a></li>
               @endcan
               <li><a href="http://hub.p2energysolutions.com/information-technology-it/quick-guides" target="_blank">User Guides</a></li>
              </ul>
            </li>
            <li><a href="/logout">Logout</a></li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar"> 

          <img src='/images/p2logo-sidebar.png' class="sidebar-logo" />
          <ul class="nav nav-sidebar">
           <div id="MainMenu">
            <div class="list-group panel">
              <a href="/dashboard" class="list-group-item list-group-item-main" data-parent="#MainMenu">Dashboard</a>
              @if(Auth::user()->queries->count() > 0)
              <a href="#demo2" class="list-group-item list-group-item-main" data-toggle="collapse" data-parent="#MainMenu" aria-expanded="{{{ (Request::is('tickets/*') ? 'true' : 'false') }}}">My Views <span class="caret"></span></a>
                   <div class="{{{ (Request::is('views/*') ? 'collapse in' : 'collapse') }}}" id="demo2">
                         @foreach(Auth::user()->queries as $view)
                              <a href="/views/{{$view->id}}" class="list-group-item list-group-item-sub {{{ (Request::is('views/'. $view->id) ? 'active' : '') }}}">{{$view->name}}</a>
                         @endforeach
                   </div>
               @endif
              @if(Auth::user()->can('create_ticket') || Auth::user()->can('update_ticket'))
                    <a href="#demo3" class="list-group-item list-group-item-main" data-toggle="collapse" data-parent="#MainMenu" aria-expanded="{{{ (Request::is('tickets/*') ? 'true' : 'false') }}}">Tickets <span class="caret"></span></a>
                   <div class="{{{ (Request::is('tickets/*') ? 'collapse in' : 'collapse') }}}" id="demo3">
                         @can('create_ticket')
                              <a href="/tickets/create" class="list-group-item list-group-item-sub {{{ (Request::is('tickets/create') ? 'active' : '') }}}">Create New Ticket</a>
                          @endcan
                          @can('create_ticket')
                              <a href="/tickets/open-tickets" class="list-group-item list-group-item-sub {{{ (Request::is('tickets/open-tickets') ? 'active' : '') }}}">My Open Tickets</a>
                         @endcan
                         @can('create_ticket')
                              <a href="/tickets/team-tickets" class="list-group-item list-group-item-sub {{{ (Request::is('tickets/team-tickets') ? 'active' : '') }}}">My Teams' Unassigned Tickets</a>
                         @endcan
                         @can('view_work_orders')
                              <a href="/tickets/work-orders" class="list-group-item list-group-item-sub {{{ (Request::is('tickets/work-orders') ? 'active' : '') }}}">My Work Orders</a>
                         @endcan
                         @can('create_ticket')
                              <a href="/tickets/all" class="list-group-item list-group-item-sub {{{ (Request::is('tickets/all') ? 'active' : '') }}}">All Tickets</a>
                         @endcan 
                   </div>
              @endif
              @if(Auth::user()->can('create_cc_ticket') || Auth::user()->can('approve_change_ticket'))
                   <a href="#demo4" class="list-group-item list-group-item-main" data-toggle="collapse" data-parent="#MainMenu" aria-expanded="{{{ (Request::is('change-control/*') ? 'true' : 'false') }}}">Change Control <span class="caret"></span></a>
                   <div class="{{{ (Request::is('change-control/*') ? 'collapse in' : 'collapse') }}}" id="demo4">
               @can('create_cc_ticket')
                     <a href="/change-control/create" class="list-group-item list-group-item-sub {{{ (Request::is('change-control/create') ? 'active' : '') }}}">Create Change Ticket</a>
               @endcan
               @can('approve_change_ticket')
                    <a href="/change-control/needs-approval" class="list-group-item list-group-item-sub {{{ (Request::is('change-control/needs-approval') ? 'active' : '') }}}">Awaiting Approval</a>
               @endcan
               @can('create_cc_ticket')
                     <a href="/change-control/my-open" class="list-group-item list-group-item-sub {{{ (Request::is('change-control/my-open') ? 'active' : '') }}}">My Open Change Tickets</a>
               @endcan
               @can('view_work_orders')
                    <a href="/change-control/work-orders" class="list-group-item list-group-item-sub {{{ (Request::is('change-control/work-orders') ? 'active' : '') }}}">My Work Orders</a>
               @endcan
               @can('create_cc_ticket')
                    <a href="/change-control/all" class="list-group-item list-group-item-sub {{{ (Request::is('change-control/all') ? 'active' : '') }}}">All Change Tickets</a>
               @endcan
              </div>
              @endif

              @if(Auth::user()->can('manage_categories') || Auth::user()->can('manage_users') || Auth::user()->can('mail_setup') || Auth::user()->can('manage_roles') || Auth::user()->can('manage_audit_units'))
                   <a href="#demo5" class="list-group-item list-group-item-main" data-toggle="collapse" data-parent="#MainMenu" aria-expanded="{{{ (Request::is('admin/*') ? 'true' : 'false') }}}">Administration <span class="caret"></span></a>
                   <div class="{{{ (Request::is('admin/*') ? 'collapse in' : 'collapse') }}}" id="demo5">
                              <a href="/admin/announcements" class="list-group-item list-group-item-sub {{{ (Request::is('admin/announcements') ? 'active' : '') }}}">Announcements</a>
                         @can('manage_audit_units')
                              <a href="/admin/audit-units" class="list-group-item list-group-item-sub {{{ (Request::is('admin/audit-units') ? 'active' : '') }}}">Audit Units</a>
                         @endcan
                         @can('manage_categories')
                              <a href="/admin/category-management" class="list-group-item list-group-item-sub {{{ (Request::is('admin/category-management') ? 'active' : '') }}}">Categories</a>
                         @endcan
                              <a href="/admin/holidays" class="list-group-item list-group-item-sub {{{ (Request::is('admin/holidays') ? 'active' : '') }}}">Holidays</a>
                         @can('manage_locations')
                              <a href="/admin/locations" class="list-group-item list-group-item-sub {{{ (Request::is('admin/locations') ? 'active' : '') }}}">Locations</a>
                         @endcan
                         @can('manage_roles')
                              <a href="/admin/roles" class="list-group-item list-group-item-sub {{{ (Request::is('admin/roles') ? 'active' : '') }}}">Roles</a>
                         @endcan
                         @can('manage_imap')
                              <a href="/admin/mail-setup" class="list-group-item list-group-item-sub {{{ (Request::is('admin/mail-setup') ? 'active' : '') }}}">Settings</a>
                         @endcan
                              <a href="/admin/teams" class="list-group-item list-group-item-sub {{{ (Request::is('admin/teams') ? 'active' : '') }}}">Teams</a>
                         @can('manage_users')
                              <a href="/admin/users" class="list-group-item list-group-item-sub {{{ (Request::is('admin/users') ? 'active' : '') }}}">Users</a>
                         @endcan
                              

                   </div>
              @endif
            </div>
          </div>
          </ul>
          
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
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
   <script src="/js/polyfills.js"></script>
    <script src="https://code.jquery.com/jquery-2.2.3.min.js"></script>
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <script src="/js/bootstrap.js"></script>
    <script src="/js/sweetalert.js"></script>
    <script src="/js/bootstrap-select.js"></script>
    <script src="/js/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/vue.resource/0.9.3/vue-resource.min.js"></script>
     <script src="/js/moment.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.11/moment-timezone-with-data.js"></script>
     <!-- SortableJS -->
  <script src="https://unpkg.com/sortablejs@1.4.2"></script>
  <!-- VueSortable -->
  <script src="https://unpkg.com/vue-sortable@0.1.3"></script>
     <script src="/js/datetimepicker.min.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.3.0/Chart.min.js"></script>

    <script type="text/javascript">
         $(document).ready(function(){   
              $('.flash').delay(4000).fadeOut(500);
              $('.selectpicker').selectpicker({
                  iconBase: 'fontawesome',
                  tickIcon: 'fa fa-check'
               });
           var hash = window.location.hash;
            if(hash) {
                 $('a[href="' + hash + '"]').tab('show');
                 $(hash).collapse('show');
            }
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
