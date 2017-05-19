<?php
$apptok = "";
$appsec = "";

if(!isset($_GET["try"])&&!isset($_GET["mls_token"])) {
  $apiurl="https://api.primeauth.com/api/v1/mls/create.json";
  
  $adata=array(
    'email' => ("someone@example.com"),
    'token' => $apptok,
    'secret' => $appsec,
    'redir' => 'https://'.$_SERVER['HTTP_HOST'].preg_replace('/\/$/','',preg_replace('/\/\//','/',dirname('/'.str_ireplace(str_ireplace('\\','/',$_SERVER['DOCUMENT_ROOT']),'',str_ireplace('\\','/',__FILE__))))).'/mls.php'
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
elseif(isset($_GET["try"]) && $_GET["try"]) { //manual check
  $apiurl="https://api.primeauth.com/api/v1/mls/check.json";
  
  $adata=array(
    'email' => ("teamhydro55555@gmail.com"),
    'mls_token' => $_GET["try"],
    'token' => $apptok,
    'secret' => $appsec
  );

  $curl=curl_init($apiurl);
  curl_setopt_array($curl,array(
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER=> array('User-Agent: Mugen Auth Beta by My1'),
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query($adata)));
  
  $response=curl_exec($curl);
  $cstat=curl_errno($curl);
  $status=curl_getinfo($curl,CURLINFO_HTTP_CODE);
  if($cstat==0) {
    $data=json_decode($response,true);
    echo "<pre>";var_dump ($data);echo "</pre>";
    if($data["accepted"]=="true") {
      echo "Login complete for ".$adata["email"];
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
elseif(isset($_GET["mls_token"])) { //came through redirect URL, generally the same
  $apiurl="https://api.primeauth.com/api/v1/mls/check.json";
  
  $adata=array(
    'email' => $_GET["email"],
    'mls_token' => $_GET["mls_token"],
    'token' => $apptok,
    'secret' => $appsec
  );

  $curl=curl_init($apiurl);
  curl_setopt_array($curl,array(
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER=> array('User-Agent: Mugen Auth Beta by My1'),
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query($adata)));
  
  $response=curl_exec($curl);
  $cstat=curl_errno($curl);
  $status=curl_getinfo($curl,CURLINFO_HTTP_CODE);
  if($cstat==0) {
    $data=json_decode($response,true);
    echo "<pre>";var_dump ($data);echo "</pre>";
    if($data["accepted"]=="true") {
      echo "Login complete for ".$_GET["email"];
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
