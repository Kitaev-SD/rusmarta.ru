<?php

$MESS['YANDEX_MARKET_UI_TRADING_HELLO_TEST_PARAMETER_REQUIRED'] = '�� ������ ������������ �������� #PARAMETER#';
$MESS['YANDEX_MARKET_UI_TRADING_HELLO_TEST_SUCCESS'] = '������ ������� ���������';
$MESS['YANDEX_MARKET_UI_TRADING_HELLO_TEST_ERROR_HTTP_NOT_FOUND'] = '����� �� ������';
$MESS['YANDEX_MARKET_UI_TRADING_HELLO_TEST_ERROR_HTTP_NOT_FOUND_DESCRIPTION'] = '
<h2 class="pos--top">����� �� ������</h2>
<p>��� ��������� �������� ������������ urlrewrite, ��������� ��������� ���-�������.</p>
<p>���� <strong>.htaccess</strong> ������ ��������� ������� urlrewrite.php (<a href="https://dev.1c-bitrix.ru/api_help/main/general/urlrewrite.php">������������</a>, ������ &mdash;&nbsp;����������� ������� ��������� �������). ��������� �������� � ������� ��������� ErrorDocument �� ��������������, � ����� ������ ���� ������� �� ����������.</p>
<div class="yamarket-code">&lt;IfModule mod_rewrite.c&gt;
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-l
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !/bitrix/urlrewrite.php$
    RewriteRule ^(.*)$ /bitrix/urlrewrite.php [L]
&lt;/IfModule&gt;</div>
';
$MESS['YANDEX_MARKET_UI_TRADING_HELLO_TEST_ERROR_HTTP_REDIRECT'] = '�������� ��������';
$MESS['YANDEX_MARKET_UI_TRADING_HELLO_TEST_ERROR_HTTP_REDIRECT_DESCRIPTION'] = '
<h2 class="pos--top">�������� ��������</h2>
<p>
#FROM# &mdash;&nbsp;����� �������<br />
#TO# &mdash;&nbsp;����� ���������������.
</p>
<p>��������� ���������������� ��������� ������ ���������� bitrix.</p>
<h3>.htaccess</h3>
<p>�������� ���������� <strong>RewriteCond %{REQUEST_URI} !^/bitrix/</strong> ����� ����������������� ��������� <strong>RewriteRule</strong>. �������� ��������, ������ ��������� ���������� ����� <strong>RewriteRule ^(.*)$ /bitrix/urlrewrite.php</strong>. ������:</p>
<div class="yamarket-code">&lt;IfModule mod_rewrite.c&gt;
  Options +FollowSymLinks
  RewriteEngine On

  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_URI} !\..{1,10}$
  RewriteCond %{REQUEST_URI} !(.*)/$
  <strong>RewriteCond %{REQUEST_URI} !^/bitrix/</strong>
  RewriteRule ^(.*)$ https://%{HTTP_HOST}/$1/ [L,R=301]

  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-l
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteCond %{REQUEST_FILENAME} !/bitrix/urlrewrite.php$
  RewriteRule ^(.*)$ /bitrix/urlrewrite.php [L]
  RewriteRule .* - [E=REMOTE_USER:%{HTTP:Authorization}]
&lt;/IfModule&gt;
</div>
';
$MESS['YANDEX_MARKET_UI_TRADING_HELLO_TEST_ERROR_TOKEN_MISSING'] = '����� ����������� ������';
$MESS['YANDEX_MARKET_UI_TRADING_HELLO_TEST_ERROR_TOKEN_MISSING_DESCRIPTION'] = '
<h2 class="pos--top">����� ����������� ������</h2>
<p>��������� ��������� Authorization HTTP ������� ���������� ������ �����������, ����� �������������� ������� �� ����������, ��������� ���������� ������� REMOTE_USER (��� REDIRECT_REMOTE_USER, ��� HTTP_AUTHORIZATION).</p>
<h3>Apache</h3>
<p>���� <strong>.htaccess</strong> ������ ��������� ������� <strong>RewriteRule .* - [E=REMOTE_USER:%{HTTP:Authorization}]</strong>. ������:</p>
<div class="yamarket-code">&lt;IfModule mod_rewrite.c&gt;
  Options +FollowSymLinks
  RewriteEngine On

  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-l
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteCond %{REQUEST_FILENAME} !/bitrix/urlrewrite.php$
  RewriteRule ^(.*)$ /bitrix/urlrewrite.php [L]
  <strong>RewriteRule .* - [E=REMOTE_USER:%{HTTP:Authorization}]</strong>
&lt;/IfModule&gt;
</div>
<h3>Nginx ��� CGI/FastCGI</h3>
<p>��� ���������� ���������� � �������-���������� ��� ��������� �������� ���������� �����������.</p>
';
$MESS['YANDEX_MARKET_UI_TRADING_HELLO_TEST_ERROR_BODY_MISSING'] = '���������� ������� �������';
$MESS['YANDEX_MARKET_UI_TRADING_HELLO_TEST_ERROR_BODY_MISSING_DESCRIPTION'] = '
<h2 class="pos--top">���������� ������� �������</h2>
<p>��� ��������� �������� ������������ urlrewrite. ��������� �������� � ������� ��������� ErrorDocument �� ��������������, � ����� ������ ���� ������� �� ����������.</p>
<h3>Apache</h3>
<p>���� <strong>.htaccess</strong> ������ ��������� ������� urlrewrite.php (<a href="https://dev.1c-bitrix.ru/api_help/main/general/urlrewrite.php">������������</a>, ������ &mdash;&nbsp;����������� ������� ��������� �������).</p>
<div class="yamarket-code">&lt;IfModule mod_rewrite.c&gt;
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-l
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !/bitrix/urlrewrite.php$
    RewriteRule ^(.*)$ /bitrix/urlrewrite.php [L]
&lt;/IfModule&gt;</div>
<h3>Nginx ��� CGI/FastCGI</h3>
<p>��� ���������� ���������� � �������-���������� ��� ��������� ��������� �������� urlrewrite ��� ������������� �������� ��������� ������.</p>
';
$MESS['YANDEX_MARKET_UI_TRADING_HELLO_TEST_ERROR_INTERNAL_ERROR'] = '���������� ������';
$MESS['YANDEX_MARKET_UI_TRADING_HELLO_TEST_ERROR_INTERNAL_ERROR_DESCRIPTION'] = '
<h2 class="pos--top">���������� ������</h2>
<p>��� ��������� �������� ��������� ���������� ������:</p>
<div class="yamarket-code">#RESPONSE#</div>
';
$MESS['YANDEX_MARKET_UI_TRADING_HELLO_TEST_ERROR_SOCKET_CONNECT'] = '������ �����������';
$MESS['YANDEX_MARKET_UI_TRADING_HELLO_TEST_ERROR_SOCKET_CONNECT_DESCRIPTION'] = '
<h2 class="pos--top">������ �����������</h2>
<p>��������� ������������ ��������� ����� � ������� https-�����������.</p>
';
$MESS['YANDEX_MARKET_UI_TRADING_HELLO_TEST_ERROR_CLIENT_ERROR'] = '������ �������';
$MESS['YANDEX_MARKET_UI_TRADING_HELLO_TEST_ERROR_CLIENT_ERROR_DESCRIPTION'] = '
<h2 class="pos--top">������ �������</h2>
<p>��� ���������� ������� �������� ������:</p>
<div class="yamarket-code">#ERROR#</div>
';
$MESS['YANDEX_MARKET_UI_TRADING_HELLO_TEST_ERROR_UNKNOWN'] = '����������� ������';
$MESS['YANDEX_MARKET_UI_TRADING_HELLO_TEST_ERROR_UNKNOWN_DESCRIPTION'] = '
<h2 class="pos--top">����������� ������</h2>
<p>��� ���������� ������� �������� ������:</p>
<div>C����� ������ &mdash;&nbsp;#STATUS#</div>
<div class="yamarket-code">#RESPONSE#</div>
';
$MESS['YANDEX_MARKET_UI_TRADING_HELLO_TEST_ERROR_LOCAL_REDIRECT'] = '�������� ��������';
$MESS['YANDEX_MARKET_UI_TRADING_HELLO_TEST_ERROR_LOCAL_REDIRECT_DESCRIPTION'] = '
<h2 class="pos--top">�������� ��������</h2>
<p>� ����� <strong>#FILE#</strong> �� ������ <strong>#LINE#</strong> ����������� �������� �� ����� <strong>#URL#</strong>. ���������� � ������������� �����, ����� ��������� ��������� ������ ���������� <strong>/bitrix/</strong>.</p>
<p>���� ������:</p>
<div class="yamarket-code">#TRACE#</div>
';
$MESS['YANDEX_MARKET_UI_TRADING_HELLO_TEST_ERROR_MODULE_REDIRECT'] = '�������� ��������';
$MESS['YANDEX_MARKET_UI_TRADING_HELLO_TEST_ERROR_MODULE_REDIRECT_DESCRIPTION'] = '
<h2 class="pos--top">�������� ��������</h2>
<p>������ <strong>#MODULE#</strong> ��������� �������� �� ����� <strong>#URL#</strong>. ���������� � ������������� ������, ����� ��������� ��������� ������ ���������� <strong>/bitrix/</strong>.</p>
<p>���� ������:</p>
<div class="yamarket-code">#TRACE#</div>
';