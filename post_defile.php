<?php

  if ( empty  ( $_POST  [ 'boxes_info'  ] ) ) {
    echo  ( '$_POST  [ \'boxes_info\'  ] is empty' . ' , post_max_size = ' .
      ini_get ( 'post_max_size' ) . ' , upload_max_filesize = ' .
      ini_get ( 'upload_max_filesize' ) ) ;
    //phpinfo ( ) ;
    die ( ) ; }

// '0x00','0xf0','0x0f','0xff' <= "aa" + "ap" + "pa" + "pp"
function  shifr_string_to_bytes ( string & $string ) : string {
  $strlen = strlen  ( $string ) ;
  if ($strlen & 1)
    --  $strlen ;
  $acode  = ord ( "a" ) ;
  $result = "" ;
  for ($i=0;$i<$strlen;$i ++) {
    $low  = ord ( $string[$i] ) - $acode ;
    ++  $i  ;
    $high = ord ( $string[$i] ) - $acode ;
    $result .= chr(($high<<4) | $low); }
  return  $result ; }
  
  $uploadfileshi = tempnam  ( ( sys_get_temp_dir  ( ) . "/" ) , "shi" ) ;
  
  if  ( ! ( $fpw = fopen  ( $uploadfileshi  , 'w+'  ) ) ) 
    die ( 'fopen ( ' . $uploadfileshi . ' )' ) ;
  $string = $_POST  [ 'boxes_info'  ] ;
  if ( ! fwrite ( $fpw  , shifr_string_to_bytes ( $string ) ) ) 
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
