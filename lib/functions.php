<?php

function amp_cu_show_message($message, $type = "success") {
    echo "<div class='notice notice-$type is-dismissible'><p>$message</p></div>";
}

function amp_c_u_update_cache_curl($url) {
    $ret_val = array(
        "status" => 500,
        "message" => "Cache update function error!",
        "info" => ""
    );

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_MAXREDIRS      => 5,
        CURLOPT_FOLLOWLOCATION => 0,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_CUSTOMREQUEST  => 'GET',
        CURLOPT_HTTPHEADER     => array("cache-control: no-cache"),
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:30.0) Gecko/20100101 Firefox/30.0',
    ));

    $curl_expo = curl_exec($curl);
    $curl_err = curl_error($curl);

    $info = curl_getinfo($curl);

    if(empty($curl_err)) {
        if(isset($info)) {
			$message = "";
			$status = $info["http_code"];
			
			if($curl_expo != "OK") {
                //$message = "AMP cache update error!";
				$status = 404;

				if(strpos($curl_expo, "<title>") === false) {
                    $message = $curl_expo;
                } else {
                    $dom = new DOMDocument();
                    @$dom->loadHTML($curl_expo);

                    $xpath = new DOMXPath($dom);

                    $p_dom = $xpath->query('/html/body/p');
                    foreach( $p_dom as $key => $node )
                    {
                        $message .= $key == 0 ? "<b>" : "";
                        $message .= $node->nodeValue;
                        $message .= $key == 0 ? "</b>" : "";
                        $message .= count($p_dom) -1 >= $key ? "<br><br>" : "";
                    }
                }
			} else {
                $status = 200;
                $message = "Güncelleme isteği gönderildi.";
            }
			
            $ret_val = array(
                "status" => $status,
                "message" => $message,
                "info" => $info
            );
        }
    } else {
		$ret_val = array(
                "status" => $info["http_code"],
                "message" => $curl_err,
                "info" => $info
            );
	}

    curl_close($curl);

    return $ret_val;
}