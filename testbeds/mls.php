<?php
$apptok = "";
$appsec = "";

if(!($_POST) && !($_GET)) {
  echo <<<endlogin
  <form method="post">
  <input type="text" name="email">
  <input type="submit">
  </form>
endlogin;
  die();
}
elseif(isset($_POST["email"])) {
  $apiurl="https://api.primeauth.com/api/v1/mls/create.json";
  
  $adata=array(
    'email' => $_POST["email"],
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
  var_dump($status);
    echo "<pre>";var_dump ($response);echo "</pre>";
    $data=json_decode($response,true);
    if($data["id"]) {
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
elseif(isset($_GET["try"]) && $_GET["try"]) { //manual check
  $apiurl="https://api.primeauth.com/api/v1/mls/check.json";
  
  $adata=array(
    'id' => $_GET["try"],
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
elseif(isset($_GET["id"])) { //came through redirect URL, generally the same
  $apiurl="https://api.primeauth.com/api/v1/mls/check.json";
  
  $adata=array(
    'id' => $_GET["id"],
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
