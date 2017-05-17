<?php
$pa_id  = ''; //numeric client ID
$apptok = ""; //token of the application
$appsec =""; //secret of the application

if(!isset($_GET["try"])) {
  $apiurl="https://api.primeauth.com/api/v1/create/authreq.json";
  
  $adata=array(
    'email' => ("someone@example.com"), //email address to authenticate
    'client_id' => $pa_id,
    'token' => $apptok,
    'secret' => $appsec,
    'ip_addr' => $_SERVER['REMOTE_ADDR'],
    'comments' => 'Mugen Auth Beta isolated test'
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
  $tokendata=json_decode($response,true);
  if($cstat==0) { //no problem, go to exchanging the token
    echo "<pre>";var_dump ($response);echo "</pre>";
    $data=json_decode($response,true);
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
  //header("Content-Type: text/plain");
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
  $tokendata=json_decode($response,true);
  if($cstat==0) { //no problem, go to exchanging the token
    //echo "<pre>";var_dump ($response);echo "</pre>";
    //$data=json_decode($response,true);
    echo ($response);
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
