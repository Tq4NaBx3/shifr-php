<?php 
/*
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
function shifr_generate_pass ( ) {
  $dice = array ( ) ;
  for ( $i  = 3 ; $i  > 0 ; $i -- ) {
    $r = rand  ( 0 , $i )  ;
    $dice [ ] = $r ; }
  return  $dice ; }

// [ 0..3 , 0..2 , 0..1 ] = [ x , y , z ] =
// = x + y * 4 + z * 12 = 0 .. 23
function  shifr_pass_to_array ( array & $password ) {
  $re = 0 ;
  $mu = 1 ; // 1 , 4 , 4 * 3
  $in = 0 ;
  do {
    $re += $password [ $in ] * $mu ;
    $mu *=  4 - $in ;
    ++  $in ;
  } while ( $in <= 2 ) ;
  return  array ( $re ) ; }
  
function  shifr_password_is_not_zero ( array & $passworda ) {
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
  
/*
буквы равны : o , i

[0,0,0] = []
[1,0,0] = [o]
[2,0,0] = [i]
[3,0,0] = [o,o]
[0,1,0] = [i,o]
[1,1,0] = [o,i]
[2,1,0] = [i,i]
[3,1,0] = [o,o,o]
[0,2,0] = [i,o,o]
[1,2,0] = [o,i,o]
[2,2,0] = [i,i,o]
[3,2,0] = [o,o,i]
[0,0,1] = [i,o,i]
[1,0,1] = [o,i,i]
[2,0,1] = [i,i,i]
[3,0,1] = [o,o,o,o]
[0,1,1] = [i,o,o,o]
[1,1,1] = [o,i,o,o]
[2,1,1] = [i,i,o,o]
[3,1,1] = [o,o,i,o]
[0,2,1] = [i,o,i,o]
[1,2,1] = [o,i,i,o]
[2,2,1] = [i,i,i,o]
[3,2,1] = [o,o,o,i]
*/  

function  shifr_password_to_string ( array $passworda ) {
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
  
$local = setlocale ( LC_ALL  , ''  ) ;  
if ( $local == 'ru_RU.UTF-8' ) $shifr_localerus = true ;
else $shifr_localerus = false ;
echo '<style> p { font-size: 36px; } </style>' ;
?>

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
?>
</p>
</body>
</html>
