<?php 
/*
 Шифр ©2020-1 Глебов А.Н.
 Shifr ©2020-1 Glebe A.N.
 
 $shifr  = new shifr ( ) ;
 shifr_init ( shifr & $shifr );
 $shifr -> localerus = true or false ;
 $shifr -> flagtext = false  or true ;
 $shifr -> password = string ; to load , generate
 $shifr -> message = string ; to encode , decode
 $shifr -> letters_mode = 95 or 62 or 10 ;
 shifr_set_version ( shifr & $sh , $ver  ) ; $ver == 2 or 3
 shifr_version ( shifr & $sh ) ; returns 2 or 3
 shifr_password_load  ( $shifr ) ; from message string
 shifr_encrypt  ( shifr & $shifr ) ;
 shifr_flush ( $shifr  ) ; after last encode to clear buffer
 shifr_flush_file  ( $shifr , $fpw ) ;
 shifr_decrypt ( $shifr ) ;
 shifr_generate_password ( $shifr  ) ;
 
*/
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
  log ( 10 , 20922789888000 ) ≈ 13.32 цифр <= 14 цифр

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

// ENG
// 2 bits salt
// 2 bits information
// total 4 bits
// encryption table: personal 2 bits + salt 2 bits => 4 bits encrypted
// personal data b00 => can be encrypted in an ordered set 2^2 = 4pcs from
// b0000 ... b1111 2^4 = 4*4 = 16 pieces
// different encryption layouts for data
// b00 = 16*15*14*13 = 43680
// b01 = 12*11*10*9 = 11880
// b10 = 8*7*6*5 = 1680
// b11 = 4*3*2*1 = 24
// generally = b00 * b01 * b10 * b11 =
//   = 16! = 20922789888000
// minimum you can write a password using
// log(2,20922789888000) ≈ 44.25 bits <= 6 bytes
// the password will have 45 bits size
// ascii letters 126-32+1 = 95 pcs
// letter password length : log ( 95 , 20922789888000 ) ≈ 6.735 letters <= 7 letters
//  log ( 62 , 20922789888000 ) ≈ 7.432 letters <= 8 letters
//  log ( 10 , 20922789888000 ) ≈ 13.32 цифр <= 14 цифр

/*
OrigData   : 01 11 11
RandomSalt : 10 11 10

Data 01---\/---10⊻11=01---\/---11⊻11=00
Salt 10___/\___01⊻11=10___/\___11⊻10=01  
Pair 0110      0110            0001
Secr xxxx      xxxx            yyyy

The salt of one element will modify (xor) the next element to remove repeats.
The data of the first element will modify (xor) the second element salt.
If all elements are of the same value, then all encrypted 
 values will have the property of pseudo-randomness.
Both data and salt have secrecy apart from the first zero salt.
Function Shifr(of pair: data+salt)should be randomly disordered.

*/
// Version 3

// RUS
// 3 бита соль
// 3 бита инфа
// итого 6 бит
// таблица шифра: личные 3 бита + соль 3 бита => 6 бита шифрованные
// личные данные b000 => могут быть зашифрованы упорядоченным набором 2^3 = 8шт из 
// b000000 ... b111111 2^6 = 8*8 = 64 штук
// разные расклады шифрования для данных
// b000 = 64*63*62*61*60*59*58*57 = 178462987637760
// b001 = 56*55*54*53*52*51*50*49 = 57274321104000
// b010 = 48*47*46*45*44*43*42*41 = 15214711438080
// b011 = 40*39*38*37*36*35*34*33 = 3100796899200
// b100 = 32*31*30*29*28*27*26*25 = 424097856000
// b101 = 24*23*22*21*20*19*18*17 = 29654190720
// b110 = 16*15*14*13*12*11*10*9  = 518918400
// b111 = 8*7*6*5*4*3*2*1         = 40320
// в общем = b000 * b001 * b010 * b011 * b100 * b101 * b110 * b111 = 64! =
// 1268869321858841641034333893351614808028655161745451921988018943752147042304e14
// ≈ 1.26886932186e89
// минимум можно записать пароль с помощью
// log(2,1.26886932186e89) ≈ 296 бит <= 37 байт
// пароль будет 296 бит
// ascii буквы 126-32+1 = 95 шт
// длина буквенного пароля : log ( 95 , 1.26886932186e89 ) ≈ 45.05 букв <= 46 букв
//  log ( 62 , 1.26886932186e89 ) ≈ 49.71 буквы <= 50 букв
//  log ( 10 , 1.26886932186e89 ) ≈ 89.1 цифр <= 90 цифр

// returns [ 0..15 , 0..14 , ... , 0..2 , 0..1 ]
function shifr_generate_pass2 ( ) : array {
  $dice = array ( ) ;
  $i  = 0xf ;
  do  {
    $r = rand  ( 0 , $i )  ;
    $dice [ ] = $r ;
    --  $i  ;
  } while ( $i  > 0 ) ;
  return  $dice ; }

// returns [ 0..63 , 0..62 , ... , 0..2 , 0..1 ]
function shifr_generate_pass3 ( ) : array {
  $dice = array ( ) ;
  $i  = 0x3f ;
  do  {
    $r = rand  ( 0 , $i )  ;
    $dice [ ] = $r ;
    --  $i  ;
  } while ( $i  > 0 ) ;
  return  $dice ; }
  
// get 4*2 bits => push 4*4 bits
function  shifr_data_sole2 ( array & $secret_data ) : array {
  $secret_data_sole = array ( ) ;
  /*if  ( count ( $secret_data  ) > 4 ) // debug
    return  $secret_data_sole ;*/
  $ra = rand ( 0 , 0xff ) ; // 4*2 = 8 bits
  foreach ( $secret_data as $da ) {
    $secret_data_sole [ ] = ( $da << 2 ) | ( $ra & 0b11 ) ;
    $ra >>= 2 ; }
  return  $secret_data_sole ; }

// get 2*3 bits => push 2*6 bits
// get 3*3 bits => push 3*6 bits
function  shifr_data_sole3 ( array & $secret_data ) : array {
  $secret_data_sole = array ( ) ;
  /*if  ( count ( $secret_data  ) > 3 ) // debug
    return  $secret_data_sole ;*/
  $ra = rand ( 0 , 0x1ff ) ; // 3*3 = 9 bits
  foreach ( $secret_data as $da ) {
    $secret_data_sole [ ] = ( $da << 3 ) | ( $ra & 0b111 ) ;
    $ra >>= 3 ; }
  return  $secret_data_sole ; }
  
// byte = 76543210b to array
// [ 0 ] = 10 ; [ 1 ] = 32 ; [ 2 ] = 54 ; [ 3 ] = 76
function  shifr_byte_to_array2 ( int $byte ) : array {
  $arr = array ( ) ;
  $i = 0 ;
  do  {
    $arr [ ] = $byte & 0b11 ;
    $byte >>= 2 ;
    ++  $i  ;
  } while ( $i < 4  ) ;
  return  $arr ; }

function  shifr_data_xor2 ( int & $old_last_data , int & $old_last_sole ,
  array & $secret_data_sole ) {
  foreach ( $secret_data_sole as & $ids ) {
    $cur_data = $ids  >>  2 ;
    $cur_sole = $ids  & 0b11 ;
    $ids  ^=  ( $old_last_sole  <<  2 ) ;
    $ids  ^=  $old_last_data  ;
    $old_last_data = $cur_data ;
    $old_last_sole  = $cur_sole ; } }

function  shifr_data_xor3 ( int & $old_last_data , int & $old_last_sole ,
  array & $secret_data_sole ) {
  foreach ( $secret_data_sole as & $ids ) {
    $cur_data = $ids  >>  3 ;
    $cur_sole = $ids  & 0b111 ;
    $ids  ^=  ( $old_last_sole  <<  3 ) ;
    $ids  ^=  $old_last_data  ;
    $old_last_data = $cur_data ;
    $old_last_sole  = $cur_sole ; } }
    
function  shifr_crypt_decrypt ( array & $datap , array & $tablep ) : array {
  $encrp = array ( ) ;
  foreach ( $datap as $id )
    $encrp [ ] = $tablep [ $id ] ;
  return  $encrp  ; }

function  shifr_decrypt_sole2 ( array & $datap , array & $tablep ,
  array & $decrp ,  & $old_last_sole ,  & $old_last_data ) {
  foreach ( $datap as $id ) {
    $data_sole = $tablep [ $id ] ;
    $newdata = ( $data_sole >> 2 ) ^ $old_last_sole ;
    $decrp [ ] = $newdata ;
    $old_last_sole = (  $data_sole & 0b11 ) ^ $old_last_data ;
    $old_last_data  = $newdata ; } }

function  shifr_decrypt_sole3 ( array & $datap , array & $tablep ,
  array & $decrp ,  & $old_last_sole ,  & $old_last_data ) {
  foreach ( $datap as $id ) {
    $data_sole = $tablep [ $id ] ;
    $newdata = ( $data_sole >> 3 ) ^ $old_last_sole ;
    $decrp [ ] = $newdata ;
    $old_last_sole = (  $data_sole & 0b111 ) ^ $old_last_data ;
    $old_last_data  = $newdata ; } }

class shifr {
  // alphabet ascii // алфавит ascii
  public  $letters95  ; // 0x20 пробел - 0x7e ~ тильда // 0x20 space - 0x7e ~ tilde
  public  $letters  ; // alphabet 62 letters digits // алфавит 62 буквы цифры
  public  $letters10  ; // alphabet 10 digits // алфавит 10 цифры
  // password alphabet mode 10 or 62 or 95 
  // режим алфавит пароля 10 или 62 или 95
  public  $letters_mode ;
  public  $localerus  ; // false or true // русская локаль false или true
  public  $flagtext ; // true or false // флаг текст true или false
  public  $password ; // пароль
  public  $message  ; // сообщение/данные // message/data
  public  $messageout  ; // сообщение/данные // message/data
  public  $old_last_data  ; // предыдущие данные
  public  $old_last_sole  ; // предыдущая соль
  // текстовый режим : букв в строке написано
  public  $bytecount  ; // text mode: letters are written in a line
  // decode : text place 0 .. 2 
  public  $buf3_index ; // расшифровка : позиция в тексте
  public  $buf3 ; // decode text buffer // расшифровка текстовый буфер
  public  $shifra ; // array for encryption // массив для шифрования
  public  $deshia ; // array for decryption // массив для расшифровки
  public  $key_mode ; // 45 или 296 // 45 or 296
  public  $filebuffrom ;
  public  $filebufto ;
  public  $in_buf  ; // buf to read // буфер чтения
  public  $out_buf  ; // buf to write // буфер записи
  public  $in_bufbitsize ; // размер буфера в битах
  public  $out_bufbitsize ; // размер буфера в битах
  public  $decode_read_index  ; // индекс чтения для расшифровки
  // Размер битового буфера чтения
  public  $bitscount  ; // reading buffer bit size
  // encode3
  // 0-2 бит буфер чтения
  public  $bufin  ; // 0-2 bits buffer reading
}
    
function  shifr_encrypt2 ( shifr & $sh ) {
  $message_array = str_split  ( $sh -> message  ) ;
  $sh -> message =  '' ;
  foreach ( $message_array as $char ) {      
    $secret_data = shifr_byte_to_array2 ( ord ( $char ) ) ;
    $secret_data_sole = shifr_data_sole2 ( $secret_data ) ;
    shifr_data_xor2 ( $sh -> old_last_data , $sh -> old_last_sole ,
      $secret_data_sole ) ;
    $encrypteddata = shifr_crypt_decrypt ( $secret_data_sole , $sh -> shifra )  ;
    if ( $sh -> flagtext ) {
      $buf16 = ( $encrypteddata [ 0 ] & 0b1111 ) |
        ( ( $encrypteddata [ 1 ] & 0b1111 ) << 4 ) |
        ( ( $encrypteddata [ 2 ] & 0b1111 ) << 8 ) |
        ( ( $encrypteddata [ 3 ] & 0b1111 ) << 12 ) ;
      $sh -> message .=  chr ( ord ( 'R' ) + ( $buf16 % 40 ) ) ;
      $buf16 = intdiv ( $buf16  , 40 )  ;
      $sh -> message .= chr ( ord ( 'R' ) + ( $buf16 % 40 ) ) ;
      $buf16 = intdiv ( $buf16  , 40 )  ;
      $sh -> message .=  chr ( ord ( 'R' ) + $buf16 ) ;
      $sh ->  bytecount += 3 ;
      if ( $sh -> bytecount >= 60 ) {
        $sh -> message .= "\n" ;
        $sh -> bytecount = 0 ; } }
    else {
      $sh -> message .=  chr ( ( $encrypteddata [ 0 ] & 0b1111 ) |
        ( ( $encrypteddata [ 1 ] & 0b1111 ) << 4 ) ) ;
      $sh -> message .=  chr ( ( $encrypteddata [ 2 ] & 0b1111 ) |
        ( ( $encrypteddata [ 3 ] & 0b1111 ) << 4 ) ) ; } } }
        
function shifr_byte_to_array3 ( shifr & $sh , int $charcode )
  : array {
  switch  ( $sh -> bitscount  ) {
  case  0 :
    // <= [ [1 0] [2 1 0] [2 1 0] ]
    $secret_data = array ( $charcode & 0x7 , ( $charcode >>  3 ) & 0x7 ) ;
    $sh -> bufin = $charcode >>  6 ;
    $sh -> bitscount  = 2 ;  // 0 + 8 - 6
    break ;
  case  1 :
    // <= [ [2 1 0] [2 1 0] [2 1] ] <= [ [0]
    $secret_data = array ( $sh -> bufin | ( ( $charcode & 0x3 ) << 1 ) ,
      ( $charcode >> 2 ) & 0x7 , $charcode >>  5 ) ;
    $sh -> bitscount  = 0 ;  // 1 + 8 - 9
    break ;
  case  2 :
    // <= [ [0] [2 1 0] [2 1 0] [2] ] <= [ [1 0] ..
    $secret_data = array ( $sh -> bufin | ( ( $charcode & 0x1 ) << 2 ) ,
      ( $charcode >> 1 ) & 0x7 , ( $charcode >>  4 ) & 0x7 ) ;
    $sh -> bufin = $charcode >>  7 ;
    $sh -> bitscount  = 1 ;  // 2 + 8 - 9
    break ;
  default :
    if  ( $shifr -> localerus )
      echo 'неожиданное значение bitscount = '  . $sh -> bitscount  ;
    else
      echo 'unexpected value bitscount = '  . $sh -> bitscount ;
    return  array ( ) ; }
  return  $secret_data  ; }
    
function  shifr_write_array ( shifr & $sh , array $secret_data  ) {
  $secret_data_sole = shifr_data_sole3 ( $secret_data ) ;
  shifr_data_xor3 ( $sh -> old_last_data , $sh -> old_last_sole ,
    $secret_data_sole ) ;
  $encrypteddata = shifr_crypt_decrypt ( $secret_data_sole , $sh -> shifra )  ;
  if ( $sh -> flagtext ) {
    foreach ( $encrypteddata as $ed ) {
      $sh -> message  .=  chr ( ord ( ';' ) + $ed ) ;
      ++ $sh -> bytecount ;
      if ( $sh -> bytecount >= 60 ) {
        $sh -> message .= "\n" ;
        $sh -> bytecount = 0 ; } } }
  else
    foreach ( $encrypteddata as $ed ) {
      if  ( $sh ->  out_bufbitsize  < 2 ) {
        $sh ->  out_buf |=  ( $ed << ( $sh ->  out_bufbitsize ) ) ;
        $sh ->  out_bufbitsize  +=  6 ; }
      else  {
        $sh -> message  .=  chr (
          ( ( $ed << ( $sh ->  out_bufbitsize ) ) & 0xff ) | $sh ->  out_buf ) ;
        // + 6 - 8
        $sh -> out_bufbitsize -= 2 ;
        $sh -> out_buf  = $ed >>
          ( 6 - ( $sh -> out_bufbitsize ) ) ; } } }
        
function  shifr_encrypt3 ( shifr & $sh ) {
  $message_array = str_split  ( $sh -> message  ) ;
  $sh -> message =  ''  ;
  foreach ( $message_array as $char ) 
    shifr_write_array ( $sh , shifr_byte_to_array3 ( $sh , ord ( $char ) ) ) ; }

function  shifr_flush ( shifr & $sh ) {
  if ( $sh  -> bitscount ) {
    shifr_write_array ( $sh , array ( $sh -> bufin ) ) ;
    $sh -> bitscount = 0 ; }
  if  ( $sh ->  out_bufbitsize ) {
    $sh -> message .= $sh ->  out_buf ;
    $sh ->  out_bufbitsize = 0 ; }
  if ( $sh  ->  flagtext and $sh -> bytecount )  {
    $sh -> bytecount = 0 ;
    $sh -> message .= "\n"  ; } }

function  shifr_flush_file  ( shifr & $sh , & $fpw ) {
  if ( $sh  -> bitscount ) {
    $sh -> message = ''  ;
    shifr_write_array ( $sh , array ( $sh -> bufin ) ) ;
    $sh -> bitscount = 0 ;
    fwrite  ( $fpw , $sh  -> message ) ; }
  if  ( $sh ->  out_bufbitsize ) {
    fwrite  ( $fpw , $sh ->  out_buf ) ;
    $sh ->  out_bufbitsize = 0 ; }
  if ( $sh  ->  flagtext and $sh -> bytecount )  {
    $sh -> bytecount = 0 ;
    fwrite  ( $fpw , "\n" ) ; } }
    
// читаю 6 бит
// 6 bits reads
function  isEOFstreambuf_read6bits ( shifr & $sh , array & $encrypteddata ) : bool {
  if  ( ( ! $sh -> flagtext ) and ( $sh -> in_bufbitsize >= 6 ) ) {
    $sh -> in_bufbitsize -=  6 ;
    $encrypteddata [ ] = $sh -> in_buf & ( 0x40 - 1 ) ;
    $sh -> in_buf  >>= 6 ;
    return  false ; }
  if ( $sh -> decode_read_index >= strlen ( $sh -> message ) )
    return true ;
  $reads = ord ( $sh -> message [ $sh -> decode_read_index ] ) ;
  ++  $sh -> decode_read_index  ;
  if  ( $sh -> flagtext ) {
    // читаем одну букву ';'-'z' -> декодируем в шесть бит
    // reads one letter ';'-'z' -> decode to six bits
    while ( ( $reads < ord ( ';' ) ) or ( $reads > ord ( 'z' ) ) ) {
      if ( $sh -> decode_read_index >= strlen ( $sh -> message ) )
        return true ;
      $reads = ord ( $sh -> message [ $sh -> decode_read_index ] ) ;
      ++  $sh -> decode_read_index  ; }
    $encrypteddata [ ] = $reads - ord ( ';' ) ; }
  else  {
    $encrypteddata [ ] = ( $sh -> in_buf | 
      ( $reads <<  $sh -> in_bufbitsize ) ) & ( 0x40 - 1 )  ;
    $sh -> in_buf = $reads >>  ( 6 - $sh -> in_bufbitsize ) ;
    $sh -> in_bufbitsize +=  2 ; } // + 8 - 6
  return  false ; }
    
// версия 6 пишу три бита для расшифровки
// version 6 write three bits to decode
function  streambuf_write3bits ( shifr & $sh , $decrypteddata ) {
  if  ( $sh -> out_bufbitsize < 5 ) {
    $sh -> out_buf |= ( $decrypteddata << ( $sh -> out_bufbitsize ) ) ;
    $sh -> out_bufbitsize +=  3 ; }
  else  {
    $to_write  = ( ( $decrypteddata << $sh -> out_bufbitsize ) |
      ( $sh -> out_buf ) ) & 0xff ;
    $sh -> messageout .= chr ( $to_write ) ;
    // + 3 - 8
    $sh -> out_bufbitsize -= 5 ;
    $sh -> out_buf = $decrypteddata >> ( 3 - ( $sh -> out_bufbitsize ) ) ; } }
    
function  shifr_decrypt3 ( shifr & $sh ) {
  $secretdata = array ( ) ;
  $sh -> messageout = ''  ;
  $sh -> decode_read_index  = 0 ;
  while ( ! isEOFstreambuf_read6bits ( $sh , $secretdata ) ) {
    $decrypteddata = array ( ) ;
    shifr_decrypt_sole3 ( $secretdata , $sh -> deshia , $decrypteddata ,
      $sh -> old_last_sole ,  $sh -> old_last_data ) ;
    $secretdata = array ( ) ;
    streambuf_write3bits ( $sh , $decrypteddata [ 0 ] ) ; }
  $sh -> message = $sh -> messageout ; }
    
function  shifr_decrypt2 ( shifr & $sh ) {
  $message_array = str_split  ( $sh -> message  ) ;
  $sh -> message = '' ;
  if ( $sh -> flagtext ) {
    for ( $i = 0 ; $i < count ( $message_array  )  ; ++ $i ) {
      do  {
        while ( ord ( $message_array [ $i ] ) < ord ( 'R' ) or
          ord ( $message_array [ $i ] ) > ord ( 'z' ) )  {
          ++  $i  ;
          if ( $i >= count ( $message_array  ) )
            break ;  }
        if ( $i >= count  (  $message_array  ) )
          break ; 
        $sh ->  buf3 [ ] = ord ( $message_array [ $i ] ) - ord ( 'R' ) ;
        ++  $sh -> buf3_index ;
        if ( $sh -> buf3_index < 3 )
          ++  $i  ;
      } while ( $sh -> buf3_index < 3 ) ;
      if ( $i >= count  (  $message_array  ) )
        break ; 
      $sh -> buf3_index = 0 ;
      $u16 = $sh  ->  buf3 [ 0 ] + 40 * ( $sh ->  buf3 [ 1 ] +
        40 * $sh  ->  buf3 [ 2 ] ) ;
      $sh ->  buf3 = array ( ) ;
        $buf = array ( $u16 & 0xff , $u16 >> 8 ) ;
        $secretdata = array (
          $buf [ 0 ] & 0xf ,
          ( $buf [ 0 ] >> 4 ) & 0xf ,
          $buf [ 1 ] & 0xf ,
          ( $buf [ 1 ] >> 4 ) & 0xf ) ;
        $decrypteddata = array ( ) ;
        shifr_decrypt_sole2 ( $secretdata , $sh -> deshia , $decrypteddata ,
          $sh -> old_last_sole , $sh -> old_last_data ) ;
        $sh -> message .= chr ( ( $decrypteddata [ 0 ] & 0x3  ) |
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
      shifr_decrypt_sole2 ( $secretdata , $sh -> deshia , $decrypteddata ,
        $sh -> old_last_sole , $sh -> old_last_data ) ;
      $sh -> message .= chr ( ( $decrypteddata [ 0 ] & 0x3  ) |
          ( ( $decrypteddata [ 1 ] & 0x3  ) << 2  ) |
          ( ( $decrypteddata [ 2 ] & 0x3  ) <<  4 ) |
          ( ( $decrypteddata [ 3 ] & 0x3  ) << 6  ) ) ; } } }    
    
// number /= div , number := floor [ деление ] , return := остаток (remainder)
function  number_div8_mod ( array & $number , int $div ) : int {
  $modi = 0 ;
  $i = count  ( $number ) ;
  while ( $i > 0 )  {
    -- $i ;
    $x = ( $modi << 8 ) | ( $number [ $i  ] ) ;
    $modi = $x % $div  ;
    $number [ $i  ] = intdiv  ( $x , $div  ) ;  }
  $i = count  ( $number ) ;
  while ( $i > 0 )  {
    -- $i ;
    if ( $number [ $i  ] != 0 )
      break ;
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
  $i = count  ( $number ) ;
  while ( $i > 0 )  {
    -- $i ;
    if ( $number [ $i  ] != 0 ) 
      break ;
    unset ( $number [ $i  ] ) ; } }
    
function  number_not_zero ( array & $number ) {
  return  count  ( $number ) >  0 ; }
  
function  shifr_generate_password_2 ( shifr & $sh ) {
  $sh ->  password  = shifr_password_to_string (  $sh ,
    shifr_pass_to_array2  ( shifr_generate_pass2  ( ) ) ) ; }

function  shifr_generate_password_3 ( shifr & $sh ) {
  $sh ->  password  = shifr_password_to_string (  $sh ,
    shifr_pass_to_array3  ( shifr_generate_pass3  ( ) ) ) ; }

function  shifr_password_to_string ( shifr & $sh , array $passworda ) : string {
  switch  ( $sh  ->  letters_mode ) {
  case  95  :
    $letters  = $sh ->  letters95  ;
    break ;
  case  62  :
    $letters  = $sh ->  letters  ;
    break ;
  case  10  :
    $letters  = $sh ->  letters10  ;
    break ;
  default :
    return  ''  ; }
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
      // echo 'number_set_byte:$byte < 0' ;
      return  ; }
    if ( $byte >= 0x100 ) {
      // echo 'number_set_byte:$byte >= 0x100' ;
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
    if ( $per == 0 ) 
      return  ;
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
  if ( $per > 0 )
    $num [ $i ] = 1 ; }

function  number_mul_byte ( array & $number , int $byte ) {
  if ( $byte == 0 ) {
    $number = array ( ) ;
    return  ; }
  if ( $byte == 1 )
    return ;
  if ( $byte < 0 ) {
    //echo 'number_mul_byte: $byte < 0' ;
    return  ; }
  if ( $byte >= 0x100 ) {
    //echo 'number_mul_byte: $byte >= 0x100' ;
    return  ; }
  $per = 0 ;
  for ( $i = 0 ; $i < count  ( $number ) ; ++ $i )  {
    $x = $number [ $i ] *  $byte  + $per ;
    $number [ $i ] = $x & 0xff ;
    $per = $x >> 8 ; }
  if ( $per > 0 )
    $number [ $i ] = $per ; }
  
// [ 0..15 , 0..14 , 0..13 , ... , 0..2 , 0..1 ] = [ x , y , z , ... , u , v ] =
// = x + y * 16 + z * 16 * 15 + ... + u * 16! / 2 / 3 + v * 16! / 2 = 0 .. 16!-1
function  shifr_pass_to_array2 ( array & $password ) : array {
  $re = array ( ) ;
  $mu = array ( 1 ) ;
  $in = 0 ;
  do {
    { // $re += $password [ $in ] * $mu ;
      $mux = $mu ;
      number_mul_byte ( $mux  , $password [ $in ] ) ;
      number_add  ( $re , $mux  ) ; }
    //$mu *=  16 - $in ;
    number_mul_byte ( $mu , 0x10 - $in  ) ;
    ++  $in ;
  } while ( $in < 0x0f ) ;
  return  $re ; }

// [ 0..63 , 0..62 , 0..61 , ... , 0..2 , 0..1 ] = [ x , y , z , ... , u , v ] =
// = x + y * 64 + z * 64 * 63 + ... + u * 64! / 2 / 3 + v * 64! / 2 = 0 .. 64!-1
function  shifr_pass_to_array3 ( array & $password ) : array {
  $re = array ( ) ;
  $mu = array ( 1 ) ;
  $in = 0 ;
  do {
    { // $re += $password [ $in ] * $mu ;
      $mux = $mu ;
      number_mul_byte ( $mux  , $password [ $in ] ) ;
      number_add  ( $re , $mux  ) ; }
    //$mu *=  64 - $in ;
    number_mul_byte ( $mu , 0x40 - $in  ) ;
    ++  $in ;
  } while ( $in < 0x3f ) ;
  return  $re ; }
  
function  shifr_password_load2  ( shifr & $sh , array $password ) {
  $sh -> shifra = array_fill  ( 0 , 16 , 0xff  ) ;
  $sh -> deshia = array_fill  ( 0 , 16 , 0xff  ) ;
  $arrind = array ( ) ;
  for ( $i = 0 ; $i < 16 ; ++ $i ) 
    $arrind [ ] = $i ;
  $inde = 0 ;
  do {
    $cindex = number_div8_mod ( $password , 16 - $inde ) ;
    $sh -> shifra [ $inde ] = $arrind [ $cindex ] ;
    $sh -> deshia [ $arrind [ $cindex ] ] = $inde ;
    unset ( $arrind [ $cindex ] ) ;
    $arrind = array_values ( $arrind ) ;
    ++ $inde  ;
  } while ( $inde < 16 ) ; }

function  shifr_password_load3  ( shifr & $sh , array $password ) {
  $sh -> shifra = array_fill  ( 0 , 64 , 0xff  ) ;
  $sh -> deshia = array_fill  ( 0 , 64 , 0xff  ) ;
  $arrind = array ( ) ;
  for ( $i = 0 ; $i < 64 ; ++ $i ) 
    $arrind [ ] = $i ;
  $inde = 0 ;
  do {
    $cindex = number_div8_mod ( $password , 64 - $inde ) ;
    $sh -> shifra [ $inde ] = $arrind [ $cindex ] ;
    $sh -> deshia [ $arrind [ $cindex ] ] = $inde ;
    unset ( $arrind [ $cindex ] ) ;
    $arrind = array_values ( $arrind ) ;
    ++ $inde  ;
  } while ( $inde < 64 ) ; }
  
function  shifr_password_load_2 ( shifr & $sh ) {
  return  shifr_password_load2  ( $sh , shifr_string_to_key_array ( $sh ,
    $sh ->  password  ) ) ; }

function  shifr_password_load_3 ( shifr & $sh ) {
  return  shifr_password_load3  ( $sh , shifr_string_to_key_array ( $sh ,
    $sh ->  password  ) ) ; }
    
function  shifr_string_to_key_array  ( shifr & $sh , string & $str ) {
  $strn = strlen  ( $str  ) ;
  $passarr = array ( ) ;
  number_set_zero ( $passarr ) ;
  if ( $strn == 0 ) 
    return $passarr ;
  switch  ( $sh ->  letters_mode ) {
  case  95  :
    $letters  = $sh ->  letters95  ;
    break ;
  case  62  :
    $letters  = $sh ->  letters  ;
    break ;
  case  10  :
    $letters  = $sh ->  letters10  ;
    break ;
  default :
    return  ; }
  $letters_count  = count ( $letters  ) ;
  $mult = array ( ) ;
  number_set_byte ( $mult , 1 ) ;
  $stringi  = 0 ;
  do  {
    $i = $letters_count ;
    do {
      -- $i ;
      if ( $str [ $stringi ] == $letters [ $i ] )
        goto found ; 
    } while ( $i ) ;
    if  ( $sh -> localerus ) 
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
  } while ( $stringi  < strlen  ( $str  ) ) ;
  return  $passarr ; }  
  
function  shifr_set_version ( shifr & $sh , $ver  ) {
  if  ( $ver  ==  2 )
    $sh ->  key_mode = 45 ;
  else 
    $sh ->  key_mode = 296 ;  }

function  shifr_version ( shifr & $sh ) {
  if  ( $sh ->  key_mode == 45 )
    return 2 ;
  return  3 ; }

function  shifr_init ( shifr & $sh ) {
  //  ascii ' ' => '~'
  $sh ->  letters95 = array ( ) ;
  for ( $i = ord  ( ' ' ) ; $i <= ord ( '~' ) ; ++ $i )
    $sh ->  letters95 [ ] = chr ( $i ) ;
  // '0' - '9' , 'A' - 'Z' , 'a' - 'z'  
  $sh ->  letters = array ( ) ;
  for ( $i = ord  ( '0' ) ; $i <= ord ( '9' ) ; ++ $i )
    $sh ->  letters [ ] = chr ( $i ) ;
  for ( $i = ord  ( 'A' ) ; $i <= ord ( 'Z' ) ; ++ $i )
    $sh ->  letters [ ] = chr ( $i ) ;
  for ( $i = ord  ( 'a' ) ; $i <= ord ( 'z' ) ; ++ $i )
    $sh ->  letters [ ] = chr ( $i ) ; 
  // '0' - '9'
  $sh ->  letters10 = array ( ) ;
  for ( $i = ord  ( '0' ) ; $i <= ord ( '9' ) ; ++ $i )
    $sh ->  letters10 [ ] = chr ( $i ) ;
  // default is digits and letters
  $sh ->  letters_mode = 62 ;
  shifr_set_version ( $sh , 3 ) ;
  $sh ->  old_last_data  = 0 ;
  $sh ->  old_last_sole  = 0 ;
  $sh ->  bytecount  = 0 ;
  $sh ->  buf3_index = 0 ;
  $sh ->  buf3 = array ( ) ;
  $sh ->  out_buf = 0 ;
  $sh ->  in_buf = 0 ;
  $sh ->  out_bufbitsize = 0 ;
  $sh ->  in_bufbitsize = 0 ;
  $sh ->  decode_read_index = 0 ;
  $sh ->  messageout = '' ;
  $sh ->  bitscount  = 0 ;
  $sh ->  bufin = 0 ;
  $sh ->  localerus = false ;
  $sh ->  flagtext  = true  ; }

function  shifr_password_load ( shifr & $shifr ) {
  if ( shifr_version  ( $shifr  ) == 2 ) 
    shifr_password_load_2 ( $shifr ) ;
  else 
    shifr_password_load_3 ( $shifr ) ; }

function  shifr_encrypt  ( shifr & $shifr ) {
  if ( shifr_version  ( $shifr  ) == 2 ) 
    shifr_encrypt2 ( $shifr ) ; 
  else 
    shifr_encrypt3 ( $shifr  ) ; }

function  shifr_decrypt  ( shifr & $shifr ) {
  if ( shifr_version  ( $shifr  ) == 2 ) 
    shifr_decrypt2 ( $shifr ) ; 
  else 
    shifr_decrypt3 ( $shifr ) ; }

function  shifr_generate_password  ( shifr & $shifr ) {
  if ( shifr_version  ( $shifr  ) == 2 ) 
    shifr_generate_password_2 ( $shifr  ) ;
  else 
    shifr_generate_password_3 ( $shifr  ) ; }

?>

