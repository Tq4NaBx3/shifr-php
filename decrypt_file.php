<?php
  $fp = fopen ( $_FILES [ 'uploadfile'  ] [ 'tmp_name'  ] , 'rb'  ) ;
  if ( $fp ) {
      $uploadfile = '/tmp/' . basename  (
        $_FILES [ 'uploadfile'  ] [ 'name'  ] ) ;
      $len_uploadfile = strlen ( $uploadfile ) ;
      if ( $len_uploadfile > 4 and substr ( $uploadfile , -4 ) == '.shi' )
        $uploadfile2 = substr ( $uploadfile , 0 , -4 ) ;
      else
        $uploadfile2 = $uploadfile  . '.des' ;
      $fpw = fopen ( $uploadfile2 , 'wb'  ) ;
      shifr_password_load ( $shifr ) ;
      while ( ! feof  ( $fp ) ) {
        set_time_limit  ( 60  ) ;
        $shifr -> message = fread ( $fp , 0x1000 ) ;
        if ( ! $shifr -> message )
          break ;
        shifr_decrypt ( $shifr ) ; 
        fwrite  ( $fpw , $shifr -> message ) ; }
      fclose  ( $fpw  ) ;
      fclose  ( $fp ) ;
      
      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename="'.
        basename ( $uploadfile2 ).'"');
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      header('Content-Length: ' . filesize($uploadfile2));
      readfile($uploadfile2); } // if ( $fp )
  exit ();
?>
