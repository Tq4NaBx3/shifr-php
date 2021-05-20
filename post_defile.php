<?php
  $path = sys_get_temp_dir  ( ) ;
  $uploadfileshi = tempnam  ( $path , "shi" ) ;
 
  if  ( ! ( $fpw = fopen  ( $uploadfileshi  , 'w+'  ) ) ) 
    die ( 'fopen ( ' . $uploadfileshi . ' )' ) ;
  if ( ! fwrite ( $fpw  , $_POST  [ 'boxes_info'  ] ) ) 
    die ( 'fwrite ( ' . $uploadfileshi . ' )' ) ;
  if (  ! fclose  ( $fpw  ) )
    die ( 'fclose ( ' . $uploadfileshi . ' )' ) ;
 
  $uploadfile = basename ( $_POST  [ 'filename_name' ] ) ;
  if ( strlen ( $uploadfile ) > 4 and substr ( $uploadfile , -4 ) == '.shi' )
    $uploadfile2 = substr ( $uploadfile , 0 , -4 ) ;
  else
    $uploadfile2 = $uploadfile  . '.des' ;
 
  header  ( 'Content-Description: File Transfer'  ) ;
  header  ( 'Content-Type: application/octet-stream'  ) ;
  header  ( 'Content-Disposition: attachment; filename="' . $uploadfile2 ) ;
  header  ( 'Expires: 0'  ) ;
  header  ( 'Cache-Control: must-revalidate'  ) ;
  header  ( 'Pragma: public'  ) ;
  header  ( 'Content-Length: ' . filesize ( $uploadfileshi  ) ) ;
 
  readfile  ( $uploadfileshi  ) ;
?>
