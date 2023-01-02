<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Test;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\NationalController;
use App\Http\Controllers\ZonalController;
use App\Http\Controllers\ClusterController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ApiControllers;
use App\Http\Controllers\SnapshotController;
use App\Http\Controllers\DataProcessingController;
use App\Http\Controllers\CodeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/
//Route::resource('/','Test@index');
//Route::resource('/test', 'App\Http\Controllers\Test');
//Route::resource('users', 'App\Http\Controllers\Test', ['except' => ['index']]);
//Route::resource('users', Test::class, ['except' => ['Login']]);
//Route::get('/', [Test::class, 'Login']);
Route::get('/form', function () {
  return view('backend.form');
});
Route::get('/', function () {
  return redirect('https://trendx.brac.net/home');
});

Route::get('/clear-cache', function () {
  $exitCode = Artisan::call('cache:clear');
  // return what you want
});
Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);
//login route
Route::get('/weblogin', [LoginController::class, 'weblogin']);
Route::get('/Logout', [LoginController::class, 'Logout']);
//event routes
Route::get('/EventCreate', [EventController::class, 'index']);
Route::get('/editOngoingEvent/{id}', [EventController::class, 'editOngoingEvent']);
Route::get('/editUpcomingEvent/{id}', [EventController::class, 'editUpcomingEvent']);
Route::get('/editOngoingEvent/fetch/{id}', [EventController::class, 'fetch']);
Route::get('/deleteEvent/{id}', [EventController::class, 'delete']);
Route::get('/editUpcomingEvent/fetch/{id}', [EventController::class, 'fetch']);
Route::get('/fetch/{id}', [EventController::class, 'fetch']);
Route::post('/EventStore', [EventController::class, 'store'])->name('event.store');
Route::post('/EventUpdate', [EventController::class, 'EventUpdate'])->name('event.update');
Route::get('/Ongoing', [EventController::class, 'ongoingEvent'])->name('event.ongoing');
Route::get('/ongingEventDataLoad', [EventController::class, 'ongingEventDataLoad'])->name('event.ongoingdata');
Route::get('/Upcoming', [EventController::class, 'upcomingEvent'])->name('event.upcoming');
Route::get('/upcomingEventDataLoad', [EventController::class, 'upcomingEventDataLoad'])->name('event.upcomingdata');
Route::get('/Closed', [EventController::class, 'closedEvent'])->name('event.closed');
Route::get('/closedEventDataLoad', [EventController::class, 'closedEventDataLoad'])->name('event.closeddata');
//user routes
Route::get('/UserList', [UserController::class, 'index']);
Route::get('/UserCreate', [UserController::class, 'create']);
Route::post('/UserCreateStore', [UserController::class, 'store']);
Route::get('/UserLoad', [UserController::class, 'userList']);
Route::get('UserEdit', [UserController::class, 'UserEdit']);
Route::get('UserDelete', [UserController::class, 'UserDelete']);
Route::post('UserEditStore', [UserController::class, 'UserEditStore']);

//Area Controller
Route::get('/AreaDashboard', [AreaController::class, 'Area_Dashboard']);
Route::get('/AreaAllPreviousView', [AreaController::class, 'All_PreviousDataView']);
Route::get('/AreaAllPrevious', [AreaController::class, 'AllPrevious']);
Route::post('/Area/Period', [AreaController::class, 'Period']);

//Region Controller
Route::get('/RegionDashboard', [RegionController::class, 'Region_Dashboard']);
Route::get('/RegionAllPreviousView', [RegionController::class, 'All_PreviousDataView']);
Route::get('/RegionAllPrevious', [RegionController::class, 'AllPrevious']);
Route::post('/Region/Period', [RegionController::class, 'Period']);
Route::POST('/Quarter', [RegionController::class, 'quarter']);
Route::get('RegionSearch', [RegionController::class, 'Region_Search']);

//Division controller 
Route::get('DivisionDashboard', [DivisionController::class, 'DDashboard']);
Route::get('RegionWise', [DivisionController::class, 'RegionWise']);
Route::get('monthRegionWise', [DivisionController::class, 'MonthWiseRegion']);
Route::get('DivisionAllPreviousView', [DivisionController::class, 'DivisionAllPreviousView']);
Route::get('DivisionPreviousData', [DivisionController::class, 'DivisionPreviousData']);
Route::get('DivisionSearch', [DivisionController::class, 'Division_Search']);

// Manager Dashboard
Route::get('ManagerDashboard', [ManagerController::class, 'ManagerDashboard']);
Route::POST('BranchData', [ManagerController::class, 'Branch_Data']);
Route::get('MSectionDetails', [ManagerController::class, 'SectionDetails']);
Route::get('Remarks', [ManagerController::class, 'Remarks']);
Route::get('frauddocuments', [ManagerController::class, 'frauddocuments']);
//National Dashboard
Route::get('NationalDashboard', [NationalController::class, 'dashboard']);
Route::get('DivisionWiseAc', [NationalController::class, 'DivisionWise']);
Route::get('monthDivisionWise', [NationalController::class, 'MonthDivisionWise']);
Route::get('NationalAllPreviousView', [NationalController::class, 'NationalAllPreviousView']);
Route::get('NationalPreviousData', [NationalController::class, 'NationalPreviousData']);
Route::get('GlobalReport', [NationalController::class, 'GlobalReport']);
Route::get('ClusterSelect', [NationalController::class, 'ClusterDash']);
Route::get('Cluster', [NationalController::class, 'ClusterView']);
Route::get('ClusterAdd', [NationalController::class, 'Cluster_Add']);
Route::get('ClusterAddAccId', [NationalController::class, 'Cluster_Asc_Id_Add']);
Route::post('ClusterAdd', [NationalController::class, 'Cluster_Store']);
Route::post('ClusterAddAccId', [NationalController::class, 'Cluster_Asc_Id_Store']);
Route::get('editCluster/{id}', [NationalController::class, 'Cluster_Edit']);
Route::get('deleteCluster/{id}', [NationalController::class, 'Cluster_Delete']);
Route::post('ClusterUpdate', [NationalController::class, 'Cluster_Update']);
Route::get('ZonalSelect', [NationalController::class, 'ZonalDash']);
Route::get('Zonal', [NationalController::class, 'ZonalView']);
Route::get('ZonalAdd', [NationalController::class, 'Zonal_Add']);
Route::get('editZonal/{id}', [NationalController::class, 'Zonal_Edit']);
Route::post('ZonalUpdate', [NationalController::class, 'Zonal_Update']);
Route::get('deleteZonal/{id}', [NationalController::class, 'Zonal_Delete']);
Route::post('ZonalAdd', [NationalController::class, 'Zonal_Store']);
Route::get('zonalDataLoad', [NationalController::class, 'zonalDataLoad']);
Route::get('Cluster/Excel', [NationalController::class, 'excelCluster']);
Route::get('Cluster/PDF', [NationalController::class, 'pdfCluster']);
Route::get('Cluster/Print', [NationalController::class, 'printCluster']);

//Export Controller
Route::get('Export', [ExportController::class, 'Export']);
Route::post('quarter', [ExportController::class, 'quarter']);
Route::post('period', [ExportController::class, 'period']);

// Cluster Dashboard
Route::get('DataLoad', [ClusterController::class, 'DataInsert']);
Route::get('ClDashboard', [ClusterController::class, 'ClusterView']);
Route::get('ClusterSearch', [ClusterController::class, 'Cluster_Search']);
Route::POST('Division', [ClusterController::class, 'Division_Search']);
Route::get('ClusterAllPreviousView', [ClusterController::class, 'All_PreviousDataView']);
Route::get('ClusterAllPreviousData', [ClusterController::class, 'AllPrevious']);

//Zonal Dashboard
Route::get('ZDataLoad', [ZonalController::class, 'ZDataInsert']);
Route::get('ZonalDashboard', [ZonalController::class, 'ZonalDashboard']);
Route::get('ZonalAllPreviousView', [ZonalController::class, 'All_PreviousDataView']);
Route::get('ZonalAllPreviousData', [ZonalController::class, 'AllPrevious']);

//Json 
Route::POST('RegionData', [ManagerController::class, 'Region_Data']);
Route::POST('AreaData', [ManagerController::class, 'Area_Data']);
Route::post('ClusterData', [NationalController::class, 'GetCluster']);
Route::get('/api', [SnapshotController::class, 'sync']);
Route::get('/dataprocess', [DataProcessingController::class, 'DataProcess']);
Route::get('/checkcode', [CodeController::class, 'DataProcess']);
Route::get('/checkcode1', [CodeController::class, 'Mnwsection3']);
Route::get('SectionDetails', [RegionController::class, 'SectionDetails']);
Route::get('monthlySectionDetails', [RegionController::class, 'monthlySectionDetails']);
Route::get('areawise', [NationalController::class, 'areawise']);
Route::get('monthlyareawise', [NationalController::class, 'monthlyareawise']);
Route::post('transection', [SnapshotController::class, 'Transections']);
Route::post('colist', [SnapshotController::class, 'COLIST']);
Route::post('SampleRespondent', [SnapshotController::class, 'Get_Respondents']);
Route::post('respondents', [ApiControllers::class, 'respondent']);
Route::post('Login', [LoginController::class, 'login']);
Route::post('download', [ApiControllers::class, 'Download_data']);
Route::post('/survey_data', [ApiControllers::class, 'survey_data']);
Route::post('/changemonitor', [LoginController::class, 'changemonitor']);
Route::post('/Delete', [ApiControllers::class, 'Delete_ALl']);
Route::post('/DeleteSnapshot', [ApiControllers::class, 'DeleteSnapshot']);
Route::post('/ClosedLoan', [SnapshotController::class, 'ClosedLoan']);
Route::post('/ChangePassword', [ApiControllers::class, 'changepassword']);
Route::post('/MemberList', [SnapshotController::class, 'MemberList']);
Route::post('/BranchCode', [SnapshotController::class, 'BranchCode']);
Route::post('CLoans', [SnapshotController::class, 'CLoans']);
Route::post('/DeleteRespondents', [ApiControllers::class, 'DeleteRespondents']);
