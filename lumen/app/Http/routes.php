<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {
    return $app->version();
});
 
$app->group([
	'prefix' => 'api/clients',
	'namespace' => 'App\Http\Controllers'
], function () use ($app) {
	$app->get('', 'ClientsController@index');
	$app->get('{id}', 'ClientsController@show');
	$app->post('', 'ClientsController@store');
	$app->put('{id}','ClientsController@update');
	$app->delete('{id}', 'ClientsController@destroy');
});

$app->group([
    'prefix' => 'api/clients/{client}/addresses',
    'namespace' => 'App\Http\Controllers'
], function () use ($app) {
    $app->get('', 'AddressesController@index');
    $app->get('{id}', 'AddressesController@show');
    $app->post('', 'AddressesController@store');
    $app->put('{id}', 'AddressesController@update');
    $app->delete('{id}', 'AddressesController@destroy');
}); 

$app->get('tcu', function(){
	$client = new \Zend\Soap\Client('http://contas.tcu.gov.br/debito/CalculoDebito?wsdl');

	echo "Informações do servidor: <br/>";
	print_r($client->getOptions());
	echo "Funções:<br/>";
	print_r($client->getFunctions());
	echo "Tipos:<br/>";
	print_r($client->getTypes());

	print_r($client->obterSaldoAtualizado([
		'parcelas' =>[
			'parcela'=> [
				'data' => '1992-02-01',
				'tipo' => 'D',
				'valor'=> '10000'
			]

		],
		'aplicaJuros' => true,
		'dataAtualizacao' => '2016-10-10'
	]));

});


/*
Routes dosen't accept dots (son-soap.wsdl) ?- 
*/

$uri = 'http://127.0.0.1:8080';

$app->get('soap/wsdl', function () use ($uri) {
   
   //phpinfo();
     $autoDiscover = new \Zend\Soap\AutoDiscover();
    $autoDiscover->setUri("$uri/server");
    $autoDiscover->setServiceName('SONSOAP');
    $autoDiscover->addFunction('soma');
    $autoDiscover->handle(); 

});

$app->post('server', function () use ($uri) {
    $server = new \Zend\Soap\Server("$uri/config.wsdl", [
        'cache_wsdl' => WSDL_CACHE_NONE
    ]);
    $server->setUri("server");
    return $server
        ->setReturnResponse(true)
        ->addFunction('soma')
        ->handle();
});
$app->get('soap_test', function () use ($uri) {
	
     $client = new \Zend\Soap\Client("$uri/config.wsdl", [
        'cache_wsdl' => WSDL_CACHE_NONE 
    ]);
	
	echo '<pre>';
	echo "Informações do servidor: <br/>";
	print_r($client->getOptions());
	echo "Funções:<br/>";
	print_r($client->getFunctions());
	echo "Tipos:<br/>";
	/*
	print_r($client->getTypes());*/
    
    //print_r($client->soma(1, 1));
	 
});
/*
 
//SOAP SERVER com CLIENT
$uriClient = "$uri/client";
$app->get('client/son-soap.wsdl', function () use ($uriClient){
	$autoDiscover = new \Zend\Soap\AutoDiscover();
	$autoDiscover->setUri("$uriClient/server");
	$autoDiscover->setServiceName('WEB_SERVER_SOAP');
	$autoDiscover->addFunction('soma');
	$autoDiscover->handle();
});

$app->post('client/server', function () use ($uriClient){
	$server = new \zend\Soap\Server("$uriClient/son-soap.wsdl", [
		'cache_wsdl' => 0
	]);
	$server->setUri("uriClient/server");
	return $server
		->setReturnResponse(true)
		->setClass(\App\Soap\ClientsSoapController::class)
		->handle();
});

$app->get('soap-client', function () use ($uriClient) {
	$client = new \Zend\Soap\Client("$uriClient/son-soap.wsdl",[
		'cache_wsdl' => WSDL_CACHE_NONE
	]);

	print_r($client->listAll());

	/*print_r($client->create([
		'name' 	=> 'Onesimo Batista',
		'email' => 'onesimobatista@gmail.com',
		'phone' => '777'
	]));

});
*/
 
/**
 * @param int $num1
 * @param int $num2
 * @return int
 */
function soma($num1, $num2)
{
	return $num1 + $num2;
}
