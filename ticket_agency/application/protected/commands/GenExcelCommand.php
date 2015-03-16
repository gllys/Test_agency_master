<?php
/**
 * Created by PhpStorm.
 * User: grg
 * Date: 8/28/14
 * Time: 3:07 PM
 */

class GenExcelCommand extends CConsoleCommand {

	public function actionIndex($date_data, $user_type, $user_id) {
		$date_data = intval($date_data);
		$user_type = intval($user_type);
		$user_id   = intval($user_id);
		$get['date-range'] = 'month';
		$get['date-data']  = $date_data;
		if (is_null($get['date-data'])) {
			$get['date-data'] = date('Ym', strtotime('last month'));
		}
		if ($user_type) {
			$monitor = Monitors::model()->getInfo($user_id);
			$pTitle = $monitor . date('Y年n月', strtotime($get['date-data'] . '01')) . '月报汇总表';
		}
		else {
			$scenic = Monitors::model()->getScenic($user_id);
			$pTitle = $scenic . date('Y年n月', strtotime($get['date-data'] . '01')) . '月报汇总表';
		}

		$path = YiiBase::getPathOfAlias('webroot') .'/../files/exports/'.$user_type.'/'. $user_id;
		if (!is_dir($path)) {
			mkdir($path,0755,true);
		}

		$file = $path .'/'. $pTitle .'.xlsx';
		if (is_file($file)) {
			unlink($file);
		}
		if (md5_file($path .'/../../template.xlsx') != 'd0ef0b2eb8f3e4add3fd47cc8831790c') {
			throw new Exception('Template file has been modified!');
		}
		copy($path .'/../../template.xlsx', $file);


		$objExcel = PHPExcel_IOFactory::load($file);
		$excel_properties = $objExcel->getProperties();
		$excel_properties->setCreator("中航工业集团 汇联皆景信息有限公司 监管平台");
		$excel_properties->setLastModifiedBy("中航工业集团 汇联皆景信息有限公司 监管平台");
		$excel_properties->setTitle($pTitle);
		$excel_properties->setSubject($pTitle);
		$excel_properties->setDescription("");
		$excel_properties->setKeywords("");
		$excel_properties->setCategory("报表");
		$excel_properties->setCompany("中航工业集团 汇联皆景信息有限公司");
		$excel_properties->setCreated();
		$excel_properties->setModified();

		$sheet = $objExcel->setActiveSheetIndex(0);
		$sheet->setCellValue('A1', $pTitle);

		list($where, $term, $get) = PassengerData::model()->query_term($get, 'a.', false, $user_type, $user_id);
		list($b_where, $b_term, $b_get) = PassengerData::model()->query_term($get, 'a.', true, $user_type, $user_id);
		$get['date-data']  -= 100; //去年
		list($c_where, $c_term, $c_get) = PassengerData::model()->query_term($get, 'a.', false, $user_type, $user_id);
		list($d_where, $d_term, $d_get) = PassengerData::model()->query_term($get, 'a.', true, $user_type, $user_id);
		$page = 1;
		$step = 4;
		$data = true;
		while ($data) {
			//当期
			$data = PassengerData::model()->report_data($where, $term, $page, $step);
			//触底情况之一
			if (!$data) {
				break;
			}
			//同年累计
			$b_data = PassengerData::model()->report_data($b_where, $b_term, $page, $step);
			//去年同期
			$c_data = PassengerData::model()->report_data($c_where, $c_term, $page, $step);
			//去年累计
			$d_data = PassengerData::model()->report_data($d_where, $d_term, $page, $step);
			$pNumRows = count($data);
			$pRow = $step * ($page - 1) + 4;
			$sheet->insertNewRowBefore($pRow, $pNumRows);
			$sheet->duplicateStyle($objExcel->getActiveSheet()->getStyle('A'.$pRow),'A'.$pRow.':A'.($pRow+$pNumRows-1));
			$source = array();
			foreach($data as $key => $record) {
				$idx = $step * ($page - 1) + 3 + $key;
				$source[] = array(
					$idx - 2,
					$record['scenic'],
					$record['monitor'],
					$record['total_num'],
					isset($c_data[$key])
						? number_format(($record['total_num'] - $c_data[$key]['total_num']) / $c_data[$key]['total_num'] * 100, 2)
						: null,
					$b_data[$key]['total_num'],
					isset($d_data[$key])
						? number_format(($b_data[$key]['total_num'] - $d_data[$key]['total_num']) / $d_data[$key]['total_num'] * 100, 2)
						: null,
					$record['overseas_num'],
					isset($c_data[$key])
						? number_format(($record['overseas_num'] - $c_data[$key]['overseas_num']) / $c_data[$key]['overseas_num'] * 100, 2)
						: null,
					null,
					null,
					$record['income'],
					isset($c_data[$key])
						? number_format(($record['income'] - $c_data[$key]['income']) / $c_data[$key]['income'] * 100, 2)
						: null,
					$b_data[$key]['income'],
					isset($d_data[$key])
						? number_format(($b_data[$key]['income'] - $d_data[$key]['income']) / $d_data[$key]['income'] * 100, 2)
						: null
				);
			}
			unset($key,$record);
			$sheet->fromArray($source, null, 'A'.($pRow-1));

			//触底情况之二
			if ($pRow < $step) {
				break;
			}
			$page += 1;
		}
		$objWriter = PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
		$objWriter->save($file);
		exit;
	}

	/**
	 * @param $scenic_id
	 * @param $date_data
	 * @param $user_type
	 * @param $user_id
	 * @throws Exception
	 * @author grg
	 */
	public function actionExport($scenic_id, $date_data, $user_type, $user_id) {
		$scenic = Monitors::model()->getScenic($scenic_id);
		$pTitle = $scenic . date('Y年n月', strtotime($date_data . '01')) . '月报数据录入表';

		$data = PassengerData::model()->getExportData($scenic_id, $date_data, $user_type, $user_id);
		if ($data !== false) {
			$path = YiiBase::getPathOfAlias('webroot') .'/../files/inputs/'.$user_type.'/'. $user_id;
			if (!is_dir($path)) {
				mkdir($path,0755,true);
			}

			$file = $path .'/'. $pTitle .'.xlsx';
			if (is_file($file)) {
				unlink($file);
			}
			if (md5_file($path .'/../../template.xlsx') != '82f8e976f32ac53b32121307788f253a') {
				throw new Exception('Template file has been modified!');
			}
			copy($path .'/../../template.xlsx', $file);

			$objExcel = PHPExcel_IOFactory::load($file);

			$excel_properties = $objExcel->getProperties();
			$excel_properties->setCreator("中航工业集团 汇联皆景信息有限公司 监管平台");
			$excel_properties->setLastModifiedBy("中航工业集团 汇联皆景信息有限公司 监管平台");
			$excel_properties->setTitle($pTitle);
			$excel_properties->setSubject($pTitle);
			$excel_properties->setDescription("");
			$excel_properties->setKeywords("");
			$excel_properties->setCategory("录入表");
			$excel_properties->setCompany("中航工业集团 汇联皆景信息有限公司");
			$excel_properties->setCreated();
			$excel_properties->setModified();

			$sheet = $objExcel->setActiveSheetIndex(0);
			$sheet->setCellValue('A1', $pTitle);

			if (count($data) === 4) {
				list($result, $detail, $source, $districts) = $data;
				//填充excel，从下往上
				$pRow = 10;
				$pNumRows = count($districts) - 1;
				$sheet->insertNewRowBefore($pRow, $pNumRows);
				$sheet->duplicateStyle($objExcel->getActiveSheet()->getStyle('A'.($pRow-1)),'A'.$pRow.':A'.($pRow+$pNumRows-1));
				$data = array();
				foreach($districts as $record) {
					$data[] = array(
						$record['name'],
						isset($detail['district'][$record['id']]) ? $detail['district'][$record['id']]['num'] : 0
					);
				}
				$sheet->fromArray($data, null, 'A'.($pRow-1));
				unset($data, $record, $districts);

				$pRow = 7;
				$pNumRows = count($source) - 1;
				$sheet->insertNewRowBefore($pRow, $pNumRows);
				$sheet->duplicateStyle($objExcel->getActiveSheet()->getStyle('A'.($pRow-1)),'A'.$pRow.':A'.($pRow+$pNumRows-1));
				$data = array();
				foreach($source as $record) {
					$data[] = array(
						$record['name'],
						isset($detail['type'][$record['id']]) ? $detail['type'][$record['id']]['num'] : 0
					);
				}
				$sheet->fromArray($data, null, 'A'.($pRow-1));
				unset($data, $record, $source);

				$sheet->setCellValue('A3', $result['total_num']);
				$sheet->setCellValue('B3', $result['income']);

			}
			$objWriter = PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
			$objWriter->save($file);
		}
		exit;
	}

	/**
	 * @param $scenic_id
	 * @param $date_data
	 * @param $user_type
	 * @param $user_id
	 * @throws Exception
	 * @author grg
	 */
	public function actionImport($scenic_id, $date_data, $user_type, $user_id) {
		$scenic = Monitors::model()->getScenic($scenic_id);
		$pTitle = $scenic . date('Y年n月', strtotime($date_data . '01')) . '月报数据录入表';

		$path = YiiBase::getPathOfAlias('webroot') . '/../files/inputs/' . $user_type . '/' . $user_id;
		$file = $path . '/' . $pTitle . '_input.xlsx';

		$objExcel = PHPExcel_IOFactory::load($file);

		$row = $objExcel->setActiveSheetIndex(0)->rangeToArray('A1:B100');
		while($row[count($row)-1] == array(null,null)) {
			array_pop($row);
		}
		echo json_encode($row, JSON_UNESCAPED_UNICODE);

		exit;
	}
}


