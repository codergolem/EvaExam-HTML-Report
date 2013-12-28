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
 * EHSoapClient
 * This class is a small wrapper for the PHP soap client and is intended to 
 * reduce the work of praper the soap client to work with Evaexam/Evasys 
 * @author Mario Diaz
 *
 * 
 */

class EHSoapClient {
    
    
    /* @parameters
     * @$m_sEndpoint  WSLD file URL
     * @$m_oClient    Soap Client
     * @$m_cWsdlCache Determinates if the WSDL will be cached.
     */
    
    
    
    private $m_sEndpoint;
    private $m_oClient;
    private $m_cWsdlCache = WSDL_CACHE_NONE;
    
    function __construct ($parameters) 
    {
     /*If array looks for endpoint and cache parameters, if string
     * take it as the endpoint parameter.
     */
        
        if (is_array($parameters)) 
        {    
        
           $this->m_sEndpoint=$parameters["endpoint"];
           
           if (isset($parameters["cache"]))
           {
               if ($parameters["cache"])
               {
                   $this->m_cWsdlCache=WSDL_CACHE_DISK; 
               }
       
          }
       }
            
       else 
       {
           $this->m_sEndpoint=$parameters;  
       }
        
       //Set the PHP soap client with trace activated.
       
       $this->m_oClient = new SoapClient($this->m_sEndpoint, 
                                       array('trace' => 1,
                                             'cache_wsdl' => $this->m_cWsdlCache));
       
       //Set the header if provided
        if (isset($parameters["header"]))
        {
                
                $this->setHeader($parameters["header"]);
        }
    
   }
  
   
 
   //Set the soap header, @parameters:
   // @user=Login @pass=Password setted in EvaExam/Evasys WebService settings.
    
   public function setHeader($user,$pass) 
   {
       
        $auth = new SoapVar(array('Login'    => $user,
                                  'Password' => $pass),
                                      SOAP_ENC_OBJECT);
        
        $header = new SoapHeader('Exam','Header',$auth);
        
        $this->m_oClient->__setSoapHeaders($header);  
   }
    
    /**
     *  Wrapper Method for the __soapCall if a soap fault exception is thrown, 
     * then is catched and returned as response.
     */
   
    public function callFunction ($functionName, $arguments) 
    {
        
        try 
        {
            $response = $this->m_oClient->__soapCall($functionName, $arguments);
            
        }
        catch (SoapFault $fault) 
        {
            $response = $fault;
        }
        return $response;
    }
    
    //Getter and Setter Methods

    public function getEndpoint() {
        return $this->m_sEndpoint;
    }

    public function setEndpoint($endpoint) {
        $this->m_sEndpoint = $endpoint;
    }

    public function getClient() {
        return $this->m_oClient;
    }

    public function setClient($client) {
        $this->m_oClient = $client;
    }

    public function getCache() {
        return $this->cache;
    }

    public function setCache($cache) {
        $this->cache = $cache;
    }

 
  }
   

?>
