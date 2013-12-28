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
 * This Special file contains the information to insert the link to the EvaExam
 * HTML Report Plugin in the examiner user interface. The file is automatically
 * recognized by EvaExam.
 *
 * Please put this file into customer/ dir. Do not rename it or its functions.
 * 
 *
 * @author Falko Timm, Mario Diaz
 * @package defaultPackage
 * @example $aLink[] = array(	'imageSrc' => "bilder/addgroup.gif",
 *					 			'link' => "fadmin.php?mode=show",
 *								'linkText' => "Linktext",
 *								'altText' => "AltText",
 * 								'cssClass' => "");
 */

function addMenuItemActiveAccountExam()
{
	
	
// 	$aLink[] = array(	'imageSrc' => "bilder/filter.gif",
// 						'link' => "psCsvModuleConverter.php",
// 						'linkText' => "CSV XML Module Converter",
// 						'altText' => "CSV XML Module Converter",
// 						'cssClass' => "");
	
	
	$aLink[] = array(	'imageSrc' => "bilder/filter.gif",
			
			'link' => "index.php?mca=custom/ehtmlreport/index&PHPSESSID=",
                        'linkText' => "Exam HTML Report",
			'altText' => "Exam HTML Report Plugin",
			'cssClass' => "");
	
	
	
	
	return $aLink;
}
