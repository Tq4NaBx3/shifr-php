// returns [ 0..15 , 0..14 , ... , 0..2 , 0..1 ]
let js_shifr_generate_pass2 = function (  ) {
  let dice  = new Array ( ) ;
  let i = 16  ;
  do  {
    let r = Math  . floor ( Math  . random  ( ) * i ) ;
    dice  . push  ( r ) ;
    --  i ;
  } while ( i >= 2 ) ;
  return  dice  ; }

// returns [ 0..63 , 0..62 , ... , 0..2 , 0..1 ]
let js_shifr_generate_pass3 = function  ( ) {
  let dice  = new Array ( ) ;
  let i = 64  ;
  do  {
    let r = Math  . floor ( Math  . random  ( ) * i ) ;
    dice  . push  ( r ) ;
    --  i ;
  } while ( i >= 2 ) ;
  return  dice  ; }

// get 4*2 bits => push 4*4 bits
let js_shifr_data_sole2 = function  ( secret_data ) {
  let secret_data_sole  = new Array ( ) ;
  let ra  = Math  . floor ( Math  . random  ( ) * 0x100 ) ; // 4*2 = 8 bits
  for ( let da  of  secret_data ) {
    secret_data_sole  . push  ( ( da  << 2 ) | ( ra & 0x3 ) ) ;
    ra >>= 2 ; }
  return  secret_data_sole  ; }
  
// get 2*3 bits => push 2*6 bits
// get 3*3 bits => push 3*6 bits
let js_shifr_data_sole3 = function  ( secret_data ) {
  let secret_data_sole  = new Array ( ) ;
  let ra  = Math  . floor ( Math  . random  ( ) * 0x200 ) ; // 3*3 = 9 bits
  for ( let da  of  secret_data ) {
    secret_data_sole  . push  ( ( da  << 3 ) | ( ra & 0x7 ) ) ;
    ra >>= 3 ; }
  return  secret_data_sole  ; }

// byte = 76543210b to array
// [ 0 ] = 10 ; [ 1 ] = 32 ; [ 2 ] = 54 ; [ 3 ] = 76
let shifr_byte_to_array2 =  function  ( byte ) {
  let arr = new Array ( ) ;
  let i = 0 ;
  do  {
    arr . push ( byte & 0x3 ) ;
    byte >>= 2 ;
    ++  i ;
  } while ( i < 4 ) ;
  return  arr ; }
