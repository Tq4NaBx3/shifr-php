<?php
  $uploadfileshi = tempnam("/tmp", "shi");
  $fpw = fopen ( $uploadfileshi , 'wb'  ) ;
  //$fpw  = $_FILES["file"]["tmp_name"] ;
      //$uploadfileshi  = '/tmp/push.shi'  ;
      //$fpw = fopen ( $uploadfileshi , 'wb'  ) ;
      if ( ! $fpw ) {
        $postfileerr = "Cannot open file ($uploadfileshi)";
        exit; }
      if ( fwrite  ( $fpw , $shifr -> boxes_info ) === FALSE ) {
        $postfileerr = "Cannot write to file ($uploadfileshi)";
        exit ; }
      $postfileerr = "";
      fclose  ( $fpw  ) ;
      
      if (file_exists($uploadfileshi)) {
      
      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename="'.
        basename ( $uploadfileshi  ).'"');
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      header('Content-Length: ' . filesize($uploadfileshi));
      readfile($uploadfileshi);
  exit ; }
  else
    $postfileerr = "Not file_exists $uploadfileshi";
?>
