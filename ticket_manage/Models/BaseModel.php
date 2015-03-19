<?php

/**
 * 对model的扩展
 * 2013-11-19 1.0 liuhe
 *
 * @author  liuhe(liuhe009@gmail.com)
 * @version 1.0
 */
class BaseModel extends Model {

    /**
     * getList的强化版本 
     * 假如查2个表时，2个表不在同一个库，这里的join不能用
     * 例： array(array('left_join'=>'test t','on'=>'t.id=tt.tid'))
     * @param array|string $where where条件
     * @param string $limit limit
     * @param string $order 排序
     * @param string $fields 字段
     * @param string $group 
     * @param array $join join数组
     * @return array
     */
    public function getListExtension($where = '', $limit = '', $order = '', $fields = '*', $group = '', $join = '') {
        $sql = 'SELECT ' . $fields . ' FROM ' . $this->table;
        if (!empty($join))
            $sql .= ' ' . $this->_join($join);
        if (!empty($where))
            $sql .= ' WHERE ' . $this->_filter($where);
        if (!empty($group))
            $sql .= ' GROUP BY ' . $group;
        if (!empty($order))
            $sql .= ' ORDER BY ' . $order;
        if (!empty($limit))
            $sql .= ' LIMIT ' . $limit;
        return $this->getListBySQL($sql);
    }

    //条件公式转换成sql条件
    protected function _filter($filter) {
        $where = $this->parseFilter($filter);
        return $where;
    }

    //连接公式转换成sql条件
    protected function _join($join) {
        $where = '';
        foreach ($join as $value) {
            $where .= $this->parseJoin($value);
        }
        return $where;
    }

    /**
     * 获取包含关联数据的list
     *
     * @param array $result 获取到的list
     * @param string $param 用逗号隔开的relate
     * @return array
     */
    public function getListRelate($result, $param) {
        $relate = explode(',', $param);
        //是否有一对多
        $hasMany = FALSE;
        foreach ($relate as $key => $value) {
            if (!array_key_exists($value, $this->relateAble)) {
                unset($relate[$key]);
            }

            if ($this->relateAbleBelongsToMany) {
                if (array_key_exists($value, $this->relateAbleBelongsToMany)) {
                    $hasMany = TRUE;
                }
            }
        }

        if ($relate) {
            $tmp = array();

            //获取ID值
            foreach ($result as $k => $v) {
                foreach ($relate as $vvv) {
                    if ($this->relateAbleBelongsToMany) {
                        if (array_key_exists($vvv, $this->relateAbleBelongsToMany)) {
                            //假如是1对多的情况
                            $tmp[$vvv][] = $v[$this->pk];
                            $manyInfo = $this->getRelateBelongsToMany($vvv);

                            //deleted_at判断
                            $model    = $this->load->model($manyInfo['0']);
                            $deletedFilter = '';
                            if($model->columnExist('deleted_at')) {
                                $deletedFilter = ' AND deleted_at IS NULL';
                            }
                            $result[$k][$vvv . '_ids'] = array_flatten($model->getList($manyInfo['1'] . "='". $v[$this->pk]."'".$deletedFilter, '', '', $manyInfo['2']));
                        } else {
                            $tmp[$vvv][] = $v[$this->getRelateField($vvv)];
                        }
                    } else {
                        $tmp[$vvv][] = $v[$this->getRelateField($vvv)];
                    }
                }
            }

            //假如有1对多的情况，先获取关联的id，再通过关联的id获取对应的数据
            if ($hasMany) {
                foreach ($tmp as $tmpK => $tmpV) {
                    if (array_key_exists($tmpK, $this->relateAbleBelongsToMany)) {
                        $manyInfo = $this->getRelateBelongsToMany($tmpK);
                        unset($tmp[$tmpK]);
                        //假如是1对多的情况
                        //deleted_at判断
                        $model    = $this->load->model($manyInfo['0']);
                        $deletedFilter = '';
                        if($model->columnExist('deleted_at')) {
                            $deletedFilter = ' AND deleted_at IS NULL';
                        }
                        $tmp[$tmpK] = array_flatten($model->getList($manyInfo['1'] . ' in("' . implode('","', $tmpV) . '")'.$deletedFilter, '', '', $manyInfo['2']));
                    }
                }
            }

            //获取关联数据
            $relateResult = array();
            foreach ($tmp as $relateKey => $relateValue) {
                if ($relateValue) {
                    $relateResult[$relateKey] = $this->getRelateItems($relateKey, array_flatten($relateValue));
                }
            }

            //组新的关联数据
            $newRelateResult = array();
            foreach ($relateResult as $itemKey => $itemVal) {
                if ($itemVal) {
                    foreach ($itemVal as $secondKey => $secondVal) {
                        $model = $this->load->model($this->getRelateModel($itemKey));
                        $newRelateResult[$itemKey][$secondVal[$model->pk]] = $secondVal;
                    }
                } else {
                    $newRelateResult[$itemKey] = array();
                }
            }

            //组需要的关联到每个item
            foreach ($result as $resultKey => $resultValue) {
                foreach ($newRelateResult as $newKey => $newValue) {
                    if ($this->relateAbleBelongsToMany) {
                        if (array_key_exists($newKey, $this->relateAbleBelongsToMany)) {
                            foreach ($resultValue[$newKey . '_ids'] as $ids) {
                                $result[$resultKey][$newKey][] = $newValue[$ids];
                            }
                        } else {
                            $result[$resultKey][$newKey] = $newValue[$resultValue[$this->getRelateField($newKey)]];
                        }
                    } else {
                        $result[$resultKey][$newKey] = $newValue[$resultValue[$this->getRelateField($newKey)]];
                    }
                }
            }
        }
        return $result;
    }

    /**
     * 获取包含关联数据的单个数据
     *
     * @param array $result 获取到的单个数据
     * @param string $param 用逗号隔开的relate
     * @return array
     */
    public function getOneRelate($result, $param) {
        $relate = explode(',', $param);
        //是否有一对多
        $hasMany = FALSE;
        foreach ($relate as $key => $value) {
            if (!array_key_exists($value, $this->relateAble)) {
                unset($relate[$key]);
            }

            if ($this->relateAbleBelongsToMany) {
                if (array_key_exists($value, $this->relateAbleBelongsToMany)) {
                    $hasMany = TRUE;
                }
            }
        }

        if ($relate) {
            $tmp = array();

            //获取ID值
            foreach ($relate as $vvv) {
                if ($this->relateAbleBelongsToMany) {
                    if (array_key_exists($vvv, $this->relateAbleBelongsToMany)) {
                        //假如是1对多的情况
                        $tmp[$vvv][] = $result[$this->pk];
                        $manyInfo = $this->getRelateBelongsToMany($vvv);

                         //deleted_at判断
                        $model    = $this->load->model($manyInfo['0']);
                        $deletedFilter = '';
                        if($model->columnExist('deleted_at')) {
                            $deletedFilter = ' AND deleted_at IS NULL';
                        }

                        $result[$vvv . '_ids'] = array_flatten($model->getList($manyInfo['1'] . "='" . $result[$this->pk] . "'".$deletedFilter, '', '', $manyInfo['2']));
                    } else {
                        $tmp[$vvv][] = $result[$this->getRelateField($vvv)];
                    }
                } else {
                    $tmp[$vvv][] = $result[$this->getRelateField($vvv)];
                }
            }

            //假如有1对多的情况，先获取关联的id，再通过关联的id获取对应的数据
            if ($hasMany) {
                foreach ($tmp as $tmpK => $tmpV) {
                    if (array_key_exists($tmpK, $this->relateAbleBelongsToMany)) {
                        $manyInfo = $this->getRelateBelongsToMany($tmpK);
                        unset($tmp[$tmpK]);
                        //假如是1对多的情况

                         //deleted_at判断
                        $model    = $this->load->model($manyInfo['0']);
                        $deletedFilter = '';
                        if($model->columnExist('deleted_at')) {
                            $deletedFilter = ' AND deleted_at IS NULL';
                        }

                        $tmp[$tmpK] = array_flatten($model->getList($manyInfo['1'] . ' in("' . implode('","', $tmpV) . '")'.$deletedFilter, '', '', $manyInfo['2']));
                    }
                }
            }

            //获取关联数据
            $relateResult = array();
            foreach ($tmp as $relateKey => $relateValue) {
                if ($relateValue) {
                    $relateResult[$relateKey] = $this->getRelateItems($relateKey, $relateValue);
                }
            }

            //组新的关联数据
            $newRelateResult = array();
            foreach ($relateResult as $itemKey => $itemVal) {
                if ($itemVal) {
                    foreach ($itemVal as $secondKey => $secondVal) {
                        $model = $this->load->model($this->getRelateModel($itemKey));
                        $newRelateResult[$itemKey][$secondVal[$model->pk]] = $secondVal;
                    }
                } else {
                    $newRelateResult[$itemKey] = array();
                }
            }

            //组需要的关联到单个数据中
            foreach ($newRelateResult as $newKey => $newValue) {
                if ($this->relateAbleBelongsToMany) {
                    if (array_key_exists($newKey, $this->relateAbleBelongsToMany)) {
                        foreach ($result[$newKey . '_ids'] as $ids) {
                            $result[$newKey][] = $newValue[$ids];
                        }
                    } else {
                        $result[$newKey] = $newValue[$result[$this->getRelateField($newKey)]];
                    }
                } else {
                    $result[$newKey] = $newValue[$result[$this->getRelateField($newKey)]];
                }
            }
        }
        return $result;
    }

    /**
     * 获取关联的数据
     *
     * @param string $item model配置的relateAble的key
     * @param array $ids id数组
     * @return array
     */
    public function getRelateItems($item, $ids) {
        $model = $this->load->model($this->getRelateModel($item));
        $deletedAtExist = $model->columnExist('deleted_at');
        if($deletedAtExist) {
            $filter = $model->pk . ' in("' . implode('","', $ids) . '") AND deleted_at IS NULL';
        } else {
            $filter = $model->pk . ' in("' . implode('","', $ids) . '")';
        }
        $result = $model->getList($filter);
        return $result;
    }

    //关联的数据的model
    public function getRelateModel($item) {
        return $this->relateAble[$item];
    }

    //关联的数据的model
    public function getRelateField($item) {
        return $this->relateField[$item];
    }

    //关联的数据的
    public function getRelateBelongsToMany($item) {
        return $this->relateAbleBelongsToMany[$item];
    }

    //关联的数据的model
    public function getWithModel($item) {
        return $this->withAble[$item];
    }

    //关联的数据的字段
    public function getWithField($item) {
        return $this->withField[$item];
    }

    /**
     * 获取with参数关联的数据
     *
     * @param array $result 获取到的list,得包含地区信息
     * @param string $param 用逗号隔开的relate
     * @return array
     */
    public function getListWith($result, $param) {
        $with = explode(',', $param);
        foreach ($with as $key => $value) {
            if (!array_key_exists($value, $this->withAble)) {
                unset($with[$key]);
            }
        }

        if ($with) {
            $tmp = array();

            //获取ID值
            foreach ($result as $k => $v) {
                foreach ($with as $vvv) {
                    $tmp[$vvv][] = $v[$this->getWithField($vvv)];
                }
            }

            //获取最终数据
            foreach ($tmp as $tmpKey => $tmpValue) {
                $model = $this->load->model($this->getWithModel($tmpKey));
                $result = $model->getListForWith($result, $tmpValue);
                unset($model);
            }
        }
        return $result;
    }

    /**
     * 获取with参数关联的单个数据
     *
     * @param array $result 获取到的单个数据,得包含地区信息
     * @return array
     */
    public function getOneWith($result, $param) {
        $with = explode(',', $param);
        foreach ($with as $key => $value) {
            if (!array_key_exists($value, $this->withAble)) {
                unset($with[$key]);
            }
        }

        if ($with) {
            $tmp = array();

            //获取ID值
            foreach ($with as $vvv) {
                $tmp[$vvv] = $result[$this->getWithField($vvv)];
            }

            //获取最终数据
            foreach ($tmp as $tmpKey => $tmpValue) {
                $model = $this->load->model($this->getWithModel($tmpKey));
                $result = $model->getOneForWith($result, $tmpValue);
                unset($model);
            }
        }
        return $result;
    }

    /**
     * where条件转换工具 
     * 例： array('id|in'=>array(1,2,3),'name|llike'=>'不错哦')  将转换为  `id` in('1','2','3') AND name like '不错哦%'
     * @param array $filter 过滤条件数组
     * @return string
     */
    public function parseFilter($filter) {
        $where = array(1);
        if (is_array($filter)) {
            foreach ($filter as $key => $value) {
                if (is_null($value)) {
                    $where[] = $key . ' is NULL ';
                } elseif (strpos($key, '|') !== false) {
                    list($key, $type) = explode('|', $key);
                    unset($filter[$key]);
                    $_str = $this->_getFilterType($type, $value);
                    if (strpos($_str, '{col}') !== false) {
                        $where[] = str_replace('{col}', $key, $_str);
                    } else {
                        $where[] = $key . $_str;
                    }
                    $_str = null;
                } else {
                    $where[] = $key . '=\'' . $value . '\'';
                }
            }
        } else {
            $where[] = $filter;
        }
        return implode($where, ' AND ');
    }

    /**
     * join条件转换工具 
     * 例： array(array('left_join'=>'test t','on'=>'t.id=tt.tid'))
     * @param array $join join数组
     * @return string
     */
    public function parseJoin($join) {
        if (is_array($join)) {
            $result = array();
            foreach ($join as $key => $value) {
                $result[] = $this->_getJoin($key) . $value;
            }
            return implode(' ', $result);
        } else {
            return $join;
        }
    }

    //过滤表达式转sql
    private function _getFilterType($type, $value) {
        $filterType = array(
            'gthan' => ' > \'' . $value . '\'',
            'lthan' => ' < \'' . $value . '\'',
            'equal' => ' = \'' . $value . '\'',
            'notequal' => ' <> \'' . $value . '\'',
            'lethan' => ' <= \'' . $value . '\'',
            'gethan' => ' >= \'' . $value . '\'',
            'like' => ' LIKE \'%' . $value . '%\'',
            'llike' => ' LIKE \'' . $value . '%\'',
            'rlike' => ' LIKE \'%' . $value . '\'',
            'notlike' => ' NOT LIKE \'%' . $value . '%\'',
            'between' => ' {col}>=\'' . $value[0] . '\' AND ' . ' {col}<=\'' . $value[1] . '\'',
            'in' => " IN ('" . implode("','", (array) $value) . "') ",
            'notin' => " NOT IN ('" . implode("','", (array) $value) . "') ",
        );
        return $filterType[$type];
    }

    private function _getJoin($key) {
        $joinKey = array(
            'left_join' => ' LEFT JOIN ',
            'right_join' => ' RIGHT JOIN ',
            'join' => ' JOIN ',
        );
        return $joinKey[$key];
    }

    /**
     * 通用的获取列表的方法 功能更全
     * 
     * @param array $param 条件  例： $param = array(
     * 		'page'   => $page,
     * 		'items'  => 10,
     * 		'relate' => 'thumbnail,level',
     * 		'with'   => 'districts',
     * 		'order'  => 'landscapes.updated_at DESC,landscapes.created_at DESC,landscapes.id DESC',
     * 		'join'   => array(array('join'=>' test on id=test.id')),
     * 		'group'  => 'id',
     * 		'fields' => '*',
     * );
     * @return array 包含数据和分页
     */
    public function commonGetList($param = '') {
        $limit = $order = $join = $group = '';

        if (isset($param['join'])) {
            $join = $param['join'];
        }

        //offset
        if (isset($param['items'])) {
            $items = $param['items'];
            $limit = ($param['page'] - 1) * $param['items'] . ',' . $param['items'];
        } else {
            $items = 15;
        }

        //排序
        if (isset($param['order'])) {
            $order = $param['order'];
        }

        //group by
        if (isset($param['group'])) {
            $group = $param['group'];
        }

        //字段
        if (isset($param['fields'])) {
            $fields = $param['fields'];
        } else {
            $fields = $this->table . '.*';
        }

        $filter = $param['filter'];
        $result['data'] = $this->getListExtension($filter, $limit, $order, $fields, $group, $join);

        //假如要查关联的表的数据
        if (isset($param['relate']) && $result['data']) {
            $result['data'] = $this->getListRelate($result['data'], $param['relate']);
        }

        //假如要查with数据
        if (isset($param['with']) && $result['data']) {
            $result['data'] = $this->getListWith($result['data'], $param['with']);
        }
        $count = $this->getListExtension($filter, '', '', 'count(DISTINCT(' . $this->table . '.id)) as total', '', $join);
        $result['pagination'] = array(
            'items' => $items,
            'count' => $count[0]['total'],
        );
        return $result;
    }

    /**
     * 通用的获取数据的方法 功能更全,不带分页
     * 
     * @param array $param 条件  例： $param = array(
     * 		'relate' => 'thumbnail,level',
     * 		'with'   => 'districts',
     * 		'order'  => 'landscapes.updated_at DESC,landscapes.created_at DESC,landscapes.id DESC',
     * 		'join'   => array(array('join'=>' test on id=test.id')),
     * 		'group'  => 'id',
     * 		'fields' => '*',
     * );
     * @return array 包含查询后所有数据
     */
    public function commonGetAll($param = '') {
        $limit = $order = $join = $group = '';

        if (isset($param['join'])) {
            $join = $param['join'];
        }

        //排序
        if (isset($param['order'])) {
            $order = $param['order'];
        }

        //group by
        if (isset($param['group'])) {
            $group = $param['group'];
        }

        //字段
        if (isset($param['fields'])) {
            $fields = $param['fields'];
        } else {
            $fields = $this->table . '.*';
        }

        $filter = $param['filter'];
        $result = $this->getListExtension($filter, $limit, $order, $fields, $group, $join);

        //假如要查关联的表的数据
        if (isset($param['relate']) && $result) {
            $result = $this->getListRelate($result, $param['relate']);
        }

        //假如要查with数据
        if (isset($param['with']) && $result) {
            $result = $this->getListWith($result, $param['with']);
        }
        return $result;
    }

    /**
     * 过滤地区 获取指定地区下的过滤条件  
     * PS:此方法只适合用于改造过的getListExtension或者最终用到parseFilter解析filter的方法
     * 
     * @param array $filter 已有的过滤条件
     * @param string $field 字段名称
     * @param int $distictId 地区id
     * @return array
     */
    public function getDistrictDeepChildFilter($filter, $field, $distictId) {
        if ($distictId == 0) {
            $distictId = '000000';
        }

        $ids = str_split($distictId, 2);
        $max = array();
        $min = array();

        foreach ($ids as $key => $value) {
            if ($value === '00') {
                $max[$key] = '99';
                $min[$key] = '00';
            } else {
                $max[$key] = $value;
                $min[$key] = $value;
            }
        }

        $max = implode('', $max);
        $min = implode('', $min);
        $filter[$field . '|lethan'] = $max;
        $filter[$field . '|gethan'] = $min;
        return $filter;
    }
    
    /* (non-PHPdoc)
     * @see Model::update()
     */
    public function update($array, $where, $order = '', $limit = '') {
		//设定更新时间
		if(!array_key_exists('updated_at', $array) && $this->columnExist('updated_at') !== false) {
			$array['updated_at'] = currentDateTime();
		}

		return parent::update($array, $where, $order, $limit);
    }

	public function strictDelete($where) {
		if (!empty($where)) {
			$sql = 'delete from `'.$this->table.'`';
			$sql .= ' where '.parent::_where($where);
			return $this->query($sql);
		}
	}

    /**
     * 生成同步用的HASH值
     * @param unknown_type $serialno 
     * @return HASH值
     */
    public function generateSyncID($serialno) {
		list($usec, $sec) = explode(' ', microtime());
		$mtrand = (float) $sec + ((float) $usec * 100000);;
		mt_srand($mtrand);
		$randval = mt_rand(1000,9000);
		$platform = getenv('FX_REMOTE');
		$platform = !empty($platform)?$platform:PlatformCommon::PLATFORM_LOCAL;
		return md5($serialno.'|'.$platform.microtime().$randval);
    }
}
