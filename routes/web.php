<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/info', function () {
    phpinfo();
});

Route::get('/login', 'Login@index');
Route::post('/login', 'Login@login');
Route::get('/logout', 'Login@logout');

Route::get('/', 'Top@index');
Route::post('/', 'Top@indexPost');
Route::post('/deletefolder', 'Top@deleteFolder');
Route::get('/memo', 'Top@memo');
Route::post('/movememo', 'Top@moveMemo');
Route::get('/form', 'Top@form');
Route::post('/form', 'Top@formPost');
Route::post('/delete', 'Top@delete');
Route::get('/image/{key}', 'Top@image');

Route::get('/tools/regex', 'Tools@regex');
Route::post('/tools/regex', 'Tools@regexPost');
Route::get('/tools/request', 'Tools@request');
Route::post('/tools/request', 'Tools@requestPost');
Route::get('/tools/base64', 'Tools@base64');
Route::post('/api/base64', 'Api@base64');
Route::get('/tools/json', 'Tools@json');
Route::post('/tools/json', 'Tools@jsonPost');
Route::get('/tools/yaml', 'Tools@yaml');
Route::post('/tools/yaml', 'Tools@yamlPost');
Route::get('/tools/serialize', 'Tools@serialize');
Route::post('/tools/serialize', 'Tools@serializePost');
Route::get('/tools/random', 'Tools@random');
Route::post('/tools/random', 'Tools@randomPost');
Route::get('/tools/encode', 'Tools@encode');
Route::post('/tools/encode', 'Tools@encodePost');
Route::get('/tools/escape', 'Tools@escape');
Route::post('/tools/escape', 'Tools@escapePost');
Route::get('/tools/blowfish', 'Tools@blowfish');
Route::post('/tools/blowfish', 'Tools@blowfishPost');
Route::get('/tools/qrcode', 'Tools@qrcode');
Route::get('/tools/xpath', 'Tools@xpath');
Route::post('/tools/xpath', 'Tools@xpathPost');
Route::get('/tools/env', 'Tools@env');
Route::get('/tools/time', 'Tools@time');
Route::post('/tools/time', 'Tools@timePost');
Route::get('/tools/ip', 'Tools@ip');
Route::post('/tools/ip', 'Tools@ipPost');
Route::get('/tools/translation', 'Tools@translation');
Route::post('/api/translation', 'Api@translation');
Route::get('/api/speak', 'Api@speak');
Route::get('/tools/csr', 'Tools@csr');
Route::post('/tools/csr', 'Tools@csrPost');
Route::match(['get', 'post'], '/tools/zen2han', 'Tools@zen2han');

Route::get('/tools/geomemo', 'Tools@geomemo');
Route::get('/geoapi/places', 'GeoApi@places');
Route::get('/geoapi/distance', 'GeoApi@distance');
Route::post('/geoapi/cookie', 'GeoApi@cookie');
Route::post('/geoapi/place', 'GeoApi@place');
Route::post('/geoapi/remove', 'GeoApi@remove');

Route::get('/samples/fck', 'Samples@fck');
Route::post('/samples/fck', 'Samples@fckPost');
Route::post('/api/ckeimage', 'Api@editorImage');
Route::get('/samples/geo', 'Samples@geo');
Route::post('/samples/geo', 'Samples@geoPost');

Route::get('/samples/oauthlogin', 'Samples@oauthlogin');
Route::post('/samples/oauthlogin', 'Samples@oauthloginPost');
Route::get('/samples/oauth', 'Samples@oauth');
Route::get('/samples/holidays', 'Samples@holidays');
Route::get('/samples/gaauth', 'Samples@gaauth');
Route::post('/samples/gaauth', 'Samples@gaauthPost');
Route::get('/samples/mailer', 'Samples@mailer');
Route::post('/samples/mailer', 'Samples@mailerPost');

Route::get('/free/params', 'Free@params');
Route::post('/free/params', 'Free@params');
