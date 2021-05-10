<!DOCTYPE html>
<?php

if  ( isset ( $_POST  )  )  {
	if ( isset ( $_POST  [ 'files' ] ) and ( $_POST  [ 'files' ] == 'файлы' or
    $_POST  [ 'files' ] == 'files') ) {
   	header  ( "Location: ./uploads/"  ) ;
   	die ( ) ; } }

require ( 'shifr.php' ) ;
$shifr  = new shifr ( ) ;
shifr_init ( $shifr  ) ;

$local = setlocale ( LC_ALL  , 'ru_RU.UTF-8'  ) ;
if ( $local == 'ru_RU.UTF-8' )
  $shifr -> localerus = true ;

if  ( $_POST  ) {

  if ( ( ( isset ( $_POST [ 'Шифрование_в_текстовом_режиме' ] ) ) and
        $_POST [ 'Шифрование_в_текстовом_режиме' ] ) or
    ( ( isset ( $_POST [ 'Encryption_in_text_mode' ] ) ) and 
       $_POST [ 'Encryption_in_text_mode' ] ) )
    $shifr -> flagtext = true  ;
  else
    $shifr -> flagtext = false ;

  if ( isset ( $_POST [ 'password'  ] ) )
    $shifr -> password = $_POST [ 'password'  ] ;

  if ( isset ( $_POST [ 'message'  ] ) )
    $shifr -> message = $_POST  [ 'message'  ] ;
  
  if  ( isset ( $_POST  [ 'submit'  ] ) ) {
    if  ( isset ( $_POST  [ 'radio' ] ) ) {
      switch  ( $_POST  [ 'radio' ] ) {
      case  'ASCII' :
        $shifr -> letters_mode = 95 ;
        break ;
      case  'LettDigit' :
        $shifr -> letters_mode = 62 ;
        break ;
      case  'Digit' :
        $shifr -> letters_mode = 10 ;
        break ;
      default :
        echo  "\$_POST  [ 'radio' ] = " . $_POST  [ 'radio' ] ;
        die ( ) ; } }
    if  ( isset ( $_POST  [ 'radiokey' ] ) ) {
      if (  $_POST  [ 'radiokey' ] == 'Key296' ) 
        shifr_set_version ( $shifr ,  3 ) ;  
      else 
        shifr_set_version ( $shifr  , 2 ) ; }
    if  ( $_POST  [ 'submit'] == 'зашифровать' or 
      $_POST  [ 'submit'  ] == 'encrypt'  ) {
      $shifr -> flagtext = true  ;
      shifr_password_load ( $shifr ) ;
      shifr_encrypt ( $shifr ) ; 
      shifr_flush ( $shifr  ) ; }
  else if  ( $_POST  [ 'submit'] == 'расшифровать' or 
      $_POST  [ 'submit'  ] == 'decrypt'  ) {
      shifr_password_load ( $shifr ) ;
      shifr_decrypt ( $shifr ) ; }
  else  if  ( $_POST  [ 'submit'] == 'генерировать' or 
      $_POST  [ 'submit'  ] == 'generate'  ) 
      shifr_generate_password ( $shifr  ) ;
  else  if  ( $_POST  [ 'submit'] == 'загрузить' or 
      $_POST  [ 'submit'  ] == 'load'  ) 
      {   }
   else
   if ( ( $_POST  [ 'submit'] == 'Зашифровать файл' or 
        $_POST  [ 'submit'  ] == 'Encrypt file'  ) and
      ( $_FILES [ 'uploadfile'  ] [ 'size' ] > 0 ) ) {
      $fp = fopen ( $_FILES [ 'uploadfile'  ] [ 'tmp_name'  ] , 'rb'  ) ;
      if ( $fp ) {
      $uploaddir = './uploads/' ;
      $uploadfile = $uploaddir  . basename  (
        $_FILES [ 'uploadfile'  ] [ 'name'  ] ) ;
      $fpw = fopen ( $uploadfile . '.shi' , 'wb'  ) ;
      shifr_password_load ( $shifr ) ;
      while ( ! feof  ( $fp ) ) {
        set_time_limit  ( 60  ) ;
        $shifr -> message = fread ( $fp , 0x1000 ) ;
        if ( ! $shifr -> message )
          break ;
        shifr_encrypt ( $shifr ) ; 
        fwrite  ( $fpw , $shifr -> message ) ; }
      shifr_flush_file  ( $shifr , $fpw ) ;
      fclose  ( $fpw  ) ;
      fclose  ( $fp ) ;
      if  ( $shifr -> localerus )
        $shifr -> message = 'зашифрованный файл сохранён с именем : ' ;
      else
        $shifr -> message = 'encrypted file saved with name : ' ;
      $shifr -> message .= "'". $uploadfile . '.shi'."'\n" ; }
      else {
        if  ( $shifr -> localerus )
          $shifr -> message = 'ошибка доступа к файлу : ' ;
        else
          $shifr -> message = 'permission denied to file : ' ;
        $shifr -> message .= "'". $uploadfile . "'\n" ; } }
   else
   if  ( ( $_POST  [ 'submit' ] == 'Расшифровать файл' or 
        $_POST  [ 'submit'  ] == 'Decrypt file' ) ) {
      if ( $_FILES [ 'uploadfile'  ] [ 'size' ] > 0 ) {
      $fp = fopen ( $_FILES [ 'uploadfile'  ] [ 'tmp_name'  ] , 'rb'  ) ;
      $uploaddir = './uploads/' ;
      $uploadfile = $uploaddir  . basename  (
        $_FILES [ 'uploadfile'  ] [ 'name'  ] ) ;
      $len_uploadfile = strlen ( $uploadfile ) ;
      if ( $len_uploadfile > 4 and substr ( $uploadfile , -4 ) == '.shi' )
        $uploadfile2 = substr ( $uploadfile , 0 , -4 ) ;
      else 
        $uploadfile2 = $uploadfile  . '.des' ;
      $fpw = fopen ( $uploadfile2 , 'wb'  ) ;
      shifr_password_load ( $shifr ) ;
      while ( ! feof  ( $fp ) ) {
        set_time_limit  ( 60  ) ;
        $shifr -> message = fread ( $fp , 0x1000 ) ;
        if ( ! $shifr -> message )
          break ;
        shifr_decrypt ( $shifr ) ; 
        fwrite  ( $fpw , $shifr -> message ) ; }
      fclose  ( $fpw  ) ;
      fclose  ( $fp ) ;
      if  ( $shifr -> localerus )
        $shifr -> message = 'расшифрованный файл сохранён с именем : ' ;
      else
        $shifr -> message = 'decrypted file saved with name : ' ;
      $shifr -> message .= "'". $uploadfile2 ."'\n" ;  }
    else { }} } }
?>
<script src="shifr.js"></script>
<script>
let js_shifr  = { } ;
js_shifr_init ( js_shifr ) ;
js_shifr . localerus = <?php
  if ( $shifr -> localerus )
    echo 'true' ;
  else
    echo 'false' ; ?> ;
</script>
<style> p { font-size: 36px; }  textarea { font-size: 36px; }
input { font-size: 36px; border-radius: 10px; }
input.largerCheckbox { transform : scale(2); }
label { font-size: 24px; }
legend { font-size: 24px; }
fieldset { font-size: 36px; border-radius: 10px; }
.menu {
    position: fixed; /* Фиксированное положение */
    right: 10px; /* Расстояние от правого края окна браузера */
    top: 10%; /* Расстояние сверху */
    padding: 10px; /* Поля вокруг текста */ 
    background: #eef; /* Цвет фона */ 
    border: 1px solid #333; /* Параметры рамки */ 
  }
</style>
<html>
<body>
<?php
if (isset($err))
  echo '<p>err = \''.$err.'\'</p>' ;
?>
<form action="<?php echo $_SERVER['PHP_SELF'] ; ?>" method=post 
  enctype=multipart/form-data id="form" >
<input type="hidden" name="MAX_FILE_SIZE" value="1024000000">

<div class="menu">
<input type="submit" value="<?php if  ( $shifr -> localerus ) echo 'файлы' ;
  else echo 'files' ;?>" name="files"/><br>
</div>
<label>
<?php
  if  ( $shifr -> localerus )
    echo 'Сообщение:'  ;
  else
    echo 'Message:' ;
?>
  <br />
  <textarea name="message" rows="12" cols="61" id="message"><?php
  echo htmlspecialchars($shifr -> message) ; ?></textarea>
</label><br />
<p>
<fieldset>
<legend>
<?php
  if  ( $shifr -> localerus )
    echo 'Алфавит знаков в пароле' ;
  else
    echo 'Alphabet of characters in a password' ;
?>
</legend>
<input type="radio" name="radio" value="ASCII" id="passalpha" <?php 
if ( $shifr -> letters_mode == 95 ) echo 'checked' ?> >ASCII <?php 
if  ( $shifr -> localerus ) echo 'буквы цифры знаки пробел'; else
echo 'letters digits signs space'; ?><br>
<input type="radio" name="radio" value="LettDigit" id="passlettersdigits" <?php
if ( $shifr -> letters_mode == 62 ) echo 'checked' ?> ><?php 
if  ( $shifr -> localerus ) echo 'цифры и буквы'; else echo 'digits and letters'; ?><br>
<input type="radio" name="radio" value="Digit" id="passdigits" <?php
if ( $shifr -> letters_mode == 10 ) echo 'checked' ?> ><?php 
if  ( $shifr -> localerus ) echo 'цифры'; else echo 'digits'; ?>
</fieldset>
<br>
<fieldset>
<legend>
<?php
  if  ( $shifr -> localerus )
    echo 'Размер ключа'  ;
  else
    echo 'Key size' ;
?>
</legend>
<input type="radio" name="radiokey" value="Key45" id="keysize45" <?php 
if ( shifr_version  ( $shifr  ) == 2 ) echo 'checked' ?> >45 <?php
if  ( $shifr -> localerus )
  echo 'бит , 6-14 букв';
else
  echo 'bits , 6-14 letters'; ?><br>
<input type="radio" name="radiokey" value="Key296" id="keysize296" <?php 
if ( shifr_version  ( $shifr  ) == 3 ) echo 'checked' ?> >296 <?php 
if  ( $shifr -> localerus ) echo 'бит , 45-90 букв'; else
echo 'bits , 45-90 letters'; ?>
</fieldset>
<script>

  if ( document . getElementById ( 'keysize45' ) . checked ) {
    js_shifr . key_mode = 45 ; }
  if ( document . getElementById ( 'keysize296' ) . checked ) {
    js_shifr . key_mode = 296 ; }

let fkeysize45  = function ( ) {
  js_shifr . key_mode = 45 ; }
let chboxg_keys45 = document  . getElementById ( 'keysize45' ) ;
chboxg_keys45 . addEventListener  ( 'click' , fkeysize45 ) ;
let fkeysize296  = function ( ) {
  js_shifr . key_mode = 296 ; }
let chboxg_keys296 = document  . getElementById ( 'keysize296' ) ;
chboxg_keys296 . addEventListener  ( 'click' , fkeysize296 ) ;

</script>
<br>
<label>
<?php
  if  ( $shifr -> localerus ) {
    echo 'Ваш пароль : '  ;
    echo '<table><tr><td><fieldset><legend><i>PHP</i></legend>' ;
    echo '<input type="submit" name="submit" value="генерировать" id="generate" /></fieldset></td>'  ;
    echo '<td><fieldset><legend><i>JavaScript</i></legend>' ;
    echo '<input type="button" name="submit2" value="генерировать" id="generate2" /></fieldset></td></tr></table>'  ; }
  else {
    echo 'Your password : ' ;
    echo '<table><tr><td><fieldset><legend><i>PHP</i></legend>' ;
    echo '<input type="submit" name="submit" value="generate" id="generate" /></fieldset></td>' ;
    echo '<td><fieldset><legend><i>JavaScript</i></legend>' ;
    echo '<input type="button" name="submit2" value="generate" id="generate2" /></fieldset></td></tr></table>'  ; }
?>
</label>
<br>
<input name="password" type="password" value="<?php 
    echo htmlspecialchars ( $shifr -> password ) ; ?>" size ="47" id="password" />
<script>

  if ( document . getElementById ( 'passlettersdigits' ) . checked ) {
    js_shifr . letters_mode = 62 ; }
  if ( document . getElementById ( 'passalpha' ) . checked ) {
    js_shifr . letters_mode = 95 ; }
  if ( document . getElementById ( 'passdigits' ) . checked ) {
    js_shifr . letters_mode = 10 ; }

let falpha95  = function ( ) {
  js_shifr . letters_mode = 95 ; }
document  . getElementById ( 'passalpha' ) . addEventListener  ( 'click' , falpha95 ) ;

let falpha62  = function ( ) {
  js_shifr . letters_mode = 62 ; }
document  . getElementById ( 'passlettersdigits' ) . addEventListener  ( 'click' , falpha62 ) ;

let falpha10  = function ( ) {
  js_shifr . letters_mode = 10 ; }
document  . getElementById ( 'passdigits' ) . addEventListener  ( 'click' , falpha10 ) ;
    
let fgenerate = function ( ) {
  js_shifr_generate_password  ( js_shifr  ) ;
  document . getElementById ( 'password' ) . value  = js_shifr . password ; }
  
let chboxg = document.getElementById('generate2');
chboxg.addEventListener('click', fgenerate ) ;

</script>
<br>
<?php
if  ( $shifr -> localerus ) {
  echo 'Показать пароль : <input type="checkbox" '.
  ' class="largerCheckbox" name="Показать_пароль" value="0"'.
  ' id="showpassword" onchange="fshowpassword()" />' ; }
else {
  echo 'Show password : <input type="checkbox"' .
  ' class="largerCheckbox" name="Show_password" value="0" id="showpassword" '.
  ' onchange="fshowpassword()" />' ; }
?>
<script>
  if ( document.getElementById('showpassword') . checked ) {
    form [ "password" ] . type  = "text"  ; }
  else {
    form [ "password" ] . type  = "password" ; }

let fshowpassword = function ( ) {
  let chbox = document.getElementById('showpassword');
	if (chbox.checked) {
    form [ "password" ] . type  = "text"  ;
    chbox.setAttribute("checked", "checked" );
    form [ 'showpassword' ] . value  = "1"  ; }
	else {
    form [ "password" ] . type  = "password" ;
    chbox.removeAttribute("checked");
    form [ 'showpassword' ] . value  = "0"  ; } }
</script>
</p>
<?php
  echo '<table><tr><td><fieldset><legend><i>PHP</i></legend>' ;
  if  ( $shifr -> localerus ) {
    echo ' <input type="submit" name="submit" value="зашифровать" />'  ;
    echo ' <input type="submit" name="submit" value="расшифровать" />'  ; }
  else {
    echo ' <input type="submit" name="submit" value="encrypt" />' ;
    echo ' <input type="submit" name="submit" value="decrypt" />' ; }
  echo  '</fieldset></td>'  ;
  
  echo '<td><fieldset><legend><i>JavaScript</i></legend>' ;
  if  ( $shifr -> localerus ) {
    echo ' <input type="button" name="submit2" value="зашифровать" id="encrypt2" />'  ;
    echo ' <input type="button" name="submit2" value="расшифровать" id="decrypt2" />' ;
    }
  else {
    echo ' <input type="button" name="submit2" value="encrypt"  id="encrypt2" />' ;
    echo ' <input type="button" name="submit2" value="decrypt"  id="decrypt2" />' ;
    }
  echo  '</fieldset></td>'  ;
  
  echo '</tr></table>' ;
?>
<hr>
<table><tr><td><fieldset><legend><i>PHP</i></legend>
<input type=file name=uploadfile><br>
<?php
if  ( $shifr -> localerus )
  echo '<input type=submit name="submit" value="Зашифровать файл" > '.PHP_EOL ;
else
  echo '<input type=submit name="submit" value="Encrypt file" > '.PHP_EOL  ;
if  ( $shifr -> localerus )
  echo '<input type=submit name="submit" value="Расшифровать файл" >'.PHP_EOL ;
else
  echo '<input type=submit name="submit" value="Decrypt file" >'.PHP_EOL  ;
?>
</fieldset></td><td><fieldset><legend><i>JavaScript</i></legend>
<input type=file name=js_uploadfile onchange="js_readFile(this)"><br>
<?php
if  ( $shifr -> localerus )
  echo '<input type="button" name="submit3" value="Зашифровать файл" id="encrypt3" > '.PHP_EOL ;
else
  echo '<input type="button" name="submit3" value="Encrypt file" id="encrypt3" > '.PHP_EOL  ;/*
if  ( $shifr -> localerus )
  echo '<input type="button" name="submit3" value="Расшифровать файл" id="decrypt3" >'.PHP_EOL ;
else
  echo '<input type="button" name="submit3" value="Decrypt file" id="decrypt3" >'.PHP_EOL  ;*/
?>
</fieldset></td></tr></table>
<?php
if  ( $shifr -> localerus )    {
  echo '<br>Шифрование в текстовом режиме : <input type="checkbox" '.
  ' class="largerCheckbox" name="Шифрование_в_текстовом_режиме" value="1"'.
  ' id="SText" '; if($shifr -> flagtext)echo 'checked'; echo ' />' ; }
else {
  echo '<br>Encryption in text mode : <input type="checkbox"' .
  ' class="largerCheckbox" name="Encryption_in_text_mode" value="1" id="SText" ';
  if($shifr -> flagtext)echo 'checked'; echo ' />' ; }
?>

<script>

js_shifr  . password  = document . getElementById ( 'password' ) . value ;
js_shifr  . message = document . getElementById ( 'message' ) . value ;
js_shifr  . flagtext  = document . getElementById ( 'SText' ) . checked ; 
let fencrypt = function ( ) {

  document . getElementById ( 'SText' ) . checked = true ;
  js_shifr  . flagtext  = true  ;
  js_shifr  . password  = document . getElementById ( 'password' ) . value ;
  js_shifr  . message = document . getElementById ( 'message' ) . value ;
  js_shifr  . message_array = js_toUTF8Array ( js_shifr . message ) ;
  
  js_shifr_password_load ( js_shifr ) ;
  js_shifr_encrypt ( js_shifr ) ; 
  js_shifr_flush ( js_shifr  ) ;
  
  document . getElementById ( 'message' ) . value  = js_shifr  . message ;
  
  }
let fdecrypt = function ( ) {

  document . getElementById ( 'SText' ) . checked = true ;
  js_shifr  . flagtext  = true  ;
  js_shifr  . password  = document . getElementById ( 'password' ) . value ;
  js_shifr  . message = document . getElementById ( 'message' ) . value ;
  js_shifr  . message_array = js_toUTF8Array ( js_shifr . message ) ;
  
  js_shifr_password_load ( js_shifr ) ;
  js_shifr_decrypt ( js_shifr ) ; 
  js_shifr . message  = js_Utf8ArrayToStr ( js_shifr  . message ) ; 
  
  js_shifr . old_last_data  = { n : 0 } ;
  js_shifr . old_last_sole  = { n : 0 } ;
  
  document . getElementById ( 'message' ) . value  = js_shifr  . message ;
  
  }
let chbox_enc = document  . getElementById  ( 'encrypt2'  ) ;
chbox_enc . addEventListener  ( 'click' , fencrypt ) ;
let chbox_dec = document  . getElementById  ( 'decrypt2'  ) ;
chbox_dec . addEventListener  ( 'click' , fdecrypt ) ;

let js_readFile = function  ( input ) {
  let file = input.files[0];
  
  let reader = new FileReader();

  reader.onload = function() {
    let buffer  = reader.result ;
    
    let view = new Uint8Array(buffer);
    
    js_shifr  . message_array = Array . from  ( view ) ;

  };

  reader.onerror = function() {
    alert(reader.error);
  };
  
  reader.readAsArrayBuffer(file);

}

let fencrypt3 = function  ( ) {

  document . getElementById ( 'SText' ) . checked = true ;
  js_shifr  . flagtext  = true  ;

  js_shifr  . password  = document . getElementById ( 'password' ) . value ;
  
  js_shifr_password_load ( js_shifr ) ;
  js_shifr_encrypt ( js_shifr ) ; 
  js_shifr_flush ( js_shifr  ) ;
  
  document . getElementById ( 'message' ) . value  = js_shifr  . message ;

}

let fdecrypt3 ;

let chbox_fenc = document  . getElementById  ( 'encrypt3'  ) ;
chbox_fenc . addEventListener  ( 'click' , fencrypt3 ) ;
/*let chbox_fdec = document  . getElementById  ( 'decrypt3'  ) ;
chbox_fdec . addEventListener  ( 'click' , fdecrypt3 ) ;
*/
</script>
</form>
</body>
</html>
