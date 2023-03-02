<?php
function EncryptString ( string & $str , string & $psw ) : string {
  $shifr  = new shifr ( ) ;
  shifr_init  ( $shifr  ) ;
  shifr_set_version ( $shifr , 3 ) ;
  $shifr  ->  flagtext = true ;
  shifr_password_set ( $shifr , $psw ) ;
  $shifr  ->  message = $str ;
  shifr_encrypt ( $shifr ) ;
  shifr_flush ( $shifr  ) ;
  return  $shifr -> message ;
}
?>
