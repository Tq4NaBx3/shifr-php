<?php 
// number /= div , number := floor [ деление ] , return := остаток (remainder)
function  number_div8_mod ( array & $number , int $div ) : int {
  $modi = 0 ;
  $i = count  ( $number ) ;
  while ( $i > 0 )  {
    -- $i ;
    $x = ( $modi << 8 ) | ( $number [ $i  ] ) ;
    $modi = $x % $div  ;
    $number [ $i  ] = intdiv  ( $x , $div  ) ; 
  }
  $i = count  ( $number ) ;
  while ( $i > 0 )  {
    -- $i ;
    if ( $number [ $i  ] != 0 )
      break ;
    unset ( $number [ $i  ] ) ;
  }
  return  $modi ;
}

function  number_dec  ( array & $number ) {
  for ( $i = 0 ; $i < count  ( $number ) ; ++ $i )  {
    if ( $number [ $i  ] != 0 ) {
      $number [ $i  ] = $number [ $i  ] - 1 ;
      break ;
    }
    $number [ $i  ] = 0b11111111 ;
  }
  if ( $i == count  ( $number ) ) {
    echo  'number_dec:$i == count  ( $number )' ;
    return  ;
  }
  $i = count  ( $number ) ;
  while ( $i > 0 )  {
    -- $i ;
    if ( $number [ $i  ] != 0 ) 
      break ;
    unset ( $number [ $i  ] ) ;
  }
}

function  number_not_zero ( array & $number ) {
  return  count  ( $number ) >  0 ;
}

function  number_set_zero ( array & $number ) {
  $number = array ( ) ;
}

function  number_set_byte ( array & $number , int $byte ) {
  if ( $byte != 0 ) {
    if ( $byte < 0 ) 
      return  ; 
    if ( $byte >= 0x100 )
      return  ; 
    $number = array ( $byte ) ;
  } else
    $number = array ( ) ;
}

function  number_add  ( array & $num , array & $xnum ) {
  $per = 0 ;
  for ( $i = 0 ; $i < count  ( $num ) and $i < count  ( $xnum ) ; ++ $i )  {
    $s = $num [ $i ] + $xnum [ $i ] + $per ;
    if ( $s >= 0x100  ) {
      $num [ $i ] = $s - 0x100 ;
      $per = 1 ;
    } else  {
      $num [ $i ] = $s  ;
      $per = 0 ; 
    }
  }
  if ( count  ( $num ) > count  ( $xnum ) ) {
    if ( $per == 0 ) 
      return  ;
    for ( ; $i < count  ( $num ) ; ++ $i )  {
      $s = $num [ $i ] + 1 ;
      if ( $s < 0x100  ) {
        $num [ $i ] = $s  ;
        return ; 
      }
      $num [ $i ] = 0 ; 
    }
    $num [ $i ] = 1 ;
    return  ;
  }
  if ( count  ( $num ) < count  ( $xnum ) ) {
    for ( ; $i < count  ( $xnum ) ; ++ $i )  {
      $s = $xnum [ $i ] + $per ;
      if ( $s == 0x100  ) {
        $num [ $i ] = 0 ;
        $per  = 1 ;
      } else  {
        $num [ $i ] = $s  ;
        $per  = 0 ;
      }
    }
  }
  if ( $per > 0 )
    $num [ $i ] = 1 ;
}

function  number_mul_byte ( array & $number , int $byte ) {
  if ( $byte == 0 ) {
    $number = array ( ) ;
    return  ;
  }
  if ( $byte == 1 )
    return ;
  if ( $byte < 0 ) 
    return  ; 
  if ( $byte >= 0x100 ) 
    return  ; 
  $per = 0 ;
  for ( $i = 0 ; $i < count  ( $number ) ; ++ $i )  {
    $x = $number [ $i ] *  $byte  + $per ;
    $number [ $i ] = $x & 0b11111111 ;
    $per = $x >> 8 ;
  }
  if ( $per > 0 )
    $number [ $i ] = $per ;
}


?>
