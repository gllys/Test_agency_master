<?php

class PoiModel extends BaseModel
{
	public $db         = 'fx';
	public $table      = 'poi';
	public $pk         = 'id';
    
    //可关联的,对应value为model前缀
    public $relateAble = array(
        'poi_last_edit_info' => 'PoiLastEdit',
        'level'        => 'landscapeLevels',
        'organization' => 'organizations'
    );

    //关联的字段,对应value为表字段
    public $relateField = array(
        'level'        => 'level_id',
        'organization' => 'organization_id',
    );

    //1对多的关联  model,主表id，关联表id
    public $relateAbleBelongsToMany = array(
        'poi_last_edit_info'    => array('PoiLastEdit', 'poi_id', 'id'),
    );

    //外键扩展树形信息,现在主要是地区信息，对应value为model前缀
    public $withAble = array(
        'districts'     => 'Districts',
    );

    //外键扩展树形信息,现在主要是地区信息，对应value为model前缀
    public $withField = array(
        'districts'     => 'district_id',
    );
    /**
     * 新增
     */
    public function save($data){
        if(count($data)>1){
            foreach($data as $k => $v){
                $this->add($v);
            }
        }else{
            $this->add($data);
        }

    }

}