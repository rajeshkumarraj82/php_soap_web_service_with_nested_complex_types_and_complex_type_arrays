<?php
/*
This code demonstrates how to create a SOAP web service that will return nested complex data types and an array of complex data types. 
This demo web service will return information about a stock such as daily values, CEO info and last 3 year financials.
*/
require_once "lib/nusoap.php";


/*
This is the main class Stock which will be sent as a web service response.
This class has references for CEO and YearlyFinancial classes
*/
class Stock{
	
	public $stockId;
	public $symbol;
	public $open;
	public $high;
	public $low;
	public $close;
	public $ceo; // Reference for CEO object
	public $lastThreeYearFinancial; // Reference for array of YearlyFinancial objects
		
	function set_stockId($stockId) {$this->stockId = $stockId;}
	function get_stockId() {return $this->stockId;}
	
	function set_symbol($symbol) {$this->symbol = $symbol;}
	function get_symbol() {return $this->symbol;}
	
	function set_open($open) {$this->open = $open;}
	function get_open() {return $this->open;}
	
	function set_high($high) {$this->high = $high;}
	function get_high() {return $this->high;}
	
	function set_low($low) {$this->low = $low;}
	function get_low() {return $this->low;}
	
	function set_close($close) {$this->close = $close;}
	function get_close() {return $this->close;}
	
	function set_ceo($ceo) {$this->ceo = $ceo;} //Accepts CEO object as a parameter
	function get_ceo() {return $this->ceo;}// Returns CEO object
	
	//Accepts array of  YearlyFinancial objects as a parameter
	function set_lastThreeYearFinancial($lastThreeYearFinancial) {$this->lastThreeYearFinancial = $lastThreeYearFinancial;}
	function get_lastThreeYearFinancial() {return $this->lastThreeYearFinancial;} //Returns array of  YearlyFinancial objects
	
}

//This CEO class is referenced in Stock class.So we can say CEO class is nested inside Stock class.
class CEO{
	public $ceoId;
	public $name;
	public $salary;
	public $age;
	
	function set_ceoId($ceoId) {$this->ceoId = $ceoId;}
	function get_ceoId() {return $this->ceoId;}
	
	function set_name($name) {$this->name = $name;}
	function get_name() {return $this->name;}
	
	function set_salary($salary) {$this->salary = $salary;}
	function get_salary() {return $this->salary;}
	
	function set_age($age) {$this->age = $age;}
	function get_age() {return $this->age;}
	
}
/*
Stock class is having a reference to an array of YearlyFinancial objects. 
So Stock and YearlyFinancial classes have a one-to-many relationship.
*/
class YearlyFinancial{
	
	public $year;
	public $grossRevenue;
	public $netRevenue;
	
	function set_year($year) {$this->year = $year;}
	function get_year() {return $this->year;}
	
	function set_grossRevenue($grossRevenue) {$this->grossRevenue = $grossRevenue;}
	function get_grossRevenue() {return $this->grossRevenue;}
	
	function set_netRevenue($netRevenue) {$this->netRevenue = $netRevenue;}
	function get_netRevenue() {return $this->netRevenue;}
	
}

//This class represents web service request
class StockInfoRequest{
	public $symbol;
	public $tradingDate;
	
	function set_symbol($symbol) {$this->symbol = $symbol;}
	function get_symbol() {return $this->symbol;}
	
	function set_tradingDate($tradingDate) {$this->tradingDate = $tradingDate;}
	function get_tradingDate() {return $this->tradingDate;}
}

// this method returns the array of objects of YearlyFinancial class
function getYearlyFinancials(){
	
	$arrayOfYearlyFinancialArray = array();
	
	$yearlyFinancial_1 = new YearlyFinancial();
	$yearlyFinancial_1->set_year(2019);
	$yearlyFinancial_1->set_grossRevenue(5.5);
	$yearlyFinancial_1->set_netRevenue(4.4);
	array_push($arrayOfYearlyFinancialArray, $yearlyFinancial_1);
	
	$yearlyFinancial_2 = new YearlyFinancial();
	$yearlyFinancial_2->set_year(2018);
	$yearlyFinancial_2->set_grossRevenue(6.5);
	$yearlyFinancial_2->set_netRevenue(5.0);
	array_push($arrayOfYearlyFinancialArray, $yearlyFinancial_2);
	
	$yearlyFinancial_3 = new YearlyFinancial();
	$yearlyFinancial_3->set_year(2017);
	$yearlyFinancial_3->set_grossRevenue(5.0);
	$yearlyFinancial_3->set_netRevenue(4.0);
	array_push($arrayOfYearlyFinancialArray, $yearlyFinancial_3);
	
	return $arrayOfYearlyFinancialArray;
	
}

function getCEO($ceoId){
	
	$ceo = new CEO();
	$ceo->set_ceoId(1);
	$ceo->set_name('Satya Nadella');
	$ceo->set_salary(13.24);
	$ceo->set_age(50);
	return $ceo;
	
}

//This method returns the Stock object that will be sent as a web service response.
function getStock($symbol){
	
	$stock = new Stock();
	$stock->set_stockId(1);
	$stock->set_symbol('MSFT');
	$stock->set_open(179.50);
	$stock->set_high(180.00);
	$stock->set_low(177.00);
	$stock->set_close(178.68);
	$stock->set_ceo(getCEO(1)); //Here we attach an object of CEO class to Stock
	$stock->set_lastThreeYearFinancial(getYearlyFinancials()); //Here we attach an array of YearlyFinancial objects to Stock
	return $stock;
	
}

//Initialize SOAP service with WSDL ...
$server = new soap_server();
$namespace = 'http://localhost/stock_soap_ws/GetStockService.php?WSDL';
$server->configureWSDL('GetStockService', $namespace);

//Adding a complex type that represents the CEO class
$server->wsdl->addComplexType('CEO','complexType','struct','all','',
array(
	'ceoId' => array('name' => 'ceoId','type' => 'xsd:int'),
	'name' => array('name' => 'name','type' => 'xsd:string'),
	'salary' => array('name' => 'salary','type' => 'xsd:decimal'),
	'age' => array('name' => 'age','type' => 'xsd:int')
	)
);

//Adding a complex type that represents the YearlyFinancial class
$server->wsdl->addComplexType('YearlyFinancial','complexType','struct','all','',
array(
	'year' => array('name' => 'year','type' => 'xsd:int'),
	'grossRevenue' => array('name' => 'grossRevenue','type' => 'xsd:decimal'),
	'netRevenue' => array('name' => 'netRevenue','type' => 'xsd:decimal')
	)
);

//Adding a complex type that represents an Array of YearlyFinancial objetcs
$server->wsdl->addComplexType('ArrayOfYearlyFinancial','complexType','array','','SOAP-ENC:Array',
        array(),
        array(
            array(
                'ref' => 'SOAP-ENC:arrayType',
                'wsdl:arrayType' => 'tns:YearlyFinancial[]'
            )
        )
);

//Adding a complex type that represents the Stock class
$server->wsdl->addComplexType('Stock','complexType','struct','all','',
array(
	'stockId' => array('name' => 'stockId','type' => 'xsd:int'),
	'symbol' => array('name' => 'symbol','type' => 'xsd:string'),
	'open' => array('name' => 'open','type' => 'xsd:decimal'),
	'high' => array('name' => 'high','type' => 'xsd:decimal'),
	'low' => array('name' => 'low','type' => 'xsd:decimal'),
	'close' => array('name' => 'close','type' => 'xsd:decimal'),
	'ceo' => array('name' => 'ceo','type' => 'tns:CEO'), //Entry for CEO Complex Type
	'lastThreeYearFinancial' => array('name' => 'lastThreeYearFinancial','type' => 'tns:ArrayOfYearlyFinancial')//Entry for ArrayOfYearlyFinancial Complex Type
	)
);

//Adding a complex type that represents the StockInfoRequest class (for web service request)
$server->wsdl->addComplexType('StockInfoRequest','complexType','struct','all','',
array(
	'symbol' => array('name' => 'symbol','type' => 'xsd:string'),
	'tradingDate' => array('name' => 'tradingDate','type' => 'xsd:string')
	)
);

//Register the web service operation
$server->register("getStock",
	array('symbol' => 'tns:StockInfoRequest'), //Request Object Type
    array('return' => 'tns:Stock') //Response Object Type
);

if ( !isset( $HTTP_RAW_POST_DATA ) ) $HTTP_RAW_POST_DATA =file_get_contents( 'php://input' );
$server->service($HTTP_RAW_POST_DATA);


?>