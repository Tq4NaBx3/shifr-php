<?php

if ( empty  ( $_POST  [ 'boxes_info'  ] ) ) {
  echo  ( '$_POST  [ \'boxes_info\'  ] is empty' . ' , post_max_size = ' .
    ini_get ( 'post_max_size' ) . ' , upload_max_filesize = ' .
    ini_get ( 'upload_max_filesize' ) ) ;
  //phpinfo ( ) ;
  die ( ) ;
}
    
$uploadfileshi = tempnam  ( ( sys_get_temp_dir  ( ) . "/" ) , "shi" ) ;
  
if  ( ! ( $fpw = fopen  ( $uploadfileshi  , 'w+'  ) ) ) 
  die ( 'fopen ( ' . $uploadfileshi . ' )' ) ;
if ( isset ( $_POST [ 'text_mode' ] ) and 
     $_POST [ 'text_mode' ] == '1' ) {
  if ( ! fwrite ( $fpw  , $_POST  [ 'boxes_info'  ] ) )
      die ( 'fwrite ( ' . $uploadfileshi . ' )' ) ;
}
else  {
  
  require ( 'shifr.php' ) ;
      
  if ( ! fwrite ( $fpw  , shifr_Base64_decode ( $_POST  [ 'boxes_info'  ] ) ) )
    die ( 'fwrite ( ' . $uploadfileshi . ' )' ) ;
}
if (  ! fclose  ( $fpw  ) )
  die ( 'fclose ( ' . $uploadfileshi . ' )' ) ;
 
header  ( 'Content-Description: File Transfer'  ) ;
header  ( 'Content-Type: application/octet-stream'  ) ;
header  ( 'Content-Disposition: attachment; filename="' .
  basename ( $_POST  [ 'filename_name' ] )  . '.shi"' ) ;
header  ( 'Expires: 0'  ) ;
header  ( 'Cache-Control: must-revalidate'  ) ;
header  ( 'Pragma: public'  ) ;
header  ( 'Content-Length: ' . filesize ( $uploadfileshi  ) ) ;
 
readfile  ( $uploadfileshi  ) ;
?>
