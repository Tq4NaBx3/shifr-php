function DecryptString ( str , psw ) {
  let js_shifr_js  = { } ;
  js_shifr_init ( js_shifr_js ) ;
  js_shifr_set_version ( js_shifr_js , 2 ) ;
  js_shifr_js  . flagtext  = true ;
  js_shifr_js  . letters_mode  = 26  ;
  js_shifr_password_set ( js_shifr_js , psw ) ;
  js_shifr_js  . message = str ;
  js_shifr_decrypt ( js_shifr_js ) ;
  return  js_Utf8ArrayToStr ( js_shifr_js  . message )  ;
}
