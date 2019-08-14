<?php



class Thylak_ACH_Model_Process extends Mage_Core_Model_Abstract
{
    const XML_PATH_WIRECARD_CC_TEST_MODE = 'payment/ACH_cc/test_mode';
    const XML_PATH_WIRECARD_CC_USERNAME = 'payment/ACH_cc/username';
    const XML_PATH_WIRECARD_CC_BUSINESSCASESIGNATURE = 'payment/ACH_cc/business_case_signature';
    const XML_PATH_WIRECARD_CC_PASSWORD = 'payment/ACH_cc/password';
    const XML_PATH_WIRECARD_CC_USAGE = 'payment/ACH_cc/usage';
    const XML_PATH_WIRECARD_CC_DISPLAYERRORS = 'payment/ACH_cc/display_errors';
    const XML_PATH_WIRECARD_CC_TRANSACTIONTYPE = 'payment/ACH_cc/checkout_transactiontype';
    
    const XML_PATH_WIRECARD_EFT_TEST_MODE = 'payment/ACH_eft/test_mode';
    const XML_PATH_WIRECARD_EFT_USERNAME = 'payment/ACH_eft/username';
    const XML_PATH_WIRECARD_EFT_BUSINESSCASESIGNATURE = 'payment/ACH_eft/business_case_signature';
    const XML_PATH_WIRECARD_EFT_PASSWORD = 'payment/ACH_eft/password';
    const XML_PATH_WIRECARD_EFT_USAGE = 'payment/ACH_eft/usage';
    const XML_PATH_WIRECARD_EFT_DISPLAYERRORS = 'payment/ACH_eft/display_errors';
    const XML_PATH_WIRECARD_EFT_TRANSACTIONTYPE = 'payment/ACH_eft/checkout_transactiontype';
    
    // standard data for test access
    protected $host = 'c3-test.wirecard.com';
    protected $port = 443;
    protected $path = '/secure/ssl-gateway';
    protected $login = '56500';
    protected $businessCaseSignature = '56500';
    protected $pass = 'TestXAPTER';
    protected $isTestMode = true;
    protected $payment = NULL;
    protected $transactionTypeNodeName = '';
    protected $functionId = '';
    protected $isCapture = false;
    protected $transactionType = '';
    protected $displayErrors = 1;
    protected $transactionNodeName = '';


    /**
     * Set transaction type to "capture"
     *
     * @return  Inmedias_Wirecard_Model_Process
     */
    public function setIsCapture($capture = true) { 

        if ($capture) {
        
            if ($this->getPaymentMethod() == 'ACH_eft' && $this->getPayment()->getWirecardTransactionType() == 'AUTHORIZATION') {
            
                $this->transactionTypeNodeName = 'FNC_FT_DEBIT';
                $this->functionId = 'debit';
            } 
            else if ($this->getPayment()->getWirecardTransactionType() == 'PREAUTHORIZATION') {
            
                $this->transactionTypeNodeName = 'FNC_CC_CAPTURE_PREAUTHORIZATION';
                $this->functionId = 'preauthorization capture';
            } 
            else {
            
                $this->transactionTypeNodeName = 'FNC_CC_CAPTURE_AUTHORIZATION';
                $this->functionId = 'authorization capture';
            }
            $this->isCapture = true;
        } else {
        
            $this->isCapture = false;
        }
        
        return $this;
    }
    
    /**
     * Set transaction type to "capture"
     *
     * @return  Inmedias_Wirecard_Model_Process
     */
    public function getIsCapture() {
    
        return $this->isCapture;
    }
    

    /**
     * Set payment data to model
     *
     * @param   Varien_Object $payment
     * @return  Inmedias_Wirecard_Model_Process
     */
    public function setPayment($payment) {
	 
        $this->payment = $payment;
        switch ($this->getPaymentMethod()) {
        
            case 'ACH_eft':

                $this->setTransactionType(Mage::getStoreConfig(self::XML_PATH_WIRECARD_EFT_TRANSACTIONTYPE));

                switch ($this->getTransactionType()) {
                
                    case 'AUTHORIZATION':
					      
                        $this->transactionTypeNodeName = 'FNC_FT_AUTHORIZATION';
                        $this->functionId = 'authorization';
                        break;
                
                    case 'DEBIT':
                    
                        $this->transactionTypeNodeName = 'FNC_FT_DEBIT';
                        $this->functionId = 'debit';
                        break;
                        
                    default:
                    
                        $error = mage::helper('ACH')->__('Please choose a valid transaction type2.');
                        Mage::throwException($error);
                }
                break;

            default:

                $this->setTransactionType(Mage::getStoreConfig(self::XML_PATH_WIRECARD_CC_TRANSACTIONTYPE));
                
                switch ($this->getTransactionType()) {
                
                    case 'PREAUTHORIZATION':
                    
                        $this->transactionTypeNodeName = 'FNC_CC_PREAUTHORIZATION';
                        $this->functionId = 'preauthorization';
                        break;
                
                    case 'AUTHORIZATION':
                    
                        $this->transactionTypeNodeName = 'FNC_CC_AUTHORIZATION';
                        $this->functionId = 'authorization';
                        break;
                
                    case 'PURCHASE':
                    
                        $this->transactionTypeNodeName = 'FNC_CC_PURCHASE';
                        $this->functionId = 'purchase';
                        break;
                        
                    default:
                    
                        $error = mage::helper('ACH')->__('Please choose a valid transaction type999999992.');
                        Mage::throwException($error);
                }
                if ($this->isTestMode && !$this->getIsCapture()) {
                
                    //$this->payment->setCcNumber('4200000000000000');
                }
                
        }

        return $this;
    }

    /**
     * Get payment data from model
     *
     * @return  Varien_Object
     */
    public function getPayment() {
    
        return $this->payment;
    }
    
    /**
     * Get payment method from model
     *
     * @return  string
     */
    public function getPaymentMethod() {
    
        return $this->payment->getMethod();
    }
    
    /**
     * Set TransactionType to model
     *
     * @param   String $transactionType
     * @return  Inmedias_Wirecard_Model_Process
     */
    public function setTransactionType($transactionType) {
    
        $this->transactionType = $transactionType;
        
        return $this;
    }

    /**
     * Get TransactionType from model
     *
     * @return  String
     */
    public function getTransactionType() {
    
        return $this->transactionType;
    }

    /**
     * Sets different variables which will be used later 
     *
     * @return  Void
     */
    protected function setBaseData() {
      
       
        switch($this->getPaymentMethod()) {

            case 'ACH_eft':        
                // check for test mode or live mode
                $this->isTestMode = Mage::getStoreConfig(self::XML_PATH_WIRECARD_EFT_TEST_MODE);
                $username = Mage::getStoreConfig(self::XML_PATH_WIRECARD_EFT_USERNAME);
                $businessCaseSignature = Mage::getStoreConfig(self::XML_PATH_WIRECARD_EFT_BUSINESSCASESIGNATURE);
                $password = Mage::getStoreConfig(self::XML_PATH_WIRECARD_EFT_PASSWORD);
                $this->displayErrors = Mage::getStoreConfig(self::XML_PATH_WIRECARD_EFT_DISPLAYERRORS);
                $this->usage = Mage::getStoreConfig(self::XML_PATH_WIRECARD_EFT_USAGE);
                $this->transactionNodeName = 'FT_TRANSACTION';
                break;
                
            case 'ACH_cc':
                // check for test mode or live mode
                $this->isTestMode = Mage::getStoreConfig(self::XML_PATH_WIRECARD_CC_TEST_MODE);
                $username = Mage::getStoreConfig(self::XML_PATH_WIRECARD_CC_USERNAME);
                $businessCaseSignature = Mage::getStoreConfig(self::XML_PATH_WIRECARD_CC_BUSINESSCASESIGNATURE);
                $password = Mage::getStoreConfig(self::XML_PATH_WIRECARD_CC_PASSWORD);
                $this->displayErrors = Mage::getStoreConfig(self::XML_PATH_WIRECARD_CC_DISPLAYERRORS);
                $this->usage = Mage::getStoreConfig(self::XML_PATH_WIRECARD_CC_USAGE);
                $this->transactionNodeName = 'CC_TRANSACTION';
                break;
            
            default:
                $this->transactionNodeName = 'Test';
                return;
        }

          if (!$this->isTestMode && strlen($username) && strlen($password)) {
        
            // set live access data instead of test data
            $this->login = $username;
            $this->businessCaseSignature = $businessCaseSignature;
            $this->pass = $password;
            //$this->host = 'c3.wirecard.com';
			  $this->host = 'https://www.paymentsgateway.net/cgi-bin/posttest.pl';
        }
    }    

    /**
     * Main method of this class
     * Send xml string to Wirecard server and record response
     *
     * @return  void
     */
    public function process() {
	
		/*$myFile = "c:/testFile.txt";
		$fh = fopen($myFile, 'w');
		//fwrite($fh, $customerinfo);
		fclose($fh);
	 */
        // don't perform capture when a purchase has already happened
        if ($this->getIsCapture() && ($this->getPayment()->getWirecardTransactionType() == 'PURCHASE' || $this->getPayment()->getWirecardTransactionType() == 'DEBIT')) {
        
            return;
        }
		
		
        
        $this->setBaseData();
       
	   
	   
        // generate XML code for request
       $poststring = $this->getRequestXml();
		
	  
        // clean up for logging; delete credit card data
      /*  $cleanedPostString = ereg_replace('<CREDIT_CARD_DATA>.*<\/CREDIT_CARD_DATA>', '<CREDIT_CARD_DATA><!-- removed --><\/CREDIT_CARD_DATA>', $poststring);
        $cleanedPostString = ereg_replace('<EXTERNAL_ACCOUNT>.*<\/EXTERNAL_ACCOUNT>', '<EXTERNAL_ACCOUNT><!-- removed --><\/EXTERNAL_ACCOUNT>', $poststring);*/

        // send request and receive response
        $output = $this->sendRequest($poststring);
        // write log to database
        $orderIncrementId = $this->getPayment()->getOrder()->getIncrementId();
		
		if ( $this->processTransactionResponseMessageIntoResponseArray( $output ) === false )					
		{					
			
			 Mage::throwException("false");				
		}		
		
		
		
       // mage::helper('wirecard')->saveLog($orderIncrementId, $cleanedPostString, $output, $this->functionId);
        
        // manage response, transfer to array
        //$xmlResponse = simplexml_load_string($output);
		
        //$arrayResponse = $this->xmlObject2Array($xmlResponse);
        
        // Error handling of general wirecard errors
        /*if (!isset($arrayResponse['W_RESPONSE'])) {
        
            $error = mage::helper('wirecard')->__('An error occured, please try later or choose a different payment method.');
            Mage::throwException($error);
        }
        if (isset($arrayResponse['W_RESPONSE']['ERROR'])) {
        
            if ($this->displayErrors) {
            
                $errorNumber = $arrayResponse['W_RESPONSE']['ERROR']['Number'];
                $errorMessage = $arrayResponse['W_RESPONSE']['ERROR']['Message'];
                $errorAdvice = ( isset($arrayResponse['W_RESPONSE']['ERROR']['Advice']) ? '(' . ( is_array($arrayResponse['W_RESPONSE']['ERROR']['Advice']) ? implode(' ', $arrayResponse['W_RESPONSE']['ERROR']['Advice']) : $arrayResponse['W_RESPONSE']['ERROR']['Advice'] ) . ')' : '' );

                $error = mage::helper('wirecard')->__('Error %s: %s %s', $errorNumber, $errorMessage, $errorAdvice);
            } else {
            
                $error = mage::helper('wirecard')->__('An error occured, please try later or choose a differenty payment method.');
            }
            Mage::throwException($error);
        }
        
        switch($this->getPaymentMethod()) {
            
            case 'wirecard_eft':
            
                $arrayProcessingStatus = $arrayResponse['W_RESPONSE']['W_JOB'][$this->transactionTypeNodeName]['FT_TRANSACTION']['PROCESSING_STATUS'];
                break;
            
            case 'wirecard_cc':
            
                $arrayProcessingStatus = $arrayResponse['W_RESPONSE']['W_JOB'][$this->transactionTypeNodeName]['CC_TRANSACTION']['PROCESSING_STATUS'];
                break;
        }
        
        // Error handling of specific wirecard errors
        if ($arrayProcessingStatus['FunctionResult'] != 'ACK') {
        
            if ($this->displayErrors) {
            
                if (isset($arrayProcessingStatus['ERROR'])) {

                    $errorNumber = $arrayProcessingStatus['ERROR']['Number'];
                    $errorMessage = $arrayProcessingStatus['ERROR']['Message'];
                    $errorAdvice = ( isset($arrayProcessingStatus['ERROR']['Advice']) ? '(' . ( is_array($arrayProcessingStatus['ERROR']['Advice']) ? implode(' ', $arrayProcessingStatus['ERROR']['Advice']) : $arrayProcessingStatus['ERROR']['Advice'] ) . ')' : '' );
    
                    $error = mage::helper('wirecard')->__('Error %s: %s %s', $errorNumber, $errorMessage, $errorAdvice);
                } else {
                
                    $errorNumber = $arrayProcessingStatus['DETAIL']['ReturnCode'];
                    $errorMessage = $arrayProcessingStatus['DETAIL']['Message'];
                    $errorAdvice = ( isset($arrayProcessingStatus['ERROR']['Advice']) ? '(' . ( is_array($arrayProcessingStatus['DETAIL']['Advice']) ? implode(' ', $arrayProcessingStatus['DETAIL']['Advice']) : $arrayProcessingStatus['DETAIL']['Advice'] ) . ')' : '' );
    
                    $error = mage::helper('wirecard')->__('Error %s: %s %s', $errorNumber, $errorMessage, $errorAdvice);
                }
            } else {
            
                $error = mage::helper('wirecard')->__('An error occured, please try later or choose a differenty payment method.');
            }
            Mage::throwException($error);
        }
        
        // success: get GuWID and add to payment object
        if (!$this->getIsCapture()) {
            $guWID = $arrayProcessingStatus['GuWID'];
            $this->getPayment()
                ->setWirecardGuwid($guWID)
                ->setWirecardTransactionType($this->getTransactionType());
        }*/
                
        return;
    }

    /**
     * Send XML data to Wirecard server via SSL and read response XML data
     * Function taken from Wirecard Documentation
     *
     * @param   String $poststring
     * @return  String
     */
	/* private function sendRequest( $postURL, $requestMessage )						
	{						
		$ch = curl_init();					
									
		// Set up curl parameters					
		curl_setopt( $ch, CURLOPT_URL, $postURL );			// set remote address		
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );		// Make CURL pass the response as a curl_exec return value instead of outputting to STDOUT			
		curl_setopt( $ch, CURLOPT_POST, 1 );	 			// Activate the POST method	
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );					
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );					
							
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $requestMessage );	// add the request message itself				
							
		// execute the connexion					
		$result = curl_exec( $ch );					
		$clean_data = str_replace("\n","&",trim(str_replace("endofdata", "", trim($result))));	
						
		$debugoutput = curl_getinfo($ch);					
		$curl_error_message = curl_error( $ch ); // must retrieve an error message (if any) before closing the curl object 					
							
		curl_close($ch);					
					
							
		if ( $result === false )					
		{					
			$this->errorString = self::GATEWAY_ERROR_CURL_ERROR.': '.$curl_error_message;				
			return false;				
		}					
							
		// we do not need the header part of the response, trim it off the result					
//		$pos = strstr( $result, "\n" );					
//		$result = substr( $result, $pos );					
	    				
		return $clean_data;					
	}					*/
    protected function sendRequest($poststring) {
        $ch = curl_init();
		// Set up curl parameters					
		curl_setopt( $ch, CURLOPT_URL, "https://www.paymentsgateway.net/cgi-bin/posttest.pl" );			// set remote address		
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );		// Make CURL pass the response as a curl_exec return value instead of outputting to STDOUT			
		curl_setopt( $ch, CURLOPT_POST, 1 );	 			// Activate the POST method	
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );					
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );					
							
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $poststring );	// add the request message itself				
							
		// execute the connexion					
		$result = curl_exec( $ch );		
		$clean_data = str_replace("\n","&",trim(str_replace("endofdata", "", trim($result))));	
		return 	$clean_data;		
		//Mage::throwException("test1");
        // send to wirecard server via ssl
        //$fp = fsockopen('ssl://'.$this->host, $this->port, $errno, $errstr, 5);
       /* if (!$fp) {
            //error; tell us
            Mage::throwException($errstr  . '(' . $errno . ')');
        } else {
            //send the server request
            fputs($fp, "POST $this->path HTTP/1.0\r\n");
            fputs($fp, "Host: $this->host\r\n");
            fputs($fp, "Content-type: text/xml\r\n");
            fputs($fp, "Content-length: ".strlen($poststring)."\r\n");
            fputs($fp, "Authorization:
            Basic ".base64_encode($this->login.":".$this->pass."\r\n"));
            fputs($fp, "Connection: close\r\n");
            fputs($fp, "\r\n");
            fputs($fp, $poststring . "\r\n\r\n");
            
            // prepare for reading the response
            stream_set_timeout($fp, 30);
            
            // here we save the response body - XML response from WireCard
            $output = '';
            
            // here we store the HTTP headers
            $headers = '';
            
            // temp. variable for detecting the end of HTTP headers.
            $is_header = 1;
            
           while(!feof($fp)) {
            
                $buffer = fgets($fp, 128);
                
                // fgets on SSL socket
                if ($buffer == FALSE) {
                    break;
                }
                
                if (!$is_header) {
                    $output .= $buffer;
                }
                
                if ($buffer == "\r\n") {
                    $is_header = 0;
                }
                
                if ($is_header) {
                    $headers .= $buffer;
                }
            }*/
            //close fp - we are done with it
            //fclose($fp);

           
       // }
		// return $output;
    }
    
    /**
     * Create XML string for request based on payment data
     *
     * @return  string
     */
    protected function getRequestXml() {
	
    
        // create Request XML from scratch
        /*$xmlMain = new SimpleXMLElement('<WIRECARD_BXML xmlns:xsi="http://www.w3.org/1999/XMLSchema-instance" xsi:noNamespaceSchemaLocation="wirecard.xsd" />');
        
        $xmlRequest = $xmlMain->addChild('W_REQUEST');

        $xmlJob = $xmlRequest->addChild('W_JOB');
        $xmlJob->addChild('JobID', 2);
        $xmlJob->addChild('BusinessCaseSignature', $this->businessCaseSignature);

        $xmlTransactionType = $xmlJob->addChild($this->transactionTypeNodeName);
        $xmlTransactionType->addChild('FunctionID', $this->functionId);
        
        $xmlTransaction = $xmlTransactionType->addChild($this->transactionNodeName);
       
        $xmlTransaction->addChild('TransactionID', $this->getPayment()->getOrder()->getIncrementId());
        $xmlTransaction->addChild('Amount', number_format($this->getPayment()->getAmount() * 100, 0, '', ''));
        $xmlTransaction->addChild('Currency', $this->getPayment()->getOrder()->getGlobalCurrencyCode());
        $xmlTransaction->addChild('CountryCode', $this->getPayment()->getOrder()->getBillingAddress()->getCountryId());
        
        if ($this->getIsCapture() || $this->getTransactionType() == 'PURCHASE' || $this->getTransactionType() == 'DEBIT') {
        
            // fields only at capture or purchase
            $xmlTransaction->addChild('SalesDate', $this->getPayment()->getOrder()->getCreatedAt());
            $xmlTransaction->addChild('Usage', mage::helper('wirecard')->__($this->usage, $this->getPayment()->getOrder()->getIncrementId()));
        }
        
        if ($this->getIsCapture() && $this->getPaymentMethod() != 'wirecard_eft') {
        
            // field only at cc capture
            $xmlTransaction->addChild('GuWID', $this->getPayment()->getWirecardGuwid());
        } 
        else {

            if ($this->getPaymentMethod() == 'wirecard_cc') {

                // fields for initial request
                $xmlCreditCardData = $xmlTransaction->addChild('CREDIT_CARD_DATA');
                $xmlCreditCardData->addChild('CreditCardNumber', $this->getPayment()->getCcNumber());
                $xmlCreditCardData->addChild('CVC2', $this->getPayment()->getCcCid());
                $xmlCreditCardData->addChild('ExpirationYear', $this->getPayment()->getCcExpYear());
                $xmlCreditCardData->addChild('ExpirationMonth', ( strlen($this->getPayment()->getCcExpMonth()) == 2 ? $this->getPayment()->getCcExpMonth() : '0' . $this->getPayment()->getCcExpMonth() ));
                $xmlCreditCardData->addChild('CardHolderName', $this->getPayment()->getCcOwner());
            
            }
        }

        if ($this->getPaymentMethod() == 'wirecard_eft') {
        
            $xmlExternalAccount = $xmlTransaction->addChild('EXTERNAL_ACCOUNT');
            $xmlExternalAccount->addChild('FirstName', $this->getPayment()->getAccountOwner());
            $xmlExternalAccount->addChild('LastName', $this->getPayment()->getAccountOwner());
            $xmlExternalAccount->addChild('AccountNumber', $this->getPayment()->getAccountNumber());
            $xmlExternalAccount->addChild('BankCode', $this->getPayment()->getBankNumber());
            $xmlExternalAccount->addChild('Country', $this->getPayment()->getOrder()->getBillingAddress()->getCountryId());
        }
     
        $xml = $xmlMain->asXML();
		*/
      
        switch($this->getPaymentMethod()) {

            case 'ACH_eft':      
					$x = "pg_merchant_id=".$this->login;
					$x .= "&pg_password=".$this->pass;
					$x .= "&pg_transaction_type=20";
					$x .= "&pg_total_amount=".number_format($this->getPayment()->getAmount() * 100, 0, '', '');
					$x .= "&pg_billto_postal_name_company=ACH";
					$x .= "&ecom_billto_postal_name_first=".$this->getPayment()->getOrder()->getBillingAddress()->getFirstname();
					$x .= "&ecom_billto_postal_name_last=".$this->getPayment()->getOrder()->getBillingAddress()->getLastname();
					$x .= "&ecom_billto_postal_street_line1=".$this->getPayment()->getOrder()->getBillingAddress()->getStreet(1);
					$x .= "&ecom_billto_postal_city=".$this->getPayment()->getOrder()->getBillingAddress()->getCity();
					$x .= "&ecom_billto_postal_stateprov=".$this->getPayment()->getOrder()->getBillingAddress()->getRegion();
					$x .= "&ecom_billto_postal_postalcode=".$this->getPayment()->getOrder()->getBillingAddress()->getPostcode();
					$x .= "&ecom_payment_check_account_type=".$this->getPayment()->getBankName();
					$x .= "&ecom_payment_check_account=".$this->getPayment()->getAccountNumber();
					$x .= "&ecom_payment_check_trn=".$this->getPayment()->getBankNumber();
					$x .= "&pg_avs_method=00000";
					$x .= "&endofdata&";break;
			default :
			            $ccType =$this->getPayment()->getCcType();
						if ($ccType=='VI')			
						$ccType = 'VISA';
						if ($ccType=='MC')			
						$ccType = 'Master Card';
						if ($ccType=='DI')			
						$ccType = 'Discover';
						if ($ccType=='AE')			
						$ccType = 'American Express';	
					$x = "pg_merchant_id=".$this->login;
					$x .= "&pg_password=".$this->pass;
					$x .= "&pg_transaction_type=15";
					$x .= "&pg_total_amount=".number_format($this->getPayment()->getAmount() * 100, 0, '', '');
					$x .= "&pg_billto_postal_name_company=ACH";
					$x .= "&ecom_billto_postal_name_first=".$this->getPayment()->getOrder()->getBillingAddress()->getFirstname();
					$x .= "&ecom_billto_postal_name_last=".$this->getPayment()->getOrder()->getBillingAddress()->getLastname();
					$x .= "&ecom_billto_postal_street_line1=".$this->getPayment()->getOrder()->getBillingAddress()->getStreet(1);
					$x .= "&ecom_billto_postal_city=".$this->getPayment()->getOrder()->getBillingAddress()->getCity();
					$x .= "&ecom_billto_postal_stateprov=".$this->getPayment()->getOrder()->getBillingAddress()->getRegion();
					$x .= "&ecom_billto_postal_postalcode=".$this->getPayment()->getOrder()->getBillingAddress()->getPostcode();						
					$x .= "&ecom_payment_Card_Name=".$this->getPayment()->getCcOwner();		
					$x .= "&ecom_payment_Card_Type=".$ccType;					
					$x .= "&ecom_payment_Card_Number=".$this->getPayment()->getCcNumber();
					$x .= "&ecom_payment_Card_ExpDate_Month=". $this->getPayment()->getCcExpMonth();
					$x .= "&ecom_payment_Card_ExpDate_Year=".$this->getPayment()->getCcExpYear();								
					$x .= "&pg_Original_authorization_code=42344&endofdata&";
					 break;
			}		

	return $x;
		
		
       // return $xml;
    }
    
    /**
     * Convert XML object to Array
     *
     * @param   SimpleXmlObject $node
     * @return  Array
     */
    protected function xmlObject2Array($node) {
    
        $xmlArray = array();
        if(is_object($node)){
            settype($node,'array') ;
        }
        foreach ($node as $key=>$value){
            if(is_array($value)||is_object($value)){
                $xmlArray[$key] = $this->xmlObject2Array($value);
            }else{
                $xmlArray[$key] = $value;
            }
        }
        return $xmlArray;
    } 
	
	private function processTransactionResponseMessageIntoResponseArray ($responseMessage )						
	{				

        
	   
		$xmlres = array();					
		$xmlres = parse_str($responseMessage);					
//		$xmlres = $responseMessage;	
       
						
		if ( $xmlres === false )					
		{					
			if ( strlen( $this->errorString ) == 0 )				
			{				
				 Mage::throwException("GATEWAY_ERROR_RESPONSE_XML_MESSAGE_ERROR");		
			}				
			return false;				
		}					
			
		
		$responseArray["raw-XML-response"] = htmlentities($responseMessage);					
					
		$statusCode = trim( $pg_response_code);					
	  	
		$statusDescription = trim($pg_response_description);					
							
		$responseArray["statusCode"] = $statusCode;					
		$responseArray["statusDescription"] = $statusDescription;					
							
		// Three digit codes indicate a repsonse from the Securepay gateway (error detected by gateway) 					
							
		 				
		$responseArray["messageID"] = trim($pg_trace_number);	
						
		//$responseArray["messageTimestamp"] = trim($xmlres['SecurePayMessage']['MessageInfo']['messageTimestamp']);					
		//$responseArray["apiVersion"] = trim($xmlres['SecurePayMessage']['MessageInfo']['apiVersion']);					
		$responseArray["RequestType"] =  trim($pg_response_type);	
		
		 if ((strcasecmp( $responseArray["RequestType"], "A" ) != 0) )
		 {
			 Mage::throwException("GATEWAY_ERROR_TXN_DECLINED"." :".$responseArray["RequestType"].": ".trim($pg_response_description));		
			return false;	
		 }	
		 
		
				
		//$responseArray["merchantID"] = trim($pg_merchant_id);					
		//$responseArray["txnType"] = trim($xmlres['SecurePayMessage']['Payment']['TxnList']['Txn']['txnType']);					
		//$responseArray["txnSource"] = trim($xmlres['SecurePayMessage']['Payment']['TxnList']['Txn']['txnSource']);					
		 
		//$responseArray["amount"] = trim($pg_total_amount);	
	
		$responseArray["approved"] = trim($pg_response_description);
				
		//$responseArray["approved"] = trim($pg_preauth_description);	
		 $responseArray["responseCode"] = trim($pg_response_code);					
		//$responseArray["responseCode"] = trim($pg_preauth_code);	
						
		//$responseArray["responseText"] = trim($pg_preauth_result);	
		  $responseArray["banktxnID"] =   trim($pg_trace_number);
		//				
		//$responseArray["banktxnID"] = trim($xmlres['SecurePayMessage']['Payment']['TxnList']['Txn']['txnID']);					
		//$responseArray["settlementDate"] = trim($xmlres['SecurePayMessage']['Payment']['TxnList']['Txn']['settlementDate']);					
						
						
				
		 	
					
		/* field "successful" = "Yes" means "triggered transaction successfully registered", anything else is failure */					
		/* responseCodes:					
			00 indicates approved,				
			08 is Honor with ID (approved) and				
			77 is Approved (ANZ only).				
			Any other 2 digit code is a decline or error from the bank. */				
		
		if ((strcasecmp( $responseArray["RequestType"], "A" ) == 0) &&					
			(strcmp( $responseArray["responseCode"], "A01" ) === 0 ||				
			 strcmp( $responseArray["responseCode"], "D" ) === 0  ) )				
		{	
							
			return true;				
		}					
		else					
		{		
			 Mage::throwException("GATEWAY_ERROR_TXN_DECLINED"." (".$responseArray["approved"]."): ".$responseArray["responseCode"]);			
			return false;				
		}	
		
		
		
						
	}						
}
