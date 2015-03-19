<?php
/**
 * 
 *
 * 2013-11-19 1.0 liuhe
 *
 * @author  liuhe(liuhe009@gmail.com)
 * @version 1.0
 */
class PoiLastEditModel extends BaseModel
{
	// 定义要操作的表名
	public $db         = 'fx';
	public $table      = 'poi_last_edit';
	public $pk         = 'id';

    //可关联的,对应value为model前缀
    public $relateAble = array(
        'level'        => 'landscapeLevels'    
    );

    //关联的字段,对应value为表字段
    public $relateField = array(
        'level'        => 'level_id'
    );

    //外键扩展树形信息,现在主要是地区信息，对应value为model前缀
    public $withAble = array(
        'districts'     => 'Districts',
    );

    //外键扩展树形信息,现在主要是地区信息，对应value为model前缀
    public $withField = array(
        'districts'     => 'district_id',
    );
}