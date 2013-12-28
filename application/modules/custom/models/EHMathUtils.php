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
 * It contains several static functions that are used to make mathematical
 * calculations during the creation of the HTML Report.
 * @author Mario Diaz
 *
 * 
 */

class EHMathUtils {
   
    
    static function calculateStandartDeviation(array $aValues, $bSample = true) 
    {
        $mean = self::calculateMean($aValues);
        $fVariance = 0.0;
        
        foreach ($aValues as $value) 
        {
            if (is_numeric($value)) 
            {
                 $fVariance += pow($value - $mean,2);
            }
        }
        
        $fVariance /= ($bSample ? count($aValues) - 1 : count($aValues));
        $sd = bcsqrt($fVariance,3);
        return $sd;
        
    }
    
    
    static function calculateMean(array $aValues) 
    {
        $total = 0;
        $counter = 0;
        foreach ($aValues as $value) 
        {
            if (is_numeric($value)) 
            {
                $total = $total + $value;
                $counter++;
            }
        }
        $mean = bcdiv($total,$counter,3);
        return $mean;
    }
    
    
    static function calculateMedian(array $aValues) 
    {
        sort($aValues);
        $count = count($aValues); //total numbers in array
        $middleval = floor(bcdiv(($count-1),2,3)); // find the middle value, or the lowest middle value
        
        if($count % 2) 
        { // odd number, middle is the median
            $median = $aValues[$middleval];
        } 
        else 
        { // even number, calculate avg of 2 medians
            $low = $aValues[$middleval];
            $high = $aValues[$middleval+1];
            $median = bcdiv(($low+$high),2,3);
        }
        return $median;
   }
   
   static function calculatePercentage($total,$amount) 
   {
       return bcdiv(bcmul($amount, 100, 3),$total,3);
   }
   
   static function getAbsolutFromPercentage ($total,$percentage) 
   {
       return bcdiv(bcmul($percentage, $total, 3),100,3);
   }
}
    


?>
