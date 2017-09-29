var app = angular.module("myApp", []);
// app.config(function($routeProvider) {
//     $routeProvider
//     .when("/patients", {
//         templateUrl : "patients",
//         controller : "patientController"
//     })
//     .when("/nol", {
//         templateUrl : "patients",
//         controller : "patientController"
//     })
//     .when("/paris", {
//         templateUrl : "paris.htm",
//         controller : "parisCtrl"
//     });
// });
app.directive('loading', function () {
    return {
      restrict: 'E',
      replace:true,
      template: '<i class="fa fa-spinner fa-spin" style="padding: 3px;"></i>',
      link: function (scope, element, attr) {
            scope.$watch('loading', function (val) {
                if (val){
                    $(element).show().next().hide().parent().prop('disabled', true);
                }
                else{
                    $(element).hide().next().show().parent().removeAttr("disabled");
                }
            });
      }
    }
});
app.directive('searchloading', function () {
    return {
      restrict: 'E',
      replace:true,
      template: '<i class="fa fa-spinner fa-spin" style="padding: 3px;"></i>',
      link: function (scope, element, attr) {
            scope.$watch('searchloading', function (val) {
                if (val){
                    $(element).show().next().hide().parent().prop('disabled', true);
                }
                else{
                    $(element).hide().next().show().parent().removeAttr("disabled");
                }
            });
      }
    }
})
app.controller("patientController", function ($timeout, $scope,$http) {
  $scope.sample = '';
  $scope.message = '';
  $scope.patient = {};
  $scope.patient_info_button = {"verb":"POST","text":"Save"};
  $scope.patient_history_button = {"verb":"POST","text":"Save"};
  $scope.genders =  [{
                        'id': '1',
                        'value': 'Male'
                    }, 
                    {
                        'id': '2',
                        'value': 'Female'
                    }]; //Use from database soon!
 $scope.civil_status =  [{
                        'id': 'Single',
                        'value': 'Single'
                    }, 
                    {
                        'id': 'Married',
                        'value': 'Married'
                    }];
/*Patient search functions*/
  $scope.submit = function() {
    $scope.patient_list = {};
    $scope.patient.info = {};
    $scope.patient.consultation = {};
    $scope.patient.dental_history = {};
    $scope.patient.medical_history = {};
    $scope.patient_info_button = {"verb":"POST","text":"Save"};
    $scope.patient_history_button = {"verb":"POST","text":"Save"};
    if ($scope.search_text) {
        $scope.searchloading = true;
        $http({
            headers: {
                // 'Content-Type': JSON,
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            method : "GET",
            url : "patient/search/"+$scope.search_text,
        }).then(function mySuccess(response) {
            $scope.patient_list = response.data;
            $scope.searchloading = false;
        }, function myError(response) {
            alert(response.statusText);
        });
        $scope.patient_list;
    }
  };
  $scope.selectedPx = function(data) {
    px_id = parseInt(data);
    $scope.patient.dental_history = {};
    $scope.patient.medical_history = {};
    $scope.patient_info_button.text = 'Update';
    if($scope.patient_list[px_id]['patient_dental_histories'].length > 0 || $scope.patient_list[px_id]['patient_medical_histories'].length > 0)
        $scope.patient_history_button.text = 'Update';
        
    $scope.patient.info.patient_id = $scope.patient_list[px_id].id;
    $scope.patient.info.surname = $scope.patient_list[px_id].surname;
    $scope.patient.info.first_name = $scope.patient_list[px_id].first_name;
    $scope.patient.info.middle_name = $scope.patient_list[px_id].middle_name;
    $scope.patient.info.gender = $scope.patient_list[px_id].gender;
    $scope.patient.info.age = $scope.patient_list[px_id].age;
    $scope.patient.info.cp_no = $scope.patient_list[px_id].cp_no;
    $scope.patient.info.tel_no = $scope.patient_list[px_id].tel_no;
    $scope.patient.info.email_address = $scope.patient_list[px_id].email_address;
    if($scope.patient_list[px_id]['consultations'].length > 0){
        $scope.patient.consultation.chief_complaint=$scope.patient_list[px_id]['consultations'][0].dental_compaint_id;
        $scope.patient.consultation.last_dental_check=$scope.patient_list[px_id]['consultations'][0].date_time;
    }
    $.each($scope.patient_list[px_id]['patient_dental_histories'], function(pdh_id, pdh_val){
        dental_history_id = pdh_val.dental_history_id;
        $scope.patient.dental_history[dental_history_id] = true;
    });
    $.each($scope.patient_list[px_id]['patient_medical_histories'], function(pmh_id, pmh_val){
        medical_history_id = pmh_val.medical_history_id;
        $scope.patient.medical_history[medical_history_id] = true;
    });
  };
  $scope.clickselectedPX = function(pid) {
      px_id = parseInt(pid);
      $timeout(function() {
          var px_element = $("#"+px_id);
          angular.element(px_element).triggerHandler('click');
      }, 0);
  };
/*End of Patient search functions*/
    $scope.new_patient = function() {
        $scope.patient_info_button.text = 'Save';
        $scope.patient_history_button.text = 'Save';
        $scope.patient_list = {};
        $scope.patient.info = {};
        $scope.patient.consultation = {};
        $scope.patient.dental_history = {};
        $scope.patient.medical_history = {};
        $("a[href='#patient_info']").click();
        $("#patient-surname").focus();
    };
    $scope.save_patient = function() {
        $scope.loading = true;
        $http({
            headers: {
                // 'Content-Type': JSON,
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            method : $scope.patient_info_button.verb,
            url : "patient"+($scope.patient_info_button.text == 'Update'?"/edit/"+$scope.patient.info.patient_id:""),
            data : {data: $scope.patient.info }
        }).then(function mySuccess(response) {
            $scope.patient.info.patient_id = response.data.patient_id;
            $scope.patient_info_button.text = 'Update';
            $scope.loading = false;
            $scope.message = response.data.message;
            if($scope.patient_info_button.text == 'Save'){
                var $active = $('.card .nav-tabs li.active');
                $($active).next().find('a[data-toggle="tab"]').click();
            }
        }, function myError(response) {
            alert("Error saving patient! "+response.statusText);
        });
    };
  $scope.patient_histories = function() {

        parseInt($scope.patient.info.patient_id);
        $scope.loading = true;
        $http({
            method : $scope.patient_history_button.verb,
            url : "dental_history"+($scope.patient_history_button.text == 'Update'?"/"+$scope.patient.info.patient_id:""),
            headers: {
                // 'Content-Type': JSON,
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            data : {data: $scope.patient }
        }).then(function mySuccess(response) {
            console.log("patient histories has been submitted!");
            $scope.patient_history_button.text = 'Update';
            $scope.loading = false;
        }, function myError(response) {
            alert("Error saving patient histories! "+response.statusText);
        });
  };
  
});



 
$(document).ready(function(){
       
  $("form[name='searchForm'] input").focus();
    document.onkeydown = function (e) {
        switch (e.keyCode) {
        case 38:
            moveUp();
        break;
        case 40:
            moveDown();
        break;
    }
};

function moveUp() {
  //If nothing selected
  if($(".selected").length==0){
      $("#start-result").addClass("selected").focus();
  }
  
  //Check if there is another link above, if no, go to bottom
  if ($(".selected").prev("a").length > 0) {
      $(".selected").removeClass("selected").prev("a").addClass("selected").focus();
      $("#searchForm input").val($(".selected").text());
  } else {
      $(".selected").removeClass("selected");
      $("#divSearchResults a:last-child").addClass("selected").focus();
      $("#searchForm input").val($(".selected").text());
  }
  angular.element('#search-panel').scope().clickselectedPX($(".selected").attr('id'));
}

function moveDown() {
  //If nothing selected
  if($(".selected").length==0){
      $("#start-result").addClass("selected").focus();
  }
  //Check if there is another link under, if no, go to top
  if ($(".selected").next("a").length > 0) {
      $(".selected").removeClass("selected").next("a").addClass("selected").focus();
      // console.log($(".selected").text());
      $("#searchForm input").val($(".selected").text());
  } else {
      $(".selected").removeClass("selected");
      $("#divSearchResults span").next().addClass("selected").focus();
      // console.log($(".selected").text());
      $("#searchForm input").val($(".selected").text());
  }
  angular.element('#search-panel').scope().clickselectedPX($(".selected").attr('id'));
}
//Remove .selected style on click outside
// $(document).on("blur", ".selected", function () {
//     $(this).removeClass("selected");
// });
  });

    
