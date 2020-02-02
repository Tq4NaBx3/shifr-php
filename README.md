# shifr-php
Symmetric stream encryption with 'salt'.
'Salt' is constantly generated, which gives good resistance.
Data size doubles in binary mode.
Tripled in text mode.
There is no diagnosis of the wrong password.
The encryption key size is 296 bits ( 45 - 50 letters ) .
Or light key with 45 bits ( 6 - 8 lettters ) .
The PHP version compatible with C.
Password encrypted × password = can serve as a hash function.
Hash ÷ decrypt (password) == password 
If the decrypted hash with the password gives this password, then the password is correct.
Double encryption of known data with a password can serve as a signature. 
For example Sha1Sum(data) * password * password = Signature 
If the signature decrypted twice gives the checksum, then this gives reason to trust the signed data.
