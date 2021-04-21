<?
use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

$strExample = <<< XML
<feed xmlns="http://www.w3.org/2005/Atom">
  <id>tag:blogger.com,1999:blog-4719018909174563858</id>
  <updated>2015-01-23T15:26:19.468-08:00</updated>
  <title type="text">Примеры фида Atom в Google Новостях</title>
  <subtitle type="html">Пример подзаголовка</subtitle>
  <author>
    <name>Анна Иванова</name>
    <email>pochta@example.com</email>
  </author>
  <entry>
    <id>http://example.com/sample-atom-478956386763692725</id>
    <published>2015-01-23T15:17:00.004-08:00</published>
    <updated>2015-01-23T15:26:19.486-08:00</updated>
    <title type="text">Образец сообщения Atom № 1</title>
    <content type="html">
      <![CDATA[<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras egestas mattis lectus eget porttitor. Nunc iaculis luctus libero, quis viverra mi ultricies sed. Nulla pellentesque dui sed maximus tristique. Sed tempor pulvinar ex in mattis.</p><p><a href='http://www.google.com/'>Образец ссылки</a></p><p><a href='https://www.google.com/images/srpr/logo11w.png'><img border='0' height='113' src='https://www.google.com/images/srpr/logo11w.png' width='320' /></a></p><p>Nulla dictum magna orci, et accumsan velit elementum sit amet. Vestibulum egestas, nulla nec facilisis iaculis, elit metus molestie mi, et vulputate enim eros vitae sem. Aliquam eget sagittis dui. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Etiam mattis ex sit amet lectus semper tempus.</p><iframe width='560' height='315' src='//www.youtube.com/embed/IS9gmW7uFXo' frameborder='0'></iframe>]]>
    </content>
    <author>
      <name>Анна Иванова</name>
      <email>pochta@example.com</email>
    </author>
  </entry>
</feed>
XML;
if(!Helper::isUtf()){
	$strExample = Helper::convertEncoding($strExample, 'UTF-8', 'CP1251');
}
?>
<div class="acrit-exp-plugin-example">
	<pre><code class="xml"><?=htmlspecialcharsbx($strExample);?></code></pre>
</div>
<script>
$('.acrit-exp-plugin-example pre code.xml').each(function(i, block) {
	highlighElement(block);
});
</script>
