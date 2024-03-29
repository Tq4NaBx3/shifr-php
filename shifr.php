<?php 
/*
 Шифр ©2020-3 Глебов А.Н.
 Shifr ©2020-3 Glebe A.N.
 
 $shifr  = new shifr ( ) ;
 shifr_init ( shifr & $shifr );
 $shifr -> localerus = true or false ;
 $shifr -> flagtext = false  or true ;
 $shifr -> message = string ; to encode , decode
 $shifr -> letters_mode = 52 or 95 or 62 or 10 or 26 ;
 shifr_set_version ( shifr & $sh , $ver  ) ; $ver == 2 or 3
 shifr_version ( shifr & $sh ) ; returns 2 or 3
 shifr_encrypt  ( shifr & $shifr ) ;
 shifr_flush ( $shifr  ) ; after last encode to clear buffer
 shifr_flush_file  ( $shifr , $fpw ) ;
 shifr_decrypt ( $shifr ) ;
 shifr_generate_password ( $shifr  ) ; returns nothing
 shifr_password_set ( $shifr , string ) ;
 shifr_password_get ( $shifr ) ; returns string
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
 длина буквенного пароля : 
  log ( 95 , 16 ! ) ≈ 6.735 <= 7 букв
  log ( 62 , 16 ! ) ≈ 7.432 <= 8 букв
  log ( 52 , 16 ! ) ≈ 7.763 <= 8 букв
  log ( 26 , 16 ! ) ≈ 9.414 <= 10 букв
  log ( 10 , 16 ! ) ≈ 13.32 <= 14 цифр

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
/*
 ENG
 2 bits salt
 2 bits information
 total 4 bits
 encryption table: personal 2 bits + salt 2 bits => 4 bits encrypted
 personal data b00 => can be encrypted in an ordered set 2^2 = 4pcs from
 b0000 ... b1111 2^4 = 4*4 = 16 pieces
 different encryption layouts for data
 b00 = 16*15*14*13 = 43680
 b01 = 12*11*10*9 = 11880
 b10 = 8*7*6*5 = 1680
 b11 = 4*3*2*1 = 24
 generally = b00 * b01 * b10 * b11 =
   = 16! = 20922789888000
 minimum you can write a password using
 log(2,20922789888000) ≈ 44.25 bits <= 6 bytes
 the password will have 45 bits size
 ascii letters 126-32+1 = 95 pcs
 letter password length : 
  log ( 95 , 16 ! ) ≈ 6.735 <= 7 letters
  log ( 62 , 16 ! ) ≈ 7.432 <= 8 letters
  log ( 52 , 16 ! ) ≈ 7.763 <= 8 letters
  log ( 26 , 16 ! ) ≈ 9.414 <= 10 letters
  log ( 10 , 16 ! ) ≈ 13.32 <= 14 digits
*/
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
/*
 Version 3

 RUS
 3 бита соль
 3 бита инфа
 итого 6 бит
 таблица шифра: личные 3 бита + соль 3 бита => 6 бита шифрованные
 личные данные b000 => могут быть зашифрованы упорядоченным набором 2^3 = 8шт из 
 b000000 ... b111111 2^6 = 8*8 = 64 штук
 разные расклады шифрования для данных
 b000 = 64*63*62*61*60*59*58*57 = 178462987637760
 b001 = 56*55*54*53*52*51*50*49 = 57274321104000
 b010 = 48*47*46*45*44*43*42*41 = 15214711438080
 b011 = 40*39*38*37*36*35*34*33 = 3100796899200
 b100 = 32*31*30*29*28*27*26*25 = 424097856000
 b101 = 24*23*22*21*20*19*18*17 = 29654190720
 b110 = 16*15*14*13*12*11*10*9  = 518918400
 b111 = 8*7*6*5*4*3*2*1         = 40320
 в общем = b000 * b001 * b010 * b011 * b100 * b101 * b110 * b111 = 64! =
 1268869321858841641034333893351614808028655161745451921988018943752147042304e14
 ≈ 1.26886932186e89
 минимум можно записать пароль с помощью
 log(2,1.26886932186e89) ≈ 296 бит <= 37 байт
 пароль будет 296 бит
 ascii буквы 126-32+1 = 95 шт
 длина буквенного пароля : 
  log ( 95 , 64 ! ) ≈ 45.05 <= 46 букв
  log ( 62 , 64 ! ) ≈ 49.71 <= 50 букв
  log ( 52 , 64 ! ) ≈ 51.92 <= 52 буквы
  log ( 26 , 64 ! ) ≈ 62.97 <= 63 буквы
  log ( 10 , 64 ! ) ≈ 89.1  <= 90 цифр
*/
/*
 ENG
 3 bits salt
 3 bits information
 total 6 bits
 encryption table: personal 3 bits + salt 3 bits => 6 bits encrypted
 personal data b000 => can be encrypted in an ordered set 2^3 = 8pcs from
 b000000 ... b111111 2^6 = 8*8 = 64 pieces
 different encryption layouts for data
 b000 = 64*63*62*61*60*59*58*57 = 178462987637760
 b001 = 56*55*54*53*52*51*50*49 = 57274321104000
 b010 = 48*47*46*45*44*43*42*41 = 15214711438080
 b011 = 40*39*38*37*36*35*34*33 = 3100796899200
 b100 = 32*31*30*29*28*27*26*25 = 424097856000
 b101 = 24*23*22*21*20*19*18*17 = 29654190720
 b110 = 16*15*14*13*12*11*10*9  = 518918400
 b111 = 8*7*6*5*4*3*2*1         = 40320
 generally = b000 * b001 * b010 * b011 * b100 * b101 * b110 * b111 = 64! =
 1268869321858841641034333893351614808028655161745451921988018943752147042304e14
 ≈ 1.26886932186e89
 minimum you can write a password using
 log(2,1.26886932186e89) ≈ 296 bits <= 37 bytes
 the password will have 296 bits size
 ascii letters 126-32+1 = 95 pcs
 letter password length : 
  log ( 95 , 64 ! ) ≈ 45.05 <= 46 letters
  log ( 62 , 64 ! ) ≈ 49.71 <= 50 letters
  log ( 52 , 64 ! ) ≈ 51.92 <= 52 letters
  log ( 26 , 64 ! ) ≈ 62.97 <= 63 letters
  log ( 10 , 64 ! ) ≈ 89.1  <= 90 digits
*/
// generate random number [ fr .. to ]
function shifr_rand_fr_to  ( int $fr , int $to ) : int {
  $wid = $to - $fr + 1 ;
  do {
    $buf = ord ( random_bytes ( 1 ) ) ;
  } while ( $buf + 0x100 % $wid >= 0x100 ) ;
  return  $fr + $buf % $wid ;
}

// returns [ 0..15 , 0..14 , ... , 0..2 , 0..1 ]
function shifr_generate_pass2 ( ) : array {
  $dice = array ( ) ;
  $i  = 0xf ; // 15
  do  {
    $r  = shifr_rand_fr_to  ( 0 , $i  ) ;
    $dice [ ] = $r ;
    --  $i  ;
  } while ( $i  > 0 ) ;
  return  $dice ;
}

// returns [ 0..63 , 0..62 , ... , 0..2 , 0..1 ]
function shifr_generate_pass3 ( ) : array {
  $dice = array ( ) ;
  $i  = 0x3f ; // 63
  do  {
    $r = shifr_rand_fr_to  ( 0 , $i )  ;
    $dice [ ] = $r ;
    --  $i  ;
  } while ( $i  > 0 ) ;
  return  $dice ;
}
  
// get 4*2 bits => push 4*4 bits
function  shifr_data_salt2 ( array & $secret_data ) : array {
  $secret_data_salt = array ( ) ;
  $ra = ord ( random_bytes ( 1 ) ) ; // 4*2 = 8 bits
  foreach ( $secret_data as $da ) {
    $secret_data_salt [ ] = ( $da << 2 ) | ( $ra & 0b11 ) ;
    $ra >>= 2 ;
  }
  return  $secret_data_salt ;
}

// data_size = 1 .. 3
// get 2*3 bits => push 2*6 bits
// get 3*3 bits => push 3*6 bits
function  shifr_data_salt3 ( array & $secret_data ) : array {
  $secret_data_salt = array ( ) ;
  if ( count ( $secret_data ) == 3 ) {
    // needs random [ 0 .. 0x1ff ]
    $ras = random_bytes ( 2 ) ; // 3*3 = 9 bits
    $ra = ( ord ( substr ( $ras , 1 , 1 ) ) << 8 ) | ord ( $ras ) ;
  } else  {
    $ras = random_bytes ( 1 ) ;
    $ra = ord ( $ras ) ;
  }
  foreach ( $secret_data as $da ) {
    $secret_data_salt [ ] = ( $da << 3 ) | ( $ra & 0b111 ) ;
    $ra >>= 3 ;
  }
  return  $secret_data_salt ;
}
  
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
  return  $arr ;
}

function  shifr_data_xor2 ( int & $old_last_data , int & $old_last_salt ,
  array & $secret_data_salt ) {
  foreach ( $secret_data_salt as & $ids ) {
    $cur_data = $ids  >>  2 ;
    $cur_salt = $ids  & 0b11 ;
    $ids  ^=  ( $old_last_salt  <<  2 ) ;
    $ids  ^=  $old_last_data  ;
    $old_last_data = $cur_data ;
    $old_last_salt  = $cur_salt ;
  }
}

function  shifr_data_xor3 ( int & $old_last_data , int & $old_last_salt ,
  array & $secret_data_salt ) {
  foreach ( $secret_data_salt as & $ids ) {
    $cur_data = $ids  >>  3 ;
    $cur_salt = $ids  & 0b111 ;
    $ids  ^=  ( $old_last_salt  <<  3 ) ;
    $ids  ^=  $old_last_data  ;
    $old_last_data = $cur_data ;
    $old_last_salt  = $cur_salt ;
  }
}
    
function  shifr_crypt_decrypt ( array & $datap , array & $tablep ) : array {
  $encrp = array ( ) ;
  foreach ( $datap as $id )
    $encrp [ ] = $tablep [ $id ] ;
  return  $encrp  ;
}

function  shifr_decrypt_salt2 ( array & $datap , array & $tablep ,
  array & $decrp ,  & $old_last_salt ,  & $old_last_data ) {
  foreach ( $datap as $id ) {
    $data_salt = $tablep [ $id ] ;
    $newdata = ( $data_salt >> 2 ) ^ $old_last_salt ;
    $decrp [ ] = $newdata ;
    $old_last_salt = (  $data_salt & 0b11 ) ^ $old_last_data ;
    $old_last_data  = $newdata ;
  }
}

function  shifr_decrypt_salt3 ( array & $datap , array & $tablep ,
  array & $decrp ,  & $old_last_salt ,  & $old_last_data ) {
  foreach ( $datap as $id ) {
    $data_salt = $tablep [ $id ] ;
    $newdata = ( $data_salt >> 3 ) ^ $old_last_salt ;
    $decrp [ ] = $newdata ;
    $old_last_salt = (  $data_salt & 0b111 ) ^ $old_last_data ;
    $old_last_data  = $newdata ;
  }
}

class shifr {
  // alphabet ascii // алфавит ascii
  public  $letters95  ; // 0x20 пробел - 0x7e ~ тильда // 0x20 space - 0x7e ~ tilde
  public  $letters62  ; // alphabet 62 letters digits a..zA..Z0..9 // алфавит 62 буквы цифры a..zA..Z0..9
  public  $letters52  ; // alphabet 52 letters a..zA..Z // алфавит 52 буквы a..zA..Z
  public  $letters26  ; // small letters a..z // маленькие буквы a..z
  public  $letters10  ; // alphabet 10 digits 0..9 // алфавит 10 цифры 0..9
  // password alphabet mode 10 or 62 or 95 or 26 or 52
  // режим алфавит пароля 10 или 62 или 95 или 26 или 52
  const letters_mode_Digit  = 10  ;
  const letters_mode_Letter = 26  ;
  const letters_mode_LetterCase = 52  ;
  const letters_mode_LetterDigit  = 62  ;
  const letters_mode_ASCII  = 95  ;
  public  $letters_mode ;
  public  $localerus  ; // false or true // русская локаль false или true
  public  $flagtext ; // true or false // флаг текст true или false
  public  $password ; // пароль
  public  $message  ; // сообщение/данные // message/data
  public  $messageout  ; // сообщение/данные // message/data
  public  $old_last_data  ; // предыдущие данные
  public  $old_last_salt  ; // предыдущая соль
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
  // string to ping pong file from JavaScript
  public  $boxes_info ; // file data
  public  $filename ;
  public  $array_log  ;
  public  $flag_debug ;
}

function  shifr_str_split ( string & $st ) {
  if ( strlen ( $st ) > 0 )
    return  str_split  ( $st  ) ;
  return  array ( ) ;
}

$shifr_base64_num_to_let  = array (
  0x00  =>  'A' , 0x01  =>  'B' , 0x02  =>  'C' , 0x03  =>  'D' , 0x04  =>  'E' , 0x05  =>  'F' ,
  0x06  =>  'G' , 0x07  =>  'H' , 0x08  =>  'I' , 0x09  =>  'J' , 0x0a  =>  'K' , 0x0b  =>  'L' ,
  0x0c  =>  'M' , 0x0d  =>  'N' , 0x0e  =>  'O' , 0x0f  =>  'P' , 0x10  =>  'Q' , 0x11  =>  'R' ,
  0x12  =>  'S' , 0x13  =>  'T' , 0x14  =>  'U' , 0x15  =>  'V' , 0x16  =>  'W' , 0x17  =>  'X' ,
  0x18  =>  'Y' , 0x19  =>  'Z' , 0x1a  =>  'a' , 0x1b  =>  'b' , 0x1c  =>  'c' , 0x1d  =>  'd' ,
  0x1e  =>  'e' , 0x1f  =>  'f' , 0x20  =>  'g' , 0x21  =>  'h' , 0x22  =>  'i' , 0x23  =>  'j' ,
  0x24  =>  'k' , 0x25  =>  'l' , 0x26  =>  'm' , 0x27  =>  'n' , 0x28  =>  'o' , 0x29  =>  'p' ,
  0x2a  =>  'q' , 0x2b  =>  'r' , 0x2c  =>  's' , 0x2d  =>  't' , 0x2e  =>  'u' , 0x2f  =>  'v' ,
  0x30  =>  'w' , 0x31  =>  'x' , 0x32  =>  'y' , 0x33  =>  'z' , 0x34  =>  '0' , 0x35  =>  '1' ,
  0x36  =>  '2' , 0x37  =>  '3' , 0x38  =>  '4' , 0x39  =>  '5' , 0x3a  =>  '6' , 0x3b  =>  '7' ,
  0x3c  =>  '8' , 0x3d  =>  '9' , 0x3e  =>  '+' , 0x3f  =>  '/' ,
) ;

$shifr_base64_let_to_num  = array (
  'A' =>  0x00  , 'B' =>  0x01  , 'C' =>  0x02  , 'D' =>  0x03  , 'E' =>  0x04  , 'F' =>  0x05  ,
  'G' =>  0x06  , 'H' =>  0x07  , 'I' =>  0x08  , 'J' =>  0x09  , 'K' =>  0x0a  , 'L' =>  0x0b  ,
  'M' =>  0x0c  , 'N' =>  0x0d  , 'O' =>  0x0e  , 'P' =>  0x0f  , 'Q' =>  0x10  , 'R' =>  0x11  ,
  'S' =>  0x12  , 'T' =>  0x13  , 'U' =>  0x14  , 'V' =>  0x15  , 'W' =>  0x16  , 'X' =>  0x17  ,
  'Y' =>  0x18  , 'Z' =>  0x19  , 'a' =>  0x1a  , 'b' =>  0x1b  , 'c' =>  0x1c  , 'd' =>  0x1d  ,
  'e' =>  0x1e  , 'f' =>  0x1f  , 'g' =>  0x20  , 'h' =>  0x21  , 'i' =>  0x22  , 'j' =>  0x23  ,
  'k' =>  0x24  , 'l' =>  0x25  , 'm' =>  0x26  , 'n' =>  0x27  , 'o' =>  0x28  , 'p' =>  0x29  ,
  'q' =>  0x2a  , 'r' =>  0x2b  , 's' =>  0x2c  , 't' =>  0x2d  , 'u' =>  0x2e  , 'v' =>  0x2f  ,
  'w' =>  0x30  , 'x' =>  0x31  , 'y' =>  0x32  , 'z' =>  0x33  , '0' =>  0x34  , '1' =>  0x35  ,
  '2' =>  0x36  , '3' =>  0x37  , '4' =>  0x38  , '5' =>  0x39  , '6' =>  0x3a  , '7' =>  0x3b  ,
  '8' =>  0x3c  , '9' =>  0x3d  , '+' =>  0x3e  , '/' =>  0x3f  ,
) ;

function  shifr_encrypt2 ( shifr & $sh ) {
  global  $shifr_base64_num_to_let ;
  $message_array = shifr_str_split  ( $sh -> message  ) ;
  $sh -> message =  '' ;
  foreach ( $message_array as $char ) {
    $secret_data = shifr_byte_to_array2 ( ord ( $char ) ) ;
    $secret_data_salt = shifr_data_salt2 ( $secret_data ) ;
    shifr_data_xor2 ( $sh -> old_last_data , $sh -> old_last_salt , $secret_data_salt ) ;
    $encrypteddata = shifr_crypt_decrypt ( $secret_data_salt , $sh -> shifra )  ;
    if ( $sh -> flagtext ) {
      $buf16 = ( $encrypteddata [ 0 ] & 0b1111 ) |
        ( ( $encrypteddata [ 1 ] & 0b1111 ) << 4 ) |
        ( ( $encrypteddata [ 2 ] & 0b1111 ) << 8 ) |
        ( ( $encrypteddata [ 3 ] & 0b1111 ) << 12 ) ;
      $sh -> message .=  $shifr_base64_num_to_let [ $buf16 % 40 ] ;
      $buf16 = intdiv ( $buf16  , 40 )  ;
      $sh -> message .= $shifr_base64_num_to_let [ $buf16 % 40 ] ;
      $buf16 = intdiv ( $buf16  , 40 )  ;
      $sh -> message .=  $shifr_base64_num_to_let [ $buf16 ] ;
      $sh ->  bytecount += 3 ;
      if ( $sh -> bytecount >= 60 ) {
        $sh -> message .= "\n" ;
        $sh -> bytecount = 0 ;
      }
    } else {
      $sh -> message .=  chr ( ( $encrypteddata [ 0 ] & 0b1111 ) |
        ( ( $encrypteddata [ 1 ] & 0b1111 ) << 4 ) ) ;
      $sh -> message .=  chr ( ( $encrypteddata [ 2 ] & 0b1111 ) |
        ( ( $encrypteddata [ 3 ] & 0b1111 ) << 4 ) ) ;
    }
  }
}

function  shifr_decrypt2 ( shifr & $sh ) {
  $message_array = shifr_str_split  ( $sh -> message  ) ;
  $sh -> message = '' ;
  $message_count  = count ( $message_array  ) ;
  if ( $sh -> flagtext ) {
    global  $shifr_base64_let_to_num ;
    for ( $i = 0 ; $i < $message_count ; ++ $i ) {
      do  {
        do  {
          $tmp  = $message_array [ $i ] ;
          $otmp = ord ( $tmp ) ;
          if ( ( $otmp >= ord ( 'a' ) and $otmp <= ord ( 'o' ) ) or (
            $otmp >= ord ( 'A' ) and  $otmp <= ord ( 'Z' ) ) )
            break ;
          ++  $i  ;
        } while ( $i < $message_count ) ;      
        if ( $i >= $message_count )
          break ;
        $sh ->  buf3 [ ] = $shifr_base64_let_to_num [ $tmp ] ;
        ++  $sh -> buf3_index ;
        if ( $sh -> buf3_index < 3 ) {
          ++  $i  ;
          if ( $i >= $message_count )
            break ;
        }
      } while ( $sh -> buf3_index < 3 ) ;
      if ( $i >= $message_count )
        break ; 
      $sh -> buf3_index = 0 ;
      $u16 = $sh  ->  buf3 [ 0 ] + 40 * ( $sh ->  buf3 [ 1 ] +
        40 * $sh  ->  buf3 [ 2 ] ) ;
      $sh ->  buf3 = array ( ) ;
        $buf = array ( $u16 & 0b11111111 , $u16 >> 8 ) ;
        $secretdata = array (
          $buf [ 0 ] & 0b1111 ,
          ( $buf [ 0 ] >> 4 ) & 0b1111 ,
          $buf [ 1 ] & 0b1111 ,
          ( $buf [ 1 ] >> 4 ) & 0b1111 ) ;
        $decrypteddata = array ( ) ;
        shifr_decrypt_salt2 ( $secretdata , $sh -> deshia , $decrypteddata ,
          $sh -> old_last_salt , $sh -> old_last_data ) ;
        $sh -> message .= chr ( ( $decrypteddata [ 0 ] & 0b11 ) |
          ( ( $decrypteddata [ 1 ] & 0b11 ) <<  2 ) |
          ( ( $decrypteddata [ 2 ] & 0b11 ) <<  4 ) |
          ( ( $decrypteddata [ 3 ] & 0b11 ) <<  6 ) ) ;
    } // for $i    
  } else {
    // binary
    for ( $i = 0 ; $i < $message_count - 1 ; $i += 2 ) {
      $secretdata = array (
        ord ( $message_array [ $i ] ) & 0b1111 ,
        ( ord ( $message_array [ $i ] ) >> 4 ) & 0b1111 ,
        ord ( $message_array [ $i + 1 ] ) & 0b1111 ,
        ( ord ( $message_array [ $i + 1 ] ) >> 4 ) & 0b1111 ) ;
      $decrypteddata = array ( ) ;
      shifr_decrypt_salt2 ( $secretdata , $sh -> deshia , $decrypteddata ,
        $sh -> old_last_salt , $sh -> old_last_data ) ;
      $sh -> message .= chr ( ( $decrypteddata [ 0 ] & 0b11 ) |
          ( ( $decrypteddata [ 1 ] & 0b11 ) <<  2 ) |
          ( ( $decrypteddata [ 2 ] & 0b11 ) <<  4 ) |
          ( ( $decrypteddata [ 3 ] & 0b11 ) <<  6 ) ) ;
    }
  }
}

function shifr_byte_to_array3 ( shifr & $sh , int $charcode ) : array {
  switch  ( $sh -> bitscount  ) {
  case  0 :
    // <= [ [1 0] [2 1 0] [2 1 0] ]
    $secret_data = array ( $charcode & 0b111 , ( $charcode >>  3 ) & 0b111 ) ;
    $sh -> bufin = $charcode >>  6 ;
    $sh -> bitscount  = 2 ;  // 0 + 8 - 6
    break ;
  case  1 :
    // <= [ [2 1 0] [2 1 0] [2 1] ] <= [ [0]
    $secret_data = array ( $sh -> bufin | ( ( $charcode & 0b11 ) << 1 ) ,
      ( $charcode >> 2 ) & 0b111 , $charcode >>  5 ) ;
    $sh -> bitscount  = 0 ;  // 1 + 8 - 9
    break ;
  case  2 :
    // <= [ [0] [2 1 0] [2 1 0] [2] ] <= [ [1 0] ..
    $secret_data = array ( $sh -> bufin | ( ( $charcode & 0b1 ) << 2 ) ,
      ( $charcode >> 1 ) & 0b111 , ( $charcode >>  4 ) & 0b111 ) ;
    $sh -> bufin = $charcode >>  7 ;
    $sh -> bitscount  = 1 ;  // 2 + 8 - 9
    break ;
  default :
    if  ( $shifr -> localerus )
      echo 'неожиданное значение bitscount = '  . $sh -> bitscount  ;
    else
      echo 'unexpected value bitscount = '  . $sh -> bitscount ;
    return  array ( ) ;
  }
  return  $secret_data  ;
}
    
function  shifr_write_array ( shifr & $sh , array $secret_data  ) {
  $secret_data_salt = shifr_data_salt3 ( $secret_data ) ;
  shifr_data_xor3 ( $sh -> old_last_data , $sh -> old_last_salt , $secret_data_salt ) ;
  $encrypteddata = shifr_crypt_decrypt ( $secret_data_salt , $sh -> shifra )  ;
  if ( $sh -> flagtext ) {
    global  $shifr_base64_num_to_let  ;
    foreach ( $encrypteddata as $ed ) {
      $sh -> message  .=  $shifr_base64_num_to_let [ $ed ] ;
      ++ $sh -> bytecount ;
      if ( $sh -> bytecount >= 60 ) {
        $sh -> message .= "\n" ;
        $sh -> bytecount = 0 ;
      }
    }
  } else
    foreach ( $encrypteddata as $ed ) {
      if  ( $sh ->  out_bufbitsize  < 2 ) {
        $sh ->  out_buf |=  ( $ed << ( $sh ->  out_bufbitsize ) ) ;
        $sh ->  out_bufbitsize  +=  6 ;
      } else  {
        $sh -> message  .=  chr (
          ( ( $ed << ( $sh ->  out_bufbitsize ) ) & 0xff ) | $sh ->  out_buf ) ;
        // + 6 - 8
        $sh -> out_bufbitsize -= 2 ;
        $sh -> out_buf  = $ed >> ( 6 - ( $sh -> out_bufbitsize ) ) ;
      }
    }
}
        
function  shifr_encrypt3 ( shifr & $sh ) {
  $message_array = shifr_str_split  ( $sh -> message  ) ;
  $sh -> message =  ''  ;
  foreach ( $message_array as $char ) 
    shifr_write_array ( $sh , shifr_byte_to_array3 ( $sh , ord ( $char ) ) ) ;
}

function  shifr_flush ( shifr & $sh ) {
  if ( $sh  -> bitscount ) {
    shifr_write_array ( $sh , array ( $sh -> bufin ) ) ;
    $sh -> bitscount = 0 ;
  }
  if  ( $sh ->  out_bufbitsize ) {
    $sh -> message .= $sh ->  out_buf ;
    $sh ->  out_bufbitsize = 0 ;
  }
  if ( $sh  ->  flagtext and $sh -> bytecount )  {
    $sh -> bytecount = 0 ;
    $sh -> message .= "\n"  ;
  }
  $sh ->  old_last_data  = 0 ;
  $sh ->  old_last_salt  = 0 ;
}

function  shifr_flush_file  ( shifr & $sh , & $fpw ) {
  if ( $sh  -> bitscount ) {
    $sh -> message = ''  ;
    shifr_write_array ( $sh , array ( $sh -> bufin ) ) ;
    $sh -> bitscount = 0 ;
    fwrite  ( $fpw , $sh  -> message ) ;
  }
  if  ( $sh ->  out_bufbitsize ) {
    fwrite  ( $fpw , $sh ->  out_buf ) ;
    $sh ->  out_bufbitsize = 0 ;
  }
  if ( $sh  ->  flagtext and $sh -> bytecount )  {
    $sh -> bytecount = 0 ;
    fwrite  ( $fpw , "\n" ) ;
  }
}
    
// читаю 6 бит
// 6 bits reads
function  isEOFstreambuf_read6bits ( shifr & $sh , array & $encrypteddata ) : bool {
  if  ( ( ! $sh -> flagtext ) and ( $sh -> in_bufbitsize >= 6 ) ) {
    $sh -> in_bufbitsize -=  6 ;
    $encrypteddata [ ] = $sh -> in_buf & 0b111111 ;
    $sh -> in_buf  >>= 6 ;
    return  false ;
  }
  $messlen  = strlen ( $sh -> message ) ;
  if ( $sh -> decode_read_index >= $messlen )
    return true ;
  $readsl = $sh -> message [ $sh -> decode_read_index ] ;
  $reads = ord ( $readsl ) ;
  ++  $sh -> decode_read_index  ;
  if  ( $sh -> flagtext ) {
    // читаем одну букву Base64 -> декодируем в шесть бит
    // reads one letter Base64 -> decode to six bits
    while ( ( $reads != ord ( '+' ) ) and
      ( ( $reads < ord ( '/' ) ) or ( $reads > ord ( '9' ) ) ) and
      ( ( $reads < ord ( 'A' ) ) or ( $reads > ord ( 'Z' ) ) ) and
      ( ( $reads < ord ( 'a' ) ) or ( $reads > ord ( 'z' ) ) ) ) {
      if ( $sh -> decode_read_index >= $messlen )
        return true ;
      $readsl = $sh -> message [ $sh -> decode_read_index ] ;
      $reads = ord ( $readsl ) ;
      ++  $sh -> decode_read_index  ;
    }
    global  $shifr_base64_let_to_num  ;
    $encrypteddata [ ] = $shifr_base64_let_to_num [ $readsl ] ;
  // flagtext
  } else  {
    $encrypteddata [ ] = ( $sh -> in_buf | 
      ( $reads <<  $sh -> in_bufbitsize ) ) & 0b111111 ;
    $sh -> in_buf = $reads >>  ( 6 - $sh -> in_bufbitsize ) ;
    $sh -> in_bufbitsize +=  2 ;
  } // + 8 - 6
  return  false ;
}
    
// версия 3 пишу три бита для расшифровки
// version 3 write three bits to decode
function  streambuf_write3bits ( shifr & $sh , $decrypteddata ) {
  if  ( $sh -> out_bufbitsize < 5 ) {
    $sh -> out_buf |= ( $decrypteddata << ( $sh -> out_bufbitsize ) ) ;
    $sh -> out_bufbitsize +=  3 ;
  } else  {
    $to_write  = ( ( $decrypteddata << $sh -> out_bufbitsize ) |
      ( $sh -> out_buf ) ) & 0xff ;
    $sh -> messageout .= chr ( $to_write ) ;
    // + 3 - 8
    $sh -> out_bufbitsize -= 5 ;
    $sh -> out_buf = $decrypteddata >> ( 3 - ( $sh -> out_bufbitsize ) ) ;
  }
}
    
function  shifr_decrypt3 ( shifr & $sh ) {
  $secretdata = array ( ) ;
  $sh -> messageout = ''  ;
  $sh -> decode_read_index  = 0 ;
  while ( ! isEOFstreambuf_read6bits ( $sh , $secretdata ) ) {
    $decrypteddata = array ( ) ;
    if ( is_array ( $sh -> deshia ) )
      shifr_decrypt_salt3 ( $secretdata , $sh -> deshia , $decrypteddata ,
        $sh -> old_last_salt ,  $sh -> old_last_data ) ;
    $secretdata = array ( ) ;
    streambuf_write3bits ( $sh , $decrypteddata [ 0 ] ) ;
  }
  $sh -> message = $sh -> messageout ;
}

require_once  'number.php' ;

function  shifr_password_to_string ( shifr & $sh , array $passworda ) : string {
  switch  ( $sh  ->  letters_mode ) {
  case  shifr :: letters_mode_ASCII  :
    $letters  = $sh ->  letters95  ;
    break ;
  case  shifr :: letters_mode_LetterDigit :
    $letters  = $sh ->  letters62 ;
    break ;
  case  shifr :: letters_mode_LetterCase  :
    $letters  = $sh ->  letters52 ;
    break ;
  case  26  :
    $letters  = $sh ->  letters26 ;
    break ;
  case  10  :
    $letters  = $sh ->  letters10  ;
    break ;
  default :
    return  ''  ;
  }
  $letters_count  = count ( $letters  ) ;
  $str = '' ;
  if ( number_not_zero ( $passworda ) ) {
    do {
      number_dec ( $passworda ) ;
      $str .= $letters [ number_div8_mod ( $passworda , $letters_count ) ] ;
    } while ( number_not_zero ( $passworda ) ) ;
  }
  return $str ;
}
  
// [ 0..15 , 0..14 , 0..13 , ... , 0..2 , 0..1 ] = [ x , y , z , ... , u , v ] =
// = x + y * 16 + z * 16 * 15 + ... + u * 16! / 2 / 3 + v * 16! / 2 = 0 .. 16!-1
function  shifr_pass_to_array2 ( array $password ) : array {
  $re = array ( ) ;
  $mu = array ( 1 ) ;
  $in = 0 ;
  do {
    { // $re += $password [ $in ] * $mu ;
      $mux = $mu ;
      number_mul_byte ( $mux  , $password [ $in ] ) ;
      number_add  ( $re , $mux  ) ;
    }
    //$mu *=  16 - $in ;
    number_mul_byte ( $mu , 0x10 - $in  ) ;
    ++  $in ;
  } while ( $in < 0b1111 ) ;
  return  $re ;
}

// [ 0..63 , 0..62 , 0..61 , ... , 0..2 , 0..1 ] = [ x , y , z , ... , u , v ] =
// = x + y * 64 + z * 64 * 63 + ... + u * 64! / 2 / 3 + v * 64! / 2 = 0 .. 64!-1
function  shifr_pass_to_array3 ( array $password ) : array {
  $re = array ( ) ;
  $mu = array ( 1 ) ;
  $in = 0 ;
  do {
    { // $re += $password [ $in ] * $mu ;
      $mux = $mu ;
      number_mul_byte ( $mux  , $password [ $in ] ) ;
      number_add  ( $re , $mux  ) ;
    }
    //$mu *=  64 - $in ;
    number_mul_byte ( $mu , 0b1000000 - $in  ) ;
    ++  $in ;
  } while ( $in < 0b111111 ) ;
  return  $re ;
}
  
function  shifr_password_load2  ( shifr & $sh , array $password ) {
  $sh -> shifra = array_fill  ( 0 , 0x10 , 0b11111111 ) ;
  $sh -> deshia = array_fill  ( 0 , 0x10 , 0b11111111 ) ;
  $arrind = array ( ) ;
  for ( $i = 0 ; $i < 0x10 ; ++ $i ) 
    $arrind [ ] = $i ;
  $inde = 0 ;
  do {
    $cindex = number_div8_mod ( $password , 0x10 - $inde ) ;
    $sh -> shifra [ $inde ] = $arrind [ $cindex ] ;
    $sh -> deshia [ $arrind [ $cindex ] ] = $inde ;
    unset ( $arrind [ $cindex ] ) ;
    $arrind = array_values ( $arrind ) ;
    ++ $inde  ;
  } while ( $inde < 0x10 ) ;
}

function  shifr_password_load3  ( shifr & $sh , array $password ) {
  $sh -> shifra = array_fill  ( 0 , 0b1000000 , 0b11111111 ) ;
  $sh -> deshia = array_fill  ( 0 , 0b1000000 , 0b11111111 ) ;
  $arrind = array ( ) ;
  for ( $i = 0 ; $i < 0b1000000 ; ++ $i ) 
    $arrind [ ] = $i ;
  $inde = 0 ;
  do {
    $cindex = number_div8_mod ( $password , 0b1000000 - $inde ) ;
    $sh -> shifra [ $inde ] = $arrind [ $cindex ] ;
    $sh -> deshia [ $arrind [ $cindex ] ] = $inde ;
    unset ( $arrind [ $cindex ] ) ;
    $arrind = array_values ( $arrind ) ;
    ++ $inde  ;
  } while ( $inde < 0b1000000 ) ;
}

function  shifr_generate_password  ( shifr & $shifr ) {
  $ar = null  ;
  if ( shifr_version  ( $shifr  ) == 2 )
    $ar = shifr_pass_to_array2  ( shifr_generate_pass2  ( ) ) ;
  else 
    $ar = shifr_pass_to_array3  ( shifr_generate_pass3  ( ) ) ;
  $str_psw  = shifr_password_to_string ( $shifr , $ar ) ;
  if  ( $shifr -> flag_debug  )
    $shifr -> array_log [ ] = 'shifr_generate_password `' . $str_psw . '`' ;
  shifr_password_set ( $shifr , $str_psw ) ;
}
  
function  shifr_password_set  ( shifr & $shifr ,  $password ) {
  if  ( $shifr -> flag_debug  )
    $shifr -> array_log [ ] = 'shifr_password_set `' . $password . '`' ;
  $shifr  ->  password  = $password ;
  shifr_string_to_key_array ( $shifr , $password ) ;
}

function  shifr_string_to_key_array  ( shifr & $sh , string & $str ) {
  $strn = strlen  ( $str  ) ;
  $passarr = array ( ) ;
  number_set_zero ( $passarr ) ;
  if ( $strn == 0 ) {
    if ( shifr_version  ( $sh  ) == 2 )
      return  shifr_password_load2  ( $sh , $passarr ) ; 
    else 
      return  shifr_password_load3  ( $sh , $passarr ) ;
    return $passarr ;
  }
  switch  ( $sh ->  letters_mode ) {
  case  shifr :: letters_mode_ASCII  :
    $letters  = $sh ->  letters95  ;
    break ;
  case  shifr :: letters_mode_LetterDigit  :
    $letters  = $sh ->  letters62  ;
    break ;
  case  shifr :: letters_mode_LetterCase  :
    $letters  = $sh ->  letters52  ;
    break ;
  case  26  :
    $letters  = $sh ->  letters26 ;
    break ;
  case  10  :
    $letters  = $sh ->  letters10  ;
    break ;
  default :
    return  ; 
  }
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
    if  ( $sh -> flag_debug  ) {
      if  ( $sh -> localerus ) 
        $sh -> array_log [ ] = 'неправильная буква в пароле'  ;
      else 
        $sh -> array_log [ ] = 'wrong letter in password' ;
    }
    return array ( ) ;
found :
    $tmp = $mult ;
    number_mul_byte ( $tmp , $i + 1 ) ;
    number_add ( $passarr , $tmp ) ;
    number_mul_byte ( $mult , $letters_count ) ;
    ++  $stringi ;
  } while ( $stringi  < strlen  ( $str  ) ) ;
  if ( shifr_version  ( $sh  ) == 2 )
    return  shifr_password_load2  ( $sh , $passarr ) ; 
  else 
    return  shifr_password_load3  ( $sh , $passarr ) ;
  return  $passarr ; 
}
  
function  shifr_set_version ( shifr & $sh , $ver  ) {
  if  ( $ver  ==  2 )
    $sh ->  key_mode = 45 ;
  else 
    $sh ->  key_mode = 296 ; 
}

function  shifr_version ( shifr & $sh ) {
  if  ( $sh ->  key_mode == 45 )
    return 2 ;
  return  3 ;
}

// ! to __construct : call init ?

function  shifr_init ( shifr & $sh ) {
  //  ascii ' ' => '~'
  $sh ->  letters95 = array ( ) ;
  for ( $i = ord  ( ' ' ) ; $i <= ord ( '~' ) ; ++ $i )
    $sh ->  letters95 [ ] = chr ( $i ) ;
  // '0' - '9' , 'A' - 'Z' , 'a' - 'z'  
  $sh ->  letters62 = array ( ) ;
  for ( $i = ord  ( '0' ) ; $i <= ord ( '9' ) ; ++ $i )
    $sh ->  letters62 [ ] = chr ( $i ) ;
  for ( $i = ord  ( 'A' ) ; $i <= ord ( 'Z' ) ; ++ $i )
    $sh ->  letters62 [ ] = chr ( $i ) ;
  for ( $i = ord  ( 'a' ) ; $i <= ord ( 'z' ) ; ++ $i )
    $sh ->  letters62 [ ] = chr ( $i ) ; 
  // 'A' - 'Z' , 'a' - 'z'  
  $sh ->  letters52 = array ( ) ;
  for ( $i = ord  ( 'A' ) ; $i <= ord ( 'Z' ) ; ++ $i )
    $sh ->  letters52 [ ] = chr ( $i ) ;
  for ( $i = ord  ( 'a' ) ; $i <= ord ( 'z' ) ; ++ $i )
    $sh ->  letters52 [ ] = chr ( $i ) ; 
  // '0' - '9'
  $sh ->  letters10 = array ( ) ;
  for ( $i = ord  ( '0' ) ; $i <= ord ( '9' ) ; ++ $i )
    $sh ->  letters10 [ ] = chr ( $i ) ;
  // 'a' - 'z'
  $sh ->  letters26 = array ( ) ;
  for ( $i  = ord ( 'a' ) ; $i  <=  ord ( 'z' ) ; ++  $i  )
    $sh ->  letters26 [ ] = chr ( $i  ) ;
  // default is digits and letters
  $sh ->  letters_mode = shifr :: letters_mode_LetterDigit ;
  shifr_set_version ( $sh , 2 ) ;
  $sh ->  old_last_data  = 0 ;
  $sh ->  old_last_salt  = 0 ;
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
  $sh ->  flagtext  = true  ;
  $sh ->  boxes_info  = ""  ;
  $sh ->  filename  = ""  ;
  $sh ->  flag_debug  = false ;
  $sh ->  array_log = array ( ) ;
}

function  shifr_password_get  ( shifr & $shifr ) {
  return  $shifr  ->  password  ; 
}

function  shifr_encrypt  ( shifr & $shifr ) {
  if ( shifr_version  ( $shifr  ) == 2 ) 
    shifr_encrypt2 ( $shifr ) ; 
  else 
    shifr_encrypt3 ( $shifr  ) ;
}

function  shifr_decrypt  ( shifr & $shifr ) {
  if ( shifr_version  ( $shifr  ) == 2 ) 
    shifr_decrypt2 ( $shifr ) ; 
  else 
    shifr_decrypt3 ( $shifr ) ;
}

// '0x00','0xf0','0x0f','0xff' <= "aa" + "ap" + "pa" + "pp"
function  shifr_Base64_decode_univer  ( string & $string  , $start_letter ,
  $bits_count ) : string {
  $strlen = strlen  ( $string ) ;
  $acode  = ord ( $start_letter ) ;
  $result = ""  ;
  $cache  = 0 ;
  $cache_size = 0 ; //  0 .. 7
  $i  = 0 ;
  while ( $i  < $strlen ) {
    do  {
      $cache  |= ( ( ord ( $string [ $i  ] ) - $acode ) <<  $cache_size ) ;
      $cache_size +=  $bits_count ;
      ++  $i  ;
    } while ( $cache_size < 8 and $i  < $strlen ) ;
    $result .=  chr ( $cache  & 0b11111111 ) ;
    $cache  >>= 8 ;
    $cache_size -=  8 ;
  }
  return  $result ;
}

/*
Base64 = ( ;<=> ?@AB CDEF GHIJ
           KLMN OPQR STUV WXYZ
           [\]^ _`ab cdef ghij
           klmn opqr stuv wxyz )
*/
function  shifr_Base64_decode ( string & $string ) : string {
  return  shifr_Base64_decode_univer  ( $string , ";" , 6 ) ;
}

?>
