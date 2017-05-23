<?php

class mugen_pa {
  private static $tok = "";
  private static $sec = "";
  private static $red;
  public static function setred($redir) { //write just once
    if(!self::$red) {
      self::$red=('https://'.$_SERVER['HTTP_HOST'].preg_replace('/\/$/','',preg_replace('/\/\//','/',dirname('/'.str_ireplace(str_ireplace('\\','/',$_SERVER['DOCUMENT_ROOT']),'',str_ireplace('\\','/',__FILE__)))))
      //get HTTP path to this folder
      .'/');
      self::$red.=$redir;
      return true;
    }
    else {
      return NULL;
    }
  }
  public static function getred() {
    return self::$red;
  }
  
  public static function req ($type="qr",$mail="",$ip="",$c="") { //id if everything okay, false is not okay and NULL is error
    switch (true) { //backwards switch. no break needed because return
      case($type=="qr") :
        return self::req_qr();
      case($type=="app" && $email && $ip) :
        return self::req_app($email,$ip,$c);
      case($type=="mail" && $email) :
        return self::req_mls($email,$ip);
      default: 
        return null;
    }
  }
  
  public static function check($type="qr",$id) { //id if everything okay, false is not okay and NULL is error
    switch (true) { //backwards switch. no break needed because return
      case($type=="qr") :
        return self::check_qr($id);
      case($type=="app") :
        return self::check_app($ip);
      case($type=="mail") :
        return self::check_mls($ip);
      default: 
        return NULL;
    }
  }
  
  //QR Stuff
  private static function req_qr() {
    $apiurl="https://api.primeauth.com/api/v1/pascan/gen.json";
    
    $adata=array(
      'token' => self::$tok,
      'secret' => self::$sec
    );
    
    $curl=curl_init($apiurl);
    curl_setopt_array($curl,array(
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER=> array('User-Agent: Mugen Auth Beta 2 by My1'),
      CURLOPT_POST=> true,
      CURLOPT_POSTFIELDS => http_build_query($adata)));
    
    $response=curl_exec($curl);
    $cstat=curl_errno($curl);
    $status=curl_getinfo($curl,CURLINFO_HTTP_CODE);
    if($cstat==0) {
      $data=json_decode($response,true); //just for the sake of it (and in case it isnt json in case of an error
      if(isset($data["error"]) && $data["error"]) {
        $log=fopen("opa-log.txt","ab");
        fwrite ($log,"PA Login API Error (step1): $status. -> ".$response."\n");
        fclose($log);
      }
      elseif($data["id"]) {
        return $data["id"];
      }
      else echo($response);
      
    }
    else{ //curl screwed up. log the error and return false
      $log=fopen("opa-log.txt","ab");
      fwrite ($log,"PA Login cURL Error (step1): $cstat / ".curl_error($curl)."\n");
      fclose($log);
      return NULL;
    }
  }
  
  public static function qr_img ($id) { //get QR image URL
    return ('https://api.primeauth.com/pascan/qrcode?id='.$id);
  }
  
  private static function check_qr ($id) {
    $apiurl="https://api.primeauth.com/api/v1/pascan/check.json";
    
    $adata=array(
      'id' => $id,
      'token' => self::$tok,
      'secret' => self::$sec
    );

    $curl=curl_init($apiurl);
    curl_setopt_array($curl,array(
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER=> array('User-Agent: Mugen Auth Beta 2 by My1'),
      CURLOPT_POST=> true,
      CURLOPT_POSTFIELDS => http_build_query($adata)));
    
    $response=curl_exec($curl);
    $cstat=curl_errno($curl);
    $status=curl_getinfo($curl,CURLINFO_HTTP_CODE);
    if($cstat==0) {
      $data=json_decode($response,true);
      if($data["accepted"]==1) {
        return $data["email"];
      }
      else return false;
    }
    else{ //curl screwed up. log the error and return false
      $log=fopen("opa-log.txt","ab");
      fwrite ($log,"PA Login cURL Error (step2): $cstat / ".curl_error($curl)."\n");
      fclose($log);
      return null;
    }
    return null;
  }
}
?>
