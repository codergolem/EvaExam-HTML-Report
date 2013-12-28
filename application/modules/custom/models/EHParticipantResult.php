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
 * It represents the indvidual results of a exam participant. It contains the
 * total points obtained in the exam. The individual points obtained in each
 * question, and if the exam has grading key, it also stores the grade and the
 * passed/failed result.
 * 
 * @author Mario Diaz
 *
 * 
 */


class EHParticipantResult 
{
    
    private $m_sParticipantId ;
    private $m_sGrade ;
    private $m_nGradeId;
    private $m_fTotalPoints;
    private $m_bPassed;
    private $m_bHasKey;
    private $m_aQuestionResults ;
    private $m_nSerialNumber;
    private $m_sTimeStamp;
    
    
    public function __construct() {
        
        $this->m_sParticipantId = '';
        $this->m_sGrade = '';
        $this->m_nGradeId = 0;
        $this->m_fTotalPoints = 0;
        $this->m_bPassed = false;
        $this->m_bHasKey = false;
        $this->m_aQuestionResults = array() ;
        $this->m_nSerialNumber = 0;
        $this->m_sTimeStamp = '';
    }
 
    
    public function getQuestionResultByIndex ($index) 
    {
        if (!isset($this->m_aQuestionResults)) 
        {
            return;
        }
        
        return $this->m_aQuestionResults[$index];
    }
    
    
    public function getParticipantId() {
        return $this->m_sParticipantId;
    }

    public function setParticipantId($sParticipantId) {
        $this->m_sParticipantId = $sParticipantId;
    }

    public function getGradeId() {
        return $this->m_nGradeId;
    }

    public function setGradeId($nGradeId) {
        $this->m_nGradeId = $nGradeId;
    }

    public function getTotalPoints() {
        return $this->m_fTotalPoints;
    }

    public function setTotalPoints($fTotalPoints) {
        $this->m_fTotalPoints = $fTotalPoints;
    }

    public function isPassed() {
        return $this->m_bPassed;
    }

    public function setPassed($bPassed) {
        $this->m_bPassed = $bPassed;
    }

    public function getHasKey() {
        return $this->m_bHasKey;
    }

    public function setHasKey($bHasKey) {
        $this->m_bHasKey = $bHasKey;
    }

    public function getQuestionResults() {
        return $this->m_aQuestionResults;
    }

    public function setQuestionResults($aQuestionResults) {
        $this->m_aQuestionResults = $aQuestionResults;
    }
    
    public function getSerialNumber() {
        return $this->m_nSerialNumber;
    }

    public function setSerialNumber($nSerialNumber) {
        $this->m_nSerialNumber = $nSerialNumber;
    }
    
    public function getTimeStamp() {
        return $this->m_sTimeStamp;
    }

    public function setTimeStamp($sTimeStamp) {
        $this->m_sTimeStamp = $sTimeStamp;
    }
    
    public function getNote() {
        return $this->m_sGrade;
    }

    public function setNote($sNote) {
        $this->m_sGrade = $sNote;
    }
    
    public function toArray() 
    {
        
        $array = array();
        
        $array['participantId']  = $this->m_sParticipantId;
        $array['note']           = $this->m_sGrade;
        $array['noteId']        =  $this->m_nGradeId;
        $array['totalPoints']    = $this->m_fTotalPoints;
        $array['isPassed']       = $this->m_bPassed;
        $array['hasKey']         = $this->m_bHasKey;
        $array['questionResults']= $this->m_aQuestionResults;
        $array['serailNumber']   = $this->m_nSerialNumber;
        $array['timeStamp']      = $this->m_sTimeStamp;
        
        return $array;
        
   }





    
}
?>
