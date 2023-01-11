<?php
session_start ( ) ;
if ( isset ( $_POST ) ) {
  if ( isset ( $_POST [ 'bodyclientwidth' ] ) ) {
    $bodyclientwidth = $_POST [ 'bodyclientwidth' ] ;
    $_SESSION [ 'bodyclientwidth'  ] = $bodyclientwidth ;
    echo  'Ok' ;
    exit  ( ) ;
  } // if post bodyclientwidth
} // if post
  sleep ( 1 ) ;
  echo  'BAD' ;
  exit  ( ) ; 
?>
