<?php
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-type: application/json');

	function check_param($param,$msg="Missing %s"){
		if(!isset($param)){
			echo json_encode(array("error" => sprintf($msg,$param)));
			exit;
		};
		return $param;
	};

	$a = @check_param($_GET["a"],"Location A is missing!");
	$b = @check_param($_GET["b"],"Location B is missing");

	$url = sprintf("http://maps.google.com/maps?saddr=%s&daddr=%s&hl=en",$a,$b);

	$data = file_get_contents($url);
	if(!$data){
		echo json_encode(array("error"=>"Error fetching data!"));
		exit;
	};

	preg_match_all ('/<ol[^>]*id="dir_altroutes_body">(.*?)<\\/ol>/i', $data, $out);

	$fr = array_shift($out);
	if(count($fr) < 1){
		echo json_encode(array("error"=> "Service response error!"));
		exit;
	};

	$fr = simplexml_load_string($fr[0]);

	$routes = array();

	foreach($fr->li as $li){
		$div = $li->div[0]->div;
		$routes[] = array("distance" => $div->span[0]."", "time" => $div->span[1][0]."");
	}

	echo json_encode($routes);
