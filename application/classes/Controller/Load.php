<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Load extends Controller {
  public function action_index() {
    $min = 5;
    $points = (3*24*60)/$min;
    echo $points;
    $begin = new DateTime('-3 day');
    $end = new DateTime();
    $interval = new DateInterval('PT'.$min.'M');
    $period = new DatePeriod($begin, $interval, $end);
    Model_Store::collection()->drop();
    echo '<ol>';
    $i = 0;
    foreach($period as $time) {
      if($i>600 && $i<700) {
        $i++;
        continue;
      }
      $url = URL::site('collect','http') . URL::query(array(
        'time' => $time->format('Y-m-d H:i:s'),
        'o' => $this->gaussian($i, $points, 2),
        'g' => $this->gaussian($i, $points, -1),
        'r' => 10+ 40 * sin($i * (2*M_PI)/$points),
        'or' => 25 * cos($i * (10*M_PI)/$points),
        'rand' => $this->rand($i, $points),
      ));
      echo '<li>', $url ,'</li>';
      $r = new Request($url);
      $r->execute();
      $i++;
    }
    echo '</ol>';
  }
  
  private function rand($x, $points) {
    $cos = 40 * cos($x/2 * (10*M_PI)/$points);
    return rand( $cos - 5, $cos +5);
  }

  private function gaussian($x, $points, $am = 1) {
    $a = 30 * $am;
    $b = $points/2;
    $c = $points * 100 / 588;
    
    return 30 + $a * pow(M_E, -1 * (pow($x-$b,2)/(2 * pow($c,2))));
  }
}