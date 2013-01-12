<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Index extends Controller {
  public function action_index() {
    $period_config =$config = Kohana::$config->load('period');
    $this->response->body(
      View::factory('index/index')
        ->bind("period_config", $period_config)
    );
  }
}