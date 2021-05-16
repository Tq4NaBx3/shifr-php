<?php
  $uploadfileshi = tempnam("/tmp", "shi");
  $fpw = fopen ( $uploadfileshi , 'wb'  ) ;
      if ( ! $fpw ) {
        //$postfileerr = "Cannot open file ($uploadfileshi)";
        exit; }
      if ( fwrite  ( $fpw , $shifr -> boxes_info ) === FALSE ) {
        //$postfileerr = "Cannot write to file ($uploadfileshi)";
        exit ; }
      //$postfileerr = "";
      fclose  ( $fpw  ) ;
      
    if (file_exists($uploadfileshi)) {
      
      $shifr -> boxes_info = "" ;
      
      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename="'.
        basename ( $shifr -> filename  ).'.shi"');
        
      $shifr  ->  filename = "" ;
      $shifr  ->  flagfinishfilepinpong = true ;
      
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      header('Content-Length: ' . filesize($uploadfileshi));
      readfile($uploadfileshi);
      exit ; }
  /*else
    $postfileerr = "Not file_exists $uploadfileshi";*/
?>
