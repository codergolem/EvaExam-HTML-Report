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
 * Helper class to get a connection object to the EvaExam database.
 *
 * @author Mario Diaz
 */

class EhDbCon {
    
    
    private static $user;
    private static $pass;
    private static $dbname;
    private static $host;
       
    
    public static function init () 
    {
        
        self::$host   = '127.0.0.1';
        self::$dbname = 'evaexam';
        self::$pass   = 'ee123';
        self::$user   = 'evaexam';
        
    } 
    
    public static function getConnection () 
    {
        $parameters = array (
            'host'      => self::$host,
            'username'  => self::$user,
            'password'  => self::$pass,
            'dbname'    => self::$dbname);
                
        
       
            $db = Zend_Db::factory('pdo_mysql', $parameters);
            $db->getConnection();
        
        
            return $db;
            
    }
    
    public static function getUser() {
        return self::$user;
    }

    public static function setUser($user) {
        self::$user = $user;
    }

    public static function getPass() {
        return self::$pass;
    }

    public static function setPass($pass) {
        self::$pass = $pass;
    }

    public static function getDbname() {
        return self::$dbname;
    }

    public static function setDbname($dbname) {
        self::$dbname = $dbname;
    }

    public static function getHost() {
        return self::$host;
    }

    public static function setHost($host) {
        self::$host = $host;
    }


    
}

?>
