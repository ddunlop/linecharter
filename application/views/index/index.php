<?php
  echo View::factory('shared/header');
?>

<div class="row-fluid">
  <div class="span12">
    <div id="vis"></div>
  </div>
</div>
<div class="row-fluid">
  <div class="span12">
    <div id="legend">
    </div>
  </div>
</div>
<div class="row-fluid controls hidden">
  <div class="span12">
    <div class="btn-group" id="period">
<?php
        foreach($period_config as $key => $pinfo) {
          $active = $key == 'day';
          echo '        <button',
            HTML::attributes(array('class'=>'btn'.($active?' active':''),'data-period'=>$key)),'>',
            $pinfo['text'],'</button>', PHP_EOL;
        }
      ?>
    </div>
  </div>
</div>
<?php

echo HTML::script('assets/d3.v3/d3.v3.min.js'), PHP_EOL;
echo HTML::script('assets/graph.js'), PHP_EOL;
echo View::factory('shared/footer');
?>