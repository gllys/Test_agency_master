<?php

/**
 * Created by PhpStorm.
 * User: zqf
 * Date: 15-4-29
 * Time: 下午1:00
 */
class SalestatController extends Base_Controller_Api
{
    private function agencyOfSupplier($supply_id)
    { //获取供应商的合作分销商
        if (empty($supply_id)) return array();
        $agency_ids = array();
        $agencyArr = OrganizationModel::model()->bySupplier($supply_id, 'distributor_id,supplier_id'); //合作分销商
        if (!empty($agencyArr)) {
            foreach ($agencyArr as $v) {
                $agency_ids[] = $v['distributor_id'];
            }
        }
        return $agency_ids;
    }

    //分销商统计详情（降序，可用于线图）
    public function agencyAction()
    {
        $year = intval($this->body['year']);
        $month = intval($this->body['month']); //月
        $supply_id = intval($this->body['supply_id']);
        $agency_id = trim($this->body['agency_id']); //分销商ID，多个逗号分割
        $product_id = trim($this->body['product_id']); //产品ID，多个逗号分割
        $province_id = intval($this->body['province_id']);
        $city_id = intval($this->body['city_id']);
        $district_id = intval($this->body['district_id']);
        $all_agency = intval($this->body['all_agency']); //统计是否包含所有分销商，1是，0否（只限制合作分销商）

        $supply_name = trim($this->body['supply_name']); //供应商名称
        $agency_name = trim($this->body['agency_name']); //分销商名称
        $product_name = trim($this->body['product_name']); //产品名称
        $scenic_id = intval($this->body['scenic_id']); //景区ID
        $scenic_name = trim($this->body['scenic_name']); //景区名称

        $type = intval($this->body['type']); //统计类型：1入园人次，2销售额，3门票销售张数
        $type <= 0 && $type = 1;
        $x_axis_type = intval($this->body['x_axis_type']); //x轴刻度：0月（默认），1日
        $statField = $type == 1 ? 'visitors' : ($type == 2 ? 'amount' : 'nums');
        $axisField = $x_axis_type == 1 ? 'day' : 'month';
        $xAxis = $x_axis_type == 1 ? "month,'-',day" : 'month'; //x轴坐标

        if ($year <= 0) {
            $year = date("Y"); //默认当年
        }
        if (empty($month)) {
            $where = " WHERE datetime>=" . strtotime($year . "-01-01 00:00:00") . " AND datetime<=" . strtotime($year . "-12-31 23:59:59");
        } else {
            $monthStr = str_pad($month, 2, 0, STR_PAD_LEFT);
            $where = " WHERE datetime>=" . strtotime($year . "-" . $monthStr . "-01 00:00:00")
                . " AND datetime<=" . strtotime($year . "-" . $monthStr . "-" . date("t", mktime(0, 0, 0, $month, 1, $year)) . " 23:59:59");
        }

        preg_match("/^[\d,]+$/", $agency_id) && $where .= " AND agency_id IN ({$agency_id})";
        preg_match("/^[\d,]+$/", $product_id) && $where .= " AND product_id IN ({$product_id})";

        $orgs = array();
        if ($province_id > 0 || $city_id > 0 || $district_id > 0) {
            $orgWhere = array('fields' => 'id', 'show_all' => 1);
            $province_id > 0 && $orgWhere['province_id'] = $province_id;
            $city_id > 0 && $orgWhere['city_id'] = $city_id;
            $district_id > 0 && $orgWhere['district_id'] = $district_id;
            $orgs = OrganizationModel::model()->getOrgInfoByAttr($orgWhere);
            if (is_array($orgs) && count($orgs) > 0) {
                $where .= " AND agency_id IN(" . implode(',', array_keys($orgs)) . ")";
            }
        }

        if(preg_match("/\S+/",$supply_name)) {
            $where .= " AND supply_name LIKE '%{$supply_name}%'";
        }
        if(preg_match("/\S+/",$agency_name)) {
            $where .= " AND agency_name LIKE '%{$agency_name}%'";
        }
        if(preg_match("/\S+/",$product_name)) {
            $where .= " AND product_name LIKE '%{$product_name}%'";
        }
        if($scenic_id>0) {
            $where .= " AND FIND_IN_SET(".$scenic_id.",scenic_id)";
        }
        if(preg_match("/\S+/",$scenic_name)) {
            $where .= " AND scenic_name LIKE '%{$scenic_name}%'";
        }

        $SaleStatModel = new SaleStatModel();
        $SaleStatModel->getDb()->exec("SET SESSION  group_concat_max_len=10240;"); //设置group_concat字节限制
        $fields = "SELECT datetime,agency_id,agency_name,month,day,sum({$statField}) as stat";
        $from = " FROM " . $SaleStatModel->getTable();
        $groupBy = " GROUP BY agency_id,{$axisField}";

        $iniStat = array();
        if ($x_axis_type == 1) {
            for ($m = 1; $m <= 12; $m++) { //初始化每个日统计数据
                $days = date("t", mktime(0, 0, 0, $m, 1, $year));
                for ($d = 1; $d <= $days; $d++) {
                    $iniStat[$m . '-' . $d] = 0;
                }
            }
        } else {
            for ($m = 1; $m <= 12; $m++) { //初始化每个月份统计数据
                $iniStat[$m] = 0;
            }
        }

        $unionStatRow = $unionStatData = array(); //平台(非合作)分销商统计
        $unionWhere = $amountWhere = $where; //平台(非合作)分销商统计
        if ($supply_id > 0) {
            $where .= " AND supply_id={$supply_id}";
            $unionWhere = $amountWhere = $where; //平台(非合作)分销商统计

            $agency_ids = $this->agencyOfSupplier($supply_id); //合作分销商
            if (count($agency_ids) > 0) {
                if ($all_agency <= 0) { //统计只限合作分销商
                    $where .= " AND agency_id IN (" . implode(',', $agency_ids) . ")";
                }
                if($agency_id==-1) {
                    $where .= " AND agency_id NOT IN (" . implode(',', $agency_ids) . ")"; //平台(非合作)分销商统计
                }

                $unionWhere .= " AND agency_id NOT IN (" . implode(',', $agency_ids) . ")"; //平台(非合作)分销商统计
                if (($province_id > 0 || $city_id > 0 || $district_id > 0) && empty($orgs)) {
                    $unionStatData = $iniStat;
                    $unionStatData['subtotal'] = 0;
                } else {
                    $unionAmount = $SaleStatModel->db->selectBySql("SELECT sum({$statField}) as stat,concat({$xAxis}) as xaxis " . $from . $unionWhere . " GROUP BY {$axisField}");
                    if (!empty($unionAmount)) {
                        foreach ($unionAmount as $v) {
                            $unionStatData[$v['xaxis'].'_'.$v['stat']] = $v['stat'];
                        }
                    }
                    $unionStatRow = array(
                        'agency_id' => -1,
                        'agency_name' => '平台分销商',
                        'stat' => implode(',',array_keys($unionStatData)),
                        'subtotal' => array_sum($unionStatData),
                    );
                }
            }
        }

        $show_data = isset($this->body['show_data']) ? intval($this->body['show_data']) : 1; //是否显示详情数据，1是（默认），0否
        $show_amount = intval($this->body['show_amount']) > 0 ? 1 : 0; //是否显示总计数据，1是，0否（默认）
        $show_pie = intval($this->body['show_pie']) > 0 ? 1 : 0; //是否显示饼图数据，1是，0否（默认）
        $pieSubAmount = $pieAmount = 0; //当前页总计,查询出的所有数据总计
        $show_stack = intval($this->body['show_stack']) > 0 ? 1 : 0; //是否显示堆叠数据，1是，0否（默认）
        $gridDate = array(); //表格数据

        if ($show_data > 0) {
            if (($province_id > 0 || $city_id > 0 || $district_id > 0) && empty($orgs)) {
                $this->count = 0;
                $this->pagenation();
                $rows = array();
            } else {
                $counts = $SaleStatModel->db->selectBySql("SELECT count(DISTINCT agency_id) as count FROM (" . $fields . $from . $where . $groupBy . " ) t");
                $this->count = !empty($counts) ? reset(reset($counts)) : 0;
                $this->pagenation();
                if (!empty($unionStatData)){
                    if($this->current == 1) {
                        $this->limit = "0,".($this->items-1);
                    } else {
                        $this->limit = ($this->items*($this->current-1)-1).",".$this->items;
                    }
                }
                $limit = " LIMIT " . $this->limit;
                $rows = $this->count ? $SaleStatModel->db->selectBySql("SELECT *,GROUP_CONCAT({$xAxis},'_',stat order by month asc,day asc) as stat, sum(stat) as subtotal FROM (" . $fields . $from . $where . $groupBy . " ) t GROUP BY agency_id ORDER BY subtotal DESC" . $limit) : array();
            }

            if(!empty($unionStatRow)) {
                if($this->current==1) {
                    array_unshift($rows, $unionStatRow);
                }
                $this->count+=1;
            }

            foreach ($rows as $row) {
                $stat = $iniStat;
                $statArr = explode(',', $row['stat']);
                foreach ($statArr as $st) {
                    if(!empty($st)) {
                        $st = explode('_', $st);
                        $stat[$st[0]] = $st[1];
                    }
                }
                $stat['subtotal'] = $row['subtotal'];
                $pieSubAmount += $row['subtotal'];
                array_push($gridDate, array(
                    'agency_id' => $row['agency_id'],
                    'agency_name' => $row['agency_name'],
                    'stat' => $stat,
                ));
            }
            $data['data'] = $gridDate;

            $data['pagination'] = array('count' => $this->count, 'current' => $this->current, 'items' => $this->items, 'total' => $this->total,);
        }

        if ($show_amount > 0) {
            if (($province_id > 0 || $city_id > 0 || $district_id > 0) && empty($orgs)) {
                $this->count = 0;
                $this->pagenation();
                $data['amount'] = $iniStat;
                $data['amount']['total'] = 0;
            } else {
                $data['amount'] = $iniStat;
                $amount = $SaleStatModel->db->selectBySql("SELECT sum({$statField}) as stat,concat({$xAxis}) as xaxis" . $from . $amountWhere . " GROUP BY {$axisField}");
                if (!empty($amount)) {
                    foreach ($amount as $v) {
                        $data['amount'][$v['xaxis']] = $v['stat'];
                    }
                }
                $data['amount']['total'] = $pieAmount = array_sum($data['amount']);
            }
        }

        if ($show_pie > 0) {
            $data['pie'] = array(
                'current' => $pieAmount > 0 ? round($pieSubAmount / $pieAmount, 4) : 0,
                'other' => $pieAmount > 0 ? round(1 - ($pieSubAmount / $pieAmount), 4) : 0,
            );
        }

        if ($show_stack > 0) {
            $data['stack'] = array();
            foreach ($gridDate as $v) {
                array_push($data['stack'], array(
                    'agency_id' => $v['agency_id'],
                    'agency_name' => $v['agency_name'],
                    'percent' => $pieSubAmount > 0 ? round($v['stat']['subtotal'] / $pieSubAmount, 4) : 0,
                ));
            }
        }
        Tools::lsJson(true, 'ok', $data);
    }

    //产品统计详情（降序，可用于线图）
    public function productAction()
    {
        $year = intval($this->body['year']);
        $month = intval($this->body['month']); //月
        $supply_id = intval($this->body['supply_id']);
        $agency_id = trim($this->body['agency_id']); //分销商ID
        $product_id = trim($this->body['product_id']); //产品ID，多个逗号分割

        $supply_name = trim($this->body['supply_name']); //供应商名称
        $agency_name = trim($this->body['agency_name']); //分销商名称
        $product_name = trim($this->body['product_name']); //产品名称
        $scenic_id = intval($this->body['scenic_id']); //景区ID
        $scenic_name = trim($this->body['scenic_name']); //景区名称

        $type = intval($this->body['type']); //统计类型：1入园人次，2销售额，3门票销售张数
        $type <= 0 && $type = 1;
        $x_axis_type = intval($this->body['x_axis_type']); //x轴刻度：0月（默认），1日
        $statField = $type == 1 ? 'visitors' : ($type == 2 ? 'amount' : 'nums');
        $axisField = $x_axis_type == 1 ? 'day' : 'month';
        $xAxis = $x_axis_type == 1 ? "month,'-',day" : 'month'; //x轴坐标

        if ($year <= 0) {
            $year = date("Y"); //默认当年
        }

        if (empty($month)) {
            $where = " WHERE datetime>=" . strtotime($year . "-01-01 00:00:00") . " AND datetime<=" . strtotime($year . "-12-31 23:59:59");
        } else {
            $monthStr = str_pad($month, 2, 0, STR_PAD_LEFT);
            $where = " WHERE datetime>=" . strtotime($year . "-" . $monthStr . "-01 00:00:00")
                . " AND datetime<=" . strtotime($year . "-" . $monthStr . "-" . date("t", mktime(0, 0, 0, $month, 1, $year)) . " 23:59:59");
        }

        preg_match("/^[\d,]+$/", $product_id) && $where .= " AND product_id IN ({$product_id})";

        $agency_id>0 && $where .= " AND agency_id={$agency_id}";

        if ($supply_id > 0) {
            $where .= " AND supply_id={$supply_id}";
            if($agency_id<0) { //平台（非合作）分销商
                $agency_ids = $this->agencyOfSupplier($supply_id); //合作分销商
                if (count($agency_ids) > 0) {
                        $where .= " AND agency_id NOT IN (" . implode(',', $agency_ids) . ")";
                }
            }
        }

        if(preg_match("/\S+/",$supply_name)) {
            $where .= " AND supply_name LIKE '%{$supply_name}%'";
        }
        if(preg_match("/\S+/",$agency_name)) {
            $where .= " AND agency_name LIKE '%{$agency_name}%'";
        }
        if(preg_match("/\S+/",$product_name)) {
            $where .= " AND product_name LIKE '%{$product_name}%'";
        }
        if($scenic_id>0) {
            $where .= " AND FIND_IN_SET(".$scenic_id.",scenic_id)";
        }
        if(preg_match("/\S+/",$scenic_name)) {
            $where .= " AND scenic_name LIKE '%{$scenic_name}%'";
        }

        $SaleStatModel = new SaleStatModel();
        $SaleStatModel->getDb()->exec("SET SESSION  group_concat_max_len=10240;"); //设置group_concat字节限制
        $fields = "SELECT datetime,product_id,product_name,month,day,sum({$statField}) as stat";
        $from = " FROM " . $SaleStatModel->getTable();
        $groupBy = " GROUP BY product_id,{$axisField}";

        $iniStat = array();
        if ($x_axis_type == 1) {
            for ($m = 1; $m <= 12; $m++) { //初始化每个日统计数据
                $days = date("t", mktime(0, 0, 0, $m, 1, $year));
                for ($d = 1; $d <= $days; $d++) {
                    $iniStat[$m . '-' . $d] = 0;
                }
            }
        } else {
            for ($m = 1; $m <= 12; $m++) { //初始化每个月份统计数据
                $iniStat[$m] = 0;
            }
        }

        $show_data = isset($this->body['show_data']) ? intval($this->body['show_data']) : 1; //是否显示详情数据，1是（默认），0否
        $show_amount = intval($this->body['show_amount']) > 0 ? 1 : 0; //是否显示总计数据，1是，0否（默认）
        $show_pie = intval($this->body['show_pie']) > 0 ? 1 : 0; //是否显示饼图数据，1是，0否（默认）
        $pieSubAmount = $pieAmount = 0; //当前页总计,查询出的所有数据总计
        $show_stack = intval($this->body['show_stack']) > 0 ? 1 : 0; //是否显示堆叠数据，1是，0否（默认）
        $gridDate = array(); //表格数据

        if ($show_data > 0) {
            $counts = $SaleStatModel->db->selectBySql("SELECT count(DISTINCT product_id) as count FROM (" . $fields . $from . $where . $groupBy . " ) t");
            $this->count = !empty($counts) ? reset(reset($counts)) : 0;
            $this->pagenation();
            $limit = " LIMIT " . $this->limit;
            $rows = $this->count ? $SaleStatModel->db->selectBySql("SELECT *,GROUP_CONCAT({$xAxis},'_',stat order by month asc,day asc) as stat, sum(stat) as subtotal FROM (" . $fields . $from . $where . $groupBy . " ) t GROUP BY product_id ORDER BY subtotal DESC" . $limit) : array();

            foreach ($rows as $row) {
                $stat = $iniStat;
                $statArr = explode(',', $row['stat']);
                foreach ($statArr as $st) {
                    if(!empty($st)) {
                        $st = explode('_', $st);
                        $stat[$st[0]] = $st[1];
                    }
                }
                $stat['subtotal'] = $row['subtotal'];
                $pieSubAmount += $row['subtotal'];
                array_push($gridDate, array(
                    'product_id' => $row['product_id'],
                    'product_name' => $row['product_name'],
                    'stat' => $stat,
                ));
            }
            $data['data'] = $gridDate;

            $data['pagination'] = array('count' => $this->count, 'current' => $this->current, 'items' => $this->items, 'total' => $this->total,);
        }

        if ($show_amount > 0) {
            $data['amount'] = $iniStat;
            $amount = $SaleStatModel->db->selectBySql("SELECT sum({$statField}) as stat,concat({$xAxis}) as xaxis" . $from . $where . " GROUP BY {$axisField}");
            if (!empty($amount)) {
                foreach ($amount as $v) {
                    $data['amount'][$v['xaxis']] = $v['stat'];
                }
            }
            $data['amount']['total'] = $pieAmount = array_sum($data['amount']);
        }

        if ($show_pie > 0) {
            $data['pie'] = array(
                'current' => $pieAmount > 0 ? round($pieSubAmount / $pieAmount, 4) : 0,
                'other' => $pieAmount > 0 ? round(1 - ($pieSubAmount / $pieAmount), 4) : 0,
            );
        }

        if ($show_stack > 0) {
            $data['stack'] = array();
            foreach ($gridDate as $v) {
                array_push($data['stack'], array(
                    'product_id' => $v['product_id'],
                    'product_name' => $v['product_name'],
                    'percent' => $pieSubAmount > 0 ? round($v['stat']['subtotal'] / $pieSubAmount, 4) : 0,
                ));
            }
        }
        Tools::lsJson(true, 'ok', $data);
    }
}