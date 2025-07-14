<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>IFS EnR Help Desk</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">


  <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
        page. However, you can choose any other skin. Make sure you
        apply the skin class to the body tag so the changes take effect.
  -->

    <link href="{{ elixir('css/app.css') }}" rel="stylesheet">
    <!-- Bootstrap 3.3.6 -->
          <!-- <link rel="stylesheet" href="css/bootstrap.css"> -->
     <!-- Admin LTE -->
          <link href="{{ elixir('css/adminlte.css') }}" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">

    <!-- Dropzone CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.3.0/dropzone.css">

    <!-- Custom styles for this template -->
    <!-- <link href="/css/dashboard.css" rel="stylesheet"> -->
    <link href="{{ elixir('css/skins.css') }}" rel="stylesheet">
     <link rel="stylesheet" type="text/css" href="/css/datetimepicker.css">
    <script src="/js/lync-presence.js"></script>

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<!--
BODY TAG OPTIONS:
=================
Apply one or more of the following classes to get the
desired effect
|---------------------------------------------------------|
| SKINS         | skin-blue                               |
|               | skin-black                              |
|               | skin-purple                             |
|               | skin-yellow                             |
|               | skin-red                                |
|               | skin-green                              |
|---------------------------------------------------------|
|LAYOUT OPTIONS | fixed                                   |
|               | layout-boxed                            |
|               | layout-top-nav                          |
|               | sidebar-collapse                        |
|               | sidebar-mini                            |
|---------------------------------------------------------|
-->
<body class="hold-transition skin-red fixed @if(Auth::user()->is_menu_collapsed) sidebar-collapse @endif">
<div class="wrapper">

  <!-- Main Header -->
  <header class="main-header">

    <!-- Logo -->
    <a href="/" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><img src="/images/p2_logo2.png"></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>IFS EnR</b> Help Desk</span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
      <!-- Sidebar toggle button-->

      <a href="#" class="sidebar-toggle" data-toggle="offcanvas"  role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- Messages: style can be found in dropdown.less-->
          @can('help_desk_portal')
          <li><a href="/helpdesk/dashboard">Help Desk Portal</a></li>
          @endcan
          <!-- User Account Menu -->
          <li class="dropdown" style="margin-right: 50px;">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">{{Auth::user()->first_name}} {{Auth::user()->last_name}}<span class="caret"></span></a>
              <ul class="dropdown-menu">
                    <li><a href="/user-settings/profile">Profile</a></li>
                    @can('create_view')
                    <li><a href="/user-settings/query-builder">Query Builder</a></li>
                    @endcan
                    @can('build_forms')
                    <li><a href="/admin/forms">Custom Forms</a></li>
                    @endcan
                    <li role="separator" class="divider"></li>
                    @can('create_work_order_templates')
                    <li><a href="/user-settings/wo-template">Work Order Templates</a></li>
                    @endcan
                    @can('create_cc_ticket')
                    <li><a href="/user-settings/cc-templates">Change Control Templates</a></li>
                    @endcan
                    <li role="separator" class="divider"></li>
                    <li><a href="https://p2energysolutions.sharepoint.com/sites/thehub/it" target="_blank">User Guides</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a href="/logout">Logout</a></li>
              </ul>
            </li>
          <!-- Control Sidebar Toggle Button -->
          <!-- <li>
            <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
          </li> -->
        </ul>
      </div>
    </nav>
  </header>
  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

      <!-- search form (Optional) -->
      <!-- <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Search...">
              <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
        </div>
      </form> -->
      <!-- /.search form -->

      <!-- Sidebar Menu -->
      <ul class="sidebar-menu">
          <li class="header">MENU</li>
          <!-- Optionally, you can add icons to the links -->
          <li><a href="/dashboard"><i class="fa fa-tachometer"></i> <span>Dashboard</span></a></li>
          <!-- <li><a href="#"><i class="fa fa-link"></i> <span>Another Link</span></a></li> -->
          <li class="treeview {{{ (Request::is('views/*') ? 'active' : '') }}}">
               <a href="#"><i class="fa fa-eye"></i> <span>My Views</span>
                    <span class="pull-right-container">
                         <i class="fa fa-angle-left pull-right"></i>
                    </span>
               </a>
               <ul class="treeview-menu">
                    @foreach(Auth::user()->queries as $view)
                    <li class="{{{ (Request::is('views/'. $view->id) ? 'active' : '') }}}"><a href="/views/{{$view->id}}"> <span>{{$view->name}}</span></a></li>
                    @endforeach
               </ul>
          </li>
          <li class="treeview {{{ (Request::is('tickets/*') ? 'active' : '') }}}">
               <a href="#"><i class="fa fa-ticket"></i> <span>Tickets</span>
                    <span class="pull-right-container">
                         <i class="fa fa-angle-left pull-right"></i>
                    </span>
               </a>
               <ul class="treeview-menu">
                    @can('create_ticket')
                         <li class="{{{ (Request::is('tickets/create') ? 'active' : '') }}}"><a href="/tickets/create"><span>Create Ticket for User</span></a></li>
                     @endcan
                     @can('create_ticket')
                         <li class="{{{ (Request::is('tickets/open-tickets') ? 'active' : '') }}}"><a href="/tickets/open-tickets"><span>My Open Tickets</span></a></li>
                    @endcan
                    @can('create_ticket')
                         <li class="{{{ (Request::is('tickets/team-tickets') ? 'active' : '') }}}"><a href="/tickets/team-tickets"><span>My Teams' Unassigned Tickets</span></a></li>
                    @endcan
                    @can('view_work_orders')
                         <li class="{{{ (Request::is('tickets/work-orders') ? 'active' : '') }}}"><a href="/tickets/work-orders"><span>My Work Orders</span></a></li>
                    @endcan
                    @can('create_ticket')
                         <li class="{{{ (Request::is('tickets/all') ? 'active' : '') }}}"><a href="/tickets/all"><span>All Tickets</span></a></li>
                    @endcan
               </ul>
          </li>
          @if(Auth::user()->can('create_cc_ticket') || Auth::user()->can('approve_change_ticket'))
           <li class="treeview {{{ (Request::is('change-control/*') ? 'active' : '') }}}">
               <a href="#"><i class="fa fa-gears"></i> <span>Change Control</span>
                    <span class="pull-right-container">
                         <i class="fa fa-angle-left pull-right"></i>
                    </span>
               </a>
               <ul class="treeview-menu">
                    @can('create_cc_ticket')
                         <li class="{{{ (Request::is('change-control/create') ? 'active' : '') }}}"><a href="/change-control/create"><span>Create Change Ticket</span></a></li>
                    @endcan
                    @can('approve_change_ticket')
                         <li class="{{{ (Request::is('change-control/needs-approval') ? 'active' : '') }}}"><a href="/change-control/needs-approval"><span>Awaiting Approval</span></a></li>
                    @endcan
                    @can('create_cc_ticket')
                         <li class="{{{ (Request::is('change-control/my-open') ? 'active' : '') }}}"><a href="/change-control/my-open"><span>My Open Change Tickets</span></a></li>
                    @endcan
                    @can('view_work_orders')
                         <li class="{{{ (Request::is('change-control/work-orders') ? 'active' : '') }}}"><a href="/change-control/work-orders"><span>My Work Orders</span></a></li>
                    @endcan
                    @can('create_cc_ticket')
                         <li class="{{{ (Request::is('change-control/all') ? 'active' : '') }}}"><a href="/change-control/all"><span>All Change Tickets</span></a></li>
                    @endcan
               </ul>
          </li>
          @endif

          @if(Auth::user()->can('manage_categories') || Auth::user()->can('manage_users') || Auth::user()->can('mail_setup') || Auth::user()->can('manage_roles') || Auth::user()->can('manage_audit_units'))
          <li class="treeview {{{ (Request::is('admin/*') ? 'active' : '') }}}">
               <a href="#"><i class="fa fa-gamepad"></i> <span>Administration</span>
                    <span class="pull-right-container">
                         <i class="fa fa-angle-left pull-right"></i>
                    </span>
               </a>
               <ul class="treeview-menu">
                   <li class="{{{ (Request::is('admin/announcements') ? 'active' : '') }}}"><a href="/admin/announcements"><span>Announcements</span></a></li>
                    @can('manage_audit_units')
                         <li class="{{{ (Request::is('admin/audit-units') ? 'active' : '') }}}"><a href="/admin/audit-units"><span>Audit Units</span></a></li>
                    @endcan
                    @can('manage_categories')
                         <li class="{{{ (Request::is('admin/category-management') ? 'active' : '') }}}"><a href="/admin/category-management"><span>Categories</span></a></li>
                    @endcan
                         <li class="{{{ (Request::is('admin/holidays') ? 'active' : '') }}}"><a href="/admin/holidays"><span>Holidays</span></a></li>
                    @can('manage_locations')
                         <li class="{{{ (Request::is('admin/locations') ? 'active' : '') }}}"><a href="/admin/locations"><span>Locations</span></a></li>
                    @endcan
                    @can('manage_roles')
                         <li class="{{{ (Request::is('admin/roles') ? 'active' : '') }}}"><a href="/admin/roles"><span>Roles</span></a></li>
                    @endcan
                    @can('manage_imap')
                         <li class="{{{ (Request::is('admin/mail-setup') ? 'active' : '') }}}"><a href="/admin/mail-setup"><span>Settings</span></a></li>
                    @endcan
                        <li class="{{{ (Request::is('admin/survey-groups') ? 'active' : '') }}}"><a href="/admin/survey-groups"><span>Survey Groups</span></a></li>
                         <li class="{{{ (Request::is('admin/teams') ? 'active' : '') }}}"><a href="/admin/teams"><span>Teams</span></a></li>
                    @can('manage_users')
                         <li class="{{{ (Request::is('admin/users') ? 'active' : '') }}}"><a href="/admin/users"><span>Users</span></a></li>
                    @endcan
               </ul>
          </li>
          @endif
     </ul>
      <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Main content -->


      @yield('content')


    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <strong>Copyright &copy; {{Carbon\Carbon::now()->format('Y')}} <a href="https://www.ifs.com">IFS EnR</a>.</strong> All rights
    reserved.
  </footer>

</div>
<!-- ./wrapper -->

<!-- REQUIRED JS SCRIPTS -->
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
         $('.sidebar-toggle').click(function() {
               $.post('/user-settings/save-menu-state');
               // '/user-settings/save-menu-state'
         });

    </script>

    @yield('footer')
    @include('app.partials.sweet-alert')
    <script src="/js/adminlte.js"></script>
<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
</body>
</html>
