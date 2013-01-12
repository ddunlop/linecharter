<!DOCTYPE html>
<html lang="en">
<head>
  <title><?php
    if(isset($title)) echo $title;
    else echo 'temp';
  ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php
    echo '    ',HTML::style('assets/bootstrap/css/bootstrap.min.css'), PHP_EOL;
    echo '    ',HTML::style('assets/bootstrap/css/bootstrap-responsive.min.css'), PHP_EOL;
    echo '    ',HTML::style('assets/graph.css'), PHP_EOL;
  ?>
</head>
<body data-ajax-base="<?php echo URL::base()?>">
  <div class="container-fluid">
    
    