<?php

class TicketType 
{
    private $_data = array (
  1 => 
  array (
    'id' => '1',
    'name' => '成人票',
  ),
  2 => 
  array (
    'id' => '2',
    'name' => '儿童票',
  ),
  3 => 
  array (
    'id' => '3',
    'name' => '老人票',
  ),
  4 => 
  array (
    'id' => '4',
    'name' => '团体票',
  ),
         5 => 
  array (
    'id' => '5',
    'name' => '学生票',
  ),
);
    /***** 单例*********/
    private static $_singleton = null;

    public static function model() {
        if (self::$_singleton) {
            return self::$_singleton;
        }
        return self::$_singleton = new self();
    }

    public function findAll() {
        return $this->_data;
    }

    public function findByPk($id) {
        return $this->_data[$id];
    }
}
