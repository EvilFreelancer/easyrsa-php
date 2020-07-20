<?php
/*
  Laravel 7 downloader and unpacker without external packages
*/
  $latestReleaseURL = 'https://api.github.com/repos/OpenVPN/easy-rsa/releases/latest';
  $latestReleaseData = Http::get($latestReleaseURL)->json();
  
  $gzArchivePath = storage_path ('app/bin/easy-rsa.tar.gz');
  $tarArchivePath = substr ($gzArchivePath, 0, -3);
  $binPath = storage_path ('app/bin/');
  
  // Gunzip
  $phar = new \PharData ($gzArchivePath);
  $phar->decompress ();

  // Unpack
  $phar = new \PharData ($tarArchivePath);
  $phar->extractTo ($binPath);
  
  // Rename
  rename ($binPath . $phar->getFilename(), $binPath . 'easy-rsa');
?>
