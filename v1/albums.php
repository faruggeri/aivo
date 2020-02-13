<?php

	error_reporting(E_ERROR | E_PARSE); 
	/*Utilizo metodo get para obtener el valor de la consulta*/
	$q= $_GET["q"];
	/*reemplazo espacios en blanco por signo "+" para evitar inconveniente en la consulta*/
    $q = str_replace ( ' ' ,"+", $q);
	/*ingreso los valores de client, que indica Spotify*/
	$client_id = '1a1173c7e4fc47c49e1a60df735bcf14'; 
    $client_secret = '0c43f468606e4408b880df6259837ef3'; 
/*Obtengo token de seguridad */

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,            'https://accounts.spotify.com/api/token' );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt($ch, CURLOPT_POST,           1 );
    curl_setopt($ch, CURLOPT_POSTFIELDS,     'grant_type=client_credentials' ); 
    curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Authorization: Basic '.base64_encode($client_id.':'.$client_secret))); 
    $result=curl_exec($ch); 
	$result = json_decode($result, true);
	    curl_close($ch);    
		
  
    $spotifyURL = 'https://api.spotify.com/v1/search?q='.$q.'&type=artist';//$q;
    $authorization = 'Authorization: Bearer '.$result["access_token"];
/*Obtengo Id de la banda*/   
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
    curl_setopt($ch, CURLOPT_URL, $spotifyURL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:x.x.x) Gecko/20041107 Firefox/x.x");
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $json = curl_exec($ch);
	$json = json_decode($json, true);
	curl_close($ch);
    
	$spotifyURL_artist =   $json['artists']['items'][0]['id'];  
    $cantidad = 50; 
	
/*Obtengo todos los discos de la banda desde la API de Spotify con limite de 50*/
	
    $urli = 'https://api.spotify.com/v1/artists/'.$spotifyURL_artist.'/albums?limit=50&offset=1';
	
    while ($cantidad == 50)	{    
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
		curl_setopt($ch, CURLOPT_URL, $urli);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:x.x.x) Gecko/20041107 Firefox/x.x");
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$json_artist = curl_exec($ch);
		$data_artist = json_decode($json_artist,true);
		curl_close($ch);
		$urli = $data_artist['next'];
		$cantidad = count($data_artist['items']);
		
/*convierto el Array devuelto en Json*/		
		foreach ($data_artist['items'] as $value) {
		

	$disco ->name = $value['name'];
	$disco-> released = $value['release_date'];
	$disco->tracks = $value['total_tracks'];
	
						foreach ($value['images'] as $cover) {
								$disco->cover->height = $cover['height'];
								$disco->cover->width = $cover['width'];
								$disco->cover->url = $cover['url'];
								}
	$json_album = json_encode($disco,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	
/*Imprimo Json con los datos solicitados*/


	print $json_album."<br/>";

	
	}
  };

 ?>