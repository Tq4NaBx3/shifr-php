// ver 2

let shif  = { } ;
js_shifr_init ( shif ) ;
js_shifr_password_set ( shif , "qwerty" ) ;
shif . message = "Lambda" ;
js_shifr_encrypt ( shif ) ;
console . log ( "message = " + shif . message )  ;
// message = gYfhZUaplgk^WtjVuW
shif . message = " !" ;
js_shifr_encrypt ( shif ) ;
console . log ( "message = " + shif . message )  ;
// message = ^idiRu
shif . message = 'gYfhZUaplgk^WtjVuW' + '^idiRu' ;
js_shifr_salt_init ( shif ) ;
js_shifr_decrypt ( shif ) ;
console . log ( "message = " + shif . message )  ;
// message = 76,97,109,98,100,97,32,33
console . log ( "js_Utf8ArrayToStr(message) = " +
  js_Utf8ArrayToStr ( shif . message ) )  ;
// js_Utf8ArrayToStr(message) = Lambda !

// ver 3
  
shif  = { } ;
js_shifr_init ( shif ) ;
js_shifr_set_version ( shif , 3 ) ;
js_shifr_password_set ( shif , "qwerty" ) ;
shif . message = "Lambda" ;
js_shifr_encrypt ( shif ) ;
console . log ( "message = " + shif . message )  ;
// message = ZWldumz`DA<r[UTb
shif . message = " !" ;
js_shifr_encrypt ( shif ) ;
console . log ( "message = " + shif . message )  ;
// message = HablF
js_shifr_flush ( shif ) ;
console . log ( "message = " + shif . message )  ;
// message = HablFx
shif . message = 'ZWldumz`DA<r[UTb' + 'HablFx' ;
js_shifr_salt_init ( shif ) ;
js_shifr_decrypt ( shif ) ;
console . log ( "message = " + shif . message )  ;
// message = 76,97,109,98,100,97,32,33
console . log ( "js_Utf8ArrayToStr(message) = " +
  js_Utf8ArrayToStr ( shif . message ) )  ;
// js_Utf8ArrayToStr(message) = Lambda !
