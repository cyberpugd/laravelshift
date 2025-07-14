
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>IFS EnR Help Desk</title>

    <!-- Bootstrap core CSS -->
    <link href="css/app.css" rel="stylesheet">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="css/splash.css" rel="stylesheet">

    
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <div class="site-wrapper">
    <img src="/images/p2logo-sidebar.png" class="logo">
      <div class="site-wrapper-inner">

        <div class="cover-container">
           <div class="messages">
              <!-- Flash messaging -->
               @include('app.partials.flash')
              <!-- Show Error Messages if they exist -->
              @include('app.partials.errors')
          </div>
          <div class="inner cover">
            <h1 class="cover-heading">IFS EnR Help Desk</h1>
            <p class="lead">Welcome to the IFS EnR Help Desk system. <br>Click the button below to sign in.</p>
            <p class="lead">

              <form action="/login" method="post" class="form-signin">
                {{csrf_field()}}
                <button class="btn btn-lg btn-default" type="submit">Sign in as {{strtolower($adid)}}</button>
              </form>
            </p>
          </div>

          <div class="mastfoot">
            <div class="inner">
              <p></p>
            </div>
          </div>

        </div>

      </div>

    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="/js/sweetalert.js"></script>
    @include('app.partials.sweet-alert')
  </body>
</html>
