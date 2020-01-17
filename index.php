<?php
require ( 'shifr.php' ) ;
$shifr  = new shifr ( ) ;
shifr_init ( $shifr  ) ;

$local = setlocale ( LC_ALL  , ''  ) ;
if ( $local == 'ru_RU.UTF-8' ) $shifr -> localerus = true ;
else $shifr -> localerus = false ; 
  
if  ( isset ( $_REQUEST [ 'Шифрование_в_текстовом_режиме' ] ) or
  isset ( $_REQUEST [ 'Encryption_in_text_mode' ] ) )
  $shifr -> flagtext = true ;
else  $shifr -> flagtext = false  ;
        
if  ( $_POST  ) {
  if  ( isset ( $_POST  [ 'submit'  ] ) ) {
    if  ( isset ( $_POST  [ 'radio' ] ) ) {
      if (  $_POST  [ 'radio' ] == 'ASCII' )  $shifr -> letters_mode = 95 ;  
      else  $shifr -> letters_mode = 62 ; }
    if  ( $_POST  [ 'submit'] == 'зашифровать' or 
      $_POST  [ 'submit'  ] == 'encrypt'  ) {
      $shifr -> password = $_REQUEST  [ 'password'  ] ;
      $shifr -> message = $_REQUEST  [ 'message' ] ;
      shifr_password_load4  ( $shifr , shifr_string_to_password4  ( $shifr ,
          $shifr -> password ) ) ;
      $shifr -> flagtext = true  ;
      shifr_encode4 ( $shifr ) ; 
      if ( $shifr -> bytecount > 0 ) $shifr -> message .= "\n" ; }
  else if  ( $_POST  [ 'submit'] == 'расшифровать' or 
      $_POST  [ 'submit'  ] == 'decrypt'  ) {
      $shifr -> password = $_REQUEST  [ 'password'  ] ;
      $shifr -> message = $_REQUEST [ 'message' ] ;
      shifr_password_load4 ( $shifr , shifr_string_to_password4  ( $shifr ,
          $shifr -> password ) ) ;
      shifr_decode4 ( $shifr ) ; }    
  else  if  ( $_POST  [ 'submit'] == 'генерировать' or 
      $_POST  [ 'submit'  ] == 'generate'  ) {
      $shifr -> password = shifr_password_to_string4 ( $shifr ,
        shifr_pass_to_array4 ( shifr_generate_pass4 ( ) ) ) ;
      $shifr -> message = $_REQUEST [ 'message' ] ; }
   else
   if ( ( $_POST  [ 'submit'] == 'Зашифровать файл' or 
        $_POST  [ 'submit'  ] == 'Encrypt file'  ) and
      ( $_FILES [ 'uploadfile'  ] [ 'size' ] > 0 ) ) {
      $fp = fopen ( $_FILES [ 'uploadfile'  ] [ 'tmp_name'  ] , 'rb'  ) ;
      $uploaddir = './uploads/' ;
      $uploadfile = $uploaddir  . basename  (
        $_FILES [ 'uploadfile'  ] [ 'name'  ] ) ;
      $fpw = fopen ( $uploadfile . '.shi' , 'wb'  ) ;
      $shifr -> password = $_REQUEST  [ 'password'  ] ;
      shifr_password_load4  ( $shifr , shifr_string_to_password4  ( $shifr ,
          $shifr -> password ) ) ;
      while ( ! feof  ( $fp ) ) {
        $shifr -> message = fread ( $fp , 0x1000 ) ;
        shifr_encode4 ( $shifr ) ;
        fwrite  ( $fpw , $shifr -> message ) ; }
      if ( $shifr -> flagtext and $shifr -> bytecount > 0 )
        fwrite  ( $fpw , "\n" ) ;
      fclose  ( $fpw  ) ;
      fclose  ( $fp ) ;
      if  ( $shifr -> localerus )
        $shifr -> message = 'зашифрованный файл сохранён с именем : ' ;
      else
        $shifr -> message = 'encrypted file saved with name : ' ;
      $shifr -> message .= "'". $uploadfile . '.shi'."'\n" ; }
   else
   if  ( ( $_POST  [ 'submit' ] == 'Расшифровать файл' or 
        $_POST  [ 'submit'  ] == 'Decrypt file' ) and
      ( $_FILES [ 'uploadfile'  ] [ 'size' ] > 0 ) ) {
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
      shifr_password_load4  ( $shifr , shifr_string_to_password4  ( $shifr ,
          $shifr -> password ) ) ;
      while ( ! feof  ( $fp ) ) {
        $shifr -> message = fread ( $fp , 0x1000 ) ;
        shifr_decode4 ( $shifr ) ;
        fwrite  ( $fpw , $shifr -> message ) ; }
      fclose  ( $fpw  ) ;
      fclose  ( $fp ) ;
      if  ( $shifr -> localerus )
        $shifr -> message = 'расшифрованный файл сохранён с именем : ' ;
      else
        $shifr -> message = 'decrypted file saved with name : ' ;
      $shifr -> message .= "'". $uploadfile2 ."'\n" ;  } } }
  
?>
<style> p { font-size: 36px; }  textarea { font-size: 36px; }
input { font-size: 36px; } input.largerCheckbox { transform : scale(2); }
</style>
<html>
<body>
<form action="<?php echo $_SERVER['PHP_SELF'] ; ?>" method=post enctype=multipart/form-data>
<?php

if  ( $shifr -> localerus )
    echo 'Сообщение:'  ;
  else
    echo 'Message:' ;

?>
  <br />
  <textarea name="message" rows="12" cols="61"><?php echo htmlspecialchars($shifr -> message) ; ?></textarea><br />
<p>

<?php if  ( $shifr -> localerus ) echo 'Алфавит знаков в пароле :' ; else
echo 'Alphabet of characters in a password :' ; ?>
<input type="radio" name="radio" value="ASCII" <?php if ( $shifr -> letters_mode == 95 ) echo 'checked' ?> >ASCII <?php if  ( $shifr -> localerus ) echo 'буквы цифры знаки пробел'; else echo 'letters digits signs space'; ?>
<input type="radio" name="radio" value="LettDigit" <?php if ( $shifr -> letters_mode == 62 ) echo 'checked' ?> ><?php if  ( $shifr -> localerus ) echo 'цифры и буквы'; else echo 'digits and letters'; ?>
<br>
<?php
  if  ( $shifr -> localerus )
    echo 'Ваш пароль : '  ;
  else
    echo 'Your password : ' ;
?>
<input name="password" type="text" value="<?php echo htmlspecialchars ( $shifr -> password ) ; ?>" /> <?php if  ( $shifr -> localerus )
    echo '<input type="submit" name="submit" value="генерировать" />'  ;
  else
    echo '<input type="submit" name="submit" value="generate" />' ;
    ?>
    <br />
</p>
<?php
  if  ( $shifr -> localerus )
    echo ' <input type="submit" name="submit" value="зашифровать" />'  ;
  else
    echo ' <input type="submit" name="submit" value="encrypt" />' ;
  if  ( $shifr -> localerus )
    echo ' <input type="submit" name="submit" value="расшифровать" />'  ;
  else
    echo ' <input type="submit" name="submit" value="decrypt" />' ;  
?>
<hr>
<input type=file name=uploadfile>
<p>
<?php
/*if  ( $shifr -> localerus )
  echo '<input type=submit name="submit" value="Загрузить" ><br>' . PHP_EOL ;
else
  echo '<input type=submit name="submit" value="Download" ><br>' . PHP_EOL  ;*/
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
  if($shifr -> flagtext)echo 'checked'; echo ' />' ;
    }
?>
</p>
</form>
</body>
</html>