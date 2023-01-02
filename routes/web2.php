<?php

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
Route::get('/form',function(){
    return view('backend.form');
  });
Route::get('/', function () {
  return redirect('https://trendx.brac.net/home');
});

Route::get('/clear-cache', function() {
  $exitCode = Artisan::call('cache:clear');
  // return what you want
});

//login route
Route::get('/weblogin', 'LoginController@weblogin');
Route::get('/Logout', 'LoginController@Logout');

//event routes
Route::get('/EventCreate','EventController@index');
Route::get('/editOngoingEvent/{id}','EventController@editOngoingEvent');
Route::get('/editUpcomingEvent/{id}','EventController@editUpcomingEvent');
Route::get('/editOngoingEvent/fetch/{id}','EventController@fetch');
Route::get('/deleteEvent/{id}','EventController@delete');
Route::get('/editUpcomingEvent/fetch/{id}','EventController@fetch');
Route::get('/fetch/{id}','EventController@fetch');
Route::post('/EventStore','EventController@store')->name('event.store');
Route::post('/EventUpdate','EventController@EventUpdate')->name('event.update');
Route::get('/Ongoing','EventController@ongoingEvent')->name('event.ongoing');
Route::get('/ongingEventDataLoad','EventController@ongingEventDataLoad')->name('event.ongoingdata');
Route::get('/Upcoming','EventController@upcomingEvent')->name('event.upcoming');
Route::get('/upcomingEventDataLoad','EventController@upcomingEventDataLoad')->name('event.upcomingdata');
Route::get('/Closed','EventController@closedEvent')->name('event.closed');
Route::get('/closedEventDataLoad','EventController@closedEventDataLoad')->name('event.closeddata');

//user routes
Route::get('/UserList','UserController@index');
Route::get('/UserCreate','UserController@create');
Route::post('/UserCreateStore','UserController@store');
Route::get('/UserLoad','UserController@userList');
Route::get('UserEdit','UserController@UserEdit');
Route::get('UserDelete','UserController@UserDelete');
Route::post('UserEditStore','UserController@UserEditStore');

//Area Controller
Route::get('/AreaDashboard','AreaController@Area_Dashboard');
Route::get('/AreaAllPreviousView','AreaController@All_PreviousDataView');
Route::get('/AreaAllPrevious','AreaController@AllPrevious');
Route::post('/Area/Period','AreaController@Period');

//Region Controller
Route::get('/RegionDashboard','RegionController@Region_Dashboard');
Route::get('/RegionAllPreviousView','RegionController@All_PreviousDataView');
Route::get('/RegionAllPrevious','RegionController@AllPrevious');
Route::post('/Region/Period','RegionController@Period');
Route::POST('/Quarter','RegionController@quarter');
Route::get('RegionSearch','RegionController@Region_Search');

//Division controller 
Route::get('DivisionDashboard','DivisionController@DDashboard');
Route::get('RegionWise','DivisionController@RegionWise');
Route::get('monthRegionWise','DivisionController@MonthWiseRegion');
Route::get('DivisionAllPreviousView','DivisionController@DivisionAllPreviousView');
Route::get('DivisionPreviousData','DivisionController@DivisionPreviousData');
Route::get('DivisionSearch','DivisionController@Division_Search');

// Manager Dashboard
Route::get('ManagerDashboard','ManagerController@ManagerDashboard');
Route::POST('BranchData','ManagerController@Branch_Data');
Route::get('MSectionDetails','ManagerController@SectionDetails');
Route::get('Remarks','ManagerController@Remarks');
Route::get('frauddocuments','ManagerController@frauddocuments');

//National Dashboard
Route::get('NationalDashboard','NationalController@dashboard');
Route::get('DivisionWiseAc','NationalController@DivisionWise');
Route::get('monthDivisionWise','NationalController@MonthDivisionWise');
Route::get('NationalAllPreviousView','NationalController@NationalAllPreviousView');
Route::get('NationalPreviousData','NationalController@NationalPreviousData');
Route::get('GlobalReport','NationalController@GlobalReport');
Route::get('ClusterSelect','NationalController@ClusterDash');
Route::get('Cluster','NationalController@ClusterView');
Route::get('ClusterAdd','NationalController@Cluster_Add');
Route::get('ClusterAddAccId','NationalController@Cluster_Asc_Id_Add');
Route::post('ClusterAdd','NationalController@Cluster_Store');
Route::post('ClusterAddAccId','NationalController@Cluster_Asc_Id_Store');
Route::get('editCluster/{id}','NationalController@Cluster_Edit');
Route::get('deleteCluster/{id}','NationalController@Cluster_Delete');
Route::post('ClusterUpdate','NationalController@Cluster_Update');
Route::get('ZonalSelect','NationalController@ZonalDash');
Route::get('Zonal','NationalController@ZonalView');
Route::get('ZonalAdd','NationalController@Zonal_Add');
Route::get('editZonal/{id}','NationalController@Zonal_Edit');
Route::post('ZonalUpdate','NationalController@Zonal_Update');
Route::get('deleteZonal/{id}','NationalController@Zonal_Delete');
Route::post('ZonalAdd','NationalController@Zonal_Store');
Route::get('zonalDataLoad','NationalController@zonalDataLoad');
Route::get('Cluster/Excel','NationalController@excelCluster');
Route::get('Cluster/PDF','NationalController@pdfCluster');
Route::get('Cluster/Print','NationalController@printCluster');

//Export Controller
Route::get('Export','ExportController@Export');
Route::post('quarter','ExportController@quarter');
Route::post('period','ExportController@period');

// Cluster Dashboard
Route::get('DataLoad','ClusterController@DataInsert');
Route::get('ClDashboard','ClusterController@ClusterView');
Route::get('ClusterSearch','ClusterController@Cluster_Search');
Route::POST('Division','ClusterController@Division_Search');
Route::get('ClusterAllPreviousView','ClusterController@All_PreviousDataView');
Route::get('ClusterAllPreviousData','ClusterController@AllPrevious');

//Zonal Dashboard
Route::get('ZDataLoad','ZonalController@ZDataInsert');
Route::get('ZonalDashboard','ZonalController@ZonalDashboard');
Route::get('ZonalAllPreviousView','ZonalController@All_PreviousDataView');
Route::get('ZonalAllPreviousData','ZonalController@AllPrevious');

//Json 
Route::POST('RegionData','ManagerController@Region_Data');
Route::POST('AreaData','ManagerController@Area_Data');
Route::post('ClusterData','NationalController@GetCluster');
Route::get('/api','SnapshotController@sync');
Route::get('/dataprocess','DataProcessingController@DataProcess');
Route::get('SectionDetails','RegionController@SectionDetails');
Route::get('monthlySectionDetails','RegionController@monthlySectionDetails');
Route::get('areawise','NationalController@areawise');
Route::get('monthlyareawise','NationalController@monthlyareawise');
Route::post('transection','SnapshotController@Transections');
Route::post('colist','SnapshotController@COLIST');
Route::post('SampleRespondent','SnapshotController@Get_Respondents');
Route::post('respondents','ApiControllers@respondent');
Route::post('Login','LoginController@login');
Route::post('download','ApiControllers@Download_data');
Route::post('/survey_data','ApiControllers@survey_data');
Route::post('/changemonitor','LoginController@changemonitor');
Route::post('/Delete','ApiControllers@Delete_ALl');
Route::post('/ClosedLoan','SnapshotController@ClosedLoan');
Route::post('/ChangePassword','ApiControllers@changepassword');
Route::post('/MemberList','SnapshotController@MemberList');
Route::post('/BranchCode','SnapshotController@BranchCode');
Route::post('CLoans','SnapshotController@CLoans');
Route::post('/DeleteRespondents','ApiControllers@DeleteRespondents');
