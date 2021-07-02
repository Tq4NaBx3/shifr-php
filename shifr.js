'use strict';

// generate random number [ fr .. to ]
let js_shifr_rand_fr_to = function  ( fr , to ) {
  const wid = to - fr + 1 ;
  const array = new Uint8Array  ( 1 ) ;
  do {
    window  . crypto  . getRandomValues ( array ) ;
  } while ( array [ 0 ] + 0x100 % wid >= 0x100 ) ;
  return  fr + array [ 0 ] % wid ; }

// returns [ 0..15 , 0..14 , ... , 0..2 , 0..1 ]
let js_shifr_generate_pass2 = function (  ) {
  const dice  = new Array ( ) ;
  let i = 0x0f  ; // 15
  do  {
    let r = js_shifr_rand_fr_to ( 0 , i ) ;
    dice  . push  ( r ) ;
    --  i ;
  } while ( i > 0 ) ;
  return  dice  ; }

// returns [ 0..63 , 0..62 , ... , 0..2 , 0..1 ]
let js_shifr_generate_pass3 = function  ( ) {
  const dice  = new Array ( ) ;
  let i = 0x3f  ; //  63
  do  {
    let r = js_shifr_rand_fr_to ( 0 , i ) ;
    dice  . push  ( r ) ;
    --  i ;
  } while ( i > 0 ) ;
  return  dice  ; }

// get 4*2 bits => push 4*4 bits
let js_shifr_data_sole2 = function  ( secret_data ) {
  let secret_data_sole  = new Array ( ) ;
  const array = new Uint8Array  ( 1 ) ;
  window  . crypto  . getRandomValues ( array ) ;
  let ra  = array [ 0 ] ; // 4*2 = 8 bits
  for ( let da  of  secret_data ) {
    secret_data_sole  . push  ( ( da  << 2 ) | ( ra & 0b11 ) ) ;
    ra  >>= 2 ; }
  return  secret_data_sole  ; }
  
// get 2*3 bits => push 2*6 bits
// get 3*3 bits => push 3*6 bits
let js_shifr_data_sole3 = function  ( secret_data ) {
  let secret_data_sole  = new Array ( ) ;
  let ra  ;
  if ( secret_data  . length  ==  3 ) {
    // needs random [ 0 .. 0x1ff ]
    const array = new Uint8Array  ( 2 ) ;
    window  . crypto  . getRandomValues ( array ) ;
    ra = ( array  [ 1 ] << 8 ) | ( array [ 0 ] ) ; } // 3*3 = 9 bits
  else  {
    const array = new Uint8Array  ( 1 ) ;
    window  . crypto  . getRandomValues ( array ) ;
    ra = array [ 0 ] ; }
  for ( let da  of  secret_data ) {
    secret_data_sole  . push  ( ( da  << 3 ) | ( ra & 0b111 ) ) ;
    ra >>= 3 ; }
  return  secret_data_sole  ; }

// byte = 76543210b to array
// [ 0 ] = 10 ; [ 1 ] = 32 ; [ 2 ] = 54 ; [ 3 ] = 76
let js_shifr_byte_to_array2 =  function  ( byte ) {
  let arr = new Array ( ) ;
  let i = 0 ;
  do  {
    arr . push ( byte & 0b11 ) ;
    byte >>= 2 ;
    ++  i ;
  } while ( i < 4 ) ;
  return  arr ; }

// old_last_data = { n : }
// old_last_sole = { n : }
// secret_data_sole = [ data_sole ]
let js_shifr_data_xor2  = function  ( old_last_data , old_last_sole ,
  secret_data_sole  ) {
  const sl  = secret_data_sole  . length  ;
  for ( let i = 0 ; i < sl ; ++  i ) {
    const ids = secret_data_sole  [ i ] ;
    const cur_data  = ids >>  2 ;
    const cur_sole  = ids & 0b11 ;
    secret_data_sole  [ i ] ^=  ( old_last_sole . n  <<  2 ) ;
    secret_data_sole  [ i ] ^=  old_last_data . n ;
    old_last_data . n = cur_data ;
    old_last_sole . n = cur_sole ; } }

let js_shifr_data_xor3  = function  ( old_last_data , old_last_sole ,
  secret_data_sole  ) {
  const sl  = secret_data_sole  . length  ;
  for ( let i = 0 ; i < sl ; ++  i ) {
    const ids = secret_data_sole  [ i ] ;
    const cur_data  = ids >>  3 ;
    const cur_sole  = ids & 0b111 ;
    secret_data_sole  [ i ] ^=  ( old_last_sole . n  <<  3 ) ;
    secret_data_sole  [ i ] ^=  old_last_data . n ;
    old_last_data . n = cur_data ;
    old_last_sole . n = cur_sole ; } }

let js_shifr_crypt_decrypt  = function  ( datap , tablep ) {
  let encrp = new Array ( ) ;
  for ( let id  of datap )
    encrp . push  ( tablep  [ id  ] ) ;
  return  encrp ; }

// old_last_data = { n : }
// old_last_sole = { n : }
let js_shifr_decrypt_sole2  = function  ( datap , tablep , decrp , 
  old_last_sole , old_last_data ) {
  for ( let id  of datap ) {
    const data_sole = tablep [ id ] ;
    const newdata = ( data_sole >> 2 ) ^ old_last_sole . n ;
    decrp . push ( newdata ) ;
    old_last_sole . n = (  data_sole & 0b11 ) ^ old_last_data . n ;
    old_last_data . n = newdata ; } }

let js_shifr_decrypt_sole3  = function  ( datap , tablep , decrp , 
  old_last_sole , old_last_data ) {
  for ( let id  of datap ) {
    const data_sole = tablep [ id ] ;
    const newdata = ( data_sole >> 3 ) ^ old_last_sole . n ;
    decrp . push ( newdata ) ;
    old_last_sole . n = (  data_sole & 0b111 ) ^ old_last_data . n ;
    old_last_data . n = newdata ; } }
/*
class shifr {
  // alphabet ascii // алфавит ascii
  letters95  ; // 0x20 пробел - 0x7e ~ тильда // 0x20 space - 0x7e ~ tilde
  letters  ; // alphabet 62 letters digits // алфавит 62 буквы цифры
  letters10  ; // alphabet 10 digits // алфавит 10 цифры
  // password alphabet mode 10 or 62 or 95 
  // режим алфавит пароля 10 или 62 или 95
  letters_mode ;
  localerus  ; // false or true // русская локаль false или true
  flagtext ; // true or false // флаг текст true или false
  password ; // пароль
  message  ; // сообщение/данные // message/data
  messageout  ; // сообщение/данные // message/data
  old_last_data  ; // предыдущие данные
  old_last_sole  ; // предыдущая соль
  // текстовый режим : букв в строке написано
  bytecount  ; // text mode: letters are written in a line
  // decode : text place 0 .. 2 
  buf3_index ; // расшифровка : позиция в тексте
  buf3 ; // decode text buffer // расшифровка текстовый буфер
  shifra ; // array for encryption // массив для шифрования
  deshia ; // array for decryption // массив для расшифровки
  key_mode ; // 45 или 296 // 45 or 296
  filebuffrom ;
  filebufto ;
  in_buf  ; // buf to read // буфер чтения
  out_buf  ; // buf to write // буфер записи
  in_bufbitsize ; // размер буфера в битах
  out_bufbitsize ; // размер буфера в битах
  decode_read_index  ; // индекс чтения для расшифровки
  // Размер битового буфера чтения
  bitscount  ; // reading buffer bit size
  // encode3
  // 0-2 бит буфер чтения
  bufin  ; // 0-2 bits buffer reading
}
*/

let js_toUTF8Array = function ( str ) {
    let utf8 = [];
    for ( let i = 0 ; i < str . length  ; ++  i ) {
        let charcode = str.charCodeAt(i);
        if (charcode < 0x80)
          utf8.push(charcode);
        else if (charcode < 0x800) {
            utf8.push(0xc0 | (charcode >> 6),
                      0x80 | (charcode & 0x3f)); }
        else if (charcode < 0xd800 || charcode >= 0xe000) {
            utf8.push(0xe0 | (charcode >> 12),
                      0x80 | ((charcode>>6) & 0x3f),
                      0x80 | (charcode & 0x3f)); }
        // surrogate pair
        else {
            i++;
            // UTF-16 encodes 0x10000-0x10FFFF by
            // subtracting 0x10000 and splitting the
            // 20 bits of 0x0-0xFFFFF into two halves
            charcode = 0x10000 + (((charcode & 0x3ff)<<10)
                      | (str.charCodeAt(i) & 0x3ff));
            utf8.push(0xf0 | (charcode >>18),
                      0x80 | ((charcode>>12) & 0x3f),
                      0x80 | ((charcode>>6) & 0x3f),
                      0x80 | (charcode & 0x3f)); } }
    return utf8; }


let js_Utf8ArrayToStr  = function (  array ) {
    let out = '' ;
    let len = array . length  ;
    let i = 0 ;
    while ( i < len ) {
      let c = array [ i ] ;
      ++  i ;
      switch  ( c >> 4  ) { 
      case 0: case 1: case 2: case 3: case 4: case 5: case 6: case 7:
        // 0xxxxxxx
        out += String . fromCharCode  ( c ) ;
        break ;
      case 12: case 13:
        // 110x xxxx   10xx xxxx
        { let char2 = array [ i ] ;
          ++  i ;
          out += String . fromCharCode  (
            ( ( c & 0x1F  ) << 6  ) | ( char2 & 0x3F  ) ) ; }
        break ;
      case 14:
        // 1110 xxxx  10xx xxxx  10xx xxxx
        { let char2 = array [ i ] ;
          ++  i ;
          if ( i < len ) {
            let char3 = array [ i ] ;
            ++  i ;
            out += String . fromCharCode  ( ( ( c & 0x0F  ) << 12 ) |
                       (  ( char2 & 0x3F  ) << 6  ) |
                       (  ( char3 & 0x3F  ) << 0  ) ) ; } }
        break ; } }
    return out  ; }
    
// sh . message_array of bytes -> sh . message of bytes
let js_shifr_encrypt2 = function ( sh ) {
  if ( sh . flagtext )
    sh . message =  '' ;
  else
    sh . message =  [ ] ;
  for ( let char of sh . message_array ) {
    let secret_data = js_shifr_byte_to_array2 ( char ) ;
    let secret_data_sole = js_shifr_data_sole2 ( secret_data ) ;
    js_shifr_data_xor2 ( sh . old_last_data , sh . old_last_sole ,
      secret_data_sole ) ;
    let encrypteddata = js_shifr_crypt_decrypt ( secret_data_sole , sh . shifra )  ;
    if ( sh . flagtext ) {
      let buf16 = ( encrypteddata [ 0 ] & 0b1111 ) |
        ( ( encrypteddata [ 1 ] & 0b1111 ) << 4 ) |
        ( ( encrypteddata [ 2 ] & 0b1111 ) << 8 ) |
        ( ( encrypteddata [ 3 ] & 0b1111 ) << 12 ) ;
      sh . message +=  String.fromCharCode (
        ( 'R' . charCodeAt ( 0 ) ) + ( buf16 % 40 ) ) ;
      buf16 = Math . floor ( ( buf16 + 0.5 ) / 40 )  ;      
      sh . message += String.fromCharCode (
        ( 'R' . charCodeAt ( 0 ) ) + ( buf16 % 40 ) ) ;
      buf16 = Math . floor ( ( buf16 + 0.5 )  / 40 )  ;            
      sh . message +=  String.fromCharCode ( ( 'R' . charCodeAt ( 0 ) ) + buf16 ) ;
      sh . bytecount += 3 ;
      if ( sh . bytecount >= 60 ) {
        sh . message += "\n" ;
        sh . bytecount = 0 ; } }
    else {
      sh . message . push ( ( encrypteddata [ 0 ] & 0b1111 ) |
        ( ( encrypteddata [ 1 ] & 0b1111 ) << 4 ) ) ;
      sh . message . push ( ( encrypteddata [ 2 ] & 0b1111 ) |
        ( ( encrypteddata [ 3 ] & 0b1111 ) << 4 ) ) ; } } }

let js_shifr_byte_to_array3 = function ( sh , charcode ) {
  let secret_data ;
  switch  ( sh . bitscount  ) {
  case  0 :
    // <= [ [1 0] [2 1 0] [2 1 0] ]
    secret_data = [ charcode & 0b111 , ( charcode >>  3 ) & 0b111 ] ;
    sh . bufin = charcode >>  6 ;
    sh . bitscount  = 2 ;  // 0 + 8 - 6
    break ;
  case  1 :
    // <= [ [2 1 0] [2 1 0] [2 1] ] <= [ [0]
    secret_data = [ sh . bufin | ( ( charcode & 0b11 ) << 1 ) ,
      ( charcode >> 2 ) & 0b111 , charcode >>  5 ] ;
    sh . bitscount  = 0 ;  // 1 + 8 - 9
    break ;
  case  2 :
    // <= [ [0] [2 1 0] [2 1 0] [2] ] <= [ [1 0] ..
    secret_data = [ sh . bufin | ( ( charcode & 0b1 ) << 2 ) ,
      ( charcode >> 1 ) & 0b111 , ( charcode >>  4 ) & 0b111 ] ;
    sh . bufin = charcode >>  7 ;
    sh . bitscount  = 1 ;  // 2 + 8 - 9
    break ;
  default :
    if  ( shifr . localerus )
      console . log ( 'неожиданное значение bitscount = ' + sh . bitscount ) ;
    else
      console . log ( 'unexpected value bitscount = '  + sh . bitscount ) ;
    return  ; }
  return  secret_data  ; }
  
let js_shifr_write_array  = function  ( sh , secret_data  ) {
  let secret_data_sole = js_shifr_data_sole3 ( secret_data ) ;
  js_shifr_data_xor3 ( sh . old_last_data , sh . old_last_sole ,
    secret_data_sole ) ;
  let encrypteddata = js_shifr_crypt_decrypt ( secret_data_sole , sh . shifra )  ;
  if ( sh . flagtext ) {
    for ( let ed  of  encrypteddata ) {
      sh . message  +=  String . fromCharCode ( ';' . charCodeAt ( 0 ) + ed ) ;
      ++ ( sh . bytecount ) ;
      if ( sh . bytecount >= 60 ) {
        sh . message += "\n" ;
        sh . bytecount = 0 ; } } }
  else
    for ( let ed  of  encrypteddata ) {
      if  ( sh .  out_bufbitsize  < 2 ) {
        sh .  out_buf |=  ( ed << ( sh .  out_bufbitsize ) ) ;
        sh .  out_bufbitsize  +=  6 ; }
      else  {
        sh . message  . push (
          ( ( ed << ( sh .  out_bufbitsize ) ) & 0xff ) | sh .  out_buf ) ;
        // + 6 - 8
        sh . out_bufbitsize -= 2 ;
        sh . out_buf  = ed >> ( 6 - ( sh . out_bufbitsize ) ) ; } } }

// sh . message_array of bytes -> sh . message of bytes
let js_shifr_encrypt3 = function ( sh ) {
  if ( sh . flagtext )
    sh . message =  ''  ;
  else
    sh . message =  [ ] ;
  for ( let char of sh . message_array ) {
    js_shifr_write_array ( sh , js_shifr_byte_to_array3 ( sh , char ) ) ; } }

let js_shifr_generate_password = function ( sh  ) {
  if ( sh . key_mode == 45 )
    js_shifr_generate_password_2 ( sh ) ;
  else
    js_shifr_generate_password_3 ( sh ) ; }
    
let js_shifr_generate_password_2 = function ( sh ) {
  sh . password  = js_shifr_password_to_string ( sh ,
    js_shifr_pass_to_array2  ( js_shifr_generate_pass2  ( ) ) ) ; }

let js_shifr_generate_password_3 = function ( sh ) {
  sh . password  = js_shifr_password_to_string ( sh ,
    js_shifr_pass_to_array3  ( js_shifr_generate_pass3  ( ) ) ) ; }
    
let js_shifr_flush  = function  ( sh ) {
  if ( sh . bitscount ) {
    js_shifr_write_array ( sh , [ sh . bufin ] ) ;
    sh . bitscount = 0 ; }
  if  ( sh . out_bufbitsize ) {
    if ( sh . flagtext  )
      sh . message += sh . out_buf ;
    else
      sh . message . push ( sh . out_buf ) ;
    sh . out_bufbitsize = 0 ; }
  if ( sh . flagtext && sh . bytecount )  {
    sh . bytecount = 0 ;
    sh . message += "\n"  ; }
    
  // ?? _init will make it
  // sh . old_last_data  = { n : 0 } ;
  // sh . old_last_sole  = { n : 0 } ;
  
  }
    
let js_number_dec = function  ( number ) {
  let i = 0 ;
  for ( ; i < number . length ; ++ i ) {
    if ( number [ i ] != 0 ) {
      number [ i ] = number [ i ] - 1 ;
      break ; }
    number [ i ] = 0xff  ; }
  if ( i == ( number . length ) ) {
    console . log ( 'js_number_dec:i == ( number . length )' ) ;
    return  ; }
  i = ( number . length ) ;
  while ( i > 0 ) {
    -- i ;
    if ( number [ i ] != 0 ) 
      break ;
    number . pop ( ) ; } }

let js_number_not_zero  = function ( number ) {
  return  ( number . length ) >  0 ; }

let js_number_set_zero = function  ( number ) {
  number . length = 0 ; }
  
let js_number_set_byte  = function  ( number , byte ) {
  if ( byte != 0 ) {
    if ( byte < 0 ) {
      alert ( 'js_number_set_byte:byte < 0' ) ;
      return  ; }
    if ( byte >= 0x100 ) {
      alert ( 'js_number_set_byte:byte >= 0x100' ) ;
      return  ; }
    number . length = 1 ;
    number [ 0 ] = byte ; }
  else
    number . length = 0 ; }

// number /= div , number := floor [ деление ] , return := остаток (remainder)
let js_number_div8_mod = function  ( number , div ) {
  let modi = 0 ;
  { let i = ( number . length ) ;
    while ( i > 0 ) {
      -- i ;
      let x = ( modi << 8 ) | ( number [ i ] ) ;
      modi = x % div ;
      number [ i ] = ( ( x - modi ) / div ) ; } }
  let i = ( number . length ) ;
  while ( i > 0 ) {
    -- i ;
    if ( number [ i ] != 0 )
      break ;
    number . pop ( ) ; }
  return  modi ; }
  
let js_shifr_password_to_string = function ( sh , passworda ) {
  let letters ;
  switch  ( sh  . letters_mode ) {
  case  95  :
    letters  = sh . letters95  ;
    break ;
  case  62  :
    letters  = sh . letters  ;
    break ;
  case  10  :
    letters  = sh . letters10  ;
    break ;
  default :
    return  ''  ; }
  let letters_count = letters . length ;
  let str = '' ;
  if ( js_number_not_zero ( passworda ) ) {
    do {
      js_number_dec ( passworda ) ;
      str += letters [ js_number_div8_mod ( passworda , letters_count ) ] ;
    } while ( js_number_not_zero ( passworda ) ) ; }
  return str ; }

let js_number_mul_byte = function ( number , byte ) {
  if ( byte == 0 ) {
    number . length = 0 ;
    return  ; }
  if ( byte == 1 )
    return ;
  if ( byte < 0 ) {
    console . log ( 'js_number_mul_byte: byte < 0' ) ;    
    return  ; }
  if ( byte >= 0x100 ) {
    console . log ( 'js_number_mul_byte: byte >= 0x100' ) ;
    return  ; }
  let per = 0 ;
  let i = 0 ;
  for ( ; i < number . length ; ++ i ) {
    let x = number [ i ] * byte + per ;
    number [ i ] = x & 0xff ;
    per = x >> 8 ; }
  if ( per > 0 )
    number [ i ] = per ; }
  
let js_number_add = function ( num , xnum ) {
  let per = 0 ;
  let i = 0 ;
  for ( ; i < num . length && i < xnum . length ; ++ i )  {
    let s = num [ i ] + xnum [ i ] + per ;
    if ( s >= 0x100  ) {
      num [ i ] = s - 0x100 ;
      per = 1 ; }
    else  {
      num [ i ] = s  ;
      per = 0 ; } }
  if ( num . length > xnum . length ) {
    if ( per == 0 )
      return  ;
    for ( ; i < num . length ; ++ i )  {
      let s = num [ i ] + 1 ;
      if ( s < 0x100  ) {
        num [ i ] = s  ;
        return ; }
      num [ i ] = 0 ; }
    num [ i ] = 1 ;
    return  ; }
  if ( num . length < xnum . length ) {
    for ( ; i < xnum . length ; ++ i )  {
      let s = xnum [ i ] + per ;
      if ( s == 0x100  ) {
        num [ i ] = 0 ;
        per  = 1 ; }
      else  {
        num [ i ] = s  ;
        per  = 0 ; } } }
  if ( per > 0 )
    num [ i ] = 1 ; }
  
// [ 0..15 , 0..14 , 0..13 , ... , 0..2 , 0..1 ] = [ x , y , z , ... , u , v ] =
// = x + y * 16 + z * 16 * 15 + ... + u * 16! / 2 / 3 + v * 16! / 2 = 0 .. 16!-1
let js_shifr_pass_to_array2 = function ( password ) {
  let re = [ ] ;
  let mu = [ 1 ] ;
  let jn = 0 ;
  do {
    { // re += password [ jn ] * mu ;
      let mux = mu . slice ( ) ;
      js_number_mul_byte ( mux  , password [ jn ] ) ;
      js_number_add  ( re , mux ) ; }
    // mu *= 16 - jn ;
    js_number_mul_byte ( mu , 0x10 - jn ) ;
    ++  jn ;
  } while ( jn < 0x0f ) ;
  return  re ; }

// [ 0..63 , 0..62 , 0..61 , ... , 0..2 , 0..1 ] = [ x , y , z , ... , u , v ] =
// = x + y * 64 + z * 64 * 63 + ... + u * 64! / 2 / 3 + v * 64! / 2 = 0 .. 64!-1
let js_shifr_pass_to_array3 = function ( password ) {
  let re = [ ] ;
  let mu = [ 1 ] ;
  let jn = 0 ;
  do {
    { // re += password [ jn ] * mu ;
      let mux = mu . slice ( ) ;
      js_number_mul_byte ( mux  , password [ jn ] ) ;
      js_number_add  ( re , mux ) ; }
    // mu *= 64 - jn ;
    js_number_mul_byte ( mu , 0x40 - jn ) ;
    ++  jn ;
  } while ( jn < 0x3f ) ;
  return  re ; }
  
let js_shifr_set_version = function ( sh , ver ) {
  if  ( ver  ==  2 )
    sh . key_mode = 45 ;
  else 
    sh . key_mode = 296 ; }

let js_shifr_sole_init = function ( sh  ) {
  sh . messageout = [ ] ;
  sh . buf3_index = 0 ;
  sh . buf3 = [ ] ;
  sh . out_buf = 0 ;
  sh . decode_read_index = 0 ;
  sh . in_buf = 0 ;
  sh . in_bufbitsize = 0 ;
  sh . bitscount = 0 ;
  sh . bufin = 0 ;
  sh . out_bufbitsize = 0 ;
  sh . bytecount = 0 ;
  sh . old_last_data  = { n : 0 } ;
  sh . old_last_sole  = { n : 0 } ; }
    
let js_shifr_init = function ( sh ) {
  //  ascii ' ' => '~'
  sh .  letters95 = [ ] ;
  for ( let i = ( ' ' . charCodeAt ( 0 ) ) ; i <= ( '~' . charCodeAt ( 0 ) ) ; ++ i )
    sh . letters95 . push ( String . fromCharCode ( i ) ) ;
  // '0' - '9' , 'A' - 'Z' , 'a' - 'z'  
  sh . letters = [ ] ;
  for ( let i = ( '0' . charCodeAt ( 0 ) ) ; i <= ( '9' . charCodeAt ( 0 ) ) ; ++ i )
    sh . letters . push ( String . fromCharCode ( i ) ) ;
  for ( let i = ( 'A' . charCodeAt ( 0 ) ) ; i <= ( 'Z' . charCodeAt ( 0 ) ) ; ++ i )
    sh . letters . push ( String . fromCharCode ( i ) ) ;
  for ( let i = ( 'a' . charCodeAt ( 0 ) ) ; i <= ( 'z' . charCodeAt ( 0 ) ) ; ++ i )
    sh . letters . push ( String . fromCharCode ( i ) ) ;
  // '0' - '9'
  sh . letters10 = [ ] ;
  for ( let i = ( '0' . charCodeAt ( 0 ) ) ; i <= ( '9' . charCodeAt ( 0 ) ) ; ++ i )
    sh . letters10 . push ( String . fromCharCode ( i ) ) ;
  // default is digits and letters
  sh . letters_mode = 62 ;
  js_shifr_set_version ( sh , 3 ) ;
  js_shifr_sole_init  ( sh  ) ;
  sh . localerus = false ;
  sh . flagtext  = true  ;  }

let js_shifr_version = function ( sh ) {
  if  ( sh . key_mode == 45 )
    return 2 ;
  return  3 ; }
  
let js_shifr_password_load = function ( shifr ) {
  if ( js_shifr_version  ( shifr  ) == 2 ) 
    js_shifr_password_load_2 ( shifr ) ;
  else 
    js_shifr_password_load_3 ( shifr ) ; }
    
let js_shifr_password_load_2 = function ( sh ) {
  return  js_shifr_password_load2  ( sh , js_shifr_string_to_key_array ( sh ,
    sh . password  ) ) ; }

let js_shifr_password_load_3 = function ( sh ) {
  return  js_shifr_password_load3  ( sh , js_shifr_string_to_key_array ( sh ,
    sh . password  ) ) ; }

let js_shifr_password_load2 = function ( sh , password0 ) {
  sh . shifra = Array ( 0x10  ) . fill  ( 0xff  ) ;
  sh . deshia = Array ( 0x10  ) . fill  ( 0xff  ) ;
  let arrind = [ ] ;
  for ( let i = 0 ; i < 0x10 ; ++ i ) 
    arrind . push ( i ) ;
  let inde = 0 ;
  let password = password0 . slice ( ) ;
  do {
    let cindex = js_number_div8_mod ( password , 0x10 - inde ) ;
    sh . shifra [ inde ] = arrind [ cindex ] ;
    sh . deshia [ arrind [ cindex ] ] = inde ;
    arrind  . splice  ( cindex  , 1 ) ;
    ++ inde  ;
  } while ( inde < 0x10 ) ; }

let js_shifr_password_load3 = function ( sh , password0 ) {
  sh . shifra = Array ( 0x40 ) . fill ( 0xff ) ;
  sh . deshia = Array ( 0x40 ) . fill ( 0xff ) ;
  let arrind = [ ] ;
  for ( let i = 0 ; i < 0x40 ; ++ i ) 
    arrind . push ( i ) ;
  let inde = 0 ;
  let password = password0 . slice ( ) ;
  do {
    let cindex = js_number_div8_mod ( password , 0x40 - inde ) ;
    sh . shifra [ inde ] = arrind [ cindex ] ;
    sh . deshia [ arrind [ cindex ] ] = inde ;
    arrind  . splice  ( cindex  , 1 ) ;
    ++ inde  ;
  } while ( inde < 0x40 ) ; }

let js_shifr_string_to_key_array  = function ( sh , str ) {
  let strn = str . length ;
  let passarr = [ ] ;
  js_number_set_zero ( passarr ) ;
  if ( strn == 0 ) 
    return passarr ;
  let letters ;
  switch  ( sh . letters_mode ) {
  case  95  :
    letters  = sh . letters95  ;
    break ;
  case  62  :
    letters  = sh . letters  ;
    break ;
  case  10  :
    letters  = sh . letters10  ;
    break ;
  default :
    alert ( 'sh . letters_mode = ' + sh . letters_mode ) ;
    return  ; }
  let letters_count  = letters . length ;
  let mult = [ ] ;
  js_number_set_byte ( mult , 1 ) ;
  let stringi  = 0 ;
  do  {
    let i = letters_count ;
search : {
      do {
        -- i ;
        if ( str [ stringi ] == letters [ i ] ) 
          break search ;
      } while ( i ) ;
      if  ( sh . localerus ) 
        alert ( 'неправильная буква в пароле' ) ;
      else 
        alert ( 'wrong letter in password' ) ;
      return ; }
    let tmp = mult . slice ( ) ;
    js_number_mul_byte ( tmp , i + 1 ) ;
    js_number_add ( passarr , tmp ) ;
    js_number_mul_byte ( mult , letters_count ) ;
    ++  stringi ;
  } while ( stringi  < str . length ) ;
  return  passarr ; }

// sh . message_array of bytes -> sh . message of bytes
let js_shifr_encrypt  = function  ( shifr ) {
  if ( js_shifr_version  ( shifr  ) == 2 ) 
    js_shifr_encrypt2 ( shifr ) ; 
  else 
    js_shifr_encrypt3 ( shifr  ) ; }

let js_shifr_decrypt  = function  ( shifr ) {
  if ( js_shifr_version  ( shifr  ) == 2 ) 
    js_shifr_decrypt2 ( shifr ) ; 
  else 
    js_shifr_decrypt3 ( shifr ) ; }

// message_array -> message ( decrypted array )
let js_shifr_decrypt2 = function  ( sh ) {
  sh  . message = [ ] ;
  if ( sh . flagtext ) {
    for ( let i = 0 ; i < sh . message_array . length ; ++ i ) {
      do  {
        while ( ( sh . message_array [ i ] ) < ( 'R' . charCodeAt ( 0 ) ) ||
          ( sh . message_array [ i ] ) > ( 'z' . charCodeAt ( 0 ) ) )  {
          ++  i  ;
          if ( i >= ( sh . message_array . length ) )
            break ; }
        if ( i >= ( sh . message_array . length ) )
          break ;
        sh . buf3 . push ( sh . message_array [ i ] - 'R' . charCodeAt ( 0 ) ) ;
        ++  sh . buf3_index ;
        if ( sh . buf3_index < 3 )
          ++  i  ;
      } while ( sh . buf3_index < 3 ) ;
      if ( i >= ( sh . message_array . length ) )
        break ;
      sh . buf3_index = 0 ;
      let u16 = sh  . buf3 [ 0 ] + 40 * ( sh . buf3 [ 1 ] +
        40 * sh  . buf3 [ 2 ] ) ;
      sh . buf3 = [ ] ;
        let buf = [ u16 & 0xff , u16 >> 8 ] ;
        let secretdata = [
          buf [ 0 ] & 0b1111 ,
          ( buf [ 0 ] >> 4 ) & 0b1111 ,
          buf [ 1 ] & 0b1111 ,
          ( buf [ 1 ] >> 4 ) & 0b1111 ] ;
        let decrypteddata = [ ] ;
        js_shifr_decrypt_sole2 ( secretdata , sh . deshia , decrypteddata ,
          sh . old_last_sole , sh . old_last_data ) ;
        sh  . message . push ( ( decrypteddata [ 0 ] & 0b11  ) |
          ( ( decrypteddata [ 1 ] & 0b11  ) << 2  ) |
          ( ( decrypteddata [ 2 ] & 0b11  ) <<  4 ) |
          ( ( decrypteddata [ 3 ] & 0b11  ) << 6  ) ) ; } // for $i    
    }
  else {
    // binary
    for ( let i = 0 ; i < sh . message_array . length - 1 ; i += 2 ) {
      let secretdata = [
        ( sh . message_array [ i ] ) & 0xf ,
        ( ( sh . message_array [ i ] ) >> 4 ) & 0xf ,
        ( sh . message_array [ i + 1 ] ) & 0xf ,
        ( ( sh . message_array [ i + 1 ] ) >> 4 ) & 0xf ] ;
      let decrypteddata = [ ] ;
      js_shifr_decrypt_sole2 ( secretdata , sh . deshia , decrypteddata ,
        sh . old_last_sole , sh . old_last_data ) ;
      sh . message . push ( ( decrypteddata [ 0 ] & 0b11 ) |
          ( ( decrypteddata [ 1 ] & 0b11 ) << 2  ) |
          ( ( decrypteddata [ 2 ] & 0b11 ) <<  4 ) |
          ( ( decrypteddata [ 3 ] & 0b11 ) << 6  ) ) ; } } }

// читаю 6 бит
// 6 bits reads
// from sh . message_array to => encrypteddata
let js_isEOFstreambuf_read6bits   = function  ( sh , encrypteddata ) {
  if  ( ( ! sh . flagtext ) && ( sh . in_bufbitsize >= 6 ) ) {
    sh . in_bufbitsize -=  6 ;
    encrypteddata . push ( sh . in_buf & ( 0x40 - 1 ) ) ;
    sh . in_buf  >>= 6 ;
    return  false ; }
  if ( sh . decode_read_index >= ( sh . message_array . length ) )
    return true ;
  let reads = ( sh . message_array [ sh . decode_read_index ] ) ;
  ++  ( sh . decode_read_index ) ;
  if  ( sh . flagtext ) {
    // читаем одну букву ';'-'z' -> декодируем в шесть бит
    // reads one letter ';'-'z' -> decode to six bits
    while ( ( reads < ( ';' . charCodeAt ( 0 ) ) ) ||
      ( reads > ( 'z' . charCodeAt ( 0 ) ) ) ) {
      if ( sh . decode_read_index >= ( sh . message_array . length ) )
        return true ;
      reads = ( sh . message_array [ sh . decode_read_index ] ) ;
      ++ ( sh . decode_read_index ) ; }
    encrypteddata . push ( reads - ( ';' . charCodeAt ( 0 ) ) ) ; } // flagtext
  else  {
    encrypteddata . push ( ( sh . in_buf | 
      ( reads <<  sh . in_bufbitsize ) ) & ( 0x40 - 1 ) ) ;
    sh . in_buf = reads >>  ( 6 - sh . in_bufbitsize ) ;
    sh . in_bufbitsize +=  2 ; } // + 8 - 6
  return  false ; }

// версия 3 пишу три бита для расшифровки
// version 3 write three bits to decode
let js_streambuf_write3bits = function  ( sh , decrypteddata ) {
  if  ( sh . out_bufbitsize < 5 ) {
    sh . out_buf |= ( decrypteddata << ( sh . out_bufbitsize ) ) ;
    sh . out_bufbitsize +=  3 ; }
  else  {
    let to_write  = ( ( decrypteddata << sh . out_bufbitsize ) |
      ( sh . out_buf ) ) & 0xff ;
    sh . messageout . push ( to_write ) ;
    // + 3 - 8
    sh . out_bufbitsize -= 5 ;
    sh . out_buf = decrypteddata >> ( 3 - ( sh . out_bufbitsize ) ) ; } }
  
// from sh . message_array to => sh . message
let js_shifr_decrypt3 = function  ( sh  ) {
  let secretdata = [ ]  ;
  sh .  messageout = [ ]  ;
  sh .  decode_read_index  = 0 ;
  while ( ! js_isEOFstreambuf_read6bits ( sh , secretdata ) ) {
    let decrypteddata = [ ] ;
    js_shifr_decrypt_sole3 ( secretdata , sh . deshia , decrypteddata ,
      sh . old_last_sole ,  sh . old_last_data ) ;
    secretdata = [ ] ;
    js_streambuf_write3bits ( sh , decrypteddata [ 0 ] ) ; }
  sh . message = sh . messageout ; }

// [0x00,0xf0,0x0f,0xff] => "aa" + "ap" + "pa" + "pp"
let js_bytearray_to_string_univer = function  ( array , start_letter  , bits_count  ) {
  const acode = start_letter  . charCodeAt  ( 0 ) ;
  let str = ''  ;
  let cache = 0 ;
  let cache_size  = 0 ; //  0 .. (bits_count - 1)
  const bitmask = ( ( 1 <<  bits_count  ) - 1 ) ;
  for ( let byte  of  array ) {
    cache |= ( byte  <<  cache_size  )  ;
    cache_size  +=  8 ;
    do  {
      str +=  String  . fromCharCode  ( acode + ( cache & bitmask ) ) ;
      cache >>= bits_count  ;
      cache_size  -=  bits_count  ;
    } while ( cache_size  >=  bits_count  ) ; }
  if  ( cache_size  !=  0 )
    str +=  String  . fromCharCode  ( acode + cache ) ;
  return  str ; }
  
/*
// Base16 = [ abcd efgh ijkl mnop ]
let js_bytearray_to_string  = function  ( array ) {
  return  js_bytearray_to_string_univer ( array , 'a' , 4 ) ; }
*/  

/*
Base32 = ( ABCD EFGH IJKL MNOP
           QRST UVWX YZ[\ ]^_` )
*/
/*
let js_bytearray_to_string = function  ( array ) {
  return  js_bytearray_to_string_univer ( array , 'A' , 5 ) ; }
*/

/*
Base64 = ( ;<=> ?@AB CDEF GHIJ
           KLMN OPQR STUV WXYZ
           [\]^ _`ab cdef ghij
           klmn opqr stuv wxyz )
*/
let js_bytearray_to_string = function  ( array ) {
  return  js_bytearray_to_string_univer ( array , ';' , 6 ) ; }
