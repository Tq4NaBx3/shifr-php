<?php
require ( 'shifr.php' ) ;
$shifr  = new shifr ( ) ;
shifr_init ( $shifr  ) ;

$local = setlocale ( LC_ALL  , ''  ) ;
if ( $local == 'ru_RU.UTF-8' ) $shifr -> localerus = true ;
  
if ( ( ! isset ( $_REQUEST [ 'Шифрование_в_текстовом_режиме' ] ) ) and
  ( ! isset ( $_REQUEST [ 'Encryption_in_text_mode' ] ) ) )
  $shifr -> flagtext = false  ;
        
if  ( $_POST  ) {
  if  ( isset ( $_POST  [ 'submit'  ] ) ) {
    if  ( isset ( $_POST  [ 'radio' ] ) ) {
      if (  $_POST  [ 'radio' ] == 'ASCII' )  $shifr -> letters_mode = 95 ;  
      else  $shifr -> letters_mode = 62 ; }
    if  ( isset ( $_POST  [ 'radiokey' ] ) ) {
      if (  $_POST  [ 'radiokey' ] == 'Key296' )  $shifr -> key_mode = 296 ;  
      else  $shifr -> key_mode = 45 ; }
    if  ( $_POST  [ 'submit'] == 'зашифровать' or 
      $_POST  [ 'submit'  ] == 'encrypt'  ) {
      $shifr -> password = $_REQUEST  [ 'password'  ] ;
      $shifr -> message = $_REQUEST  [ 'message' ] ;
      $shifr -> flagtext = true  ;
      if ( $shifr -> key_mode == 45 ) {
        shifr_password_load_4  ( $shifr ) ;
        shifr_encode4 ( $shifr ) ; }
      else {
        shifr_password_load_6 ( $shifr ) ;
        shifr_encode6 ( $shifr  ) ; }
      shifr_flush ( $shifr  ) ; }
  else if  ( $_POST  [ 'submit'] == 'расшифровать' or 
      $_POST  [ 'submit'  ] == 'decrypt'  ) {
      $shifr -> password = $_REQUEST  [ 'password'  ] ;
      $shifr -> message = $_REQUEST [ 'message' ] ;
      if ( $shifr -> key_mode == 45 ) {
        shifr_password_load_4 ( $shifr ) ;
        shifr_decode4 ( $shifr ) ; }
      else {
        shifr_password_load_6 ( $shifr ) ;
        shifr_decode6 ( $shifr ) ; } }
  else  if  ( $_POST  [ 'submit'] == 'генерировать' or 
      $_POST  [ 'submit'  ] == 'generate'  ) {
      if ( $shifr -> key_mode == 45 )
        shifr_generate_password_4 ( $shifr  ) ;
      else
        shifr_generate_password_6 ( $shifr  ) ;
      $shifr -> message = $_REQUEST [ 'message' ] ; }
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
      $shifr -> password = $_REQUEST  [ 'password'  ] ;
      if ( $shifr -> key_mode == 45 )
        shifr_password_load_4  ( $shifr ) ;
      else
        shifr_password_load_6  ( $shifr ) ;
      while ( ! feof  ( $fp ) ) {
        set_time_limit  ( 60  ) ;
        $shifr -> message = fread ( $fp , 0x1000 ) ;
        if ( ! $shifr -> message ) break ;
        if ( $shifr -> key_mode == 45 ) shifr_encode4 ( $shifr ) ;
        else  shifr_encode6 ( $shifr ) ;
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
      else  $uploadfile2 = $uploadfile  . '.des' ;
      $fpw = fopen ( $uploadfile2 , 'wb'  ) ;
      $shifr -> password = $_REQUEST  [ 'password'  ] ;
      if ( $shifr -> key_mode == 45 )
        shifr_password_load_4  ( $shifr ) ;
      else
        shifr_password_load_6  ( $shifr ) ;
      while ( ! feof  ( $fp ) ) {
        set_time_limit  ( 60  ) ;
        $shifr -> message = fread ( $fp , 0x1000 ) ;
        if ( ! $shifr -> message ) break ;
        if ( $shifr -> key_mode == 45 ) shifr_decode4 ( $shifr ) ;
        else  shifr_decode6 ( $shifr ) ;
        fwrite  ( $fpw , $shifr -> message ) ; }
      fclose  ( $fpw  ) ;
      fclose  ( $fp ) ;
      if  ( $shifr -> localerus )
        $shifr -> message = 'расшифрованный файл сохранён с именем : ' ;
      else
        $shifr -> message = 'decrypted file saved with name : ' ;
      $shifr -> message .= "'". $uploadfile2 ."'\n" ;  } } } }
?>
<style> p { font-size: 36px; }  textarea { font-size: 36px; }
input { font-size: 36px; } input.largerCheckbox { transform : scale(2); }
</style>
<html>
<body>
<form action="<?php echo $_SERVER['PHP_SELF'] ; ?>" method=post enctype=multipart/form-data>
<input type="hidden" name="MAX_FILE_SIZE" value="1024000000">
<?php

if  ( $shifr -> localerus )
    echo 'Сообщение:'  ;
  else
    echo 'Message:' ;

?>
  <br />
  <textarea name="message" rows="12" cols="61"><?php echo htmlspecialchars($shifr -> message) ; ?></textarea><br />
<p>

<?php
  if  ( $shifr -> localerus ) echo 'Алфавит знаков в пароле :' ;
  else echo 'Alphabet of characters in a password :' ;
?>
<input type="radio" name="radio" value="ASCII" <?php if ( $shifr -> letters_mode == 95 ) echo 'checked' ?> >ASCII <?php if  ( $shifr -> localerus ) echo 'буквы цифры знаки пробел'; else echo 'letters digits signs space'; ?>
<input type="radio" name="radio" value="LettDigit" <?php if ( $shifr -> letters_mode == 62 ) echo 'checked' ?> ><?php if  ( $shifr -> localerus ) echo 'цифры и буквы'; else echo 'digits and letters'; ?>
<br>
<?php
  if  ( $shifr -> localerus )
    echo 'Размер ключа : '  ;
  else
    echo 'Key size : ' ;
?>
<input type="radio" name="radiokey" value="Key45" <?php if ( $shifr -> key_mode == 45 ) echo 'checked' ?> >45 <?php if  ( $shifr -> localerus ) echo 'бит , 6-8 букв'; else echo 'bits , 6-8 letters'; ?>
<input type="radio" name="radiokey" value="Key296" <?php if ( $shifr -> key_mode == 296 ) echo 'checked' ?> >296 <?php if  ( $shifr -> localerus ) echo 'бит , 45-50 букв'; else echo 'bits , 45-50 letters'; ?>
<br>
<?php
  if  ( $shifr -> localerus ) {
    echo 'Ваш пароль : '  ;
    echo '<input type="submit" name="submit" value="генерировать" />'  ; }
  else {
    echo 'Your password : ' ;
    echo '<input type="submit" name="submit" value="generate" />' ; }
?>
<br>
<input name="password" type="text" value="<?php echo htmlspecialchars ( $shifr -> password ) ; ?>" size ="51" />
</p>
<?php
  if  ( $shifr -> localerus ) {
    echo ' <input type="submit" name="submit" value="зашифровать" />'  ;
    echo ' <input type="submit" name="submit" value="расшифровать" />'  ; }
  else {
    echo ' <input type="submit" name="submit" value="encrypt" />' ;
    echo ' <input type="submit" name="submit" value="decrypt" />' ; }      
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
  echo '<br>Шифрование в текстовом режиме : <input type="checkbox" class="largerCheckbox" name="Шифрование_в_текстовом_режиме" value="1" id="SText" '; if($shifr -> flagtext)echo 'checked'; echo ' />' ; }
else {
  echo '<br>Encryption in text mode : <input type="checkbox" class="largerCheckbox" name="Encryption_in_text_mode" value="1" id="SText" ';
  if($shifr -> flagtext)echo 'checked'; echo ' />' ; }
?>
</p>
</form>
</body>
</html>
