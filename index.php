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
function shifr_generate_password ( ) {
  $dice = array ( ) ;
  for ( $i  = 3 ; $i  > 0 ; $i -- ) {
    $r = rand  ( 0 , $i )  ;
    $dice [ ] = $r ; }
  return  $dice ; }

// [ 0..3 , 0..2 , 0..1 ] = [ x , y , z ] =
// = x + y * 4 + z * 12 = 0 .. 23
function  shifr_password_to_array ( array & $password ) {
  $re = 0 ;
  $mu = 1 ; // 1 , 4 , 4 * 3
  $in = 0 ;
  do {
    $re += $password [ $in ] * $mu ;
    $mu *=  4 - $in ;
    ++  $in ;
  } while ( $in <= 2 ) ;
  return  array ( $re ) ; }
  
function  shifr_password_array_is_not_zero ( array & $passworda ) {
  foreach ( $passworda as $digit ) {
    if ( $digit != 0 ) return true ; }
  return false ; }

function  shifr_password_array_dec ( array & $passworda ) {
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

function  shifr_password_to_string ( array $passworda , array & $letters ) {
  $letters_count  = count ( $letters  ) ;
  $str = "" ;
  if ( $passworda [ 0 ] ) {
    while ( true ) {
      shifr_password_array_dec ( $passworda ) ;
      $str .= $letters [ $passworda [ 0 ] % $letters_count ] ;
      if ( $passworda [ 0 ] < $letters_count ) return $str ;
      $passworda [ 0 ] = intdiv ( $passworda [ 0 ] , $letters_count ) ; } } }
  
echo '<style> p { font-size: 36px; } </style>' ;
?>

<html>
<body>
<h1>It works!</h1>
<p>
<?php
$p = shifr_generate_password ( ) ;
echo  '<p>Пароль = ' ;
print_r ( $p ) ;
echo '</p>'.PHP_EOL ;
$ar = shifr_password_to_array ( $p  ) ;
echo '<p>Пароль в массив = ' ;
print_r ( $ar ) ;
echo '</p>'.PHP_EOL ;
echo '<p>Пароль не ноль = ' ;
print_r ( shifr_password_array_is_not_zero ( $ar ) ) ;
echo '</p>'.PHP_EOL ;
shifr_password_array_dec ( $ar )  ;
echo  '<p>-- пароль ; => ' ;
print_r ( $ar ) ;
echo '</p>' ;
$le = array ( 'o' , 'i' ) ;
$st = shifr_password_to_string ( $ar , $le ) ;
echo  '<p>пароль в строку => буквы : ' ;
print_r ( $le ) ;
echo '<br>пароль буквами = \'' ;
print_r ( $st ) ;
echo '\'</p>' ;
$le = array ( ) ;
for ( $i = 0 ; $i < 24 ; ++ $i )
  $le [ ] = chr ( ord ( 'a' ) + $i ) ;
echo '<p>буквы = ';
print_r ( $le ) ;
echo '</p>';
$st = shifr_password_to_string ( $ar , $le ) ;
echo '<p>пароль буквами = \'' ;
print_r ( $st ) ;
echo '\'</p>' ;
?>
</p>
</body>
</html>
