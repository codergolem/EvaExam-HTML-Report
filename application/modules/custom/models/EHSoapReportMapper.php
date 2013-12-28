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

/**
 * EHSoapReporMapper
 * Gathers all necessary information that will be displayed in the HTML report to
 * crate a EHReport object. It implements the interface EHReportMapper by using
 * the SOAP EvaExam interface to retrieve all the data.
 * 
 * @author Mario Diaz
 *
 * 
 */


class EHSoapReportMapper implements EHReportMapper 
{
    /** 
     *  @Member variable: $m_oSoapClient : EHSoapClient used to make the soap calls
     *  @Member variable: $m_sCsvSeparator : As the participant results data need 
     *  to be read from a csv file, this parameter should be set to the one used 
     *  in the file.
     */
    private $m_oSoapClient ;
    private $m_sCsvSeparator ;
  
    public function __construct() 
    {
        $this->m_sCsvSeparator = ';' ;
    }
    
    
    public function setSoapClient ($soapClient)
    {
    
        if (!$soapClient instanceof EHSoapClient) 
        {
            throw new Exception('Invalid Soap Client Provided');
        }
        
        $this->m_oSoapClient = $soapClient; 
        return $this;
    }
    
    public function  getSoapClient() 
    {
        
        return $this->m_oSoapClient;
    }
    
    /**
     * buildReport function wrapps the construction of EHReport method, returns 
     * null if the exam does not exist, is not open, or the results from the 
     * CSV file could not be correctly read.
     */
    
    public function buildReport($examId,$EHReport) 
    {
        
        //Call the collectExamInformation function to retrieve Exam information
        if (is_null($this->collectExamInformation($examId, $EHReport))) 
        {
            return;
        }
        
        //Read results from csv file and put them into the participant results array.
        
        $hasGradingKey = false;
        if (!is_null($EHReport->getGradingKey())) 
        {
            $hasGradingKey = true;
        }
        $isOnline = $EHReport->getPlaceHolder('isOnline');
  
        $aResultArray = $this->readCsvResults($examId,
                                              $isOnline, 
                                              $hasGradingKey,
                                              $EHReport->getPlaceHolder('approvalThreshold'),
                                              $EHReport->getGradingkey());
                                              
        if (is_null ($aResultArray)) 
        {
            return;
        }
            
        $EHReport->setParticipantsResult($aResultArray);
        
        
        
        return 1;
    }
    
    /** 
     * getGradingKey returns a EHGraingKey object build with the information 
     * from a grading key existing in EvaExam. Use the soap function also 
     * called GetGradingKey and receives as parameter the grading key id.
     */
    public function getGradingKey($nKeyId)
    {
        
        $aReqParameters = array ('GradingKeyId' => $nKeyId);
        $oResponse  = $this->m_oSoapClient->callFunction('GetGradingKey',$aReqParameters);
        
        
        if (is_soap_fault($oResponse)) 
        {
            if ($oResponse->faultstring == 'ERR_600_401') 
            {
                $dateAndTime = date('Y-m-d H:i:s',time());
                file_put_contents(EVAROOT.'data/log/EHReport.log', $dateAndTime.': '.$oResponse->detail.PHP_EOL, FILE_APPEND);
            }
            return;          
        }
        
        
        $oGradingKey = new EHGradingKey();
        $oGradingKey->setId($nKeyId);
        $oGradingKey->setName($oResponse->Name);
        $oGradingKey->setDescription($oResponse->Comment);
        $oGradingKey->setPassedGradeId($oResponse->PassedGradeId);
        $oGradingKey->setMinPassedPercentage($oResponse->PassedGrade->Percentage);
      
        
        $aOfEhGrades = Array ();
        $aGrades = $oResponse->Grades->Grades;
       
        foreach ($aGrades as $grade) 
        {
            $oGrade = new EHGrade();
            $oGrade->setId($grade->Id);
            $oGrade->setName($grade->Name);
            $oGrade->setPercentage($grade->Percentage);
            $aOfEhGrades[$oGrade->getId()] = $oGrade;
        }
        
        $oGradingKey->setGrades($aOfEhGrades);
        
        return $oGradingKey;
        
    }
    
    
    /** 
     * readCsvResults retrieve the CSV file with the particpant results from an 
     * exam.Uses the soap function 'GetExamResults' to get CSV file.
     * It put each indvidual results into a EHParticipantResult object an return
     * an array containing all results. Additionaly if the exam has a grading 
     * key assigned uses the parameter $fMinThreshold to determinate if a participant
     * has passed or not.
     */
    
    //@parameter: $fMinThreshold : Minimal points in absolut value to pass the exam.
    
    public function readCsvResults ($examId,$isOnline = false ,$hasNote = false , $fMinThreshold = 0 , $gradingKey = null) 
    {
    
        $aGetExamResultsPar = array ('ExamId' => $examId,'ExportType' => 'csv');
        $pathToCsvFile = $this->m_oSoapClient->callFunction('GetExamResults', $aGetExamResultsPar,FILE_APPEND);
         
        if (is_soap_fault($pathToCsvFile)) 
        {
            if ($pathToCsvFile->faultstring == 'ERR_600_202') {
                $dateAndTime = date('Y-m-d H:i:s',time());
                file_put_contents(EVAROOT.'data/log/EHReport.log', $dateAndTime.': '.$pathToCsvFile->detail.PHP_EOL, FILE_APPEND);
            }
            return;
        }
        
        
        /* Results of the readFromCsv : 
         * @participantResults  Array of EHParticpantResult objects that contain
         */
        $participantResults = array();
        
        /* @parameteres:
         * $row     Counter of the csv file lines,@nFields:number of fields in each line
         * $nOffset Define in which colum will be found the grading key and the timestamp if they exist in the csv file
         */
        $row = 0;
        $nFields = null;
        $nOffset = 2;
       
        if ($hasNote) 
        {
            $nOffset++;
        }
        
        if ($isOnline)
        {
            $nOffset++;
        }
        
        if (($handle = fopen($pathToCsvFile, "r")) !== FALSE) 
        {
            //Read line per line creating a participantResult object per each result line
            while (($line = fgetcsv($handle,250,$this->m_sCsvSeparator)) !== FALSE) 
            {
                //First line is descarted and only used to calculate the number of fields(columns) in the csv file
                 if ($row == 0)
                 {
                    $nFields = count($line);
                 }
                 //Start to read the participan result lines and put the information into the EHParticipantResult object
                 if ($row > 0) 
                 {
                     $participantResult = new EHParticipantResult();
                     $participantResult->setParticipantId($line[0]);
                     $participantResult->setSerialNumber(intval($line[1]));
                     
                     //Reading columns containing points per question
                     $aQuestionResults = array();
                     for ($i = 2; $i < ($nFields - $nOffset); $i++) 
                     {
                         $aQuestionResults[$i -2] = (float)str_replace(',','.',$line[$i]);
                     }
                     
                     $participantResult->setQuestionResults($aQuestionResults);
                     
                     
                     //Assign Total Points Value 
                     $participantResult->setTotalPoints((float)str_replace(',','.',$line[$nFields - 2]));
                     
                     //Reading note(grade) and timestamp if exists
                     if ($hasNote) 
                     {
                         
                         if ($isOnline) 
                         {
                            $participantResult->setTimeStamp($line[$nFields - 4]);
                         }
                         //Set participant note with the vale in the csv file
                         $participantResult->setNote($line[$nFields - 3]);
                         
                         //Define noteId if gradingKey was provided
                         if(isset($gradingKey))
                         { 
                             
                             $oGrade = $gradingKey->getGradeByName($participantResult->getNote());
                             $participantResult->setGradeId($oGrade->getId());
                         }
                         
                         //Determiante if a participant has passed or failed
                         $nIsPassed = bccomp($participantResult->getTotalPoints(),$fMinThreshold,3);
                         if($nIsPassed == -1 ) 
                         {
                             $participantResult->setPassed(false);
                         }
                         else 
                         {
                            $participantResult->setPassed(true);
                            
                         }
                        
                     }
                     else 
                     {
                         if ($isOnline) 
                         {
                            $participantResult->setTimeStamp($line[$nFields - 3]);
                         }
                     }
                     //Enter the participant result object into the results array
                    
                     $participantResults[$row - 1] = $participantResult;
                 }
                 $row++;
            }
            fclose($handle);
            
       }
       return $participantResults;
   }
   
   
   /** 
    * collecExamInformation maps all values returned from the 'GetExam' soap
    * function to the corresponding placeholders in the EHReport object.
    */
   public function collectExamInformation($examId, EHReport $EHReport) 
   {
      
        $aGetExamPar = array ('ExamId'=> $examId);
        $oGetExamResponse = $this->m_oSoapClient->callFunction('GetExam',$aGetExamPar);
        
        if (is_soap_fault($oGetExamResponse)) 
        {
            //if (strcmp($oGetExamResponse->faultstring,'ERR_600_202') === 0) 
            //{
                $dateAndTime = date('Y-m-d H:i:s',time());
                file_put_contents(EVAROOT.'data/log/EHReport.log', $dateAndTime.': '.$oGetExamResponse->detail.PHP_EOL,FILE_APPEND);
            //}
            return;
        }
        
        if ($oGetExamResponse->Status <> 1) 
        {
            $dateAndTime = date('Y-m-d H:i:s',time());
            file_put_contents(EVAROOT.'data/log/EHReport.log', $dateAndTime.': Exam '.$examId.' in status different from 1'.PHP_EOL, FILE_APPEND);
            return;
        }
        
        //Asign correponding values
        
        $EHReport->setExamId($oGetExamResponse->ExamId);
        
        //Set Placeholders
        
        $EHReport->setPlaceHolder('createDate',$oGetExamResponse->CreateDate);
        $EHReport->setPlaceHolder('examStatus',$oGetExamResponse->Status);
        $EHReport->setPlaceHolder('examName'  ,$oGetExamResponse->Name);
        $EHReport->setPlaceHolder('examCount' ,$oGetExamResponse->ExamCount);
        $EHReport->setPlaceHolder('formId',$oGetExamResponse->FormId);
        $EHReport->setPlaceHolder('formShortName', $oGetExamResponse->ShortName);
        $EHReport->setPlaceHolder('maxReachablePoints', $oGetExamResponse->MaxReachablePoints);
        $EHReport->setPlaceHolder('participantCount', $oGetExamResponse->ParticipantCount);
        
        $isOpen = ($oGetExamResponse->Open == 1) ? true : false;
        $EHReport->setPlaceHolder('isOpen',$isOpen);
        $isOnline = ($oGetExamResponse->IsOnline == 1) ? true : false;
        $EHReport->setPlaceHolder('isOnline',$isOnline) ;
        
      
        
        
        //If a grading key exists for the exam, then assign such key to report object
        if (isset($oGetExamResponse->GradingKeyId)) 
        {
            $oGradingKey = $this->getGradingKey($oGetExamResponse->GradingKeyId);
            $EHReport->setGradingKey($oGradingKey); 
            
            //Calculate Approval Threshold
            $fMinPercentage = $EHReport->getGradingKey()->getMinPassedPercentage();
            $fMinPassedPoints = EHMathUtils::getAbsolutFromPercentage($EHReport->getPlaceHolder('maxReachablePoints'),$fMinPercentage);
            $EHReport->setPlaceHolder('approvalThreshold',$fMinPassedPoints);
        }
        
        return 1;
       
   }
   
   /** 
    * Returns an array of soap response objects each corresponding to an exams for the current user, 
    * NOTE: This are not EHReport objects, and do not have any GET or SET methods, the public members
    * need to be called by its name directly, the response object correspond to the one returne vy the
    * SOAP method GetExam.
    * 
    * Some of the mot importan variable names:
    * 
    * ExamId
    * Status
    * Name
    * ExamCount
    * FormId
    * ShortName
    * GradingKeyId
    */
   
   public function getExamsByUser ($userId) 
   {
       
       $aGetAllExamFoldersPar = array ('UserId'=> $userId);
       $oRequestResponse = $this->m_oSoapClient->callFunction('GetAllExamFolders',$aGetAllExamFoldersPar );
       
        if (is_soap_fault($oRequestResponse)) 
        {
                $dateAndTime = date('Y-m-d H:i:s',time());
                file_put_contents(EVAROOT.'/data/log/EHReport.log', $dateAndTime.': '.$oRequestResponse->detail.PHP_EOL,FILE_APPEND);
				
                return;
        }
        
       
        //Get all folders id
        $afolders = $oRequestResponse->ExamFolders;
        $foldersCounter = 0;
		
        if (is_array($afolders))
	{
            foreach ($afolders as $folder) 
            {
                $aFoldersId[$foldersCounter] = $folder->FolderId;
		$folder->FolderId;
		$foldersCounter++;
				
	    } 
        }
        else
	{
            $aFoldersId[0] = $afolders->FolderId;
	}
        
        $aAllExams = array();
        foreach ($aFoldersId as $folderId) 
        {
            $aReqParameters = array ('FolderId' => $folderId);
            $oGetAllExamsRequest = $this->m_oSoapClient->callFunction('GetAllExamsByFolderId',$aReqParameters);
            
            if (is_soap_fault($oGetAllExamsRequest) && ($oGetAllExamsRequest->faultstring != "ERR_600_201")) 
            {		
                    $dateAndTime = date('Y-m-d H:i:s',time());
                    file_put_contents(EVAROOT.'data/log/EHReport.log', $dateAndTime.': '.$oGetAllExamsRequest->detail.PHP_EOL,FILE_APPEND);
                    return;
            }
            
            $aExams = $oGetAllExamsRequest->Exams;
            if (!is_array($aExams)) 
            {
                $tmpExams = $aExams;
                $aExams = array();
                $aExams[0] = $tmpExams;
            }
            
            $aAllExams = array_merge($aAllExams,$aExams);
        }
        
        return $aAllExams;
            
       
   }
   
   public function getCsvSeparator() {
       return $this->m_sCsvSeparator;
   }

   public function setCsvSeparator($sCsvSeparator) {
       $this->m_sCsvSeparator = $sCsvSeparator;
   }


    
}

?>
