<?php

namespace App\Http\Controllers;

use App;
use App\GeoMemo;
use App\Util;
use Illuminate\Http\Request;
use Endroid\QrCode\QrCode;

class Tools extends Controller
{
    public function __construct()
    {
        $this->middleware('login');
    }

    public function regex(Request $request)
    {
        return view('tools.regex', [
            'posted' => false,
            'pattern' => null,
            'str' => null,
            'replace' => null,
            'matched' => null,
            'matches' => null,
            'replaced' => null,
        ]);
    }

    public function regexPost(Request $request)
    {
        try {
            $matched = preg_match(
                $request->post('pattern'),
                $request->post('str'),
                $matches
            );

            $replaced = preg_replace(
                $request->post('pattern'),
                $request->post('replace'),
                $request->post('str')
            );
        } catch (\Exception $e) {
            $matched = 0;
            $matches = null;
            $replaced = null;
        }

        return view('tools.regex', [
            'posted' => true,
            'pattern' => $request->post('pattern'),
            'str' => $request->post('str'),
            'replace' => $request->post('replace'),
            'matched' => $matched,
            'matches' => $matches,
            'replaced' => $replaced,
        ]);
    }

    public function request(Request $request)
    {
        return view('tools.request', [
            'response' => null,
            'type' => 'server',
            'method' => 'GET',
            'url' => null,
            'ua' => $request->header('user-agent'),
            'bauser' => null,
            'bapw' => null,
            'header_names' => [],
            'header_values' => [],
            'names' => [],
            'values' => [],
            'error' => null,
        ]);
    }

    public function requestPost(Request $request)
    {
        if ($request->post('action') == 'save') {
            $file = "request.json";
            header("Content-disposition: attachment; filename=" . $file);
            header("Content-type: application/json; name=" . $file);
            header("Cache-Control: public");
            header("Pragma: public");
            echo $request->post('json');
            exit;
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // 接続タイムアウト
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // SSL証明書をチェックしない
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // SSL証明書をチェックしない

        if (strlen($request->post('ua')) > 0) {
            curl_setopt($ch, CURLOPT_USERAGENT, $request->post('ua'));
        }

        if (strlen($request->post('bauser')) > 0) {
            curl_setopt(
                $ch,
                CURLOPT_USERPWD,
                $request->post('bauser') . ':' . $request->post('bapw')
            );
        }

        $request_params = array();
        $size = $request->post('names') ? count($request->post('names')) : 0;
        for ($i = 0; $i < $size; $i++) {
            $name = trim($request->post('names')[$i]);
            if (strlen($name) == 0) continue;
            $value = $request->post('values')[$i];
            if (isset($request_params[$name])) {
                if (is_array($request_params[$name])) {
                    $request_params[$name][] = $value;
                } else {
                    $request_params[$name] = array($request_params[$name]);
                    $request_params[$name][] = $value;
                }
            } else {
                $request_params[$name] = $value;
            }
        }

        $request_url = trim($request->post('url'));
        $response = null;
        $error = null;
        if (strlen($request_url)) {
            if ($request->post('method') == "POST") {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($request_params));
            } else if ($request->post('method') == "PUT") {
                curl_setopt($ch, CURLOPT_POSTFIELDS, true);
            } else if ($request->post('method') == "DELETE") {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            } else {
                $request_url .= "?" . http_build_query($request_params);
            }

            // 追加ヘッダー
            $headers = array();
            $size = $request->post('header_names') ? count($request->post('header_names')) : 0;
            for ($i = 0; $i < $size; $i++) {
                $name = trim($request->post('header_names')[$i]);
                if (strlen($name) == 0) continue;
                $headers[] = $name . ":" . $request->post('header_values')[$i];
            }

            if (count($headers)) curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            // ターゲットURL
            curl_setopt($ch, CURLOPT_URL, $request_url);

            $response = curl_exec($ch); // レスポンス取得
            $error = curl_error($ch);

            curl_close($ch);
        }

        $parameters = $request->all();
        $parameters['response'] = $response;
        $parameters['error'] = $error;
        $parameters['header_names'] = $request->post('header_names', []);
        $parameters['header_values'] = $request->post('header_values', []);
        $parameters['names'] = $request->post('names', []);
        $parameters['values'] = $request->post('values', []);

        return view('tools.request', $parameters);
    }

    public function base64(Request $request)
    {
        return view('tools.base64');
    }

    public function json(Request $request)
    {
        return view('tools.json', [
            'input' => null,
            'parsed' => null,
            'yaml' => null,
        ]);
    }

    public function jsonPost(Request $request)
    {
        $jsonStr = $request->post('input');
        $parsed = json_decode($jsonStr, true);
        $yaml = null;
        $phpStr = null;
        if ($parsed !== null) {
            try {
                $yaml = yaml_emit($parsed, YAML_UTF8_ENCODING);
            } catch (\Exception $e) {
                Util::setMessage('error', $e->getMessage());
            }

            $phpStr = str_replace(': ' , ' => ', $jsonStr);
            $phpStr = str_replace('{' , '[', $phpStr);
            $phpStr = str_replace('}' , ']', $phpStr);

        } else {
            Util::setMessage('error', 'parse error');
        }

        return view('tools.json', [
            'input' => $request->post('input'),
            'parsed' => $parsed,
            'yaml' => $yaml,
            'php' => $phpStr,
        ]);
    }

    public function yaml(Request $request)
    {
        return view('tools.yaml', [
            'input' => null,
            'parsed' => null,
            'json' => null,
        ]);
    }

    public function yamlPost(Request $request)
    {
        $parsed = yaml_parse($request->post('input'));
        $json = null;
        if ($parsed !== null) {
            $json = json_encode($parsed, true);
        } else {
            Util::setMessage('error', 'parse error');
        }

        return view('tools.yaml', [
            'input' => $request->post('input'),
            'parsed' => $parsed,
            'json' => $json,
        ]);
    }

    public function serialize(Request $request)
    {
        $sample = array(
            'string' => 'hello',
            'numner' => 123.4,
            'array' => array('apple', 'pine', 'tomato')
        );

        return view('tools.serialize', [
            'input' => $request->post('input'),
            'parsed' => null,
            'sample' => $sample,
        ]);
    }

    public function serializePost(Request $request)
    {
        $parsed = null;
        try {
            $parsed = unserialize($request->post('input'));
        } catch (\ErrorException $e) {
            Util::setMessage('error', $e->getMessage());
        }

        $sample = array(
            'string' => 'hello',
            'numner' => 123.4,
            'array' => array('apple', 'pine', 'tomato')
        );

        return view('tools.serialize', [
            'input' => $request->post('input'),
            'parsed' => $parsed,
            'sample' => $sample,
        ]);
    }

    public function random(Request $request)
    {
        return view('tools.random', [
            'length' => null,
            'random' => null,
        ]);
    }

    public function randomPost(Request $request)
    {
        $len = (int)$request->post('length');
        $random = Util::makePassword($len);

        return view('tools.random', [
            'length' => $request->post('length'),
            'random' => $random,
        ]);
    }

    public function encode(Request $request)
    {
        return view('tools.encode', [
            'encoding' => 'UTF-8',
            'input_plain' => null,
            'input_encoded' => null,
            'encoded' => null,
            'decoded' => null,
        ]);
    }

    public function encodePost(Request $request)
    {
        $encoded = urlencode(mb_convert_encoding(
            $request->post('input_plain'),
            $request->post('encoding'),
            'UTF-8'
        ));
        $decoded = mb_convert_encoding(
            urldecode($request->post('input_encoded')),
            'UTF-8',
            $request->post('encoding')
        );

        return view('tools.encode', [
            'encoding' => $request->post('encoding'),
            'input_plain' => $request->post('input_plain'),
            'input_encoded' => $request->post('input_encoded'),
            'encoded' => $encoded,
            'decoded' => $decoded,
        ]);
    }

    public function escape(Request $request)
    {
        return view('tools.escape', [
            'input' => null,
            'escaped' => null,
        ]);
    }

    public function escapePost(Request $request)
    {
        $escaped = htmlspecialchars($request->post('input'));

        return view('tools.escape', [
            'input' => $request->post('input'),
            'escaped' => $escaped,
        ]);
    }

    public function blowfish(Request $request)
    {
        return view('tools.blowfish', [
            'key_hex' => null,
            'input' => null,
            'ivHex' => null,
            'encryptedHex' => null,
            'input4decode_hex' => null,
            'iv4decode_hex' => null,
            'decoded' => null,
        ]);
    }

    public function blowfishPost(Request $request)
    {
        $cipher = 'BF-CBC';
        $key = pack('H*', $request->post('key_hex'));

        $size = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($size);
        $ivHex = bin2hex($iv);

        $encrypted = openssl_encrypt(
            $request->post('input'),
            $cipher,
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );
        $encryptedHex = bin2hex($encrypted);

        $input4decode = pack('H*', $request->post('input4decode_hex'));
        $iv4decode = pack('H*', $request->post('iv4decode_hex'));

        try {
            $decoded = openssl_decrypt(
                $input4decode,
                $cipher,
                $key,
                OPENSSL_RAW_DATA,
                $iv4decode
            );
        } catch (\Exception $e) {
            Util::setMessage('error', $e->getMessage());
        }

        return view('tools.blowfish', [
            'key_hex' => $request->post('key_hex'),
            'input' => $request->post('input'),
            'ivHex' => $ivHex,
            'encryptedHex' => $encryptedHex,
            'input4decode_hex' => $request->post('input4decode_hex'),
            'iv4decode_hex' => $request->post('iv4decode_hex'),
            'decoded' => $decoded,
        ]);
    }

    public function qrcode(Request $request)
    {
        $output = false;
        if ($request->method() == 'POST') {
            if (
                strlen($request->post('type')) &&
                strlen($request->post('size')) &&
                strlen($request->post('text'))
            ) {
                $output = true;
            }
        }

        return view('tools.qrcode', [
            'type' => $request->post('type', 'png'),
            'size' => $request->post('size', 300),
            'text' => $request->post('text'),
            'output' => $output
        ]);
    }

    public function qrImage(Request $request)
    {
        $qr = new QrCode($request->get('text'));
        $qr->setSize($request->get('size'));
        $qr->setWriterByName($request->get('type'));

        $content = $qr->writeString();

        return response($content)->withHeaders([
            'Content-Type' => $qr->getContentType(),
            'Content-Length' => strlen($content),
        ]);
    }

    public function xpath(Request $request)
    {
        $xml = <<<__END__
<?xml version="1.0" encoding="UTF-8"?>
<menu_tab_list>
  <menu_tab tab_number="1">
    <tab_number>1</tab_number>
    <display_order>1</display_order>
    <class_id>0</class_id>
    <tab_name>メニュー</tab_name>
    <menu_display_type>1</menu_display_type>
    <tab_detail_text>メニュー</tab_detail_text>
    <tab_free_text>メニューメニュー</tab_free_text>
    <delivery_flag>1</delivery_flag>
    <catering_flag>1</catering_flag>
    <takeout_flag>1</takeout_flag>
    <delete_flag>0</delete_flag>
    <create_date>2007-12-13 10:47:58</create_date>
    <update_date>2007-12-27 18:27:39</update_date>
    <menu_tab_image image_number="0"/>
    <menu_tab_image image_number="1"/>
    <menu menu_seq="1" menu_type="3">
      <menu_seq>1</menu_seq>
      <item_id>26</item_id>
      <display_order>1</display_order>
      <menu_type>3</menu_type>
      <menu_title></menu_title>
      <menu_caption></menu_caption>
      <menu_caption_display_type>0</menu_caption_display_type>
      <price_display_flag>1</price_display_flag>
      <delete_flag>0</delete_flag>
      <create_date>2007-12-27 18:27:39</create_date>
      <update_date>2007-12-27 18:27:39</update_date>
      <item item_id="26">
        <item_id>26</item_id>
        <item_name>トスカーナ風お茶漬け</item_name>
        <item_detail_text></item_detail_text>
        <thumbnail_image/>
        <image/>
        <price_class class_id="0">
          <class_id>0</class_id>
          <class_name></class_name>
          <price>0</price>
          <gnavi_point_rate>0</gnavi_point_rate>
        </price_class>
      </item>
    </menu>
    <menu menu_seq="2" menu_type="3">
      <menu_seq>2</menu_seq>
      <item_id>615</item_id>
      <display_order>2</display_order>
      <menu_type>3</menu_type>
      <menu_title></menu_title>
      <menu_caption></menu_caption>
      <menu_caption_display_type>0</menu_caption_display_type>
      <price_display_flag>1</price_display_flag>
      <delete_flag>0</delete_flag>
      <create_date>2007-12-27 18:27:39</create_date>
      <update_date>2007-12-27 18:27:39</update_date>
      <item item_id="615">
        <item_id>615</item_id>
        <item_name>バスターズ味噌汁</item_name>
        <item_detail_text>早い、安い、うまい</item_detail_text>
        <thumbnail_image/>
        <image/>
        <price_class class_id="1">
          <class_id>1</class_id>
          <class_name>Ｍ</class_name>
          <price>1890.000</price>
          <gnavi_point_rate>1.000</gnavi_point_rate>
        </price_class>
        <price_class class_id="2">
          <class_id>2</class_id>
          <class_name>Ｌ</class_name>
          <price>2835.000</price>
          <gnavi_point_rate>1.000</gnavi_point_rate>
        </price_class>
        <group_item item_id="617">
          <item_id>617</item_id>
          <item_name>生地</item_name>
          <option_item item_id="417">
            <item_id>417</item_id>
            <item_name>トスバッティング</item_name>
            <price>0.000</price>
          </option_item>
          <option_item item_id="418">
            <item_id>418</item_id>
            <item_name>ＣＲ白ごはん</item_name>
            <price>0.000</price>
          </option_item>
        </group_item>
        <group_item item_id="618">
          <item_id>618</item_id>
          <item_name>トッピング</item_name>
          <option_item item_id="420">
            <item_id>420</item_id>
            <item_name>うりソ－ス抜き</item_name>
            <price>0.000</price>
          </option_item>
          <option_item item_id="500">
            <item_id>500</item_id>
            <item_name>ブラックペッパー抜き</item_name>
            <price>0.000</price>
          </option_item>
          <option_item item_id="442">
            <item_id>442</item_id>
            <item_name>イタリア風納豆</item_name>
            <price>420.000</price>
          </option_item>
          <option_item item_id="421">
            <item_id>421</item_id>
            <item_name>マヨネーズ</item_name>
            <price>315.000</price>
          </option_item>
          <option_item item_id="522">
            <item_id>522</item_id>
            <item_name>テリヤキまぐろ抜き</item_name>
            <price>-420.000</price>
          </option_item>
          <option_item item_id="443">
            <item_id>443</item_id>
            <item_name>ペパロニサラミ</item_name>
            <price>420.000</price>
          </option_item>
          <option_item item_id="422">
            <item_id>422</item_id>
            <item_name>エクストラひじき抜き</item_name>
            <price>-420.000</price>
          </option_item>
          <option_item item_id="444">
            <item_id>444</item_id>
            <item_name>ショルダー豚足</item_name>
            <price>420.000</price>
          </option_item>
          <option_item item_id="423">
            <item_id>423</item_id>
            <item_name>エクストラひじき</item_name>
            <price>420.000</price>
          </option_item>
          <option_item item_id="445">
            <item_id>445</item_id>
            <item_name>ペッパーハム</item_name>
            <price>420.000</price>
          </option_item>
          <option_item item_id="424">
            <item_id>424</item_id>
            <item_name>パルメザンひじき</item_name>
            <price>315.000</price>
          </option_item>
          <option_item item_id="446">
            <item_id>446</item_id>
            <item_name>アップルウッドスモークド豚足</item_name>
            <price>420.000</price>
          </option_item>
          <option_item item_id="425">
            <item_id>425</item_id>
            <item_name>オニオン</item_name>
            <price>315.000</price>
          </option_item>
          <option_item item_id="447">
            <item_id>447</item_id>
            <item_name>生ハム</item_name>
            <price>420.000</price>
          </option_item>
          <option_item item_id="426">
            <item_id>426</item_id>
            <item_name>ピーマン</item_name>
            <price>315.000</price>
          </option_item>
          <option_item item_id="448">
            <item_id>448</item_id>
            <item_name>粗びき納豆</item_name>
            <price>420.000</price>
          </option_item>
          <option_item item_id="427">
            <item_id>427</item_id>
            <item_name>切りもち</item_name>
            <price>420.000</price>
          </option_item>
          <option_item item_id="449">
            <item_id>449</item_id>
            <item_name>チョリソ</item_name>
            <price>420.000</price>
          </option_item>
          <option_item item_id="428">
            <item_id>428</item_id>
            <item_name>グリーンアスパラ</item_name>
            <price>420.000</price>
          </option_item>
          <option_item item_id="450">
            <item_id>450</item_id>
            <item_name>テリヤキまぐろ</item_name>
            <price>420.000</price>
          </option_item>
          <option_item item_id="429">
            <item_id>429</item_id>
            <item_name>クラッシュドパイナップル</item_name>
            <price>315.000</price>
          </option_item>
          <option_item item_id="451">
            <item_id>451</item_id>
            <item_name>スパイシーセット抜き</item_name>
            <price>0</price>
          </option_item>
          <option_item item_id="430">
            <item_id>430</item_id>
            <item_name>コーン</item_name>
            <price>315.000</price>
          </option_item>
          <option_item item_id="452">
            <item_id>452</item_id>
            <item_name>チェリーうり</item_name>
            <price>315.000</price>
          </option_item>
          <option_item item_id="431">
            <item_id>431</item_id>
            <item_name>ポテト</item_name>
            <price>420.000</price>
          </option_item>
          <option_item item_id="457">
            <item_id>457</item_id>
            <item_name>マヨネーズ抜き</item_name>
            <price>-315.000</price>
          </option_item>
          <option_item item_id="432">
            <item_id>432</item_id>
            <item_name>フレッシュダイスうり</item_name>
            <price>315.000</price>
          </option_item>
          <option_item item_id="458">
            <item_id>458</item_id>
            <item_name>オニオン抜き</item_name>
            <price>-315.000</price>
          </option_item>
          <option_item item_id="433">
            <item_id>433</item_id>
            <item_name>フレッシュうり</item_name>
            <price>315.000</price>
          </option_item>
          <option_item item_id="459">
            <item_id>459</item_id>
            <item_name>コーン抜き</item_name>
            <price>-315.000</price>
          </option_item>
          <option_item item_id="434">
            <item_id>434</item_id>
            <item_name>フレッシュマッシュルーム</item_name>
            <price>420.000</price>
          </option_item>
          <option_item item_id="461">
            <item_id>461</item_id>
            <item_name>ショルダー豚足抜き</item_name>
            <price>-420.000</price>
          </option_item>
          <option_item item_id="435">
            <item_id>435</item_id>
            <item_name>たんざくネギ</item_name>
            <price>420.000</price>
          </option_item>
          <option_item item_id="462">
            <item_id>462</item_id>
            <item_name>パセリ抜き</item_name>
            <price>0.000</price>
          </option_item>
          <option_item item_id="436">
            <item_id>436</item_id>
            <item_name>青ねぎ</item_name>
            <price>420.000</price>
          </option_item>
          <option_item item_id="476">
            <item_id>476</item_id>
            <item_name>ペパロニサラミ抜き</item_name>
            <price>-420.000</price>
          </option_item>
          <option_item item_id="437">
            <item_id>437</item_id>
            <item_name>ガーリック</item_name>
            <price>315.000</price>
          </option_item>
          <option_item item_id="487">
            <item_id>487</item_id>
            <item_name>ポテト抜き</item_name>
            <price>-420.000</price>
          </option_item>
          <option_item item_id="438">
            <item_id>438</item_id>
            <item_name>エビ</item_name>
            <price>420.000</price>
          </option_item>
          <option_item item_id="488">
            <item_id>488</item_id>
            <item_name>ヤリシャコ抜き</item_name>
            <price>-420.000</price>
          </option_item>
          <option_item item_id="439">
            <item_id>439</item_id>
            <item_name>ヤリシャコ</item_name>
            <price>420.000</price>
          </option_item>
          <option_item item_id="496">
            <item_id>496</item_id>
            <item_name>フレッシュうり抜き</item_name>
            <price>-315.000</price>
          </option_item>
          <option_item item_id="440">
            <item_id>440</item_id>
            <item_name>貝柱</item_name>
            <price>420.000</price>
          </option_item>
          <option_item item_id="497">
            <item_id>497</item_id>
            <item_name>エビ抜き</item_name>
            <price>-420.000</price>
          </option_item>
          <option_item item_id="441">
            <item_id>441</item_id>
            <item_name>ツナ</item_name>
            <price>420.000</price>
          </option_item>
        </group_item>
      </item>
    </menu>
  </menu_tab>
</menu_tab_list>
__END__;

        return view('tools.xpath', [
            'query' => '/menu_tab_list/menu_tab/menu/item',
            'xml' => $xml,
            'doc' => null,
            'nodeList' => null,
        ]);
    }

    public function xpathPost(Request $request)
    {
        $doc = new \DOMDocument();
        $doc->loadXML($request->post('xml'));
        $xpath = new \DOMXPath($doc);
        $nodeList = $xpath->query($request->post('query'));

        return view('tools.xpath', [
            'query' => $request->post('query'),
            'xml' => $request->post('xml'),
            'doc' => $doc,
            'nodeList' => $nodeList,
        ]);
    }

    public function env(Request $request)
    {
        return view('tools.env', [
            'variables' => $request->server(),
        ]);
    }

    public function time(Request $request)
    {
        return view('tools.time', [
            'input_date' => date('Y-m-d'),
            'int' => time(),
            'str' => date('Y-m-d H:i:s'),
            'days' => 0,
            'diff1_date' => null,
            'diff2_date' => null,
            'result_date' => null,
            'diffdays' => null,
            'int2time' => null,
            'str2time_int' => null,
            'str2time' => null,
        ]);
    }

    public function timePost(Request $request)
    {
        $result_date = date(
            "Y-m-d",
            strtotime($request->post('input_date')) + 86400 * (int)$request->post('days')
        );
        $diffdays = (
            strtotime($request->post('diff1_date')) - strtotime($request->post('diff2_date'))
        ) / 86400;
        $int2time = date("Y-m-d H:i:s", intval($request->post('int')));
        $str2time_int = intval(strtotime($request->post('str')));
        $str2time = date("Y-m-d H:i:s", $str2time_int);

        return view('tools.time', [
            'input_date' => $request->post('input_date'),
            'int' => $request->post('int'),
            'str' => $request->post('str'),
            'days' => $request->post('days'),
            'diff1_date' => $request->post('diff1_date'),
            'diff2_date' => $request->post('diff2_date'),
            'result_date' => $result_date,
            'diffdays' => $diffdays,
            'int2time' => $int2time,
            'str2time_int' => $str2time_int,
            'str2time' => $str2time,
        ]);
    }

    public function ip(Request $request)
    {
        return view('tools.ip', [
            'range_mask' => '24',
            'range_ip' => '192.168.0.0',
            'check_mask' => '24',
            'check_range_ip' => '192.168.0.0',
            'check_ip' => '192.168.0.1',
            'range_from' => null,
            'range_to' => null,
            'check_result' => null,
        ]);
    }

    public function ipPost(Request $request)
    {
        function _createMask($bit)
        {
            $bit = intval($bit);
            return bindec(
                str_repeat("1", $bit) . str_repeat("0", 32 - $bit)
            );
        }

        $mask = _createMask($request->post('range_mask'));
        $ip_long = ip2long($request->post('range_ip'));
        $range_from = long2ip($ip_long & $mask);
        $range_to = long2ip($ip_long | ~$mask);
        $mask = _createMask($request->post('check_mask'));
        $range_ip_long = ip2long($request->post('check_range_ip'));
        $ip_long = ip2long($request->post('check_ip'));
        $check_result =
            ($range_ip_long & $mask) == ($ip_long & $mask) ? "○" : "×";

        return view('tools.ip', [
            'range_mask' => $request->post('range_mask'),
            'range_ip' => $request->post('range_ip'),
            'check_mask' => $request->post('check_mask'),
            'check_range_ip' => $request->post('check_range_ip'),
            'check_ip' => $request->post('check_ip'),
            'range_from' => $range_from,
            'range_to' => $range_to,
            'check_result' => $check_result,
        ]);
    }

    public function translation(Request $request)
    {
        return view('tools.translation', []);
    }

    public function csr(Request $request)
    {
        return view('tools.csr', [
            'config' => config('secret.firebase')
        ]);
    }

    public function geomemo(Request $request)
    {
        $cookie = GeoMemo::getCookie();

        if (isset($cookie['lat']) == false || !$cookie['lat']) {
            $cookie = [ 'lat' => 35.0, 'lng' => 139.0, 'time' => 0 ];
        }
        if (isset($cookie['zoom']) == false || !$cookie['zoom']) {
            $cookie['zoom'] = 15;
        }

        return view('tools.geomemo', [
           'cookie' => $cookie,
        ]);
    }

    public function zen2han(Request $request)
    {
        $han = null;
        if ($request->method() == 'POST') {
            $han = mb_convert_kana($request->post('zen'), 'as');
        }
        return view('tools.zen2han', [
            'zen' => $request->post('zen'),
            'han' => $han,
        ]);
    }

    public function fixedLength(Request $request)
    {
        $configs = yaml_parse_file(App::basePath() . '/config/fixed-length.yaml');

        $rows = null;
        if ($request->method() == 'POST') {
            $config = $configs[$request->post('type')];

            do {
                if (!$request->hasFile('file')) {
                    Util::setMessage('error', 'select file');
                    break;
                }

                $file = $request->file('file');
                if (!$file->isValid()) {
                    Util::setMessage('error', 'invalid file');
                    break;
                }

                $fp = fopen($file->path(), 'r');
                if (!$fp) {
                    Util::setMessage('error', 'file reading error:' . $file->path());
                    break;
                }

                $rows = [];
                while (!feof($fp)) {
                    $line = trim(fgets($fp));
                    if (strlen($line) == 0) { continue; }
                    $columns = [];
                    $pos = 0;
                    foreach ($config['columns'] as $row) {
                        list($len, $name) = $row;
                        $val = mb_convert_encoding(
                            substr($line, $pos, $len),
                            'UTF-8',
                            $request->post('encoding')
                        );
                        $val = str_replace('　', $request->post('replace'), $val);
                        $columns[] = [
                            'name' => $name,
                            'value' => $val,
                            'len' => $len,
                            'pos' => $pos + 1,
                        ];
                        $pos += $len;
                    }
                    $rows[] = [
                        'columns' => $columns,
                        'total' => $pos,
                    ];
                }
                fclose($fp);
            } while(false);
            $post = $request->post();
        } else {
            $post = ['encoding' => 'SJIS-win'];
        }

        $files = [];
        foreach ($configs as $key => $config) {
            $files[$key] = $config['name'];
        }

        return view('tools.fixed-length', [
            'files' => $files,
            'rows' => $rows,
            'post' => $post,
        ]);
    }
}
