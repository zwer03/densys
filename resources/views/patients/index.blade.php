@extends('layouts.app')

@section('content')
<script src="{{ asset('js/web_js.js')}}"></script>
<link href="{{URL::asset('css/web_css.css')}}" rel="stylesheet">
<link href="{{asset('dental-chart-master/css/style.css')}}" rel="stylesheet" />
<script src="{{asset('dental-chart-master/js/snap.svg.js')}}"></script>
<script src="{{asset('dental-chart-master/js/chart.js')}}"></script>

<div class="container" id="patient-module" ng-app="myApp" ng-controller="patientController" >
  <div class="row" style= "margin-bottom:50px;" id="search-panel"  >
    <div class="col-sm-offset-2 col-sm-8">
      <form  class="form-horizontal" ng-submit="submit()" name="searchForm">
        <div class="input-group" id="adv-search">
            <input ng-model ="search_text"  type="text" class="form-control" placeholder="Search for patient" name="findme" autocomplete="off"/>
            <div class="input-group-btn">
                <div class="btn-group" role="group">
                    <div class="dropdown dropdown-lg">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><span class="caret"></span></button>
                        <div class="dropdown-menu dropdown-menu-right" role="menu">

                            <form class="form-horizontal" role="form">
                              <div class="form-group">
                                <label for="filter">Filter by</label>
                                <select class="form-control">
                                    <option value="0" selected>All Patient</option>
                                    <option value="1">Featured</option>
                                    <option value="2">Most popular</option>
                                    <option value="3">Top rated</option>
                                    <option value="4">Most commented</option>
                                </select>
                              </div>
                              <div class="form-group">
                                <label for="contain">Dentist</label>
                                <input class="form-control" type="text" />
                              </div>
                              <div class="form-group">
                                <label for="contain">Contains the words</label>
                                <input class="form-control" type="text" />
                              </div>
                              <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
                            </form>
                        </div>

                    </div>
                    <button ng-click="submit()" type="button" class="btn btn-primary"><searchloading></searchloading><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
                </div>
            </div>
        </div>
        <div ng-show="!patient_list.length" >
            <span id = "start-result"></span>
            <a  class="col-sm-offset-2 col-sm-8"  ng-repeat="patient in patient_list track by patient.id" ng-click = "selectedPx(patient.id)"  id="@{{patient.id}}">@{{ patient.surname + ', ' + patient.first_name + ' ' + patient.middle_name }}</a>
            {{--  <div>@{{patient_list}}</div>  --}}
        </div>
        <div ng-show="patient_list.length">
            <a  class="col-sm-offset-2 col-sm-8"  ng-click = "new_patient()" >@{{patient_list}} Click here to add.</a>
        </div>
      </form>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-offset-2 col-sm-8">
        <button ng-click="new_patient()" type="button" class="btn btn-link"><i class="fa fa-user-plus" ></i> New Patient</button>
        <button type="button" class="btn btn-link"><i class="fa fa-print"></i> Print</button>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-offset-1 col-sm-10">
        <!-- Nav tabs -->
        <div class="card">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#patient_info" aria-controls="patient_info" role="tab" data-toggle="tab"><i class="fa fa-user"></i>  <span>Patient Info</span></a></li>
                <li role="presentation"><a href="#dental_history" aria-controls="dental_history" role="tab" data-toggle="tab"><i class="fa fa-stethoscope"></i>  <span>Dental History</span></a></li>
                <li role="presentation"><a href="#medical_history" aria-controls="medical_history" role="tab" data-toggle="tab"><i class="fa fa-hospital-o"></i>  <span>Medical History</span></a></li>
                <li role="presentation"><a href="#dental_chart" aria-controls="dental_chart" role="tab" data-toggle="tab"><i class="fa fa-hospital-o"></i>  <span>Dental Chart</span></a></li>
            </ul>
            
            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="patient_info">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            
                        </div>
                        <div class="panel-body">
                            <!-- Display Validation Errors -->
                            @include('common.errors')
                            @if(session('status'))
                                <div class="alert alert-success">
                                {{session('status')}}
                                </div>
                            @endif
                            <!-- New Patient Form -->
                            <form ng-submit="save_patient()" class="form-horizontal"  name = "userForm" novalidate>
                                <input ng-model = "patient.info.patient_id" type="hidden" name="patient_id" id="patient-id">
                                <!-- Patient Name -->
                                <div class="form-group" ng-class="{ 'has-error has-feedback' : userForm.surname.$invalid && !userForm.surname.$pristine }">
                                    <label for="patient-surname" class="col-sm-3 control-label">Surname</label>
                                    <div class="col-sm-6">
                                        <input ng-model = "patient.info.surname" type="text" name="surname" id="patient-surname" class="form-control" required>
                                        <span class="glyphicon glyphicon-remove form-control-feedback" ng-show="userForm.surname.$invalid && !userForm.surname.$pristine" ></span>
                                        <p ng-show="userForm.surname.$invalid && !userForm.surname.$pristine" class="help-block">This field is required.</p>
                                    </div>
                                </div>
                                <div class="form-group" ng-class="{ 'has-error has-feedback' : userForm.first_name.$invalid && !userForm.first_name.$pristine }">
                                    <label for="patient-first_name" class="col-sm-3 control-label">First Name</label>

                                    <div class="col-sm-6">
                                        <input ng-model = "patient.info.first_name" type="text" name="first_name" id="patient-first_name" class="form-control" required>
                                        <span class="glyphicon glyphicon-remove form-control-feedback" ng-show="userForm.first_name.$invalid && !userForm.first_name.$pristine" ></span>
                                        <p ng-show="userForm.first_name.$invalid && !userForm.first_name.$pristine" class="help-block">This field is required.</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="patient-middle_name" class="col-sm-3 control-label">Middle Name</label>

                                    <div class="col-sm-6">
                                        <input ng-model = "patient.info.middle_name" type="text" name="middle_name" id="patient-middle_name" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="patient-age" class="col-sm-3 control-label">Age</label>

                                    <div class="col-sm-6">
                                        <input ng-model="patient.info.age" type="text" name="age" id="patient-age" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group" ng-class="{ 'has-error has-feedback' : userForm.gender.$invalid && !userForm.gender.$pristine }">
                                    <label for="patient-gender" class="col-sm-3 control-label">Gender</label>
                                    <div class="col-sm-6">
                                        <select ng-model="patient.info.gender" ng-value="patient.info.gender" ng-options="gender.id as gender.value for gender in genders track by gender.id" class="form-control" id="patient-gender" name="gender" required>
                                            <option value="">-- choose gender --</option>
                                        </select>
                                        {{--  <span class="glyphicon glyphicon-remove form-control-feedback" ng-show="userForm.gender.$invalid && !userForm.gender.$pristine" ></span>  --}}
                                        <p ng-show="userForm.gender.$invalid && !userForm.gender.$pristine" class="help-block">This field is required.</p>
                                    </div>
                                </div>
                                {{--  <div class="form-group">
                                    <label for="patient-birthdate" class="col-sm-3 control-label">Birthdate</label>

                                    <div class="col-sm-6">
                                        <input ng-model="patient.birthdate" type="text" name="birthdate" id="patient-birthdate" class="form-control" placeholder="1900-01-01">
                                    </div>
                                </div>  
                                {{--  <div class="form-group">
                                    <label for="patient-civil_status" class="col-sm-3 control-label">Civil Status</label>
                                    <div class="col-sm-6">
                                    <select ng-model="patient.civil_status" ng-options="civil_status.id as civil_status.value for civil_status in civil_status track by civil_status.id" class="form-control" id="patient-civil_status" name="civil_status" required>
                                        <option value="">-- choose civil status --</option>
                                    </select>
                                    </div>
                                </div>  
                                <div class="form-group">
                                    <label for="patient-address" class="col-sm-3 control-label">Address</label>

                                    <div class="col-sm-6">
                                        <input ng-model="patient.address" type="text" name="address" id="patient-address" class="form-control">
                                    </div>
                                </div>--}}
                                <div class="form-group">
                                    <label for="patient-cp_no" class="col-sm-3 control-label">Mobile No.</label>

                                    <div class="col-sm-6">
                                        <input ng-model="patient.info.cp_no" type="text" name="cp_no" id="patient-cp_no" class="form-control" placeholder="09xx-xxx-xxxx">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="patient-tel_no" class="col-sm-3 control-label">Tel. No.</label>

                                    <div class="col-sm-6">
                                        <input ng-model="patient.info.tel_no" type="text" name="tel_no" id="patient-tel_no" class="form-control" placeholder="888-8888">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="patient-email_address" class="col-sm-3 control-label">E-mail Address</label>

                                    <div class="col-sm-6">
                                        <input ng-model="patient.info.email_address" type="email" name="email_address" id="patient-email_address" class="form-control" placeholder="sample@gmail.com" >
                                    </div>
                                </div>

                                <!-- Add Patient Button -->
                                <div class="form-group">
                                    <div class="col-sm-offset-3 col-sm-6">
                                        <button ng-click="save_patient()" type="button" class="btn btn-primary" ng-disabled="userForm.$invalid"><loading></loading><i class="fa fa-btn fa-sign-in"></i> <span ng-bind="patient_info_button.text"></span> Patient
                                        </button>
                                    </div>
                                </div>
                                
                            </form>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="dental_history">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                        </div>
                        <div class="panel-body">
                            @if(session('status'))
                                <div class="alert alert-success">
                                {{session('status')}}
                                </div>
                            @endif
                            {{--  <form name="dentalHistoryForm" ng-submit="dental_history_submit()">  --}}
                            <form action="{{ url('dental_history') }}" method="POST" class="form-horizontal"  name = "dentalHistoryForm" novalidate>
                                
                                {{--  {{ csrf_field() }}  --}}
                                {{--  <input ng-model="patient._method" name="_method" value="PATCH" autocomplete="off" type="hidden">  --}}
                                <div class="form-group">
                                    <?php 
                                        use App\Http\Controllers\DentalComplaintController;
                                        $dental_complaints = DentalComplaintController::get();
                                    ?>
                                    <label for="chief-complaint" class="col-xs-3 control-label">Chief Complaint</label>

                                    <div class="col-xs-8">
                                        <select ng-model="patient.consultation.chief_complaint" ng-value="patient.consultation.chief_complaint" name="chief_complaint" id="chief-complaint" class="form-control">
                                            <option value="">-- choose complaint --</option>
                                            <?php  foreach($dental_complaints as $dental_complaints_key => $dental_complaints):?>
                                                <option value="<?php echo $dental_complaints->id;?>"><?php echo $dental_complaints->name;?></option>
                                            <?php endforeach;?>
                                        </select>
                                    </div>
                                </div>
                                 <div class="form-group" ng-if="patient.consultation.last_dental_check">
                                    <label for="last_dental_check" class="col-xs-3 control-label">Last Dental Visit:</label>

                                    <div class="col-xs-8">
                                        <input ng-model="patient.consultation.last_dental_check" type="text" name="last_dental_check" ng-model="patient.last_dental_check" id="last_dental_check" class="form-control">
                                    </div>
                                </div>
                                <?php 
                                    use App\Http\Controllers\DentalHistoryController;
                                    $dental_histories = DentalHistoryController::get();
                                ?>
                                <div class="form-group btn-group-vertical col-xs-12" >
                                <?php  foreach($dental_histories as $dental_history_key => $dental_history):?>
                                    <label class="btn btn-default">
                                        <input style="visibility:none;" type="checkbox" name="dental_history[]" ng-model="patient.dental_history[<?php echo $dental_history->id;?>]" id="<?php echo $dental_history->id;?>" ><?php echo $dental_history->name?>
                                    </label>
                                <?php endforeach?>
                                </div>
                                {{--  <div class="form-group">
                                
                                    <div class="col-xs-12">
                                        <button ng-submit="dental_history_submit()" type="button" class="btn btn-primary">
                                            <i class="fa fa-btn fa-sign-in"></i> PROCEED
                                        </button>
                                    </div>
                                </div>  --}}
                                {{--  <div >
                                    <p>Enter your Name: <input type = "text" ng-model = "name"></p>
                                    <p>Hello @{{patient}}!</p>
                                </div>  --}}
                            </form>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="medical_history">
                    <div class="panel panel-default">
                        <div class="panel-heading"> 
                        </div>
                        <div class="panel-body">
                            @if(session('status'))
                            <div class="alert alert-success">
                                {{session('status')}}
                            </div>
                            @endif
                            <form action="{{ url('medical_history') }}" method="POST" class="form-horizontal"  name = "medicalHistoryForm" novalidate>
                            {{ csrf_field() }}
                                <?php 
                                    use App\Http\Controllers\MedicalHistoryController;
                                    $medical_histories = MedicalHistoryController::get();
                                ?>
                                <div class="form-group btn-group-vertical col-xs-12" >
                                <?php  foreach($medical_histories as $medical_history_key => $medical_history):?>
                                    <label class="btn btn-default">
                                        <input type="checkbox" name="medical_history[]" ng-model="patient.medical_history[<?php echo $medical_history->id;?>]" id="<?php echo $medical_history->id;?>" value="<?php echo $medical_history->id;?>"><?php echo $medical_history->name?>
                                    </label>
                                <?php endforeach?>
                                </div>
                                <div class="form-group">
                                    <div class="col-xs-12">
                                        <button ng-click="patient_histories()" type="button" class="btn btn-primary" ng-disabled="!dentalHistoryForm.$dirty">
                                            <loading></loading><i class="fa fa-btn fa-sign-in"></i> @{{patient_history_button.text}}
                                        </button>
                                    </div>
                                </div>
                                <div >
                                    <p>Enter your Name: <input type = "text" ng-model = "name"></p>
                                    <p>Hello @{{patient}}!</p>
                                </div>  
                            </form>
                        </div>
                    </div> 
                </div>
                <div role="tabpanel" class="tab-pane" id="dental_chart">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                        </div>
                        <div class="panel-body">
                            <svg id="chart" preserveAspectRatio="xMidYMid meet" viewbox="0 0 5820 740"></svg>
                            <section class='treatment-form'>
                                <select>
                                    <option value="amalgam">Amalgam</option>
                                    <option value="composite">Composite</option>
                                    <option value="extract">Extract</option>
                                    <option value="crown">Crown</option>
                                </select>
                                <button id='btn_add_treatment'>Add treatment</button>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
</div>
    
@endsection
