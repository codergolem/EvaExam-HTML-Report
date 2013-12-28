<?php

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

/** EvaExam HTML Report Controller
 *  This class represents the controller in the arquitecture of the EvaExam
 *  Report Plugin controller. It determinates which view must be shown based 
 *  on user request and prepared the data to be shwon in the report.
 *  @author Mario Diaz
 */



class Custom_EhtmlReportController extends Zend_Controller_Action {
    
    
/**
 * It creates the connection to the soap api and to the database, determinates
 * the language of the report and creates the EHReport object.
 */    
    public function init() 
    {
        //parent::init();
        include_once 'customer/EhDbCon.php';
        
        // add path to module-specific models to the include_pat
        $sModulename = $this->getRequest()->getModuleName();
        set_include_path(
                        join(PATH_SEPARATOR,
                        array(
                                APPLICATION_DIR . 'modules/' . $sModulename . '/models',
                                get_include_path()
                             )
                        )
        );
        
        
        //SOAP Connection Parameters
        
        $parameters["endpoint"]= EVAROOT."/services/examserver-v51.wsdl";
        $parameters["cache"]= true;
        $user="ehreport";
        $pass="ehreport";
        
        //Creation of SOAP Client, Report Mapper and Report Model Object
        $this->soapClient = new EHSoapClient($parameters);
        $this->soapClient->setHeader($user,$pass);

        $this->oMapper = new EHSoapReportMapper();
        $this->oMapper->setSoapClient($this->soapClient);
         
        $this->oReport = new EHReport();  
        
        //Get current user id from the session file
        $this->userId = $_SESSION["user_ID"];
        
         //Get user language
        EhDbCon::init();
        $dbCon = EhDbCon::getConnection();
        $lang = EHCoreUtils::getLanguage($dbCon);
        
        $this->view->translator = new Zend_Translate (
                
                array(
                    'adapter' => 'array',
                    'content' => 'res/evaexam/ehreport.'.$lang.'.inc',
                    'locale'  => 'en'  
                )
        );
        
        $this->view->lang = $lang;
        
    }


    /** The index action display a list of all exams with available results
     *  for the current user, including individual links for the HTML Report
     */
    
    
    public function indexAction() 
    {
        
        //Colect all exams with results
        $aExams = $this->oMapper->getExamsByUser($this->userId);
       
        
        
        $this->view->exams = $aExams;
    
    }
    
    
    /**
     *  displayAction is called when the Exam HTML Report is going to be shown
     *  for an exam. It gets the results for the exam and creates the report.
     */
    public function displayAction () 
    {
        
        //Get Exam Id
        
        $request = $this->getRequest();
        $examId = intval($request->getParam('examid'));
        
        //Get Object EHReport
        
        $this->oMapper->buildReport($examId, $this->oReport);
        $this->view->Report = $this->oReport;
        $this->view->participantsResult = $this->oReport->getParticipantsResult();
        
        
        
        
        //Set userId,name and lastname
        
        $this->view->userId = $this->userId;
        $this->view->userFirstName  = $_SESSION['user_firstname'];
        $this->view->userSecondName = $_SESSION['user_name'] ;


       
       
        
        //Define question count, participant count and max values for participant and question pages
        $aParticipantSample = $this->oReport->getParticipantsResult();
        $this->view->questionCount = count($aParticipantSample[0]->getQuestionResults());
        $this->view->participantCount = count($this->oReport->getParticipantsResult());
        $this->view->maxQuestionsToDisplay = 20;
        $this->view->maxParticipantsToDisplay = 25;
        $this->view->displayParticipantPages  = true;
        $this->view->displayQuestionPages     = true;

        //Define if participant pages will be displayed
        if ($this->view->participantCount <= $this->view->maxParticipantsToDisplay)
        {
            $this->view->displayParticipantPages  = false;
        }
        //Define if question pages will be displayed
        if ($this->view->questionCount <= $this->view->maxQuestionsToDisplay)
        {
            $this->view->displayQuestionPages = false;
        }
        
        //Define gradingKey values if any
        
        if (!(is_null($this->oReport->getGradingKey()))) 
        {
            //Define failed/passed quote
            $passFailedArray = $this->oReport->getParticipantsByFailedPassed(0);
            $this->view->PassedParticipants = $passFailedArray['passed'];
            $this->view->FailedParticipants = $passFailedArray['failed'];
            //Define grade statistics
            $this->view->gradeArray = $this->oReport->getGradingKey()->getGrades();
            $this->view->participantsByNote = $this->oReport->getParticipantsResultByGrade();
        }
        
        
        //Define mean,median,standart deviation
        $this->view->mean =   EHMathUtils::calculateMean($this->oReport->getParticipantTotalPointsArray());
        $this->view->median = EHMathUtils::calculateMedian($this->oReport->getParticipantTotalPointsArray());
        $this->view->stdev =  EHMathUtils::calculateStandartDeviation($this->oReport->getParticipantTotalPointsArray());
        $this->_helper->layout->setLayout('ehreport');
    }
 
    
    
    
}



?>
