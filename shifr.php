<?php 

/*
RUS
2 бит соль
2 бит инфо
в сумме 4 бита
b00 может быть зашифрован четырьмя способами из шестнадцати :
 b0000 ... b1111
вариантов шифрования :
 b00 = 16*15*14*13 = 43680
 b01 = 12*11*10*9 = 11880
 b10 = 8*7*6*5 = 1680
 b11 = 4*3*2*1 = 24
в общем 16 ! = 20922789888000 шт
минимум можно записать пароль с помощью
 log(2,20922789888000) ≈ 44.25 бит <= 6 байт
 пароль будет 45 бит
 ascii буквы 126-32+1 = 95 шт
 длина буквенного пароля : log ( 95 , 20922789888000 ) ≈ 6.735 букв <= 7 букв
  log ( 62 , 20922789888000 ) ≈ 7.432 буквы <= 8 букв

OrigData   : 01 11 11
RandomSalt : 10 11 10

Data 01---\/---10⊻11=01---\/---11⊻11=00
Salt 10___/\___01⊻11=10___/\___11⊻10=01  
Pair 0110      0110            0001
Secr xxxx      xxxx            yyyy

Соль одного элемента будет ксорить следующий элемент для исчезания повторов.
Данные первого элемента будут ксорить соль второго элемента.
Если все элементы будут одного значения, тогда все
 шифрованные значения будут иметь свойство псевдо-случайности.
И данные и соль имеют секретность кроме первой нулевой соли.
Функция Шифр(пары: данные+соль) должна быть случайной неупорядоченной.  
  
*/

// returns [ 0..15 , 0..14 , ... , 0..2 , 0..1 ]
function shifr_generate_pass4 ( ) : array {
  $dice = array ( ) ;
  for ( $i  = 15 ; $i  > 0 ; -- $i ) {
    $r = rand  ( 0 , $i )  ;
    $dice [ ] = $r ; }
  return  $dice ; }

// returns [ 0..63 , 0..62 , ... , 0..2 , 0..1 ]
function shifr_generate_pass6 ( ) : array {
  $dice = array ( ) ;
  for ( $i  = 63 ; $i  > 0 ; -- $i ) {
    $r = rand  ( 0 , $i )  ;
    $dice [ ] = $r ; }
  return  $dice ; }
  
function  shifr_data_sole4 ( array $secret_data ) : array {
  $secret_data_sole = array ( ) ;
  $ra = rand ( 0 , 0xff ) ; // 4*2 = 8 bits
  foreach ( $secret_data as $da ) {
    $secret_data_sole [ ] = ( $da << 2 ) | ( $ra & 0x3 ) ;
    $ra >>= 2 ; }
  return  $secret_data_sole ; }

function  shifr_data_sole6 ( array $secret_data ) : array {
  $secret_data_sole = array ( ) ;
  $ra = rand ( 0 , 0x1ff ) ; // 3*3 = 9 bits
  foreach ( $secret_data as $da ) {
    $secret_data_sole [ ] = ( $da << 3 ) | ( $ra & 0x7 ) ;
    $ra >>= 3 ; }
  return  $secret_data_sole ; }
  
function  shifr_byte_to_array4 ( int $byte ) : array {
  $arr = array ( ) ;
  for ( $i = 0 ; $i < 8 ; $i += 2 ) {
    $arr [ ] = $byte & 0x3 ;
    $byte >>= 2 ; }
  return  $arr ; }

function  shifr_data_xor4 ( int & $old_last_data , int & $old_last_sole ,
  array & $secret_data_sole ) {
  foreach ( $secret_data_sole as & $ids ) {
    $cur_data = $ids  >>  2 ;
    $cur_sole = $ids  & 0x3 ;
    $ids  ^=  ( $old_last_sole  <<  2 ) ;
    $ids  ^=  $old_last_data  ;
    $old_last_data = $cur_data ;
    $old_last_sole  = $cur_sole ; } }

function  shifr_data_xor6 ( int & $old_last_data , int & $old_last_sole ,
  array & $secret_data_sole ) {
  foreach ( $secret_data_sole as & $ids ) {
    $cur_data = $ids  >>  3 ;
    $cur_sole = $ids  & 0x7 ;
    $ids  ^=  ( $old_last_sole  <<  3 ) ;
    $ids  ^=  $old_last_data  ;
    $old_last_data = $cur_data ;
    $old_last_sole  = $cur_sole ; } }
    
function  shifr_crypt_decrypt ( array $datap , array & $tablep ) : array {
  $encrp = array ( ) ;
  foreach ( $datap as $id ) $encrp [ ] = $tablep [ $id ] ;
  return  $encrp  ; }

function  shifr_decrypt_sole4 ( array & $datap , array & $tablep ,
  array & $decrp ,  & $old_last_sole ,  & $old_last_data ) {
  foreach ( $datap as $id ) {
    $data_sole = $tablep [ $id ] ;
    $newdata = ( $data_sole >> 2 ) ^ $old_last_sole ;
    $decrp [ ] = $newdata ;
    $old_last_sole = (  $data_sole & 0x3 ) ^ $old_last_data ;
    $old_last_data  = $newdata ; } }

function  shifr_decrypt_sole6 ( array & $datap , array & $tablep ,
  array & $decrp ,  & $old_last_sole ,  & $old_last_data ) {
  foreach ( $datap as $id ) {
    $data_sole = $tablep [ $id ] ;
    $newdata = ( $data_sole >> 3 ) ^ $old_last_sole ;
    $decrp [ ] = $newdata ;
    $old_last_sole = (  $data_sole & 0x7 ) ^ $old_last_data ;
    $old_last_data  = $newdata ; } }

function  shifr_encode4 ( ) {
  global  $shifr_password ;
  global  $shifr_message  ;
  global  $shifr_shifra ;
  global  $shifr_old_last_data  ;
  global  $shifr_old_last_sole  ;
  global  $shifr_bytecount  ;
  global  $shifr_flagtext ;
      $message_array = str_split  ( $shifr_message  ) ;
      $shifr_message =  '';
      foreach ( $message_array as $char ) {      
        $secret_data = shifr_byte_to_array4 ( ord ( $char ) ) ;
        $secret_data_sole = shifr_data_sole4 ( $secret_data ) ;
        shifr_data_xor4 ( $shifr_old_last_data , $shifr_old_last_sole ,
          $secret_data_sole ) ;
        $encrypteddata = shifr_crypt_decrypt ( $secret_data_sole , $shifr_shifra )  ;
        if ( $shifr_flagtext ) {
          $buf16 = ( $encrypteddata [ 0 ] & 0xf ) |
            ( ( $encrypteddata [ 1 ] & 0xf ) << 4 ) |
            ( ( $encrypteddata [ 2 ] & 0xf ) << 8 ) |
            ( ( $encrypteddata [ 3 ] & 0xf ) << 12 ) ;
          $shifr_message .=  chr ( ord ( 'R' ) + ( $buf16 % 40 ) ) ;
          $buf16 = intdiv ( $buf16  , 40 )  ;
          $shifr_message .= chr ( ord ( 'R' ) + ( $buf16 % 40 ) ) ;
          $buf16 = intdiv ( $buf16  , 40 )  ;
          $shifr_message .=  chr ( ord ( 'R' ) + $buf16 ) ;
          $shifr_bytecount += 3 ;
          if ( $shifr_bytecount == 60 ) {
            $shifr_message .= "\n" ;
            $shifr_bytecount = 0 ; } }
        else {
          $shifr_message .=  chr ( ( $encrypteddata [ 0 ] & 0xf ) |
            ( ( $encrypteddata [ 1 ] & 0xf ) << 4 ) ) ;
          $shifr_message .=  chr ( ( $encrypteddata [ 2 ] & 0xf ) |
            ( ( $encrypteddata [ 3 ] & 0xf ) << 4 ) ) ; } } }   
    
function shifr_byte_to_array6 ( int $charcode , int & $bufin ,
  int & $bitscount  ) : array {
  global  $shifr_localerus  ;
  switch  ( $bitscount  ) {
  case  0 :
    // <= [ [1 0] [2 1 0] [2 1 0] ]
    $secret_data = array ( $charcode & 0x7 , ( $charcode >>  3 ) & 0x7 ) ;
    $bufin = $charcode >>  6 ;
    $bitscount  = 2 ;  // 0 + 8 - 6
    break ;
  case  1 :
    // <= [ [2 1 0] [2 1 0] [2 1] ] <= [ [0]
    $secret_data = array ( $bufin | ( ( $charcode & 0x3 ) << 1 ) ,
      ( $charcode >> 2 ) & 0x7 , $charcode >>  5 ) ;
    $bitscount  = 0 ;  // 1 + 8 - 9
    break ;
  case  2 :
    // <= [ [0] [2 1 0] [2 1 0] [2] ] <= [ [1 0] ..
    $secret_data = array ( $bufin | ( ( $charcode & 0x1 ) << 2 ) ,
      ( $charcode >> 1 ) & 0x7 , ( $charcode >>  4 ) & 0x7 ) ;
    $bufin = $charcode >>  7 ;
    $bitscount  = 1 ;  // 2 + 8 - 9
    break ;
  default :
    if  ( $shifr_localerus )
      echo 'неожиданное значение bitscount = '  . $bitscount  ;
    else
      echo 'unexpected value bitscount = '  . $bitscount ; }
  return  $secret_data  ; }
    
function  shifr_write_array ( array $secret_data  ) {
  global  $shifr_old_last_data  ;
  global  $shifr_old_last_sole  ;
  global  $shifr_shifra ;
  global  $shifr_flagtext ;
  global  $shifr_message  ;
  global  $shifr_bytecount  ;
  $secret_data_sole = shifr_data_sole6 ( $secret_data ) ;
  shifr_data_xor6 ( $shifr_old_last_data , $shifr_old_last_sole ,
    $secret_data_sole ) ;
  $encrypteddata = shifr_crypt_decrypt ( $secret_data_sole , $shifr_shifra )  ;
  if ( $shifr_flagtext ) {
    foreach ( $encrypteddata as $ed )
      $shifr_message  .=  chr ( ord ( ';' ) + $ed ) ;
    $shifr_bytecount += count ( $encrypteddata ) ;
    if ( $shifr_bytecount >= 60 ) {
      $shifr_message .= "\n" ;
      $shifr_bytecount = 0 ; } }
  else
    foreach ( $encrypteddata as $ed ) {
      /*
if  ( streambuf_bufbitsize  ( me  ) < 2 ) {
      streambuf_buf ( me  ) or_eq ( ( ( * encrypteddata ) [ i ] ) <<
        streambuf_bufbitsize  ( me  ) ) ;
      streambuf_bufbitsize  ( me  ) +=  6 ; }
    else  {
      uint8_t const to_write  = ( ( ( * encrypteddata ) [ i ] ) <<
        streambuf_bufbitsize  ( me  ) ) bitor streambuf_buf ( me  ) ;
        size_t  writen_count  ;
        writen_count = fwrite ( & to_write , 1 , 1 ,
          streambuf_file  ( me  ) ) ;
      if ( writen_count < 1 ) {
        clearerr ( streambuf_file  ( me  ) ) ; 
        ns_shifr  . string_exception  = ( ns_shifr . localerus ? 
          ( strcp ) & u8"streambuf_write6: ошибка записи байта" :
          ( strcp ) & "streambuf_write6: byte write error" ) ;
        longjmp(ns_shifr  . jump,1); }
      
        // + 6 - 8
        streambuf_bufbitsize  ( me  ) -= 2 ;
        streambuf_buf ( me  ) = ( ( * encrypteddata ) [ i ] ) >>
          ( 6 - streambuf_bufbitsize  ( me  ) ) ;  }      
      */
      } }
    
function  shifr_encode6 ( ) {
  global  $shifr_message  ;
  $message_array = str_split  ( $shifr_message  ) ;
  $shifr_message =  '';
  $bufin = 0 ;
  $bufout = array ( ) ;
  $bitscount  = 0 ;
  foreach ( $message_array as $char ) 
    shifr_write_array ( shifr_byte_to_array6 ( ord ( $char ) , $bufin , 
      $bitscount  ) ) ;
  if ( $bitscount ) shifr_write_array ( array ( $bufin )  ) ; }
    
function  shifr_decode4 ( ) {
  global  $shifr_password ;
  global  $shifr_message  ;
  global  $shifr_deshia ;
  global  $shifr_old_last_data  ;
  global  $shifr_old_last_sole  ;
  global  $shifr_flagtext ;
  $message_array = str_split  ( $shifr_message  ) ;
  $shifr_message = '' ;
  if ( $shifr_flagtext ) {
      for ( $i = 0 ; $i < count($message_array) - 2 ; $i += 3 ) {
        $hexarray =  array( ) ;
        while ( $message_array [ $i ] < 'R' or $message_array [ $i ] > 'z') {
          ++ $i ;
          if ( $i + 3 > count($message_array) ) break ; }
        if ( $i + 3 > count($message_array) ) break ;
        $buf3 = array ( ) ;
        $buf3 [ 0 ] = ord ( $message_array [ $i ] ) - ord ( 'R' ) ;
        for ( $j = 1; $j <= 2 ; ++ $j ) {
          $hexdig = 0 ;
          if  ( $message_array [ $i + $j ] >= 'R' and
            $message_array [ $i + $j ] <= 'z' )
            $buf3 [ $j  ] = ord ( $message_array [ $i + $j ] ) - ord ( 'R' ) ;
          else return ; }
        $u16 = $buf3 [ 0 ] + 40 * ( $buf3 [ 1 ] + 40 * $buf3 [ 2 ] ) ;
        $buf = array ( $u16 & 0xff , $u16 >> 8 ) ;
        $secretdata = array (
          $buf [ 0 ] & 0xf ,
          ( $buf [ 0 ] >> 4 ) & 0xf ,
          $buf [ 1 ] & 0xf ,
          ( $buf [ 1 ] >> 4 ) & 0xf ) ;
        $decrypteddata = array ( ) ;
        shifr_decrypt_sole4 ( $secretdata , $shifr_deshia , $decrypteddata ,
          $shifr_old_last_sole , $shifr_old_last_data ) ;
        $shifr_message .= chr ( ( $decrypteddata [ 0 ] & 0x3  ) |
          ( ( $decrypteddata [ 1 ] & 0x3  ) << 2  ) |
          ( ( $decrypteddata [ 2 ] & 0x3  ) <<  4 ) |
          ( ( $decrypteddata [ 3 ] & 0x3  ) << 6  ) ) ; } // for $i    
    }
  else {
    // binary
    for ( $i = 0 ; $i < count ( $message_array  ) - 1 ; $i += 2 ) {
      $secretdata = array (
        ord ( $message_array [ $i ] ) & 0xf ,
        ( ord ( $message_array [ $i ] ) >> 4 ) & 0xf ,
        ord ( $message_array [ $i + 1 ] ) & 0xf ,
        ( ord ( $message_array [ $i + 1 ] ) >> 4 ) & 0xf ) ;
      $decrypteddata = array ( ) ;
      shifr_decrypt_sole4 ( $secretdata , $shifr_deshia , $decrypteddata ,
        $shifr_old_last_sole , $shifr_old_last_data ) ;
      $shifr_message .= chr ( ( $decrypteddata [ 0 ] & 0x3  ) |
          ( ( $decrypteddata [ 1 ] & 0x3  ) << 2  ) |
          ( ( $decrypteddata [ 2 ] & 0x3  ) <<  4 ) |
          ( ( $decrypteddata [ 3 ] & 0x3  ) << 6  ) ) ; } } }    
    
// number /= div , number := floor [ деление ] , return := остаток
function  number_div8_mod ( array & $number , int $div ) : int {
  $modi = 0 ;
  for ( $i = count  ( $number ) ; $i > 0 ; )  {
    -- $i ;
    $x = ( $modi << 8 ) | ( $number [ $i  ] ) ;
    $modi = $x % $div  ;
    $number [ $i  ] = intdiv  ( $x , $div  ) ;  }
  for ( $i = count  ( $number ) ; $i > 0 ; )  {
    -- $i ;
    if ( $number [ $i  ] != 0 ) break ;
    unset ( $number [ $i  ] ) ; }
  return  $modi ; }
    
function  number_dec  ( array & $number ) {
  for ( $i = 0 ; $i < count  ( $number ) ; ++ $i )  {
    if ( $number [ $i  ] != 0 ) {
      $number [ $i  ] = $number [ $i  ] - 1 ;
      break ; }
    $number [ $i  ] = 0xff  ; }
  if ( $i == count  ( $number ) ) {
    echo  'number_dec:$i == count  ( $number )' ;
    return  ; }
  for ( $i = count  ( $number ) ; $i > 0 ; )  {
    -- $i ;
    if ( $number [ $i  ] != 0 ) break ;
    unset ( $number [ $i  ] ) ; } }
    
function  number_not_zero ( array & $number ) {
  return  count  ( $number ) >  0 ; }

function  shifr_password_to_string4 ( array $passworda ) : string {
  global  $shifr_letters  ;
  global  $shifr_letters95  ;
  global  $shifr_letters_mode ;
  if ( $shifr_letters_mode ==  95 )
    $letters  = $shifr_letters95  ;
  else
    $letters  = $shifr_letters  ;
  $letters_count  = count ( $letters  ) ;
  $str = '' ;
  if ( number_not_zero ( $passworda ) ) {
    do {
      number_dec ( $passworda ) ;
      $str .= $letters [ number_div8_mod ( $passworda , $letters_count ) ] ;
    } while ( number_not_zero ( $passworda ) ) ; }
  return $str ; }
  
function  number_set_zero ( array & $number ) {
  $number = array ( ) ; }

function  number_set_byte ( array & $number , int $byte ) {
  if ( $byte != 0 ) {
    if ( $byte < 0 ) {
      echo 'number_set_byte:$byte < 0' ;
      return  ; }
    if ( $byte >= 0x100 ) {
      echo 'number_set_byte:$byte >= 0x100' ;
      return  ; }
    $number = array ( $byte ) ; }
  else
    $number = array ( ) ; }
  
function  number_add  ( array & $num , array & $xnum ) {
  $per = 0 ;
  for ( $i = 0 ; $i < count  ( $num ) and $i < count  ( $xnum ) ; ++ $i )  {
    $s = $num [ $i ] + $xnum [ $i ] + $per ;
    if ( $s >= 0x100  ) {
      $num [ $i ] = $s - 0x100 ;
      $per = 1 ; }
    else  {
      $num [ $i ] = $s  ;
      $per = 0 ;  } }
  if ( count  ( $num ) > count  ( $xnum ) ) {
    if ( $per == 0 )  return  ;
    for ( ; $i < count  ( $num ) ; ++ $i )  {
      $s = $num [ $i ] + 1 ;
      if ( $s < 0x100  ) {
        $num [ $i ] = $s  ;
        return ;  }
      $num [ $i ] = 0 ;  }
    $num [ $i ] = 1 ;
    return  ; }
  if ( count  ( $num ) < count  ( $xnum ) ) {
    for ( ; $i < count  ( $xnum ) ; ++ $i )  {
      $s = $xnum [ $i ] + $per ;
      if ( $s == 0x100  ) {
        $num [ $i ] = 0 ;
        $per  = 1 ; }
      else  {
        $num [ $i ] = $s  ;
        $per  = 0 ; } } }
  if ( $per > 0 ) $num [ $i ] = 1 ; }

function  number_mul_byte ( array & $number , int $byte ) {
  if ( $byte == 0 ) {
    $number = array ( ) ;
    return  ; }
  if ( $byte == 1 ) return ;
  if ( $byte < 0 ) {
    echo 'number_mul_byte: $byte < 0' ;
    return  ; }
  if ( $byte >= 0x100 ) {
    echo 'number_mul_byte: $byte >= 0x100' ;
    return  ; }
  $per = 0 ;
  for ( $i = 0 ; $i < count  ( $number ) ; ++ $i )  {
    $x = $number [ $i ] *  $byte  + $per ;
    $number [ $i ] = $x & 0xff ;
    $per = $x >> 8 ; }
  if ( $per > 0 ) $number [ $i ] = $per ; }
  
// [ 0..15 , 0..14 , 0..13 , ... , 0..2 , 0..1 ] = [ x , y , z , ... , u , v ] =
// = x + y * 16 + z * 16 * 15 + ... + u * 16! / 2 / 3 + v * 16! / 2 = 0 .. 16!-1
function  shifr_pass_to_array4 ( array & $password ) : array {
  $re = array ( ) ;
  $mu = array ( 1 ) ;
  $in = 0 ;
  do {
    // $re += $password [ $in ] * $mu ;
    $mux = $mu ;
    number_mul_byte ( $mux  , $password [ $in ] ) ;
    number_add  ( $re , $mux  ) ;
    //$mu *=  16 - $in ;
    number_mul_byte ( $mu , 16 - $in  ) ;
    ++  $in ;
  } while ( $in < 15 ) ;
  return  $re ; }
  
function  shifr_password_load4  ( array $password ) {
  global  $shifr_shifra ;
  global  $shifr_deshia ;
  $shifr_shifra = array_fill  ( 0 , 16 , 0xff  ) ;
  $shifr_deshia = array_fill  ( 0 , 16 , 0xff  ) ;
  $arrind = array ( ) ;
  for ( $i = 0 ; $i < 16 ; ++ $i ) $arrind [ ] = $i ;
  $inde = 0 ;
  do {
    $cindex = number_div8_mod ( $password , 16 - $inde ) ;
    $shifr_shifra [ $inde ] = $arrind [ $cindex ] ;
    $shifr_deshia [ $arrind [ $cindex ] ] = $inde ;
    unset ( $arrind [ $cindex ] ) ;
    $arrind = array_values ( $arrind ) ;
    ++ $inde  ;
  } while ( $inde < 16 ) ; }
  
function  shifr_string_to_password4  ( string & $str ) {
  global  $shifr_localerus  ;
  global  $shifr_letters  ;
  global  $shifr_letters95  ;
  global  $shifr_letters_mode ;
  $strn = strlen  ( $str  ) ;
  $passarr = array ( ) ;
  number_set_zero ( $passarr ) ;
  if ( $strn == 0 ) return $passarr ;
  if  ( $shifr_letters_mode ==  95  )
    $letters  = $shifr_letters95  ;
  else
    $letters  = $shifr_letters  ;
  $letters_count  = count ( $letters  ) ;
  $mult = array ( ) ;
  number_set_byte ( $mult , 1 ) ;
  $stringi  = 0 ;
  do  {
    $i = $letters_count ;
    do {
      -- $i ;
      if ( $str [ $stringi ] == $letters [ $i ] ) goto found ; 
    } while ( $i ) ;
    if  ( $shifr_localerus )
      echo 'неправильная буква в пароле'  ;
    else
      echo 'wrong letter in password' ;
    return ;
found :
    $tmp = $mult ;
    number_mul_byte ( $tmp , $i + 1 ) ;
    number_add ( $passarr , $tmp ) ;
    number_mul_byte ( $mult , $letters_count ) ;
    ++  $stringi ;
  } while ( ord ( $str [ $stringi ] ) ) ;
  return  $passarr ; }  
  
$shifr_letters95 = array ( ) ;
for ( $i = ord(' ') ; $i <= ord('~') ; ++ $i )
  $shifr_letters95 [ ] = chr ( $i ) ;
  
$shifr_letters = array ( ) ;
for ( $i = ord('0') ; $i <= ord('9') ; ++ $i )
  $shifr_letters [ ] = chr ( $i ) ;
for ( $i = ord('A') ; $i <= ord('Z') ; ++ $i )
  $shifr_letters [ ] = chr ( $i ) ;
for ( $i = ord('a') ; $i <= ord('z') ; ++ $i )
  $shifr_letters [ ] = chr ( $i ) ;
  
$shifr_letters_mode = 62 ;  
  
$local = setlocale ( LC_ALL  , ''  ) ;  
if ( $local == 'ru_RU.UTF-8' ) $shifr_localerus = true ;
else $shifr_localerus = false ;
/*
if  ( isset ( $_REQUEST [ 'Шифрование_в_текстовом_режиме' ] ) or
  isset ( $_REQUEST [ 'Encryption_in_text_mode' ] ) )*/
  $shifr_flagtext = true ;
        
if  ( $_POST  ) {
  if  ( isset ( $_POST  [ 'submit'  ] ) ) {
  
    if  ( isset ( $_POST  [ 'radio' ] ) ) {
      if (  $_POST  [ 'radio' ] == 'ASCII' )  $shifr_letters_mode = 95 ;  
      else  $shifr_letters_mode = 62 ; }
  
    if  ( $_POST  [ 'submit'] == 'зашифровать' or 
      $_POST  [ 'submit'  ] == 'encrypt'  ) {
      $shifr_password = $_REQUEST['password'] ;
      $shifr_message = $_REQUEST['message'] ;
      shifr_password_load4  ( shifr_string_to_password4  ( $shifr_password ) ) ;
      $shifr_old_last_data  = 0 ;
      $shifr_old_last_sole  = 0 ;
      $shifr_bytecount  = 0 ;
      shifr_encode4 ( ) ; 
      if ( $shifr_flagtext && $shifr_bytecount > 0 ) $shifr_message .= "\n" ; }
  else if  ( $_POST  [ 'submit'] == 'расшифровать' or 
      $_POST  [ 'submit'  ] == 'decrypt'  ) {
      $shifr_password = $_REQUEST['password'] ;
      $shifr_message = $_REQUEST['message'] ;
      shifr_password_load4 ( shifr_string_to_password4  ( $shifr_password ) ) ;
      $shifr_old_last_data  = 0 ;
      $shifr_old_last_sole  = 0 ;
      shifr_decode4 ( ) ; }    
  else  if  ( $_POST  [ 'submit'] == 'генерировать' or 
      $_POST  [ 'submit'  ] == 'generate'  ) {
      $shifr_password = shifr_password_to_string4 ( shifr_pass_to_array4 (
          shifr_generate_pass4 ( ) ) ) ;
      $shifr_message = $_REQUEST['message'] ; }
  else  if  ( $_POST  [ 'submit'] == 'Загрузить' or 
      $_POST  [ 'submit'  ] == 'Download'  ) { 
      // Каталог, в который мы будем принимать файл:
      $uploaddir = './uploads/';
      $uploadfile = $uploaddir  . basename  (
        $_FILES [ 'uploadfile'  ] [ 'name'  ] ) ;
      // Копируем файл из каталога для временного хранения файлов:
      if (  copy  ( $_FILES [ 'uploadfile'  ] [ 'tmp_name'  ] , $uploadfile ) ) {
        }
      else {
        if  ( $shifr_localerus )
          echo '<h3>Ошибка! Не удалось загрузить файл на сервер!</h3>' ;
        else  echo '<h3>Error! Unsuccessful loading file to server!</h3>' ;
        exit  ; }
      $fp = fopen ( $uploadfile , 'rb'  ) ;
      $shifr_message  = ''  ;
      do  {
        $shifr_message .= fread ( $fp , 0x1000 ) ;
      } while ( ! feof  ( $fp ) ) ;
      fclose  ( $fp ) ; } 
   else
   if  ( $_POST  [ 'submit'] == 'Зашифровать файл' or 
      $_POST  [ 'submit'  ] == 'Encrypt file'  ) {
      $fp = fopen ( $_FILES [ 'uploadfile'  ] [ 'tmp_name'  ] , 'rb'  ) ;
      $shifr_message_encode = ''  ;
      $shifr_password = $_REQUEST['password'] ;
      shifr_password_load4  ( shifr_string_to_password4  ( $shifr_password ) ) ;
      $shifr_old_last_data  = 0 ;
      $shifr_old_last_sole  = 0 ;
      $shifr_bytecount  = 0 ;
      while ( ! feof  ( $fp ) ) {
        $shifr_message = fread ( $fp , 0x1000 ) ;
        shifr_encode4 ( ) ;
        $shifr_message_encode .= $shifr_message ; }
      fclose  ( $fp ) ;
      $shifr_message = $shifr_message_encode  ;
      if ( $shifr_flagtext && $shifr_bytecount > 0 ) $shifr_message .= "\n" ; }
   else
   if  ( $_POST  [ 'submit'] == 'Расшифровать файл' or 
      $_POST  [ 'submit'  ] == 'Decrypt file'  ) {
      $fp = fopen ( $_FILES [ 'uploadfile'  ] [ 'tmp_name'  ] , 'rb'  ) ;
      $shifr_message_decode = ''  ;
      $shifr_password = $_REQUEST['password'] ;
      shifr_password_load4  ( shifr_string_to_password4  ( $shifr_password ) ) ;
      $shifr_old_last_data  = 0 ;
      $shifr_old_last_sole  = 0 ;
      $shifr_bytecount  = 0 ;
      while ( ! feof  ( $fp ) ) {
        $shifr_message = fread ( $fp , 0x1000 ) ;
        shifr_decode4 ( ) ;
        $shifr_message_decode .= $shifr_message ; }
      fclose  ( $fp ) ;
      $shifr_message = $shifr_message_decode  ; } } }
  
?>
<style> p { font-size: 36px; }  textarea { font-size: 36px; }
input { font-size: 36px; } input.largerCheckbox { transform : scale(2); }
</style>
<html>
<body>
<form action="<?php echo $_SERVER['PHP_SELF'] ; ?>" method=post enctype=multipart/form-data>
<input type=file name=uploadfile>
<?php
if  ( $shifr_localerus )
  echo '<input type=submit name="submit" value="Загрузить" ><br>' . PHP_EOL ;
else
  echo '<input type=submit name="submit" value="Download" ><br>' . PHP_EOL  ;
if  ( $shifr_localerus )
  echo '<input type=submit name="submit" value="Зашифровать файл" > '.PHP_EOL ;
else
  echo '<input type=submit name="submit" value="Encrypt file" > '.PHP_EOL  ;
if  ( $shifr_localerus )
  echo '<input type=submit name="submit" value="Расшифровать файл" ><br>'.PHP_EOL ;
else
  echo '<input type=submit name="submit" value="Decrypt file" ><br>'.PHP_EOL  ;

if  ( $shifr_localerus )
    echo 'Сообщение:'  ;
  else
    echo 'Message:' ;

?>
  <br />
  <textarea name="message" rows="12" cols="61"><?php echo $shifr_message ; ?></textarea><br />
<p>

<?php if  ( $shifr_localerus ) echo 'Алфавит знаков в пароле :' ; else
echo 'Alphabet of characters in a password :' ; ?>
<input type="radio" name="radio" value="ASCII" <?php if ( $shifr_letters_mode == 95 ) echo 'checked' ?> >ASCII <?php if  ( $shifr_localerus ) echo 'буквы цифры знаки пробел'; else echo 'letters digits signs space'; ?>
<input type="radio" name="radio" value="LettDigit" <?php if ( $shifr_letters_mode == 62 ) echo 'checked' ?> ><?php if  ( $shifr_localerus ) echo 'цифры и буквы'; else echo 'digits and letters'; ?>
<br>
<?php
  if  ( $shifr_localerus )
    echo 'Ваш пароль : '  ;
  else
    echo 'Your password : ' ;
?>
<input name="password" type="text" value="<?php echo $shifr_password ; ?>" /> <?php if  ( $shifr_localerus )
    echo '<input type="submit" name="submit" value="генерировать" />'  ;
  else
    echo '<input type="submit" name="submit" value="generate" />' ;
    /*
if  ( $shifr_localerus )    {
  echo '<br>Шифрование в текстовом режиме : <input type="checkbox" class="largerCheckbox" name="Шифрование_в_текстовом_режиме" value="1" id="SText" '; if($shifr_flagtext)echo 'checked'; echo ' />' ; }
else {
  echo '<br>Encryption in text mode : <input type="checkbox" class="largerCheckbox" name="Encryption_in_text_mode" value="1" id="SText" ';
  if($shifr_flagtext)echo 'checked'; echo ' />' ;
    }*/
    ?>

    <br />
</p>
<?php
  if  ( $shifr_localerus )
    echo ' <input type="submit" name="submit" value="зашифровать" />'  ;
  else
    echo ' <input type="submit" name="submit" value="encrypt" />' ;
  if  ( $shifr_localerus )
    echo ' <input type="submit" name="submit" value="расшифровать" />'  ;
  else
    echo ' <input type="submit" name="submit" value="decrypt" />' ;  
?>
</form>
</body>
</html>

