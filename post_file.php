<?php

  $_SESSION [ 'flagfinishfilepingpong'  ] = true  ;
  $_SESSION [ 'postfilefopenerror'  ] = false ;
  $_SESSION [ 'postfilefwriteerror'  ] = false  ;
  $_SESSION [ 'postfilefcloseerror'  ] = false ;
  $_SESSION [ 'postfilenotexisterror'  ]  = false ;

  $uploadfileshi = tempnam  ( "/tmp"  , "shi" ) ;
  $fpw = fopen ( $uploadfileshi , 'wb'  ) ;
  if ( $fpw === false ) {
    $_SESSION [ 'postfilefopenerror'  ] = $uploadfileshi  ;
    exit ; }
  if ( fwrite  ( $fpw , $shifr -> boxes_info ) === FALSE ) {
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
  header  ( 'Content-Disposition: attachment; filename="' .
    basename ( $shifr -> filename  )  . '.shi"' ) ;
  header  ( 'Expires: 0'  ) ;
  header  ( 'Cache-Control: must-revalidate'  ) ;
  header  ( 'Pragma: public'  ) ;
  header  ( 'Content-Length: ' . filesize ( $uploadfileshi  ) ) ;
  readfile  ( $uploadfileshi  ) ;
  exit ;
?>
