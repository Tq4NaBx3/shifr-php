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
  
if ( ( ! isset ( $_REQUEST [ 'Шифрование_в_текстовом_режиме' ] ) ) and
  ( ! isset ( $_REQUEST [ 'Encryption_in_text_mode' ] ) ) )
  $shifr -> flagtext = false  ;
  
if ( isset ( $_REQUEST  [ 'password'  ] ) )
  $shifr -> password = $_REQUEST  [ 'password'  ] ;        

if ( isset ( $_REQUEST  [ 'message'  ] ) )
  $shifr -> message = $_REQUEST  [ 'message'  ] ;        
        
if  ( $_POST  ) {
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
<input type="radio" name="radio" value="LettDigit" <?php
if ( $shifr -> letters_mode == 62 ) echo 'checked' ?> ><?php 
if  ( $shifr -> localerus ) echo 'цифры и буквы'; else echo 'digits and letters'; ?><br>
<input type="radio" name="radio" value="Digit" <?php
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
<input type="radio" name="radiokey" value="Key45" id="keysize" <?php 
if ( shifr_version  ( $shifr  ) == 2 ) echo 'checked' ?> >45 <?php
if  ( $shifr -> localerus )
  echo 'бит , 6-14 букв';
else
  echo 'bits , 6-14 letters'; ?><br>
<input type="radio" name="radiokey" value="Key296" <?php 
if ( shifr_version  ( $shifr  ) == 3 ) echo 'checked' ?> >296 <?php 
if  ( $shifr -> localerus ) echo 'бит , 45-90 букв'; else
echo 'bits , 45-90 letters'; ?>
</fieldset>
<br>
<label>
<?php
  if  ( $shifr -> localerus ) {
    echo 'Ваш пароль : '  ;
    echo '<fieldset><legend><i>PHP</i></legend>' ;
    echo '<input type="submit" name="submit" value="генерировать" id="generate" /></fieldset>'  ;
    echo '<fieldset><legend><i>JavaScript</i></legend>' ;
    echo '<input type="button" name="submit2" value="генерировать" id="generate2" /></fieldset>'  ; }
  else {
    echo 'Your password : ' ;
    echo '<fieldset><legend><i>PHP</i></legend>' ;
    echo '<input type="submit" name="submit" value="generate" id="generate" /></fieldset>' ;
    echo '<fieldset><legend><i>JavaScript</i></legend>' ;
    echo '<input type="button" name="submit2" value="generate" id="generate2" /></fieldset>'  ; }
?>
</label>
<br>
<input name="password" type="password" value="<?php 
    echo htmlspecialchars ( $shifr -> password ) ; ?>" size ="47" id="password" />
<script>
let fgenerate = function () {
  let pbox = document.getElementById('password') ;
  if ( pbox.value  === "12345" )
    pbox.value  = "abcde"  ;
  else
    pbox.value = "12345"  ;
  /*alert("pbox.value  = \"" + pbox.value + "\"");*/ }
let chboxg = document.getElementById('generate2');
chboxg.addEventListener('click', fgenerate ) ;
let fshowpassword = function () {
  let chbox = document.getElementById('showpassword');
	if (chbox.checked) {
    form [ "password" ] . type  = "text"  ;
    chbox.setAttribute("checked", "checked" );
    //chbox . attr  ( 'checked' , '' )  ;
    form [ 'showpassword' ] . value  = "1"  ; }
	else {
    form [ "password" ] . type  = "password" ;
    chbox.removeAttribute("checked");
    form [ 'showpassword' ] . value  = "0"  ; } }
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
</p>
<?php
  if  ( $shifr -> localerus ) {
    echo ' <input type="submit" name="submit" value="зашифровать" />'  ;
    echo ' <input type="submit" name="submit" value="расшифровать" />'  ;
    /*echo ' <input type="submit" name="submit" value="загрузить" />'  ;*/ }
  else {
    echo ' <input type="submit" name="submit" value="encrypt" />' ;
    echo ' <input type="submit" name="submit" value="decrypt" />' ; 
    /*echo ' <input type="submit" name="submit" value="load" />'  ;*/ }
?>
<hr>
<input type=file name=uploadfile>
<p>
<?php
if  ( $shifr -> localerus )
  echo '<input type=submit name="submit" value="Зашифровать файл" > '.PHP_EOL ;
else
  echo '<input type=submit name="submit" value="Encrypt file" > '.PHP_EOL  ;
if  ( $shifr -> localerus )
  echo '<input type=submit name="submit" value="Расшифровать файл" ><br>'.PHP_EOL ;
else
  echo '<input type=submit name="submit" value="Decrypt file" ><br>'.PHP_EOL  ;
if  ( $shifr -> localerus )    {
  echo '<br>Шифрование в текстовом режиме : <input type="checkbox" '.
  ' class="largerCheckbox" name="Шифрование_в_текстовом_режиме" value="1"'.
  ' id="SText" '; if($shifr -> flagtext)echo 'checked'; echo ' />' ; }
else {
  echo '<br>Encryption in text mode : <input type="checkbox"' .
  ' class="largerCheckbox" name="Encryption_in_text_mode" value="1" id="SText" ';
  if($shifr -> flagtext)echo 'checked'; echo ' />' ; }
?>
</p>
<script>
let pass2 = js_shifr_generate_pass2 ( ) ;
console . log ( 'js_shifr_generate_pass2 = [ '  + pass2 + ' ]' ) ;
let pass3 = js_shifr_generate_pass3 ( ) ;
console . log ( 'js_shifr_generate_pass3 = [ '  + pass3 + ' ]' ) ;
let data  = [ 0 , 1 , 2 , 3 ] ;
let datasole  = js_shifr_data_sole2 ( data  ) ;
let dss = new String ( 'datasole = [ ' ) ;
for ( let ds  of  datasole ) {
  dss += ds . toString ( 2 ) ;
  dss += ' , ' ; }
dss += ' ]' ;
console . log ( dss ) ;
for ( let data3 of [ [ 0 , 1 , 2 ] , [ 3 , 4 , 5 ] , [ 6 , 7 ] ] ) {
  let datasole3  = js_shifr_data_sole3 ( data3  ) ;
  let dss3 = new String ( 'datasole3 = [ ' ) ;
  for ( let ds  of  datasole3 ) {
    dss3 += ds . toString ( 2 ) ;
    dss3 += ' , ' ; }
  dss3 += ' ]' ;
  console . log ( dss3 ) ; }
let byte = Math . floor ( Math . random ( ) * 0x100 ) ;
let bytes = new String ( 'byte = [ ' ) ;
bytes += byte . toString ( 2 ) ;
bytes += ' ]' ;
console . log ( bytes ) ;  
let arrbyte = shifr_byte_to_array2  ( byte  ) ;
let dssba = new String ( 'arrbyte = [ ' ) ;
for ( let ds  of  arrbyte ) {
  dssba += ds . toString ( 2 ) ;
  dssba += ' , ' ; }
dssba += ' ]' ;
console . log ( dssba ) ;  
</script>
</form>
</body>
</html>
