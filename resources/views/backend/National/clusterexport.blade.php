<?php

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


$helper = new Sample();
if ($helper->isCli()) {
    $helper->log('This example should only be run from a Web Browser' . PHP_EOL);

    return;
}
// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();

// Set document properties
$spreadsheet->getProperties()->setCreator('Q-Soft')
    ->setLastModifiedBy('Q-Soft')
    ->setTitle('Office 2007 XLSX')
    ->setSubject('Office 2007 XLSX')
    ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
    ->setKeywords('office 2007 openxml php')
    ->setCategory('Test result file');

// Table Header
$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1', 'Cluster ID')
    ->setCellValue('B1', 'Cluster Name')
    ->setCellValue('C1', 'Branch Code')
    ->setCellValue('D1', 'Branch Name')
    ->setCellValue('E1', 'Area Name')
    ->setCellValue('F1', 'Region Name')
    ->setCellValue('G1', 'Division Name')
    ->setCellValue('H1', 'Zonal Code')
    ->setCellValue('I1', 'Zonal Name');

// Table Data
$clusterdata =DB::table('mnw_progoti.cluster')->orderBy('cluster_id','ASC')->get();
$key=2;

foreach ($clusterdata as $sr=>$value) {$array=[];
    $zonal=DB::table('mnw_progoti.zonal')->where('zonal_code',$value->zonal_code)->first();
    $area_id=$value->area_id;
    $branch_ary=DB::table('branch')->where('area_id',$area_id)->where('program_id',5)->get();
    $branch_count=$branch_ary->count();
    foreach ($branch_ary as $serial => $branch) {
        $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A'.$key, $value->cluster_id)
        ->setCellValue('B'.$key, $value->cluster_name)
        ->setCellValue('C'.$key, $branch->branch_id)
        ->setCellValue('D'.$key, $branch->branch_name)
        ->setCellValue('E'.$key, $value->area_name)
        ->setCellValue('F'.$key, $value->region_name)
        ->setCellValue('G'.$key, $value->division_name)
        ->setCellValue('H'.$key, $value->zonal_code)
        ->setCellValue('I'.$key, $zonal->zonal_name);
        $array[]='A'.$key;
        $key++;
    }
}
//table marge
$key=2;
foreach ($clusterdata as $sr=>$value) {
    $array=[];
    $zonal=DB::table('mnw_progoti.zonal')->where('zonal_code',$value->zonal_code)->first();
    $area_id=$value->area_id;
    $branch_ary=DB::table('branch')->where('area_id',$area_id)->where('program_id',5)->get();
    $branch_count=$branch_ary->count();
    foreach ($branch_ary as $serial => $branch) {
        $array['A'][]='A'.$key;
        $array['B'][]='B'.$key;
        $array['E'][]='E'.$key;
        $array['F'][]='F'.$key;
        $array['G'][]='G'.$key;
        $array['H'][]='H'.$key;
        $array['I'][]='I'.$key;
        $key++;
    }
    $fa = reset($array['A']);
    $la = end($array['A']);

    $fb = reset($array['B']);
    $lb = end($array['B']);

    $fe = reset($array['E']);
    $le = end($array['E']);
    
    $ff = reset($array['F']);
    $lf = end($array['F']);

    $fg = reset($array['G']);
    $lg = end($array['G']);

    $fh = reset($array['H']);
    $lh = end($array['H']);

    $fi = reset($array['I']);
    $li = end($array['I']);

    $mergeCellA=$fa.":".$la;
    $mergeCellB=$fb.":".$lb;
    $mergeCellE=$fe.":".$le;
    $mergeCellF=$ff.":".$lf;
    $mergeCellG=$fg.":".$lg;
    $mergeCellH=$fh.":".$lh;
    $mergeCellI=$fi.":".$li;
    $spreadsheet->setActiveSheetIndex(0)->mergeCells($mergeCellA);
    $spreadsheet->setActiveSheetIndex(0)->mergeCells($mergeCellB);
    $spreadsheet->setActiveSheetIndex(0)->mergeCells($mergeCellE);
    $spreadsheet->setActiveSheetIndex(0)->mergeCells($mergeCellF);
    $spreadsheet->setActiveSheetIndex(0)->mergeCells($mergeCellG);
    $spreadsheet->setActiveSheetIndex(0)->mergeCells($mergeCellH);
    $spreadsheet->setActiveSheetIndex(0)->mergeCells($mergeCellI);
}
// $spreadsheet->setActiveSheetIndex(0)->mergeCells('A2:A6');
//table header width
$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('A')->setWidth(15);
$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('B')->setWidth(15);
$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('C')->setWidth(15);
$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('D')->setWidth(15);
$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('E')->setWidth(15);
$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('F')->setWidth(15);
$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('G')->setWidth(15);
$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('H')->setWidth(15);
$spreadsheet->setActiveSheetIndex(0)->getColumnDimension('I')->setWidth(15);

// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('Clusters');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Clusterlist.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');
exit;