<?php
  $path = sys_get_temp_dir();
  $uploadfileshi = tempnam($path, "shi");
 
  $fpw = fopen($uploadfileshi, 'w+');
  fwrite($fpw, $_POST['boxes_info']);
  fclose($fpw);
 
  header('Content-Description: File Transfer');
  header('Content-Type: application/octet-stream');
  header('Content-Disposition: attachment; filename="' . basename($uploadfileshi) . '.shi"');
  header('Expires: 0');
  header('Cache-Control: must-revalidate');
  header('Pragma: public');
  header('Content-Length: ' . filesize($uploadfileshi));
 
  readfile($uploadfileshi);
?>
<?php
/*
  $_SESSION [ 'flagfinishfilepingpong'  ] = true  ;
  $_SESSION [ 'postfilefopenerror'  ] = false ;
  $_SESSION [ 'postfilefwriteerror'  ] = false  ;
  $_SESSION [ 'postfilefcloseerror'  ] = false ;
  $_SESSION [ 'postfilenotexisterror'  ]  = false ;
  $_SESSION [ 'postboxes_infoerror'  ]  = false ;
  $_SESSION [ 'postfilename_nameerror'  ]  = false ;

  if ( ! isset ( $_POST  [ 'boxes_info'  ] ) ) {
    $_SESSION [ 'postboxes_infoerror'  ]  = true ;
    exit  ; }
  
  if ( ! isset ( $_POST  [ 'filename_name' ] ) ) {
    $_SESSION [ 'postfilename_nameerror'  ]  = true ;
    exit ; }
  
  $temppath = sys_get_temp_dir  ( ) ;
  $uploadfileshi = tempnam  ( $temppath , "shi" ) ;
  $fpw = fopen ( $uploadfileshi , 'wb'  ) ;
  if ( $fpw === false ) {
    $_SESSION [ 'postfilefopenerror'  ] = $uploadfileshi  ;
    exit ; }
  if ( fwrite  ( $fpw , $_POST  [ 'boxes_info'  ] ) === FALSE ) {
    $_SESSION [ 'postfilefwriteerror'  ] = $uploadfileshi  ;
    exit ;  }
  if ( fclose  ( $fpw  ) === false ) {
    $_SESSION [ 'postfilefcloseerror'  ] = $uploadfileshi  ;
    exit ;  }
  if ( file_exists ( $uploadfileshi  ) === false ) {
    $_SESSION [ 'postfilenotexisterror'  ] = $uploadfileshi ;
    exit  ; }
      
  header  ( 'Content-Description: File Transfer'  ) ;
  header  ( 'Content-Type: application/octet-stream'  ) ;
  //header  ( 'Content-Disposition: attachment; filename="' .
  //  basename ( $shifr -> filename  )  . '.shi"' ) ;
  header  ( 'Content-Disposition: attachment; filename="' .
    basename ( $_POST  [ 'filename_name' ] )  . '.shi"' ) ;
  header  ( 'Expires: 0'  ) ;
  header  ( 'Cache-Control: must-revalidate'  ) ;
  header  ( 'Pragma: public'  ) ;
  header  ( 'Content-Length: ' . filesize ( $uploadfileshi  ) ) ;
  //header  ( 'Content-Length: ' . strlen ( $_POST  [ 'boxes_info'  ] ) ) ;
  //echo  $_POST  [ 'boxes_info'  ] ;
  //header  ( 'Refresh: 0; url='  . $_SERVER  [ 'PHP_SELF'  ] ) ;
  readfile  ( $uploadfileshi  ) ;
  exit ;*/
?>
