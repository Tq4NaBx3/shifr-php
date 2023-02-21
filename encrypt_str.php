<?php
require_once  'shifr.php' ;
function ShifrEncryptString ( string & $str  , $secrethtmlpsw ) : string {
  $shifr  = new shifr ( ) ;
  shifr_init ( $shifr  ) ;
  shifr_set_version ( $shifr , 2 ) ;
  $shifr -> flagtext = true ;
  $shifr_js -> letters_mode = shifr :: letters_mode_Letter ;
  shifr_password_set ( $shifr , $secrethtmlpsw ) ;
  $shifr -> message = $str ;
  shifr_encrypt ( $shifr ) ;
  shifr_flush ( $shifr  ) ;
  return  $shifr -> message ;
}
?>
