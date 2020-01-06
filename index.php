<?php 

/*
RUS
1 бит соль
1 бит инфо
в сумме 2 бита
b0 может быть зашифрован двумя способами из четырёх:
00 , 01 , 10 , 11
вариантов шифрования b0 :
b0 = 4 * 3 = 12 шт
b1 = 2 * 1 = 2 шт
в общем 4 ! = 24 шт
минимум можно записать пароль с помощью
log(2,24) = 4.585 бит <= 5 бит
пароль будет 5 бит
можно разрешить только одну маленькую букву

*/
// возвращается массивы 
// [ 0..3 , 0..2 , 0..1 ]

function shifr_generate_pass ( ) : array {
  $dice = array ( ) ;
  for ( $i  = 3 ; $i  > 0 ; $i -- ) {
    $r = rand  ( 0 , $i )  ;
    $dice [ ] = $r ; }
  return  $dice ; }

// [ 0..3 , 0..2 , 0..1 ] = [ x , y , z ] =
// = x + y * 4 + z * 12 = 0 .. 23
function  shifr_pass_to_array ( array & $password ) : array {
  $re = 0 ;
  $mu = 1 ; // 1 , 4 , 4 * 3
  $in = 0 ;
  do {
    $re += $password [ $in ] * $mu ;
    $mu *=  4 - $in ;
    ++  $in ;
  } while ( $in <= 2 ) ;
  return  array ( $re ) ; }
  /*
function  shifr_password_is_not_zero ( array & $passworda ) : bool {
  foreach ( $passworda as $digit ) {
    if ( $digit != 0 ) return true ; }
  return false ; }*/

function  shifr_password_dec ( array & $passworda ) {
  $i = 0 ;
  do {
    if ( $passworda [ $i ] != 0 ) {
      -- $passworda [ $i ] ;
      break ; }
    $passworda [ $i ] = 23 ;
    ++  $i  ;
  } while ( $i < 1 ) ; }

function  shifr_password_to_string ( array $passworda ) : string {
  global  $shifr_letters  ;
  $letters_count  = count ( $shifr_letters  ) ;
  $str = '' ;
  if ( $passworda [ 0 ] ) {
    while ( true ) {
      shifr_password_dec ( $passworda ) ;
      $str .= $shifr_letters [ $passworda [ 0 ] % $letters_count ] ;
      if ( $passworda [ 0 ] < $letters_count ) return $str ;
      $passworda [ 0 ] = intdiv ( $passworda [ 0 ] , $letters_count ) ; } }
  return $str ; }
      
function  shifr_string_to_password  ( string & $str ) {
  global  $shifr_localerus  ;
  global  $shifr_letters  ;
  $strn = strlen  ( $str  ) ;
  if ( $strn == 0 ) return  array ( 0 ) ;
  $letters_count  = count ( $shifr_letters  ) ;
  $pass = 0 ;
  $mult = 1 ;
  $stringi  = 0 ;
  do  {
    $i = $letters_count ;
    do {
      -- $i ;
      if ( $str [ $stringi ] == $shifr_letters [ $i ] ) goto found ; 
    } while ( $i ) ;
    if  ( $shifr_localerus )
      echo 'неправильная буква в пароле'  ;
    else
      echo 'wrong letter in password' ;
    return ;
found :
    $pass  +=  ( $i + 1 ) * $mult ;
    $mult  *=  $letters_count ;
    ++  $stringi ;
  } while ( $str [ $stringi ] ) ;
  return  array ( $pass ) ; }
    
function  shifr_password_load  ( array $password ) {
  global  $shifr_shifra ;
  global  $shifr_deshia ;
  $shifr_shifra = array_fill  ( 0 , 4 , 0xff  ) ;
  $shifr_deshia = array_fill  ( 0 , 4 , 0xff  ) ;
  $arrind = array ( ) ;
  for ( $i = 0 ; $i < 4 ; ++ $i ) $arrind [ ] = $i ;
  $inde = 0 ;
  do {
    $cindex = $password [ 0 ] % ( 4 - $inde ) ;
    $password [ 0 ] = intdiv (  $password [ 0 ] , 4 - $inde ) ;
    $shifr_shifra [ $inde ] = $arrind [ $cindex ] ;
    $shifr_deshia [ $arrind [ $cindex ] ] = $inde ;
    unset ( $arrind [ $cindex ] ) ;
    $arrind = array_values ( $arrind ) ;
    ++ $inde  ;
  } while ( $inde < 4 ) ; }
  
function  shifr_data_sole ( array $secret_data ) : array {
  $secret_data_sole = array ( ) ;
  $ra = rand ( 0 , 0xff ) ;
  foreach ( $secret_data as $da ) {
    $secret_data_sole [ ] = ( $da << 1 ) | ( $ra & 0x1 ) ;
    $ra >>= 1 ; }
  return  $secret_data_sole ; }
  
function  shifr_byte_to_array ( int $byte ) : array {
  $arr = array ( ) ;
  for ( $i = 0 ; $i < 8 ; ++ $i ) {
    $arr [ ] = $byte & 0x1 ;
    $byte >>= 1 ; }
  return  $arr ; }
  
function  shifr_data_xor ( int & $old_last_data , int & $old_last_sole ,
  array & $secret_data_sole ) {
  foreach ( $secret_data_sole as & $ids ) {
    $cur_data = $ids  >>  1 ;
    $cur_sole = $ids  & 0x1 ;
    $ids  ^=  ( $old_last_sole  <<  1 ) ;
    $ids  ^=  $old_last_data  ;
    $old_last_data = $cur_data ;
    $old_last_sole  = $cur_sole ; } }
  
function  shifr_crypt_decrypt ( array $datap , array & $tablep ) : array {
  $encrp = array ( ) ;
  foreach ( $datap as $id ) $encrp [ ] = $tablep [ $id ] ;
  return  $encrp  ; }
  
function  shifr_array_to_bytes ( array $arr ) : array {
  $bytes = array ( ) ;
  for ( $j = 0 ; $j < 2 ; ++ $j ) {
    $bytes [ $j ] = 0 ;
    for ( $i = 0 ; $i < 4 ; ++ $i ) {
      $bytes [ $j ] |= $arr [ $j * 4 + $i ] << ( $i << 1 ) ; } }
  return  $bytes ; }
  
function  shifr_byte_to_hex ( int $buf ) : array {
  $c = $buf & 0xf ;
  $res = array ( ) ;
  if ( $c <= 9 ) $res [ 0 ] = chr ( ord ( '0' ) + $c ) ;
  else  $res [ 0 ] = chr ( ord ( 'a' ) + $c - 10 ) ;
  $c = ( $buf >> 4 ) & 0xf ;
  if ( $c <= 9 ) $res [ 1 ] = chr ( ord ( '0' ) + $c ) ;
  else  $res  [ 1 ] = chr ( ord ( 'a' ) + $c - 10 ) ; 
  return  $res ; }
  
function  shifr_decrypt_sole ( array & $datap , array & $tablep ,
  array & $decrp ,  & $old_last_sole ,  & $old_last_data ) {
  foreach ( $datap as $id ) {
    $data_sole = $tablep [ $id ] ;
    $newdata = ( $data_sole >> 1 ) ^ $old_last_sole ;
    $decrp [ ] = $newdata ;
    $old_last_sole = (  $data_sole & 0x1 ) ^ $old_last_data ;
    $old_last_data  = $newdata ; } }

function  shifr_encode2 ( ) {
  global  $shifr_password ;
  global  $shifr_message  ;
  global  $shifr_shifra ;
  global  $shifr_old_last_data  ;
  global  $shifr_old_last_sole  ;
  global  $shifr_bytecount  ;
  global  $shifr_flagtext ;
      $message_array = str_split  ( $shifr_message  ) ;
      $shifr_message =  '';
//print_r ( '$shifr_flagtext = ' ) ; var_dump ( $shifr_flagtext ) ;      
      foreach ( $message_array as $char ) {
        $secret_data = shifr_byte_to_array ( ord ( $char ) ) ;
        $secret_data_sole = shifr_data_sole ( $secret_data ) ;
        shifr_data_xor ( $shifr_old_last_data , $shifr_old_last_sole ,
          $secret_data_sole ) ;
        $encrypteddata = shifr_crypt_decrypt ( $secret_data_sole , $shifr_shifra )  ;
        $encryptedbytes = shifr_array_to_bytes ( $encrypteddata ) ;
//print_r ( '$encryptedbytes = ' ) ; var_dump ( $encryptedbytes ) ;
        if ( $shifr_flagtext ) {
          foreach ( $encryptedbytes as $byte2 ) {
            $hexarr = shifr_byte_to_hex ( $byte2 ) ;
            foreach ( $hexarr as $h ) $shifr_message .= $h ; }
          ++ $shifr_bytecount ;
          if ( $shifr_bytecount == 15 ) {
            $shifr_message .= "\n" ;
            $shifr_bytecount = 0 ; } }
        else {
          foreach ( $encryptedbytes as $byte2 )
            $shifr_message .= chr ( $byte2 ) ;  } } }
    
function  shifr_decode2 ( ) {
  global  $shifr_password ;
  global  $shifr_message  ;
  global  $shifr_deshia ;
  global  $shifr_old_last_data  ;
  global  $shifr_old_last_sole  ;
  global  $shifr_flagtext ;
  $message_array = str_split  ( $shifr_message  ) ;
  $shifr_message = '' ;
  if ( $shifr_flagtext ) {
      for ( $i = 0 ; $i < count($message_array) ; $i += 4 ) {
        $hexarray =  array( ) ;
        while ( ( $message_array[$i] < '0' or $message_array[$i] > '9') and
            ( $message_array[$i] < 'a' or $message_array[$i] > 'f') ) {
          ++ $i ;
          if ( $i + 4 > count($message_array) ) break ; }
        if ( $i + 4 > count($message_array) ) break ;
        for ( $j = 0; $j < 4 ; ++ $j ) {
          if  ($message_array[$i+$j] >= '0' and $message_array[$i+$j] <= '9')
            $hexdig = ord($message_array[$i+$j]) - ord('0');
          else
            if($message_array[$i+$j] >= 'a' and $message_array[$i+$j] <= 'f')
              $hexdig = 10 + (ord($message_array[$i+$j]) - ord('a'));
            else return ;
          $hexarray [ ] = $hexdig % 4 ;
          $hexarray [ ] = $hexdig >> 2 ; }
      $decrypteddata = array ( ) ;
      shifr_decrypt_sole ( $hexarray , $shifr_deshia , $decrypteddata ,
        $shifr_old_last_sole , $shifr_old_last_data ) ;
      $shifr_message .= chr (
        ( $decrypteddata [ 0 ] & 0x1  ) |
        ( ( $decrypteddata [ 1 ] & 0x1  ) << 1  ) |
        ( ( $decrypteddata [ 2 ] & 0x1  ) << 2  ) |
        ( ( $decrypteddata [ 3 ] & 0x1  ) << 3  ) |
        ( ( $decrypteddata [ 4 ] & 0x1  ) << 4  ) |
        ( ( $decrypteddata [ 5 ] & 0x1  ) << 5  ) |
        ( ( $decrypteddata [ 6 ] & 0x1  ) << 6  ) |
        ( ( $decrypteddata [ 7 ] & 0x1  ) << 7  ) ) ; } // for $i    
  }
  else {
//print_r('$message_array=');var_dump($message_array);  
    // binary
    for ( $i = 0 ; $i < count ( $message_array  ) - 1 ; $i += 2 ) {
//print_r('$i=');var_dump($i);
      $binarray = array ( ) ;
//print_r('$binarray=');var_dump($binarray);      
      for ( $j = 0 ; $j <= 1 ; ++ $j ) {
//print_r('$j=');var_dump($j);      
        for ( $d = 0 ; $d < 8 ; $d += 2 ) {
//print_r('$d=');var_dump($d);        
          $binarray [ ] = ( ord($message_array [ $i + $j ]) >> $d ) & 3 ;
//print_r('$binarray=');var_dump($binarray);          
          } }
print_r('$binarray=');var_dump($binarray);          
      $decrypteddata = array ( ) ;
      shifr_decrypt_sole ( $binarray , $shifr_deshia , $decrypteddata ,
        $shifr_old_last_sole , $shifr_old_last_data ) ;
      $shifr_message .= chr (
        ( $decrypteddata [ 0 ] & 0x1  ) |
        ( ( $decrypteddata [ 1 ] & 0x1  ) << 1  ) |
        ( ( $decrypteddata [ 2 ] & 0x1  ) << 2  ) |
        ( ( $decrypteddata [ 3 ] & 0x1  ) << 3  ) |
        ( ( $decrypteddata [ 4 ] & 0x1  ) << 4  ) |
        ( ( $decrypteddata [ 5 ] & 0x1  ) << 5  ) |
        ( ( $decrypteddata [ 6 ] & 0x1  ) << 6  ) |
        ( ( $decrypteddata [ 7 ] & 0x1  ) << 7  ) ) ; } } }
    
$shifr_letters = array ( ) ;
      for ( $i = 0 ; $i < 24 ; ++ $i )
        $shifr_letters [ ] = chr ( ord ( 'a' ) + $i ) ;

$local = setlocale ( LC_ALL  , ''  ) ;  
if ( $local == 'ru_RU.UTF-8' ) $shifr_localerus = true ;
else $shifr_localerus = false ;

if  ( isset ( $_REQUEST [ 'Шифрование_в_текстовом_режиме' ] ) or
  isset ( $_REQUEST [ 'Encryption_in_text_mode' ] ) )
  $shifr_flagtext = true ;
        
if  ( $_POST  ) {
  if  ( isset ( $_POST  [ 'submit'  ] ) ) {
//echo '$_POST  [ \'submit\'] = "' . $_POST  [ 'submit'] .'"'.PHP_EOL;
//echo '$_REQUEST  [ \'password\'] = "' . $_REQUEST  [ 'password'] .'"'.PHP_EOL;
//echo '$_REQUEST  [ \'message\'] = "' . $_REQUEST  [ 'message'] .'"'.PHP_EOL;
    if  ( $_POST  [ 'submit'] == 'зашифровать' or 
      $_POST  [ 'submit'  ] == 'encrypt'  ) {
      $shifr_password = $_REQUEST['password'] ;
      $shifr_message = $_REQUEST['message'] ;
      shifr_password_load  ( shifr_string_to_password  ( $shifr_password ) ) ;
      $shifr_old_last_data  = 0 ;
      $shifr_old_last_sole  = 0 ;
      $shifr_bytecount  = 0 ;
      shifr_encode2 ( ) ; }
  else if  ( $_POST  [ 'submit'] == 'расшифровать' or 
      $_POST  [ 'submit'  ] == 'decrypt'  ) {
      $shifr_password = $_REQUEST['password'] ;
      $shifr_message = $_REQUEST['message'] ;
      shifr_password_load  ( shifr_string_to_password  ( $shifr_password ) ) ;
      $shifr_old_last_data  = 0 ;
      $shifr_old_last_sole  = 0 ;
      shifr_decode2 ( ) ; }
  else  if  ( $_POST  [ 'submit'] == 'генерировать' or 
      $_POST  [ 'submit'  ] == 'generate'  ) {
      $shifr_password = shifr_password_to_string ( shifr_pass_to_array (
          shifr_generate_pass ( ) ) ) ;
      $shifr_message = $_REQUEST['message'] ; }
  else  if  ( $_POST  [ 'submit'] == 'Загрузить' or 
      $_POST  [ 'submit'  ] == 'Download'  ) { 
      $fp = fopen ( $_FILES['uploadfile']['tmp_name'] , 'rb'  ) ;
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
      shifr_password_load  ( shifr_string_to_password  ( $shifr_password ) ) ;
      $shifr_old_last_data  = 0 ;
      $shifr_old_last_sole  = 0 ;
      $shifr_bytecount  = 0 ;
      while ( ! feof  ( $fp ) ) {
        $shifr_message = fread ( $fp , 0x1000 ) ;
        shifr_encode2 ( ) ;
        $shifr_message_encode .= $shifr_message ; }
      fclose  ( $fp ) ;
      $shifr_message = $shifr_message_encode  ; }
   else
   if  ( $_POST  [ 'submit'] == 'Расшифровать файл' or 
      $_POST  [ 'submit'  ] == 'Decrypt file'  ) {
      $fp = fopen ( $_FILES [ 'uploadfile'  ] [ 'tmp_name'  ] , 'rb'  ) ;
      $shifr_message_decode = ''  ;
      $shifr_password = $_REQUEST['password'] ;
      shifr_password_load  ( shifr_string_to_password  ( $shifr_password ) ) ;
      $shifr_old_last_data  = 0 ;
      $shifr_old_last_sole  = 0 ;
      $shifr_bytecount  = 0 ;
      while ( ! feof  ( $fp ) ) {
        $shifr_message = fread ( $fp , 0x1000 ) ;
        shifr_decode2 ( ) ;
        $shifr_message_decode .= $shifr_message ; }
      fclose  ( $fp ) ;
      $shifr_message = $shifr_message_decode  ;
      }   } }
  
?>
<style> p { font-size: 36px; }  textarea { font-size: 36px; }
input { font-size: 36px; } input.largerCheckbox { transform : scale(2); }
</style>
<html>
<body>
<h1>Шифруемся!</h1>
<?php
//echo '$_FILES=';var_dump($_FILES);
if  ( $shifr_localerus )
    echo 'Сообщение:'  ;
  else
    echo 'Message:' ;
?>
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

?>
  <br />
  <textarea name="message" rows="12" cols="61"><?php echo $shifr_message ; ?></textarea><br />
<p>
<?php
  if  ( $shifr_localerus )
    echo 'Ваш пароль:'  ;
  else
    echo 'Your password:' ;
?>
<input name="password" type="text" value="<?php echo $shifr_password ; ?>" /> <?php if  ( $shifr_localerus )
    echo '<input type="submit" name="submit" value="генерировать" />'  ;
  else
    echo '<input type="submit" name="submit" value="generate" />' ;
    
if  ( $shifr_localerus )    {
  echo '<br>Шифрование в текстовом режиме : <input type="checkbox" class="largerCheckbox" name="Шифрование_в_текстовом_режиме" value="1" id="SText" '; if($shifr_flagtext)echo 'checked'; echo ' />' ; }
else {
  echo '<br>Encryption in text mode : <input type="checkbox" class="largerCheckbox" name="Encryption_in_text_mode" value="1" id="SText" ';
  if($shifr_flagtext)echo 'checked'; echo ' />' ;
    }
    ?>

    <br />
</p>
<?php
  if  ( $shifr_localerus )
    echo '<input type="submit" name="submit" value="зашифровать" />'  ;
  else
    echo '<input type="submit" name="submit" value="encrypt" />' ;
  if  ( $shifr_localerus )
    echo ' <input type="submit" name="submit" value="расшифровать" />'  ;
  else
    echo ' <input type="submit" name="submit" value="decrypt" />' ;
?>
</form>
</body>
</html>

