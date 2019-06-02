[req]
prompt = no
distinguished_name = my_distinguished_name

[my_distinguished_name]
commonName = {{ $cn }}

countryName = {{ $c }}

stateOrProvinceName = {{ $st }}

localityName = {{ $l }}

organizationName = {{ $o }}

@if ($ou)
organizationalUnitName = {{ $ou }}
@endif
