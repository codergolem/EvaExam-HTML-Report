/*  Copyright 2013, Electric Paper Evaluationssysteme GmbH
 
    This file is part of EvaExam HTML Report
 
    EvaExam HTML Report is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    EvaExam HTML is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with EvaExam HTML Report.  If not, see <http://www.gnu.org/licenses/>.
 *
 */



/** ehreport.js contains the code for all javascript functions in the
 *  EvaExam HTML Report. It is written using Jquery as base library.
 *  @author Mario Diaz
 */



$(document).ready(function(){
 
  //Initial Display Configuration
 
  //If the report has a grade seciton then it draws the grade and passed/failed graphics
  if ($("#grade_report_section").length) {
                drawPassedFailedGraphic();
                drawParticipantGradeGraphic();   
  }
  //Set the inital layout when page is loaded for first time, only the Exam Report Section is shown
  initialDisplay();
            
            
  //Events that control the filters in the Exam Report Section
  
  $("#show_passed_button").click(function ()
  {
      $(".failed").parent().hide();
      $(".passed").parent().show();
  });
            
  $("#show_failed_button").click(function (event)
  {
      $(".passed").parent().hide();
      $(".failed").parent().show();
  });
            
  $("#show_all_button").click(function (event)
  {
      $("#participant_table").find("tr").show();
                
  });
            
  $(".grade_filter_link").click(function (event)
  {
      var attrId = $(this).attr('id');
      attrId = attrId.split('_');
      //var cPage = $('#p_main_page_counter').html();
                
      //$("#part_main_page_"+cPage).find("tr").hide();
      
      $("#participant_table").find("tbody > tr").hide();
      $(".part_note_id_"+attrId[3]).parent().show();
      
      return false;
  });
  
  //Actions that control the navigation throug the different sections of the report.
            
  $('#exam_report_link').click(function() {
      hideAllSections();
      $('#exam_report_section').show();
  });
            
  $("#questions_results_link").click(function() {
      hideAllSections();
      $("#questions_results_section").show();
            });
            
  $('#grade_report_link').click(function() {
      hideAllSections();
      $('#grade_report_section').show();
  });
            
  $('#general_statistics_link').click(function() {
      hideAllSections();
      $("#general_statistics_section").show();
  });
  
  //Navigating trhoug the question table
  
  $('.q_nav_button').click(function() {
      
      var cPage =  $('#q_page_counter').html();
      var nextPage;
      var overNextPage;
      if ( $(this).attr('id') === 'back_question_arrow'){
          nextPage = parseInt(cPage) - 1;
          overNextPage = parseInt(nextPage) - 1;
          $('#next_question_arrow').css('visibility','visible');
         
      }
      
      if ( $(this).attr('id') === 'next_question_arrow'){
          nextPage = parseInt(cPage) + 1;
          overNextPage = parseInt(nextPage) + 1;
          $('#back_question_arrow').css('visibility','visible');
          
      }
      
      
      //Set only display current question page
      $('.question_page_'+cPage).hide();
      $('.question_page_'+nextPage).show();
      $('#q_page_counter').html(nextPage);
      
      //Set segment text
      var firstQuestion = $("#question_results_table").find("th:visible").eq(3).html();
      var lastQuestion =  $("#question_results_table").find("th:visible").last().html();
      $("#current_question_segment").html(firstQuestion+"-"+lastQuestion);
      
      
      
      if ($('.question_page_'+overNextPage).length === 0){
          
          $(this).css("visibility","hidden");
      }
      
  
      
      
      
      
  });
  
  
  //Navigating through the participant table
  
  $('.p_nav_button').click(function() {
      
    if (!($(this).parent().attr("class") === "disabled")){
      var cPage;
      var nextPage;
      var overNextPage;
      
      var buttonId  = $(this).attr('id').split('_');
      var section   =   buttonId[1];
      
      var direction = buttonId[2];
     
      cPage =  $('#participant_'+section+'_page_counter').html();
    
      
      if ( direction === 'previous'){
          nextPage = parseInt(cPage) - 1;
          overNextPage = parseInt(nextPage) - 1;
          $('#participant_'+ section + '_next_button').parent().removeClass("disabled");
      }
      
      if ( direction === 'next'){
          nextPage = parseInt(cPage) + 1;
          overNextPage = parseInt(nextPage) + 1;
          $('#participant_'+section + '_previous_button').parent().removeClass("disabled");
      }
      
     
      $('#participant_'+section+'_page_'+cPage).hide();
      $('#participant_'+section+'_page_'+nextPage).show();
      $('#participant_'+section+'_page_counter').html(nextPage);
      
      
      
      if ($('#participant_'+section+'_page_'+overNextPage).length === 0){
          
          $(this).parent().addClass("disabled");
      }
    } 
      return false;
      
  });
  
  //Change to full view
  
  $('.f_view_button').click(function () {
      var buttonId  = $(this).attr('id').split('_');
      var section   = buttonId[0];
     
      $('#participant_'+section + '_nav' ).hide();
      
      if (section === 'mainsection'){
          $('#participant_table').find('tbody').show();
          $("#inner_filter_bar").show();
      }
      
      if (section === 'questionsection'){
          $('#question_results_table').find('tbody').show();
      }
      
      $(this).hide();
      
      
  });
  
  
  //Functions Definitions
  
   function drawPassedFailedGraphic(){
        var passedLabel = $(document).find("#passed_label_cell").html();
        var passedPercentage = parseFloat($(document).find("#passed_value_cell").html());
        var failedLabel = $(document).find("#failed_label_cell").html();
        var failedPercentage = parseFloat($(document).find("#failed_value_cell").html());
    
        var data = [
                    [passedLabel,passedPercentage],[failedLabel,failedPercentage]
                   ];
        var container = 'grade_passed_graphic';
        var gColors = ['#228B22','#DC143C'];
        drawGraphic(container,data,gColors);
                
    }
    
    function drawGraphic(container,data,scolors) {
        
        var gColors = ["#4bb2c5", "#c5b47f", "#EAA228", "#579575", "#839557", "#958c12",
        "#953579", "#4b5de4", "#d8b83f", "#ff5800", "#0085cc"];
        
        if (scolors) {
            gColors = scolors;
        }
        
        jQuery.jqplot (container, [data], 
        {    
            
            seriesDefaults: {
                
                // Make this a pie chart.
                renderer: jQuery.jqplot.PieRenderer, 
                rendererOptions: {
                // Put data labels on the pie slices.
                // By default, labels show the percentage of the slice.
                showDataLabels: true
                }
           }, 
           legend: { show:true, location: 'e', showLabels: true, placement: 'inside' },
           
           //Color for the different sections
           
           seriesColors: gColors
        }
    
       );
   }
           
                    
   function drawParticipantGradeGraphic() {
       var $gradeRows = $('#grade_participant_table_body').children();
       var graphicData = new Array();
       $gradeRows.each(function(index) {
           var firstRow= $(this).find('td:nth-child(1)').text();
           var secondRow= parseFloat($(this).find('td:nth-child(3)').text());
            
           graphicData[index] = [firstRow,secondRow];    
            
       });
       var container = 'participant_grade_graphic';  
       drawGraphic(container,graphicData,null);
   }
          
   function initialDisplay() {
       
              $("#questions_results_section").hide();
              $("#general_statistics_section").hide();
              $("#grade_report_section").hide();

              if ($('#mainsection_fullview_button').length > 0){
                  if($('#inner_filter_bar').length > 0){
                      $('#inner_filter_bar').hide();
                  }
              }
  }
          
  function hideAllSections() {
              
              $("#exam_report_section").hide();
              $("#questions_results_section").hide();
              $("#general_statistics_section").hide();
              $("#grade_report_section").hide();
              
  }
  
  
            
});


