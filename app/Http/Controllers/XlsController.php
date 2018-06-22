<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\XlsData;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;

class XlsController extends Controller
{
    const KEYS = array('Фамилия', 'Имя', 'Отчество', 'Год рождения', 'Должность', 'Зп. в год');

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $docTitles = XlsData::distinct()->pluck('filename');
        $cuttedKeys = $this->getActualKeys();

        return response()->json(['titles' => $docTitles, 'keys' => $cuttedKeys]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }


    public function getActualKeys(){
        $DBkeys = DB::connection()->getSchemaBuilder()->getColumnListing('xls_datas');

        return array_slice($DBkeys, 1, -2);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $this->validate($request, array(
            'fl' => 'required ',
        ));
        $uploadedFile = $request->fl;
        $fileExt = $uploadedFile->getClientOriginalExtension();
        $fileName = $uploadedFile->getClientOriginalName();
        $alreadyExistedFilenames = (array) XlsData::distinct('filename')->pluck('filename');
        if(!in_array($fileName, array_values($alreadyExistedFilenames)[0])){
            if(in_array($fileExt, ['xls', 'xlsx', ]) ){
                $cuttedKeys = $this->getActualKeys();
                $reader = IOFactory::createReader(ucfirst($fileExt));
                $spreadsheet = $reader->load($uploadedFile)->getActiveSheet()->toArray();
                $dataOnlyArray = array_slice($spreadsheet, 1);
                $bulkCreationArr = [];
                foreach ($dataOnlyArray as $element){
                    $element[] =  $fileName;
                    $element[5] = trim(explode('грн', $element[5])[0]);
                    $bulkCreationArr[] = array_combine($cuttedKeys, $element);
                }
                XlsData::insert($bulkCreationArr);

                return response()->json(['statusText' => 'Загрузка произошла успешно', 'class' => 'alert-success', 'code' => 1,]);
            }
        };

        return response()->json(['statusText' => 'Загруженный файл уже существует в системе, слишком велик или не является файлом Excel','class' => 'alert-danger', 'code' => 0,]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $filename
     * @return \Illuminate\Http\Response
     */
    public function show($filename)
    {
        $dataSet = XlsData::where('filename', $filename)->get();

        return $dataSet;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{
        $row = XlsData::find($id);
        $newSet = $request->row;
        $row->fill($newSet)->save();
        return response()->json(['statusText' => 'Запись успешно обновлена','class' => 'alert-success', 'code' => 1,]);
        }catch (Exception $exception){
            return response()->json(['statusText' => 'Запись не обновлена','class' => 'alert-danger', 'code' => 0,]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
        $row = XlsData::find($id);
        $row->delete();
        return response()->json(['statusText' => 'Запись успешно удалена','class' => 'alert-success', 'code' => 1,]);
        }catch(Exception $exception){
            return response()->json(['statusText' => 'Запись не удалена','class' => 'alert-danger', 'code' => 0,]);
        }
    }

    public function loadFile(Request $request){
        $rows = XlsData::where('filename', $request->file)->latest()->get();
        $rowArr[] = XlsController::KEYS;
        foreach($rows as $row){
            $elem = array();
            $elem[] = $row->first_name;
            $elem[] = $row->middle_name;
            $elem[] = $row->surname;
            $elem[] = $row->birth_year;
            $elem[] = $row->occupation;
            $elem[] = $row->annual_salary;
            $rowArr[] = $elem;
            unset($elem);
        }
        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $spreadsheet->setActiveSheetIndex(0);
        $spreadsheet->getActiveSheet()
            ->fromArray(
                $rowArr,  // The data to set
                NULL,        // Array values with this value will not be set
                'A1'         // Top left coordinate of the worksheet range where
            //    we want to set these values (default is A1)
            );
// Rename worksheet
        $spreadsheet->getActiveSheet()->setTitle('Output');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="file.xlsx"');
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
    }

    public function deleteWholeFile($name){
        $fileSet = XlsData::where('filename', $name)->get();
        foreach ($fileSet as $row){
            $row->delete();
        }

        return response()->json(['statusText' => 'Файл успешно удален','class' => 'alert-success', 'code' => 1,]);
    }
//receives
//  first_name
//  middle_name
//  surname
//  birth_year
//  occupation
//  name
    public function storeRow(Request $request){
        $rowData = $request->data;
        $tableName = $request->name;
        $element = new XlsData();
        $element->first_name = $rowData['first_name'];
        $element->middle_name = $rowData['middle_name'];
        $element->surname = $rowData['surname'];
        $element->birth_year = $rowData['birth_year'];
        $element->occupation = $rowData['occupation'];
        $element->filename = $tableName;
        $element->save();

        return response()->json(['statusText' => 'Запись успешно добавлена','class' => 'alert-success', 'code' => 1,]);
    }
}
