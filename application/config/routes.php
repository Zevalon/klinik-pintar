<?php
return [
    '' => 'Dashboard@index',
    'login' => 'AuthController@login',
    'logout' => 'AuthController@logout',
    'daftar' => 'PublicRegistration@index',
    'daftar/simpan' => 'PublicRegistration@store',
    'daftar/cari-pasien' => 'PublicRegistration@searchPatient',
    'monitor-antrian' => 'PublicQueueMonitor@index',
    'monitor-antrian/data' => 'PublicQueueMonitor@data',
];
