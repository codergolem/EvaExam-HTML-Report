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
 * It stores information for the grading key of an exam. Individual grade 
 * information is stored in instances of the class EHGrade
 * @author Mario Diaz
 *
 * 
 */

class EHGradingKey 
{
    
    private $m_nId;
    private $m_sName;
    private $m_sDescription;
    private $m_nPassedGradeId;
    private $m_fMinPassedPercentage;
    private $m_aGrades;
    
    public function getId() {
        return $this->m_nId;
    }

    public function setId($nId) {
        $this->m_nId = $nId;
    }

    public function getName() {
        return $this->m_sName;
    }

    public function setName($sName) {
        $this->m_sName = $sName;
    }

    public function getDescription() {
        return $this->m_sDescription;
    }

    public function setDescription($sDescription) {
        $this->m_sDescription = $sDescription;
    }

    public function getPassedGradeId() {
        return $this->m_nPassedGradeId;
    }

    public function setPassedGradeId($nPassedGradeId) {
        $this->m_nPassedGradeId = $nPassedGradeId;
    }

    public function getGrades() {
        return $this->m_aGrades;
    }

    public function setGrades($aGrades) {
        $this->m_aGrades = $aGrades;
    }
    
    public function getMinPassedPercentage() {
        return $this->m_fMinPassedPercentage;
    }

    public function setMinPassedPercentage($fMinPassedPercentage) {
        $this->m_fMinPassedPercentage = $fMinPassedPercentage;
    }
    
    public function getPassedGrade() {
        return $this->m_aGrades[$this->m_nPassedGradeId];
    }
    
     public function getGradeById(int $id) {
        return $this->m_aGrades[$id];
    }
    
    public function getGradeByName($name) {
        foreach ($this->m_aGrades as $grade) {
            if (strcmp($name,$grade->getName()) == 0 ) {
                return $grade;
            }
        }
    }
    
    
    






     
}

?>
