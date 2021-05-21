<?php
  $fp = fopen ( $_FILES [ 'uploadfile'  ] [ 'tmp_name'  ] , 'rb'  ) ;
  if ( $fp ) {
      $uploadfileshi  = ( sys_get_temp_dir  ( ) . "/" ) . basename  (
        $_FILES [ 'uploadfile'  ] [ 'name'  ] ) . '.shi'  ;
      $fpw = fopen ( $uploadfileshi , 'wb'  ) ;
      shifr_password_load ( $shifr ) ;
      while ( ! feof  ( $fp ) ) {
        set_time_limit  ( 60  ) ;
        $shifr -> message = fread ( $fp , 0x1000 ) ;
        if ( ! $shifr -> message )
          break ;
        shifr_encrypt ( $shifr ) ; 
        fwrite  ( $fpw , $shifr -> message ) ; }
      shifr_flush_file  ( $shifr , $fpw ) ;
      fclose  ( $fpw  ) ;
      fclose  ( $fp ) ;
      
      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename="'.
        basename ( $uploadfileshi  ).'"');
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      header('Content-Length: ' . filesize($uploadfileshi));
      readfile($uploadfileshi); } // if ( $fp )
  exit ();
?>
