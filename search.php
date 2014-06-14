<!DOCTYPE html>
<html>
  <head>
    <title>iGEM Biobricks Finder</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="search.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="http://cdn.bootcss.com/html5shiv/3.7.0/html5shiv.min.js"></script>
        <script src="http://cdn.bootcss.com/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="ctr"><center>
        <div class="ctr"><img src="igem_logo.png"></img></div>
        <form action="dosui.php">
          <div class="input-group" class="bigctr">
            <input type="text" class="form-control" name="search" maxlength=100 placeholder="Input your search expression.">
            <span class="input-group-btn">
              <button class="btn btn-default" type="submit">Brick!</button>
            </span>
          </div><!-- /input-group -->
        </form>
    </center></div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="http://cdn.bootcss.com/jquery/1.10.2/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="bootstrap/js/bootstrap.min.js"></script>
  </body>
</html>
