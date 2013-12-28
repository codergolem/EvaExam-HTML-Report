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
 * EHReport Class
 * The EHREport class works as an aggreageted from other objects and should 
 * contain all data to be displayed in the PDF report. For scalar type data
 * that are set once and are not changed later, the PlaceHolders array can be 
 * used this an associative array that contain arbitrary data to be displayed in
 * the report.
 * 
 * @author Mario Diaz
 *
 * 
 */


class EHReport 
{
    
    private $m_sReportName ;
    private $m_nExamId;
    private $m_aParticipants ;
    private $m_oGradingKey;
    private $m_aPlaceHolders;
   
    public function __construct() 
    {
        //Initilizate members variables
        $this->m_sReportName = '';
        $this->m_nExamId = 0;
        $this->m_aParticipants = Array();
        $this->m_oGradingKey = null;
        $this->m_aPlaceHolders = array();
        
        //Initializate placeholders
        $this->m_aPlaceHolders['examName'] = '';
        $this->m_aPlaceHolders['timeLimit']  =  0;
        $this->m_aPlaceHolders['createDate'] = '';
        $this->m_aPlaceHolders['examCount']  =  0;
        $this->m_aPlaceHolders['formId']     =  0;
        $this->m_aPlaceHolders['formShortName']  = '';
        $this->m_aPlaceHolders['isOnline']   = false;
        $this->m_aPlaceHolders['maxReachablePoints'] = 0;
        $this->m_aPlaceHolders['approvalThreshold']  = 0;
        $this->m_aPlaceHolders['approvedParticipants'] = 0;
        $this->m_aPlaceHolders['isOpen'] = false;
        $this->m_aPlaceHolders['examStatus'] = 0;
        
        
    }
    
    //Getting and Setters
    
    public function setPlaceHolder ($name,$value) {
        $this->m_aPlaceHolders[$name] = $value;
    }
    
    public function getPlaceHolder ($name) {
        return $this->m_aPlaceHolders[$name];
    }
   //Getter and Setter for the EHReport properties
    
    public function getExamId() {
        return $this->m_nExamId;
    }

    public function setExamId($nExamId) {
        $this->m_nExamId = $nExamId;
    }

    public function getParticipantsResult() {
        return $this->m_aParticipants;
    }

    public function setParticipantsResult($aParticipants) {
        $this->m_aParticipants = $aParticipants;
    }

    public function getGradingKey() {
        return $this->m_oGradingKey;
    }

    public function setGradingKey($oGradindKey) {
        $this->m_oGradingKey = $oGradindKey;
    }
    
    public function getAllPlaceHolders() {
        return $this->m_aPlaceHolders;
    }

    
    public function getReportName() {
        return $this->m_sReportName;
    }

    public function setReportName($m_sReportName) {
        $this->m_sReportName = $m_sReportName;
    }

    
             
   /** 
    * The getParticipantTotalPointsArray fucntion builds an array with the 
    * total points obtained by each participant, it is useful for some 
    * calculation as the mean and median.
    */
    public function getParticipantTotalPointsArray() 
    {
        
        $counter = 0;    
        foreach ($this->m_aParticipants as $participant ) 
        {
             $aParticipantTotalPoints[$counter] = $participant->getTotalPoints();
             $counter++;
        }
        return $aParticipantTotalPoints;
    }
    
    
    /** 
     * getParticipantsResultByGrade 
     * Returns an array with the grade name as key and the number of participants
     * that got such grade as the value. For instance :
     * 'A' => 3
     * 'B => 10
     */
    
    public function getParticipantsResultByGrade() 
    {
        //If a grading key is defined for the exam 
        if (isset($this->m_oGradingKey)) 
        {
             $aPGrades = array();
             $aGrades = $this->m_oGradingKey->getGrades();
                
             foreach ($aGrades as $grade) 
             {
                    $aPGrades[$grade->getName()] = 0;
             }
                
             foreach ($this->m_aParticipants as $participant) 
             {
                    $note = $participant->getNote();
                    foreach ($aGrades as $cGrade) 
                    {
                        if(strcmp($note,$cGrade->getName()) == 0) 
                        {
                            $aPGrades[$note]++;
                        }
                    }
             }
              
            
             return $aPGrades;
       }
        
       else 
       {
            return;
       }
    }
    
    
    /**
     * Returns the participantResult object having the id given.
     */
    
    public function getParticipantResultById($pId) 
    {
        foreach ($this->m_aParticipants as $participant) 
        {
             if (strcmp($participant->getParticipantId(),$pId) == 0 ) 
             {
                 return $participant;
             }
             else 
             {
                 return;
             }
        }
    }
    
    /**
     * Returns the participants based on if they passed or failed the exam,
     *  @parameters:
     *  if $cVal 0 returns an associative array with two elements 'passed' and
     *  'failed', 'passed' is an array with all passed participants and 'failed
     *   a an array with all 'failed' participants.
     *  if $cVal 1 returns an array of all passed participant
     *  if $cVal 2 returns an array of all failed participant.
     */
    public function getParticipantsByFailedPassed($cVal) 
    {
        if (!($this->m_oGradingKey === NULL)) 
        {   
            $resArray = array();
            $pasArrray =array();
            $failArray =array();
            
            foreach ($this->m_aParticipants as $part)
            {
                if ($part->isPassed())
                {
                    $pasArrray[$part->getParticipantId()] = $part;
                }
                else
                {
                    $failArray[$part->getParticipantId()] = $part;
                }    
            }
            
            if ($cVal == 0)
            {
               $resArray['passed'] = $pasArrray;
               $resArray['failed'] = $failArray;
            }
            if ($cVal == 1)
            {
                $resArray = $pasArrray;
            }
            if ($cVal == 2)
            {
                $resArray = $failArray;
            }
            
            return $resArray;
        }
        else
        {
            return;
        }
        
        
        
    }
   




}

?>
