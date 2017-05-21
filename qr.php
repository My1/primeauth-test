<?php
$apptok = ""; //token of the application
$appsec = ""; //secret of the application

if(!isset($_GET["try"])) {
  $apiurl="https://api.primeauth.com/api/v1/pascan/gen.json";
  
  $adata=array(
    'token' => $apptok,
    'secret' => $appsec
  );
  
  $curl=curl_init($apiurl);
  curl_setopt_array($curl,array(
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER=> array('User-Agent: Mugen Auth Beta by My1'),
    CURLOPT_POST=> true,
    CURLOPT_POSTFIELDS => http_build_query($adata)));
  
  $response=curl_exec($curl);
  $cstat=curl_errno($curl);
  $status=curl_getinfo($curl,CURLINFO_HTTP_CODE);
  if($cstat==0) {
    var_dump ($status); // HTTP status code
    $data=json_decode($response,true); //just for the sake of it (and in case it isnt json in case of an error
    echo "<pre>";var_dump ($data);echo "</pre>"; //php's interpretation
    if($data["id"]) {
      echo "<img src='https://api.primeauth.com/pascan/qrcode?id=".$data["id"]."'>";
      echo "<a href=?try=".$data["id"].">Login</a>";
    }
  }
  else{ //curl screwed up. log the error and return false
    $log=fopen("opa-log.txt","ab");
    fwrite ($log,"PA Login cURL Error (step1): $cstat / ".curl_error($curl)."\n");
    fclose($log);
    die();
  }
  die();
}


elseif($_GET["try"]) {
  $apiurl="https://api.primeauth.com/api/v1/pascan/check.json";
  
  $adata=array(
    'id' => $_GET["try"],
    'token' => $apptok,
    'secret' => $appsec
  );

  $curl=curl_init($apiurl);
  curl_setopt_array($curl,array(
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER=> array('User-Agent: Mugen Auth Beta by My1'),
    CURLOPT_POST=> true,
    CURLOPT_POSTFIELDS => http_build_query($adata)));
  
  $response=curl_exec($curl);
  $cstat=curl_errno($curl);
  $status=curl_getinfo($curl,CURLINFO_HTTP_CODE);
  if($cstat==0) {
    var_dump ($status);
    echo $response;
    $data=json_decode($response,true);
    echo "<pre>";var_dump ($data);echo "</pre>";
    if($data["accepted"]==1) {
      echo "Login complete for ".$data["email"];
    }
  }
  else{ //curl screwed up. log the error and return false
    $log=fopen("opa-log.txt","ab");
    fwrite ($log,"PA Login cURL Error (step2): $cstat / ".curl_error($curl)."\n");
    fclose($log);
    die();
  }
  die();
}
?>
