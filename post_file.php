<?php
  $path = sys_get_temp_dir  ( ) ;
  $uploadfileshi = tempnam  ( $path , "shi" ) ;
 
  if  ( ! ( $fpw = fopen  ( $uploadfileshi  , 'w+'  ) ) ) 
    die ( 'fopen ( ' . $uploadfileshi . ' )' ) ;
  if ( ! fwrite ( $fpw  , $_POST  [ 'boxes_info'  ] ) ) 
    die ( 'fwrite ( ' . $uploadfileshi . ' )' ) ;
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
