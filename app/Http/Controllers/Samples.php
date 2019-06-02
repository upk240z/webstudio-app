<?php

namespace App\Http\Controllers;

use App\Mail\Simple;
use App\Util;
use Illuminate\Http\Request;
use League\OAuth2\Client\Provider\Facebook;
use League\OAuth2\Client\Provider\Github;
use PHPGangsta_GoogleAuthenticator;

class Samples extends Controller
{
    public function __construct()
    {
        $this->middleware('login');
    }

    public function fck(Request $request)
    {
        return view('samples.fck', [
            'savedata' => \Cache::get('fck'),
        ]);
    }

    public function fckPost(Request $request)
    {
        \Cache::put('fck', $request->post('editor1'), 3600);
        return view('samples.fck', [
            'savedata' => $request->post('editor1'),
        ]);
    }

    public function geo(Request $request)
    {
        $pos = \Cache::get('geo');
        if (!$pos) {
            $pos = [
                'lat' => 35.62201844755655,
                'lon' => 139.68707309421438,
                'zoom' => 12,
            ];
        }
        return view('samples.geo', [
            'lat' => $pos['lat'],
            'lon' => $pos['lon'],
            'zoom' => $pos['zoom'],
        ]);
    }

    public function geoPost(Request $request)
    {
        $pos = [
            'lat' => $request->post('lat'),
            'lon' => $request->post('lon'),
            'zoom' => $request->post('zoom'),
        ];

        \Cache::put('geo', $pos, 3600);
        Util::setMessage('success', 'saved position');

        return redirect('samples/geo');
    }

    public function oauthlogin(Request $request)
    {
        return view('samples.oauthlogin', []);
    }

    public function oauthloginPost(Request $request)
    {
        $providerCode = $request->post('provider');
        $config = config('secret.oauth')[$providerCode];

        $provider = null;
        if ($providerCode == 'github') {
            $provider = new Github([
                'clientId' => $config['id'],
                'clientSecret' => $config['secret'],
            ]);
        } else if ($providerCode == 'facebook') {
            $provider = new Facebook([
                'clientId' => $config['id'],
                'clientSecret' => $config['secret'],
                'redirectUri' => url('/') . '/samples/oauth',
                'graphApiVersion' => $config['graphApiVersion']
            ]);
        }

        $url = $provider->getAuthorizationUrl();
        \Session::put('oauthstate', $provider->getState());
        \Session::put('oauthprovider', $providerCode);

        return redirect($url);
    }

    public function oauth(Request $request)
    {
        $providerCode = \Session::get('oauthprovider');
        $config = config('secret.oauth')[$providerCode];

        $provider = null;
        if ($providerCode == 'github') {
            $provider = new Github([
                'clientId' => $config['id'],
                'clientSecret' => $config['secret'],
            ]);
        } else if ($providerCode == 'facebook') {
            $provider = new Facebook([
                'clientId' => $config['id'],
                'clientSecret' => $config['secret'],
                'redirectUri' => url('/') . '/samples/oauth',
                'graphApiVersion' => $config['graphApiVersion']
            ]);
        }

        $token = $owner = null;

        if ($request->get('state') && \Session::get('oauthstate') == $request->get('state')) {
            try {
                $token = $provider->getAccessToken('authorization_code', [
                    'code' => $request->get('code')
                ]);
                $owner = $provider->getResourceOwner($token);
            } catch (\Exception $ex) {
                $token = $ex->getMessage();
            }
        }

        return view('samples.oauth', [
            'all' => $request->all(),
            'token' => $token,
            'owner' => $owner,
        ]);
    }

    public function holidays(Request $request)
    {
        return view('samples.holidays', [
            'holidays' => Util::getHolidays(),
        ]);
    }

    public function gaauth(Request $request)
    {
        return view('samples.gaauth', [
            'password' => null,
            'result' => null,
        ]);
    }

    public function gaauthPost(Request $request)
    {
        $ga = new PHPGangsta_GoogleAuthenticator();
        $result = $ga->verifyCode(
            config('secret.ga-secret'),
            $request->post('password')
        );

        return view('samples.gaauth', [
            'password' => $request->post('password'),
            'result' => $result,
        ]);
    }

    public function mailer(Request $request)
    {
        return view('samples.mailer', [
            'sender' => 'hasegawa@zaf.jp',
            'receiptto' => 'hasegawa@zaf.jp',
            'subject' => 'test mail',
            'body' => 'test mail body',
        ]);
    }

    public function mailerPost(Request $request)
    {
        $mail = new Simple(
            $request->post('sender'),
            $request->post('subject'),
            $request->post('body')
        );

        try {
            \Mail::to($request->post('receiptto'))->send($mail);
            Util::setMessage('success', '送信しました');
        } catch (\Exception $e) {
            Util::setMessage('error', $e->getMessage());
        }

        return view('samples.mailer', [
            'sender' => $request->post('sender'),
            'receiptto' => $request->post('receiptto'),
            'subject' => $request->post('subject'),
            'body' => $request->post('body'),
        ]);
    }
}
