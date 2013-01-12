<?php defined('SYSPATH') or die('No direct script access.');

class Model_Store {
  static $mongo = false;
  
  public static function mongo() {
    $config = Kohana::$config->load('mongo');
    if(!self::$mongo) {
      $m = new MongoClient();
      self::$mongo  = $m->selectDB($config['db']);
    }
    return self::$mongo;
  }
  public static function collection() {
    $config = Kohana::$config->load('mongo');
    $m = self::mongo();
    return $m->selectCollection($config['collection']);
  }
  
  public static function legend() {
    $config = Kohana::$config->load('mongo');
    $m = self::mongo();
    return $m->selectCollection($config['legend'])->findOne();
  }
}