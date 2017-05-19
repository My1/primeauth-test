<?php
$apptok = ""; //token of the application
$appsec = ""; //secret of the application

if(!isset($_GET["try"])) {
  $apiurl="https://api.primeauth.com/api/v1/create/authreq.json";
  
  $adata=array(
    'email' => ("someone@example.com"), //email address to authenticate
    'token' => $apptok,
    'secret' => $appsec,
    'ip_addr' => $_SERVER['REMOTE_ADDR'],
    'comments' => 'Some Comment' //optional
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
  if($cstat==0) { //no problem, go to exchanging the token
    $data=json_decode($response,true);
    echo "<pre>";var_dump ($data);echo "</pre>";
    if($data["token"]) {
      echo "<a href=?try=".$data["token"].">Login</a>";
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
  $apiurl="https://www.primeauth.com/api/v1/check/id.json";
  
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
  if($cstat==0) { //no problem, go to exchanging the token
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
