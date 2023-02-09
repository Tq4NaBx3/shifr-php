<?php
session_start ( ) ;
require ( 'shifr.php' ) ;
$shifr  = new shifr ( ) ;
shifr_init ( $shifr  ) ;

$local = setlocale ( LC_ALL  , 'ru_RU.UTF-8'  ) ;
if ( $local == 'ru_RU.UTF-8' )
  $shifr -> localerus = true ;

if  ( $_POST  ) {

  if ( ( ( isset ( $_POST [ 'Encryption_in_text_mode' ] ) ) and 
       $_POST [ 'Encryption_in_text_mode' ] == '1' ) )
    $shifr -> flagtext = true  ;
  else
    $shifr -> flagtext = false ;

  if ( isset ( $_POST [ 'message'  ] ) )
    $shifr -> message = $_POST  [ 'message'  ] ;
  
  if  ( isset ( $_POST  [ 'radio' ] ) ) {
    switch  ( $_POST  [ 'radio' ] ) {
    case  'ASCII' :
      $shifr -> letters_mode = 95 ;
      break ;
    case  'LettDigit' :
      $shifr -> letters_mode = 62 ;
      break ;
    case  'SmallLetters' :
      $shifr  ->  letters_mode  = 26  ;
      break ;
    case  'Digit' :
      $shifr -> letters_mode = 10 ;
      break ;
    default :
      echo  "\$_POST  [ 'radio' ] = " . $_POST  [ 'radio' ] ;
      die ( ) ;
    } // switch radio
  }  // if radio
    
  if  ( isset ( $_POST  [ 'radiokey' ] ) ) {
    if (  $_POST  [ 'radiokey' ] == 'Key296' ) 
      shifr_set_version ( $shifr ,  3 ) ;  
    else 
      shifr_set_version ( $shifr  , 2 ) ;
  }  
  
  if ( isset ( $_POST  [ 'password_generate' ] ) ) {
    shifr_generate_password ( $shifr  ) ;
    if  ( $shifr -> flag_debug  )
      $shifr -> array_log [ ] = 'index : generate password `' .
        $shifr -> password . '`' ;
  } else  if ( isset ( $_POST [ 'password'  ] ) ) {
    shifr_password_set  ( $shifr  , $_POST [ 'password'  ] ) ;
    if  ( $shifr -> flag_debug  )
      $shifr -> array_log [ ] = 'index : set password `' .
        $_POST [ 'password'  ] . '`' ;
  }
  
  if  ( isset ( $_POST  [ 'encrypt_decrypt_name'  ] ) ) {
    if  ( $_POST  [ 'encrypt_decrypt_name'] == 'зашифровать' or 
      $_POST  [ 'encrypt_decrypt_name'  ] == 'encrypt'  ) {
      $shifr -> flagtext = true  ;
      shifr_encrypt ( $shifr ) ; 
      shifr_flush ( $shifr  ) ;
    } else if  ( $_POST  [ 'encrypt_decrypt_name'] == 'расшифровать' or 
      $_POST  [ 'encrypt_decrypt_name'  ] == 'decrypt'  ) 
      shifr_decrypt ( $shifr ) ; 
    else  if  ( $_POST  [ 'encrypt_decrypt_name'] == 'загрузить' or 
      $_POST  [ 'encrypt_decrypt_name'  ] == 'load'  ) {
    } else if ( ( $_POST  [ 'encrypt_decrypt_name'] == 'Зашифровать файл' or 
        $_POST  [ 'encrypt_decrypt_name'  ] == 'Encrypt file'  ) and
      ( $_FILES [ 'uploadfile'  ] [ 'size' ] > 0 )/*isset ( $_FILES [ 'uploadfile'  ] ) and
      ( ( ! isset($_FILES['file_upload']['error'] ) ) or
          $_FILES['file_upload']['error'] != UPLOAD_ERR_NO_FILE )*/) {
      include  ( './encrypt_file.php' ) ;
      die ( ) ;
    } else if  ( ( $_POST  [ 'encrypt_decrypt_name' ] == 'Расшифровать файл' or 
        $_POST  [ 'encrypt_decrypt_name'  ] == 'Decrypt file' ) ) {
          // Encrypt file
      if ( $_FILES [ 'uploadfile'  ] [ 'size' ] > 0 ) {
        include  ( './decrypt_file.php' ) ;
        die ( ) ;
      } // if ( $_FILES [ 'uploadfile'  ] [ 'size' ] > 0 )
    }/*Decrypt file*/
  } // if  ( isset ( $_POST  [ 'encrypt_decrypt_name'  ] ) )
} // if  ( $_POST  )
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width">
<link rel="shortcut icon" href="shifr.ico" type="image/x-icon" />
<title>Shifr</title>
<style>
p { font-size: 36px; }
textarea { font-size: 36px; }
input { font-size: 36px; border-radius: 10px; }
input.largerCheckbox { transform : scale(2); }
label { font-size: 24px; }
legend { font-size: 24px; }
fieldset { font-size: 36px; border-radius: 10px; }
body  {
  width: 99%;
}
table.wide { width: 99%; }
</style>
</head>
<body>
<div id="shifrcode"></div>
<?php
$stringsec  = '' ;
$stringsec  .=  '<form action="' . $_SERVER  [ 'PHP_SELF'  ] .
  '" method="POST" enctype="multipart/form-data" id="form_id"  >
<table class="wide">
<tr>
  <td>
  <label>' . PHP_EOL  ;
$stringsec  .=  ( ( $shifr -> localerus ) ? 'Сообщение:' : 'Message:' ) ;
$stringsec  .=  '  </label>
  <textarea name="message" rows="12" cols="61" id="message" style="width: 99%"' .
  ' maxlength="2048000000">' . htmlspecialchars ( $shifr -> message ) . '</textarea>
  </td>
  <td>
<fieldset><legend><i>PHP</i></legend>
 <input type="submit" name="encrypt_decrypt_name" value=' .
  ( ( $shifr -> localerus ) ? '"зашифровать"' : "encrypt" ) . ' /><br>
 <input type="submit" name="encrypt_decrypt_name" value=' .
  ( ( $shifr -> localerus ) ? '"расшифровать"' : "decrypt" ) . ' /><br>
</fieldset><br>
<fieldset><legend><i>JavaScript</i></legend>
 <input type="button" name="submit2" value='  .
  ( ( $shifr -> localerus ) ? '"зашифровать"' : '"encrypt"' ) . ' id="encrypt2" /><br>
 <input type="button" name="submit2" value='  .
  ( ( $shifr -> localerus ) ? '"расшифровать"' : '"decrypt"' ) . ' id="decrypt2" /><br>
</fieldset><br>
  </td>
<tr>
</table>
<table style="width: 99%">
<tr>
<td>
<fieldset>
<legend>' . PHP_EOL ;
if  ( $shifr -> localerus )
  $stringsec  .= 'Алфавит знаков в пароле' ;
else
  $stringsec  .= 'Alphabet of characters in a password' ;
$stringsec  .=  '</legend>
<input type="radio" name="radio" value="ASCII" id="passalpha" '  ;
if ( $shifr -> letters_mode == 95 )
  $stringsec  .= 'checked' ;
$stringsec  .= ' >ASCII ' ;
if  ( $shifr -> localerus ) 
  $stringsec  .= 'буквы цифры знаки пробел' ;
else
  $stringsec  .= 'letters digits signs space' ;
$stringsec  .= '<br>
<input type="radio" name="radio" value="LettDigit" id="passlettersdigits" ' ;
if ( $shifr -> letters_mode == 62 ) 
  $stringsec  .= 'checked' ;
$stringsec  .= ' >' ;
if  ( $shifr -> localerus )
  $stringsec  .= 'цифры и буквы'  ;
else 
  $stringsec  .= 'digits and letters' ;
$stringsec  .= '<br>
<input type="radio" name="radio" value="SmallLetters" id="passSmallLetters" ' ;
if ( $shifr -> letters_mode == 26 )
  $stringsec  .= 'checked' ;
$stringsec  .= ' >' ;
if  ( $shifr -> localerus )
  $stringsec  .= 'маленькие буквы'  ;
else 
  $stringsec  .= 'small letters' ;
$stringsec  .= '<br>
<input type="radio" name="radio" value="Digit" id="passdigits" ' ;
if ( $shifr -> letters_mode == 10 )
  $stringsec  .= 'checked' ;
$stringsec  .=  ' >' ;
if  ( $shifr -> localerus )
  $stringsec  .= 'цифры'  ;
else
  $stringsec  .= 'digits' ;
$stringsec  .=  '</fieldset>
</td>
<td>
<fieldset>
<legend>' . PHP_EOL ;
if  ( $shifr -> localerus )
  $stringsec  .= 'Размер ключа'  ;
else
  $stringsec  .= 'Key size' ;
$stringsec  .= '</legend>
<input type="radio" name="radiokey" value="Key45" id="keysize45" ' ;
if ( shifr_version  ( $shifr  ) == 2 )
  $stringsec  .= 'checked' ;
$stringsec  .=  ' >45 ' ;
if  ( $shifr -> localerus )
  $stringsec  .= 'бит , 6-14 букв' ;
else
  $stringsec  .= 'bits , 6-14 letters' ;
$stringsec  .= '<br>
<input type="radio" name="radiokey" value="Key296" id="keysize296" ' ;
if ( shifr_version  ( $shifr  ) == 3 )
  $stringsec  .= 'checked' ;
$stringsec  .=  ' >296 '  ;
if  ( $shifr -> localerus )
  $stringsec  .= 'бит , 45-90 букв' ;
else
  $stringsec  .= 'bits , 45-90 letters' ;
$stringsec  .= '</fieldset>
</td>
</tr>
</table>
<br>
<label>' . PHP_EOL ;
if  ( $shifr -> localerus )
  $stringsec  .= 'Ваш пароль : '  ;
else
  $stringsec  .= 'Your password : ' ;
$stringsec  .= '<table>
  <tr>
    <td>
      <fieldset>
        <legend><i>PHP</i></legend>
        <input type="submit" name="password_generate" value="' ;
          if  ( $shifr -> localerus )        
            $stringsec  .= 'генерировать' ;
          else
            $stringsec  .= 'generate' ;
$stringsec  .= '" id="generate_id" />
      </fieldset>
    </td>
    <td>
      <fieldset>
        <legend><i>JavaScript</i></legend>
        <input type="button" name="submit2" value="' ;
          if  ( $shifr -> localerus )        
            $stringsec  .= 'генерировать' ;
          else
            $stringsec  .= 'generate' ;
$stringsec  .= '" id="generate2" />
      </fieldset>
    </td>
  </tr>
</table>
</label>
<br>
<div style="width: 99%">
<input name="password" type="password" value="' ;
$stringsec  .= htmlspecialchars ( shifr_password_get ( $shifr ) ) ;
$stringsec  .= '" size ="60" id="password" style="width: 99%" />
</div>
<br>' . PHP_EOL ;
if  ( $shifr -> localerus )
  $stringsec  .= 'Показать пароль' ;
else
  $stringsec  .= 'Show password' ;
$stringsec  .= ' : <input type="checkbox" class="largerCheckbox" name="' ;
if  ( $shifr -> localerus )
  $stringsec  .=  'Показать_пароль' ;
else
  $stringsec  .= 'Show_password' ;
$stringsec  .= '" value="0" id="showpassword" onchange="fshowpassword()" />
<br>
<hr>
<br>' . PHP_EOL ;
if  ( $shifr -> localerus )
  $stringsec  .= 'Шифрование в текстовом режиме' ;
else 
  $stringsec  .= 'Encryption in text mode' ;
$stringsec  .= ' : <input type="checkbox"' .
  ' class="largerCheckbox" name="Encryption_in_text_mode" value="1" id="SText" ' ;
if  ( $shifr -> flagtext  )
  $stringsec  .= 'checked' ;
$stringsec  .=  ' />
<div class="fieldsetclassphp" id="iddivfieldsetclassphp"  style="width: 99%">
<fieldset id="idfieldsetclassphp" >
<legend><i>PHP</i></legend>
<input type="hidden" name="MAX_FILE_SIZE" value="2048000000" />
<input type=file name=uploadfile><br>
<input type=submit name="encrypt_decrypt_name" value="' ;
if  ( $shifr -> localerus )
  $stringsec  .= 'Зашифровать файл' ;
else
  $stringsec  .= 'Encrypt file' ;
$stringsec  .= '" >
<input type=submit name="encrypt_decrypt_name" value="' ;
if  ( $shifr -> localerus )
  $stringsec  .= 'Расшифровать файл' ;
else
  $stringsec  .= 'Decrypt file' ;
$stringsec  .= '" >
</fieldset>
</div>
</form>
<form method="POST" enctype="multipart/form-data" id="form_file">
<div class="fieldsetclassjavascript" ' .
  'id="iddivfieldsetclassjavascript"  style="width: 99%">
<fieldset>
<legend><i>JavaScript</i></legend>
<input type="hidden" name="MAX_FILE_SIZE" value="2048000000" />
<input type=file name="js_uploadfile_name" ' .
  'onchange="js_readFile(this)" id="js_inputfile_id"><br>
<input type="submit" name="encrypt3" value="' ;
if  ( $shifr -> localerus )
  $stringsec  .=  'Зашифровать файл' ;
else
  $stringsec  .=  'Encrypt file' ;
$stringsec  .=  '" id="encrypt3" >
<input type="button" name="decrypt3" value="' ;
if  ( $shifr -> localerus )
  $stringsec  .=  'Расшифровать файл' ;
else
  $stringsec  .=  'Decrypt file' ;
$stringsec  .=  '" id="decrypt3" >
</fieldset>
</div>' . PHP_EOL ; // fieldsetclassjavascript
$stringsec  .=  '<br>
<textarea name="boxes_info"  rows="2" cols="61" id="boxes_info" value = "" ' .
  'maxlength="2048000000" readonly hidden >' . htmlspecialchars ( $shifr -> boxes_info  ) .
  '</textarea>
<textarea name="filename_name" rows="1" cols="61" id="filename_id" value = "" maxlength="2048" ' .
  'readonly hidden >' . htmlspecialchars  ( $shifr -> filename  ) . '</textarea>
<input type="checkbox" name="text_mode" value="1" id="JSText" ' ;
  if ( $shifr -> flagtext )
    $stringsec  .=  'checked ' ;
$stringsec  .=  'hidden />
</form>' . PHP_EOL ;
$shifrhtml  = new shifr ( ) ;
  shifr_init ( $shifrhtml  ) ;
  shifr_set_version ( $shifrhtml , 2 ) ;
  $shifrhtml -> flagtext = true ;
  $shifrhtml -> letters_mode = shifr :: letters_mode_Letter ;
  $secrethtmlpsw = 'qwertyuiop' ;
  shifr_password_set ( $shifrhtml , $secrethtmlpsw ) ;
  $shifrhtml -> message = $stringsec ;
  shifr_encrypt ( $shifrhtml ) ;
  shifr_flush ( $shifrhtml  ) ;
?>
<script>
'use strict';
</script>
<script type="text/javascript" src="shifr.js?time=<?=time();?>"></script>
<script>
let stext = '<?php
  echo str_replace ( "\n" , "' +" . PHP_EOL . " '" , $shifrhtml -> message ) ;
  ?>' ;
let js_shifrhtml  = { } ;
js_shifr_init ( js_shifrhtml ) ;
js_shifr_set_version ( js_shifrhtml , 2 ) ;
js_shifrhtml  . flagtext  = true ;
js_shifrhtml  . letters_mode  = 26  ;
js_shifr_password_set ( js_shifrhtml , '<?php echo $secrethtmlpsw ; ?>' ) ;
js_shifrhtml  . message = stext ;
js_shifr_decrypt ( js_shifrhtml ) ;
document . getElementById ( 'shifrcode' ) . innerHTML = js_Utf8ArrayToStr ( js_shifrhtml  . message ) ;

  let js_shifr  = { } ;
  js_shifr_init ( js_shifr ) ;
  js_shifr . localerus = <?php
  if ( $shifr -> localerus )
    echo 'true' ;
  else
    echo 'false' ; ?> ;
<?php
if  ( $shifr -> flag_debug  )
  foreach ( $shifr -> array_log as $value  )
    echo  'js_shifr . array_log . push ( \'php : ' . str_replace (
      array  ( "\\" , "'" ) , array  ( "\\\\" , "\'" ) , $value ) . '\' ) ;' ;
?>

if ( document . getElementById ( 'keysize45' ) . checked )
  js_shifr_set_version  ( js_shifr  , 2 ) ;
else  if ( document . getElementById ( 'keysize296' ) . checked )
  js_shifr_set_version  ( js_shifr  , 3 ) ;

let fkeysize45  = function ( ) {
  js_shifr_set_version  ( js_shifr  , 2 ) ;
}
let chboxg_keys45 = document  . getElementById ( 'keysize45' ) ;
chboxg_keys45 . addEventListener  ( 'click' , fkeysize45 ) ;
let fkeysize296  = function ( ) {
  js_shifr_set_version  ( js_shifr  , 3 ) ;
}
let chboxg_keys296 = document  . getElementById ( 'keysize296' ) ;
chboxg_keys296 . addEventListener  ( 'click' , fkeysize296 ) ;

if ( document . getElementById ( 'passlettersdigits' ) . checked )
  js_shifr . letters_mode = 62 ;
else  if ( document . getElementById ( 'passalpha' ) . checked )
  js_shifr . letters_mode = 95 ;
else  if  ( document  . getElementById  ( 'passSmallLetters'  ) . checked )
  js_shifr  . letters_mode  = 26  ;
else  if ( document . getElementById ( 'passdigits' ) . checked )
  js_shifr . letters_mode = 10 ;

let falpha95  = function ( ) {
  js_shifr . letters_mode = 95 ;
}
document  . getElementById ( 'passalpha' ) . addEventListener  ( 'click' , falpha95 ) ;

let falpha62  = function ( ) {
  js_shifr . letters_mode = 62 ;
}
document  . getElementById ( 'passlettersdigits' ) . addEventListener  ( 'click' , falpha62 ) ;

let falpha26  = function  ( ) {
  js_shifr  . letters_mode  = 26  ;
}
document  . getElementById  ( 'passSmallLetters'  ) . addEventListener  ( 'click' , falpha26  ) ;

let falpha10  = function ( ) {
  js_shifr . letters_mode = 10 ;
}
document  . getElementById ( 'passdigits' ) . addEventListener  ( 'click' , falpha10 ) ;

let fgenerate = function ( ) {
  js_shifr_generate_password  ( js_shifr  ) ;
  document . getElementById ( 'password' ) . value  = js_shifr_password_get ( js_shifr ) ;
}

let chboxg = document.getElementById('generate2');
chboxg.addEventListener('click', fgenerate ) ;

document  . forms [ 'form_id' ] [ 'password' ] . type  =
  ( ( document  . getElementById  ( 'showpassword'  ) . checked ) ?
    'text'  : 'password'  ) ;
let fshowpassword = function ( ) {
  let chbox = document.getElementById('showpassword');
	if (chbox.checked) {
    document  . forms [ 'form_id' ] [ 'password' ] . type  = 'text'  ;
    chbox.setAttribute("checked", "checked" );
  }	else {
    document  . forms [ 'form_id' ] [ 'password' ] . type  = 'password'  ;
    chbox.removeAttribute("checked");
  }
}
js_shifr_password_set ( js_shifr , document . getElementById ( 'password' ) . value ) ;
js_shifr  . message = document . getElementById ( 'message' ) . value ;
js_shifr  . flagtext  = document . getElementById ( 'SText' ) . checked ;
let fencrypt = function ( ) {

  document . getElementById ( 'SText' ) . checked = true ;
  js_shifr  . flagtext  = true  ;
  js_shifr_password_set ( js_shifr , document . getElementById ( 'password' ) . value ) ;
  js_shifr  . message = document . getElementById ( 'message' ) . value ;

  js_shifr_salt_init  ( js_shifr ) ;
  js_shifr_encrypt ( js_shifr ) ;
  js_shifr_flush ( js_shifr  ) ;

  document . getElementById ( 'message' ) . value  = js_shifr  . message ;
}
let fdecrypt = function ( ) {

  document . getElementById ( 'SText' ) . checked = true ;
  js_shifr  . flagtext  = true  ;
  js_shifr_password_set ( js_shifr , document . getElementById ( 'password' ) . value ) ;
  js_shifr  . message = document . getElementById ( 'message' ) . value ;

  js_shifr_salt_init  ( js_shifr ) ;
  js_shifr_decrypt ( js_shifr ) ;
  js_shifr . message  = js_Utf8ArrayToStr ( js_shifr  . message ) ;

  document . getElementById ( 'message' ) . value  = js_shifr  . message ;

}
let chbox_enc = document  . getElementById  ( 'encrypt2'  ) ;
chbox_enc . addEventListener  ( 'click' , fencrypt ) ;
let chbox_dec = document  . getElementById  ( 'decrypt2'  ) ;
chbox_dec . addEventListener  ( 'click' , fdecrypt ) ;

let js_readFile = function  ( input ) {
  if ( input  . files . length > 0 ) {
    let file = input  . files [ 0 ] ;
    let filename  = document  . getElementById  ( 'filename_id' ) ;
    filename  . value = input . files [ 0 ] . name  ;
    let reader = new FileReader();

    reader.onload = function() {
      let buffer  = reader.result ;

      let view = new Uint8Array(buffer);

      js_shifr  . message = Array . from  ( view ) ;

// ! save file name for post_file ?
// ... = document . getElementById ( 'filename_id' ) . value
      // document . getElementById ( 'filename_id' ) . value = null ;

    } ;

    reader.onerror = function() {
      alert(reader.error);
    };

    reader.readAsArrayBuffer(file);
  }
}

let fencrypt3 = function  ( ) {
  if ( document . getElementById ( 'SText' ) . checked )
    js_shifr  . flagtext  = true  ;
  else
    js_shifr  . flagtext  = false ;
  document . getElementById ( 'JSText' ) . checked  = js_shifr  . flagtext ;

  js_shifr_password_set ( js_shifr , document . getElementById ( 'password' ) . value ) ;

  js_shifr_salt_init  ( js_shifr ) ;
  js_shifr_encrypt ( js_shifr ) ;
  js_shifr_flush ( js_shifr  ) ;
  if ( !  js_shifr  . flagtext )
    js_shifr . message  = js_shifr_Base64_encode ( js_shifr  . message ) ;
  let boxinfo = document . getElementById ( 'boxes_info' ) ;
  boxinfo . value = js_shifr  . message ;

  document  . forms [ 'form_file' ] . action  = 'post_file.php' ;
  document  . forms [ 'form_file' ] . submit  ( ) ;
}

let fdecrypt3 = function ( ) {
  if ( document . getElementById ( 'SText' ) . checked )
    js_shifr  . flagtext  = true  ;
  else
    js_shifr  . flagtext  = false ;
  document . getElementById ( 'JSText' ) . checked  = js_shifr  . flagtext ;

  js_shifr_password_set ( js_shifr , document . getElementById ( 'password' ) . value ) ;

  js_shifr_salt_init  ( js_shifr ) ;
  js_shifr_decrypt ( js_shifr ) ;
  js_shifr . message  = js_shifr_Base64_encode ( js_shifr  . message ) ;

  let boxinfo = document . getElementById ( 'boxes_info' ) ;
  boxinfo . value = js_shifr  . message ;

  document  . forms [ 'form_file' ] . action  = 'post_defile.php' ;
  document  . forms [ 'form_file' ] . submit ( ) ;
}

let chbox_fdec = document  . getElementById  ( 'decrypt3'  ) ;
chbox_fdec . addEventListener  ( 'click' , fdecrypt3 ) ;

  let chbox_fenc = document  . getElementById  ( 'encrypt3'  ) ;
  chbox_fenc . addEventListener  ( 'click' , fencrypt3 ) ;  
</script>
  </body>
</html>
