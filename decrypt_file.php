<?php
$fp = fopen ( $_FILES [ 'uploadfile'  ] [ 'tmp_name'  ] , 'rb'  ) ;
if ( $fp ) {
  $uploadfile = ( sys_get_temp_dir  ( ) . "/" ) . basename  (
    $_FILES [ 'uploadfile'  ] [ 'name'  ] ) ;
  if ( strlen ( $uploadfile ) > 4 and substr ( $uploadfile , -4 ) == '.shi' )
    $uploadfile2 = substr ( $uploadfile , 0 , -4 ) ;
  else
    $uploadfile2 = $uploadfile  . '.des' ;
  $fpw = fopen ( $uploadfile2 , 'wb'  ) ;
  do {
    set_time_limit  ( 60  ) ;
    $shifr -> message = fread ( $fp , 0x1000 ) ;
    if ( ! $shifr -> message )
      break ;
    shifr_decrypt ( $shifr ) ; 
    fwrite  ( $fpw , $shifr -> message ) ;
  } while ( ! feof  ( $fp ) ) ;
  fclose  ( $fpw  ) ;
  fclose  ( $fp ) ;
  unlink  ( $_FILES [ 'uploadfile'  ] [ 'tmp_name'  ] ) ;
      
  header('Content-Description: File Transfer');
  header('Content-Type: application/octet-stream');
  header('Content-Disposition: attachment; filename="'.
    basename ( $uploadfile2 ).'"');
  header('Expires: 0');
  header('Cache-Control: must-revalidate');
  header('Pragma: public');
  header('Content-Length: ' . filesize($uploadfile2));
  readfile($uploadfile2);
} // if ( $fp )
exit ();
?>
