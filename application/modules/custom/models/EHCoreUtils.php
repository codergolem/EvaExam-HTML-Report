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
 * This class contains  helper methods that are used in the creation of 
 * the HTML report.
 * @author Mario Diaz
 */

class EHCoreUtils {
    
     
     /**
      * Get the string constant that indicates the language of the examiner user
      * @param object $dbCon databse connection
      * @return string language constant
      */
     public static function getLanguage ($dbCon) 
     {
         
		$nUserID = '';
		if (isset($_SESSION) && isset($_SESSION["user_ID"]))
		{
			$nUserID = $_SESSION["user_ID"];
                        
		}

		// determine the fallback-language
		$sFallbackLang = "en";
                
                if ($nUserID != '')
		{
			// look up user language in DB
			$sql = "SELECT sprache FROM benutzer WHERE userid = 10";
                        $stm = $dbCon->query($sql);
			$aResult = $stm->fetchAll();
                    
                        
			if (is_array($aResult) && count($aResult) == 1 && intval($aResult[0]["sprache"]) > 0)
			{
				$sql = "SELECT datei FROM sprache WHERE sprache.sprache_idx = '".$aResult[0]["sprache"]."'";
				$stm = $dbCon->query($sql);
                                $aResult = $stm->fetchAll();
                               

				if (is_array($aResult) && count($aResult) == 1)
					$sLanguage = $aResult[0]["datei"];
			}
			else  //fallback
			{
				if(defined("DEFAULT_LANGUAGE")) 
                                {
					$sLanguage = DEFAULT_LANGUAGE;
                                }
                                else
                                {    
                                    $sLanguage = $sFallbackLang;
                                }
			}
		}
                
                return $sLanguage;
     }
}

?>
