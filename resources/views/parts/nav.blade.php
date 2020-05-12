<?php
$_base = url('/');
$_url = URL::current();
$isMemo = $_url == $_base || preg_match('@/form$@', $_url);
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-info my-3 rounded shadow">
    <a class="navbar-brand" href="{{ $_base }}">WebStudio</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link<?php echo $isMemo ? ' active' : '' ?>" href="{{ $_base }}">
                    Memo
                </a>
            </li>

            <li class="nav-item dropdown<?php echo preg_match('@/tools/@', $_url) ? ' active' : '' ?>">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Tools
                </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ $_base }}/tools/regex">regex</a>
                    <a class="dropdown-item" href="{{ $_base }}/tools/request">HTTP request</a>
                    <a class="dropdown-item" href="{{ $_base }}/tools/base64">Base64</a>
                    <a class="dropdown-item" href="{{ $_base }}/tools/json">JSON</a>
                    <a class="dropdown-item" href="{{ $_base }}/tools/yaml">YAML</a>
                    <a class="dropdown-item" href="{{ $_base }}/tools/serialize">Serialize</a>
                    <a class="dropdown-item" href="{{ $_base }}/tools/random">Random</a>
                    <a class="dropdown-item" href="{{ $_base }}/tools/encode">Encode</a>
                    <a class="dropdown-item" href="{{ $_base }}/tools/escape">HTML escape</a>
                    <a class="dropdown-item" href="{{ $_base }}/tools/blowfish">BlowFish</a>
                    <a class="dropdown-item" href="{{ $_base }}/tools/qrcode">QR code</a>
                    <a class="dropdown-item" href="{{ $_base }}/tools/xpath">XPATH</a>
                    <a class="dropdown-item" href="{{ $_base }}/tools/time">calc time</a>
                    <a class="dropdown-item" href="{{ $_base }}/tools/ip">IP address</a>
                    <a class="dropdown-item" href="{{ $_base }}/tools/translation">translation</a>
                    <a class="dropdown-item" href="{{ $_base }}/tools/geomemo">Geo</a>
                    <a class="dropdown-item" href="{{ $_base }}/tools/csr">CSR</a>
                    <a class="dropdown-item" href="{{ $_base }}/tools/env">environment</a>
                    <a class="dropdown-item" href="{{ $_base }}/tools/zen2han">zen2han</a>
                    <a class="dropdown-item" href="{{ $_base }}/tools/flen">Fixed length file</a>
                </div>
            </li>

            <li class="nav-item dropdown<?php echo preg_match('@/samples/@', $_url) ? ' active' : '' ?>">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Samples
                </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ $_base }}/samples/fck">CKEditor</a>
                    <a class="dropdown-item" href="{{ $_base }}/samples/geo">Geo Location</a>
                    <a class="dropdown-item" href="{{ $_base }}/samples/oauthlogin">oAuth2.0</a>
                    <a class="dropdown-item" href="{{ $_base }}/samples/mailer">E-Mail</a>
                    <a class="dropdown-item" href="{{ $_base }}/samples/holidays">Holidays</a>
                    <a class="dropdown-item" href="{{ $_base }}/samples/gaauth">Google auth</a>
                </div>
            </li>

        </ul>
        <ul class="nav justify-content-end">
            <li class="nav-item">
                <a class="nav-link" href="{{ $_base }}/logout"><i class="fas fa-sign-out-alt"></i></a>
            </li>
        </ul>
    </div>
</nav>
