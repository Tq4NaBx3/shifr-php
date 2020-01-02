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
  
function  shifr_password_is_not_zero ( array & $passworda ) : bool {
  foreach ( $passworda as $digit ) {
    if ( $digit != 0 ) return true ; }
  return false ; }

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
  
if  ( $_POST  ) {
  if  ( isset ( $_POST  [ 'submit'  ] ) ) {
    if  ( $_POST  [ 'submit'] == 'зашифровать сообщение' or 
      $_POST  [ 'submit'  ] == 'encrypt a message'  ) {
      $flag_encrypt_message = true ;
      $password = $_REQUEST['password'] ;
      $message = $_REQUEST['message'] ;
      
      $shifr_letters = array ( ) ;
      for ( $i = 0 ; $i < 24 ; ++ $i )
        $shifr_letters [ ] = chr ( ord ( 'a' ) + $i ) ;
      $pa = shifr_string_to_password  ( $password ) ;
      shifr_password_load  ( $pa ) ;
      $message_array = str_split  ( $message  ) ;
      $message =  '';
      $old_last_data  = 0 ;
      $old_last_sole  = 0 ;
      foreach ( $message_array as $char ) {
        $secret_data = shifr_byte_to_array ( ord ( $char ) ) ;
        $secret_data_sole = shifr_data_sole ( $secret_data ) ;
        shifr_data_xor ( $old_last_data , $old_last_sole ,
          $secret_data_sole ) ;
        $encrypteddata = shifr_crypt_decrypt ( $secret_data_sole , $shifr_shifra )  ;
        $encryptedbytes = shifr_array_to_bytes ( $encrypteddata ) ;
        foreach ( $encryptedbytes as $byte2 ) {
          $hexarr = shifr_byte_to_hex ( $byte2 ) ;
          foreach ( $hexarr as $h ) $message .= $h ; } } } } }
  
$local = setlocale ( LC_ALL  , ''  ) ;  
if ( $local == 'ru_RU.UTF-8' ) $shifr_localerus = true ;
else $shifr_localerus = false ;
?>
<style> p { font-size: 36px; }  textarea { font-size: 36px; } </style>
<html>
<body>
<h1>Шифруемся!</h1>
<p>
<?php
echo  '<p>Локаль = \'' ;
print_r ($local);
echo '\' ; $shifr_localerus = '.$shifr_localerus.'</p>'.PHP_EOL ;
$p = shifr_generate_pass ( ) ;
echo  '<p>Пасс = ' ;
print_r ( $p ) ;
echo '</p>'.PHP_EOL ;
$ar = shifr_pass_to_array ( $p  ) ;
echo '<p>Пароль в массив = ' ;
print_r ( $ar ) ;
echo '</p>'.PHP_EOL ;
echo '<p>Пароль не ноль = ' ;
print_r ( shifr_password_is_not_zero ( $ar ) ) ;
echo '</p>'.PHP_EOL ;
/*shifr_password_dec ( $ar )  ;
echo  '<p>-- пароль ; => ' ;
print_r ( $ar ) ;
echo '</p>'.PHP_EOL ;*/
/*$shifr_letters = array ( 'o' , 'i' ) ;
$st = shifr_password_to_string ( $ar ) ;
echo  '<p>пароль в строку => буквы : ' ;
print_r ( $shifr_letters ) ;
echo '<br>пароль буквами = \'' ;
print_r ( $st ) ;
echo '\'</p>'.PHP_EOL ;*/
$shifr_letters = array ( ) ;
for ( $i = 0 ; $i < 24 ; ++ $i )
  $shifr_letters [ ] = chr ( ord ( 'a' ) + $i ) ;
echo '<p>буквы = ';
print_r ( $shifr_letters ) ;
echo '</p>'.PHP_EOL;
$st = shifr_password_to_string ( $ar ) ;
echo '<p>пароль буквами = \'' ;
print_r ( $st ) ;
echo '\'</p>'.PHP_EOL ;
$pa = shifr_string_to_password  ( $st ) ;
echo '<p>пароль числами = ' ;
print_r ( $pa ) ;
echo '</p>'.PHP_EOL ;
shifr_password_load  ( $pa ) ;
echo '<p>$shifr_shifra = ' ;
print_r ( $shifr_shifra ) ;
echo '<br>'.PHP_EOL ;
echo '$shifr_deshia = ' ;
print_r ( $shifr_deshia ) ;
echo '</p>'.PHP_EOL ;

$secret_data = shifr_byte_to_array ( 0xaa ) ;
$secret_data_sole = shifr_data_sole ( $secret_data ) ;
echo '<p>$secret_data_sole(';
print_r ( $secret_data ) ;
echo ') = ' ;
print_r ( $secret_data_sole ) ;
echo '</p>'.PHP_EOL ;
$old_last_data  = 0 ;
$old_last_sole  = 0 ;
shifr_data_xor ( $old_last_data , $old_last_sole ,
  $secret_data_sole ) ;
echo '<p>после XOR $secret_data_sole = ' ;
print_r ( $secret_data_sole ) ;
echo '</p>'.PHP_EOL ;
$encrypteddata = shifr_crypt_decrypt ( $secret_data_sole , $shifr_shifra )  ;
echo '<p>$encrypteddata = ' ;
print_r ( $encrypteddata ) ;
echo '</p>'.PHP_EOL ;
$encryptedbytes = shifr_array_to_bytes ( $encrypteddata ) ;
echo '<p>$encryptedbytes = ' ;
print_r ( $encryptedbytes ) ;
echo '</p>'.PHP_EOL ;
echo '<p>HEX $encryptedbytes = [' ;
foreach ( $encryptedbytes as $byte ) {
  $hexarr = shifr_byte_to_hex ( $byte ) ;
  print_r ( $hexarr ) ; }
echo ']</p>'.PHP_EOL ;

?>
</p>
<form method="post" action="<?php echo $_SERVER['PHP_SELF'] ; ?>">
<?php
  if  ( $shifr_localerus )
    echo 'Сообщение:'  ;
  else
    echo 'Message:' ;
?>
  <br />
  <textarea name="message" rows="12" cols="60">
<?php
  if (  $flag_encrypt_message ) {
    echo $message ;
    }
?>
  </textarea><br />
<p>
<?php
  if  ( $shifr_localerus )
    echo 'Ваш пароль:'  ;
  else
    echo 'Your password:' ;
?>
<input name="password" type="text" value="<?php echo $st ; ?>" /><br />
</p>
<?php
  if  ( $shifr_localerus )
    echo '<input type="submit" name="submit" value="зашифровать сообщение" />'  ;
  else
    echo '<input type="submit" name="submit" value="encrypt a message" />' ;
?>
</form>
</body>
</html>
