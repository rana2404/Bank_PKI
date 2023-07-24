# Bank_PKI
Securing Financial Data with PKI Cryptography in Banking

This PHP code allows users to upload three files: "fileToUpload," "fileToUploadSig," and "fileToUploadX509." It verifies the size of "fileToUpload," checks for any errors during the upload process, and prints the result. If the upload is successful, it proceeds to validate the digital signature of "fileToUpload" using the public key extracted from the X509 certificate specified in "fileToUploadX509." Additionally, it checks whether the certificate is issued by a trusted Certificate Authority (CA) and whether it has expired. The results of these checks are printed for the user.
