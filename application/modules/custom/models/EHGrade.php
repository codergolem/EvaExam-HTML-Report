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
 * It stores the grade information.
 * @author Mario Diaz
 *
 * 
 */

class EHGrade {
    
    private $m_nId;
    private $m_sName;
    private $m_fPercentage;
    
    
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

    public function getPercentage() {
        return $this->m_fPercentage;
    }

    public function setPercentage($nPercentage) {
        $this->m_fPercentage = $nPercentage;
    }


}

?>
