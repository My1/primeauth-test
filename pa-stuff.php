<?php
require("pa-include.php");

//mugen_pa::setred("pa-stuff.php");
//var_dump(mugen_pa::getred());

//var_dump(mugen_pa::req("qr"));
if(!isset($_GET["try"])) {
  $aid=mugen_pa::req("qr");
  //var_dump($aid);
  $imgurl=mugen_pa::qr_img($aid);
  echo "$aid<br><img src='$imgurl'><br>
  <a href='?try=$aid'>Login</a>";
}
elseif($_GET["try"]) {
  var_dump(mugen_pa::check("qr",$_GET["try"]));
}