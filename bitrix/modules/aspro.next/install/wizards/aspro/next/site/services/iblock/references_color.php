<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)	die();

if(!defined("WIZARD_SITE_ID")) return;
if(!defined("WIZARD_SITE_DIR")) return;
if(!defined("WIZARD_SITE_PATH")) return;
if(!defined("WIZARD_TEMPLATE_ID")) return;
if(!defined("WIZARD_TEMPLATE_ABSOLUTE_PATH")) return;
if(!defined("WIZARD_THEME_ID")) return;

$bitrixTemplateDir = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."/";
//$bitrixTemplateDir = $_SERVER["DOCUMENT_ROOT"]."/local/templates/".WIZARD_TEMPLATE_ID."/";	

set_time_limit(0);

if (!CModule::IncludeModule("highloadblock"))
	return;

if (!WIZARD_INSTALL_DEMO_DATA)
	return;

$HL_ID = $_SESSION["NEXT_HBLOCK_COLOR_ID"];
unset($_SESSION["NEXT_HBLOCK_COLOR_ID"]);

//adding rows
WizardServices::IncludeServiceLang("references.php", LANGUAGE_ID);

use Bitrix\Highloadblock as HL;
global $USER_FIELD_MANAGER;

if($HL_ID){
	$hldata = HL\HighloadBlockTable::getById($HL_ID)->fetch();
	$hlentity = HL\HighloadBlockTable::compileEntity($hldata);

	$entity_data_class = $hlentity->getDataClass();
	$arColors = array(
		"PURPLE" => "references_files/iblock/c83/c83a3686bf6c835d451529d76e89cad8.png",
		"BROWN" => "references_files/iblock/aaf/aaf783fa069e20ea30337d1cc47c4b51.png",
		"WHITE" => "references_files/iblock/198/198144922e4d4467dc05d60657225561.png",
		"BLUE" => "references_files/iblock/1f0/1f0a6ff2475e5b4092c97e8ca6ccb949.png",
		"BLACK" => "references_files/iblock/bfa/bfa103d49398290243ea6dcaa4eeb063.png",
		"GRAY" => "references_files/iblock/b8f/b8f88be82f50c3f82a433436aef4e2f3.png",
		"RED" => "references_files/iblock/185/185eca4ec3dfbc4eaa469e042e059b21.png",
		"C2" => "references_files/iblock/f59/f59d613a490e8508b2f6591df23f1310.jpg",
		"C3_5" => "references_files/iblock/f12/f12bb0113ae6e5fbbab89c7871be815e.jpg",
		"C3" => "references_files/iblock/66b/66b5156552a54ee43a604d3d037c83f1.jpg",
		"C4_5" => "references_files/iblock/214/2143bfaac712981a0e790479b4aac2f4.jpg",
		"C4" => "references_files/iblock/94a/94adc271a57dadb714621b7a17de7540.jpg",
		"C5_5" => "references_files/iblock/7c1/7c1a5af257c79d1db5fc048b4aa1cbfd.jpg",
		"C6" => "references_files/iblock/58f/58fbe5b1c0856110785bf6b2ce8ce755.jpg",
		"C7" => "references_files/iblock/d0f/d0fdfcbc487344ed5f902abca76f3c32.jpg",
		"C8" => "references_files/iblock/ed1/ed160c1cc132584723732fd755db333c.jpg",
		"C30" => "references_files/iblock/ac8/ac873fe2ab73bc075b06db89be42f125.jpg",
		"C35" => "references_files/iblock/f02/f025b8c01241d4ff01c2b6dbcf2d08f3.jpg",
		"C40" => "references_files/iblock/113/11364443a6fe83c8700eecb0776a157b.jpg",
		"N3" => "references_files/iblock/d6b/d6bd4c0890eb3c423da51dc5a6eec5d9.jpg",
		"N4" => "references_files/iblock/2d1/2d11986ba49580164f8280ec24a9dda0.jpg",
		"N5" => "references_files/iblock/ba5/ba53ae4d68b35b4ebf5f07c5f93274fb.jpg",
		"N9" => "references_files/iblock/c5e/c5e50fcbc9a683181fd79eeed7bd90b9.jpg",
		"NC15" => "references_files/iblock/49b/49b2be0dff54df1a5e8b3418cb5f637e.jpg",
		"NC20" => "references_files/iblock/44f/44fc7c831738af332fc9498bd6683549.jpg",
		"NC25" => "references_files/iblock/7a7/7a78ba59437c3e9820d37b0eb9490843.jpg",
		"NC30" => "references_files/iblock/fbe/fbecd45ed88069dd69b30ffc36c10cc3.jpg",
		"NC35" => "references_files/iblock/59f/59fd171db27a2770927feac08e0212f7.jpg",
		"NC40" => "references_files/iblock/d9c/d9c01ef336434be73bc494162a8834c3.jpg",
		"NC42" => "references_files/iblock/c60/c60e4b6cc9b716c039b63dbfb0750f29.jpg",
		"NC43" => "references_files/iblock/a3e/a3e4478df2a706ae2711d3725b384c9e.jpg",
		"NC45" => "references_files/iblock/e27/e27e706b4d9ea0758359235eca80f218.jpg",
		"NC50" => "references_files/iblock/ed1/ed1531bedd1db9bd23db14c26c4abcd1.jpg",
		"NC55" => "references_files/iblock/cdd/cdd1a82c1d2d630536a1b96417abdee3.jpg",
		"NW10" => "references_files/iblock/bdf/bdf10b9f944245af888dbe63a191785b.jpg",
		"NW13" => "references_files/iblock/3c7/3c704df63810c1a2d085b17fedd10ac0.jpg",
		"NW15" => "references_files/iblock/4d3/4d392dc2cb5938a48eb848297d863eb8.jpg",
		"NW18" => "references_files/iblock/655/6550cb7789af90d1c4c91e00bcab304d.jpg",
		"NW20" => "references_files/iblock/8a0/8a07d72288d26f0edcb6d1df4b0021b3.jpg",
		"NW22" => "references_files/iblock/3a8/3a89c59869932396642b5682de85c4ea.jpg",
		"NW25" => "references_files/iblock/0b3/0b376f86f75c23105f1a8f3e89b3de65.jpg",
		"NW30" => "references_files/iblock/796/796698b567f1410e615911214b289fe2.jpg",
		"NW33" => "references_files/iblock/204/20432a2a01999df0148d95874778c900.jpg",
		"NW35" => "references_files/iblock/90a/90a376f4816e95b1711b5cf5ef543f05.jpg",
		"NW40" => "references_files/iblock/c8b/c8b4d60baf95661db79dbbccacb777d7.jpg",
		"NW43" => "references_files/iblock/0ca/0ca7aa0bde03c047f5ad11d0f8ab13e8.jpg",
		"NW45" => "references_files/iblock/e98/e982b3d54899f3d6068e0e4a5455c92c.jpg",
		"NW50" => "references_files/iblock/6d6/6d6004988ae46f9e10f8fae7071a2de7.jpg",
		"13" => "references_files/iblock/789/7899517a1aa5815e9064c36888158eca.png",
		"14" => "references_files/iblock/861/8613d5e4cbf023ef08ed6641cfee20ab.png",
		"22" => "references_files/iblock/c3c/c3ca0ba702bd1b046359137da16e5790.png",
		"30" => "references_files/iblock/3aa/3aa7e343352778e25510e30d23ac1978.png",
		"31" => "references_files/iblock/13d/13d634439a8da9f3edbd65193e92abbd.png",
		"32" => "references_files/iblock/940/9408e7acf0be04650ad66d5aedf308ea.png",
		"33" => "references_files/iblock/cf6/cf6d1f1b8359103baf40060f01de42e6.png",
		"35" => "references_files/iblock/e89/e89d5d691d482532643bd67c89b2b5e4.png",
		"37" => "references_files/iblock/792/7921d074e3629fcfdd1d8674967e0c5f.png",
		"45" => "references_files/iblock/7c5/7c5112198b069e4cfa66e151bffab7f3.png",
		"51" => "references_files/iblock/a29/a297b0e92fdcf0c3d0af539c42dfa5de.png",
		"52" => "references_files/iblock/1d5/1d5d180561531c9e8969810d59073ab8.png",
		"56" => "references_files/iblock/9c0/9c0dbb25a0f772107791fd42667b5be0.png",
		"58" => "references_files/iblock/973/973c619a386e6bfcdf85c62d12485e29.png",
		"59" => "references_files/iblock/6a7/6a72c6305e147bf5836cafb7b51c7f9a.png",
		"61" => "references_files/iblock/43b/43b0465c9ebb717d9d4bb471d8489288.png",
		"71" => "references_files/iblock/f9f/f9feb1935b8176daf5a23d83fdb306b5.png",
		"72" => "references_files/iblock/655/6552659cd6699d24a18baf20bdfbbb42.png",
		"73" => "references_files/iblock/e0c/e0cdc8d81eac32c10384b56346c993db.png",
		"75" => "references_files/iblock/c17/c174e5a74ce7f52c2dcee6ea8d384b2c.png",
		"76" => "references_files/iblock/uf/543/543686b66ed62a2663a6beaf42f8fad4.png",
		"79" => "references_files/iblock/85a/85a69b8a5e3988d922c6fcfb0da097fc.png",
		"80" => "references_files/iblock/336/33649d34fbabea6516851e8aad4529dd.png",
		"82" => "references_files/iblock/88c/88c5462fb49f3e4cbd8545b98e273640.png",
		"84" => "references_files/iblock/de1/de1b82c34b78fa41b24fcaea7dfca46c.png",
		"85" => "references_files/iblock/a81/a812a2e1cffd3ca015bbf44b61793d10.png",
		"86" => "references_files/iblock/797/79752030f2c5689c0f41f292afb3d420.png",
		"88" => "references_files/iblock/0be/0beabc0b2572e112dee2a63ef48c7f97.png",
		"111" => "references_files/iblock/5b5/5b558c8353480a48ace49db4d9ea5475.png",
		"112" => "references_files/iblock/439/439a2e760e4d6c56159719ae3677c5f8.png",
		"113" => "references_files/iblock/913/9138124e73fd5181bb7fd9e0aba24664.png",
		"114" => "references_files/iblock/eee/eee36e2f6bfe987b8d296a9c5929c334.png",
		"115" => "references_files/iblock/133/133272360a00e0ddb8a0b3200faeee25.png",
		"116" => "references_files/iblock/d17/d1757c1f00cec6b94a3434f573558a90.png",
		"117" => "references_files/iblock/807/8077f7aca29f68136179524ec04321ec.png",
		"118" => "references_files/iblock/43f/43f5fe4ba550c927b93344872f7123d5.png",
		"119" => "references_files/iblock/49c/49c970d1330094d556ca69874d0ed51b.png",
		"120" => "references_files/iblock/8d9/8d92649447a94f5c3ddd4118a047c18d.png",
		"116zol" => "references_files/iblock/4eb/4eb8610cc338552f984c0ca7fab4acfc.png",
		"130andr" => "references_files/iblock/399/399001a7ea52f63c75e0d3f88d76fcf0.png",
		"131mistanget" => "references_files/iblock/716/716e00c83f23a157a326fe09b4f56414.png",
		"133bes" => "references_files/iblock/016/0160d78b8953e41aad1f85ea26702fab.png",
		"134shchegol" => "references_files/iblock/7c4/7c4e38696c6aa644badbc7378ead5938.png",
		"136izyashchnyyflamingo" => "references_files/iblock/150/1509ac7a41709d932b31866d3e50033e.jpg",
		"228vip" => "references_files/iblock/572/572af900e6c671174bb710bd9eddf7ab.jpg",
		"229klishi" => "references_files/iblock/69f/69f9bbe5dc5e53cd003b82d349740a17.jpg",
		"230korallovyypokaz" => "references_files/iblock/c3f/c3f21f7fc3fa8effba5100cfcc562206.jpg",
		"232nezhnyy" => "references_files/iblock/ed8/ed896b16dcbc41bbc27a86b12693603b.jpg",
		"235nezhnayaroza" => "references_files/iblock/b5b/b5bbb31b004f83123eae87b75ab15c96.jpg",
		"238vecheri" => "references_files/iblock/2a4/2a4db297f1874a5f2e6e918279597686.jpg",
		"256igriv" => "references_files/iblock/3e4/3e4dd1187f63411c9b0cea459013057c.jpg",
		"265rozv" => "references_files/iblock/a06/a067902cb27c1e560a96119459549946.jpg",
		"285rozv" => "references_files/iblock/262/262b96652d126012335bf37131018392.jpg",
		"302rozv" => "references_files/iblock/4c3/4c3b543c7001625e7c3129b248779b47.jpg",
		"303rozv" => "references_files/iblock/d90/d90ba61d47f64db98bf7228e37b0eb64.jpg",
		"335vkras" => "references_files/iblock/8f5/8f56fa5ae6a5e0e97410af4472a1de27.jpg",
		"364vandm" => "references_files/iblock/3e9/3e969b766f0e2a27536b908b1b9a3189.jpg",
		"377sover" => "references_files/iblock/73a/73a2de21e1f038a1e69ea89b72539539.jpg",
		"378rozovyy" => "references_files/iblock/beb/bebb5b15496a14571dda22778c68326c.jpg",
		"631bess" => "references_files/iblock/daf/daf210b617d951c0ac0648a283bf4702.jpg",
		"632surov" => "references_files/iblock/35d/35d64461b156bf7428d53ba541881cb1.jpg",
		"703oder" => "references_files/iblock/4f2/4f293faea8b389c105e566e47b87ec3a.jpg",
		"005sakha" => "references_files/iblock/301/301ddb58a1baf06cd76a0068bd214cd4.jpg",
		"006klyukv" => "references_files/iblock/a07/a07efdabc17654cf2a3893e0245456d0.jpg",
		"015fruktsok" => "references_files/iblock/a8d/a8defb9e3d22c03655ee9b4bd583685b.jpg",
		"016dikayayagoda" => "references_files/iblock/114/114f617782a2fc1a013948734191e533.jpg",
		"019parizh" => "references_files/iblock/ef7/ef7c3faab9210e10de71d0b2326d9518.jpg",
		"023gork" => "references_files/iblock/bee/bee2eae07ded301506c04c4337853063.jpg",
		"026zeml" => "references_files/iblock/5a3/5a318579f50db486ccdfb64630f060bb.jpg",
		"031milksheyk" => "references_files/iblock/24f/24f6546cffb689cd71f8ce724a960d9c.jpg",
		"032sakhar" => "references_files/iblock/d0c/d0cee4f98c2dbdce00e824964649287c.jpg",
		"034zhemchuzhnoeozherele" => "references_files/iblock/569/569202e60dcf54922880a27fa7684b45.jpg",
		"046zvezd" => "references_files/iblock/430/430f8b116ef8419c500e83e565462ebb.jpg",
		"051bely" => "references_files/iblock/8c0/8c01c2f317252680da4c486cdf380b24.jpg",
		"053rozovye" => "references_files/iblock/f9f/f9ffb147e6224c0bddba5a56bde81153.jpg",
		"069rozov" => "references_files/iblock/580/580526314f7efecf8042d565e19efdd9.jpg",
		"070alpiysk" => "references_files/iblock/e38/e38a50bb3e9d1079f775f6a2887f5c0e.jpg",
		"071bronzovyyzagar" => "references_files/iblock/e0a/e0a6a3fd2408f4f2c268adb58f1718b8.jpg",
		"075krasnyybarkhat" => "references_files/iblock/c1c/c1c16cd42a24ef587b4497429f9c6287.jpg",
		"080sinyaya" => "references_files/iblock/429/42976dac7a9fbadd8aff6a1e760ba8b0.jpg",
		"083klubnichnyy" => "references_files/iblock/80b/80b7d5fa6bd1324fea98e0529952c00b.jpg",
		"091korallovyy" => "references_files/iblock/f60/f6026f3b980fe324413ebbbdffdd4be6.jpg",
		"101zagadochnyy" => "references_files/iblock/cc7/cc7085d422b83edb59bd29f4209fca93.jpg",
		"103sedmoe" => "references_files/iblock/80a/80a34340c586327cbb049ce61a75100c.jpg",
		"106zheleznaya" => "references_files/iblock/451/451766d49908c60023da2174ce978194.jpg",
		"107almaz" => "references_files/iblock/641/64152233882c010b5274aba6e1503126.jpg",
		"108zolotoy" => "references_files/iblock/3a9/3a937b06b5b7923663fa236fbee05cba.jpg",
		"109kofe" => "references_files/iblock/739/739ca40b8a7c4a5801d06ad67a68aabe.jpg",
		"rozovyygibiskus" => "references_files/iblock/3f3/3f3ac166a262713c8a67022c3e5b5b23.jpg",
		"112morskayagalka" => "references_files/iblock/270/27059fb80f7a51f5db6b0df9114c5264.jpg",
		"120nyu_york" => "references_files/iblock/a8e/a8e6010930b65c0f2be1a41d71e215d1.jpg",
		"140sladkayachereshnya" => "references_files/iblock/f26/f26ef7cc8f4bb771d92746a723e5ea4c.jpg",
		"utr5ryyj" => "references_files/iblock/dbd/dbd3b5bd880061bbc15ecefc8f39b996.jpg",
		"155zavod" => "references_files/iblock/628/62864395037bbdf3952c078cdf3d054e.jpg",
		"165sogrevayushchiy" => "references_files/iblock/a76/a76e8f053d36d867ab97bc90998d8a40.jpg",
		"214myatnyy" => "references_files/iblock/a94/a9474a78b32b7b69eb7d3a05819c67e0.jpg",
		"215butony" => "references_files/iblock/ebf/ebf83ce1df6f30a6e7a31a5cbbd70827.jpg",
		"216spelyybaklazhan" => "references_files/iblock/71f/71f263d424e6874750cafbd577589e3c.jpg",
		"217letnyaya" => "references_files/iblock/105/1054a35d67c617fd7b57f0221e114025.jpg",
		"254vanil" => "references_files/iblock/e41/e41aea9d54224a534e8530dcafeb7f35.jpg",
		"261nasyshcheny" => "references_files/iblock/ef5/ef5c91e9984ea027f09ad7a34c2e03f3.jpg",
		"262rozy" => "references_files/iblock/851/851f1beb81f81c79d4c2e078423c4c3f.jpg",
		"266osennyayazelen" => "references_files/iblock/431/431d520cf52e9d95934eb3ca90d0a564.jpg",
		"267zimnyayasv" => "references_files/iblock/666/6666dd4e5817301b77db636e42cfb18a.jpg",
		"268derzki" => "references_files/iblock/4db/4db26fab5caf1198a17890c8bbc94d13.jpg",
		"269dragotse" => "references_files/iblock/05d/05d7f7f236c7d0259eb1b372a853fd31.jpg",
		"270zelenyy" => "references_files/iblock/986/986e301e06763f07fc6a838b449bc103.jpg",
		"271olivkovayaosen" => "references_files/iblock/64f/64fb6f607f87d1290974a589f3542bb2.jpg",
		"272ledyanoyvzglyad" => "references_files/iblock/57f/57fffee299ca6e4163a7413c471cfabe.jpg",
		"273nasyshchennyy" => "references_files/iblock/e2f/e2f8004bd5a4595fd8535abf2b1756e3.jpg",
		"282marokkansk" => "references_files/iblock/0de/0defc0f59a5e6234ad7c0ba95aec615f.jpg",
		"283golubayaakvarel" => "references_files/iblock/f71/f71cf7ef37ce2147697abf058444ac2f.jpg",
		"284nezabyv" => "references_files/iblock/799/7997c0c36ea72154116f836eab075245.jpg",
		"285goluba" => "references_files/iblock/2e7/2e7dc0ea0a4ea4648ac2c42dfac02a48.jpg",
		"JaJIVPUy" => "references_files/iblock/c28/c2837ecc9fac31f68a228c3c0fa1707c.jpg",
		"287ritm" => "references_files/iblock/4ea/4ea8c0faed1bf9e33f738705c4fbc373.jpg",
		"288ledyanoesiyanie" => "references_files/iblock/b0a/b0a6547242f9adaaa2cb28c33109be78.jpg",
		"289utren" => "references_files/iblock/307/307af345e6d1b4cbfad6b64d80a1a0f3.jpg",
		"290smelost" => "references_files/iblock/956/956d8776b663af293c4676a878c6857f.jpg",
		"291nochnoepriklyuche" => "references_files/iblock/84b/84b6abeb2757845b5ee5c440fe4dc401.jpg",
		"292sverkayushch" => "references_files/iblock/d49/d4904d9776acae3ef2976ec02ba0e509.jpg",
		"293moroz" => "references_files/iblock/848/8484c55b134e1172783bbb1892be356c.jpg",
		"300nezhnyy" => "references_files/iblock/441/4419f316a7c1a83dcc633a51e1db946d.jpg",
		"301rozv" => "references_files/iblock/8fe/8fe98ab5f97f6ccc1f6c0e06309464a8.jpg",
		"303nezhny" => "references_files/iblock/337/337d57f49f3f1f7f926a6087048d2f06.jpg",
		"304retro" => "references_files/iblock/38b/38b3a03d95ff86d1923265acc362123f.jpg",
		"305kholodny" => "references_files/iblock/6f2/6f2643169508a638cb9b94560da0ab44.jpg",
		"310yarkiy" => "references_files/iblock/20e/20e7cd00dfd6dcef56fab3f8b4197f16.jpg",
		"311korallovoe" => "references_files/iblock/265/2655b47c7b04594ae125e0d8ab7ac68e.jpg",
		"313sochnyyapelsin" => "references_files/iblock/8a5/8a5aa7aabee36702e1adfa7982ad17a0.jpg",
		"314lepest" => "references_files/iblock/573/573e9e3a57b709700b77f1f5b13f4d9a.jpg",
		"315klubnichny" => "references_files/iblock/f60/f60bcf77a78ad7edf100784b0a3c96f3.jpg",
		"317neveroya" => "references_files/iblock/3ed/3ed469e79d6beaf3f571ecd59198cc34.jpg",
		"318ognenny" => "references_files/iblock/bf5/bf5baca2f302a911056848e8e4bb7a6c.jpg",
		"319roskoshna" => "references_files/iblock/64d/64d431df033e99aac003b785bff197cf.jpg",
		"320aly" => "references_files/iblock/c7d/c7d52f035d3c58d29f607935753463be.jpg",
		"321yarkiymandarin" => "references_files/iblock/086/08691d1dcff8d7506979c6e24f746b70.jpg",
		"322rosko" => "references_files/iblock/8a4/8a42b3488e84dd5910a100c765db570c.jpg",
		"324tsvet" => "references_files/iblock/131/131cd7d05af7cab3cdca645255cd0314.jpg",
		"325letnya" => "references_files/iblock/f7b/f7bd609b7f6837f31c8a208b0b8347a6.jpg",
		"326zaga" => "references_files/iblock/51d/51d059c3cebfa7b4b2cf3da6b563e87f.jpg",
		"327merts" => "references_files/iblock/1c6/1c63bd3dee9dfe80d84d53212c89e57c.jpg",
		"331roz" => "references_files/iblock/a3d/a3d37e6cb53a964c7c5b7f9252edad5a.jpg",
		"334myatn" => "references_files/iblock/330/330aeb909fe1a7e32e3675c681e19511.jpg",
		"337zvezd" => "references_files/iblock/049/04905c8d2d790315d447f9f2b2d1dbfd.jpg",
		"352zakat" => "references_files/iblock/016/01692510951bf61a257c83c6473e2192.jpg",
		"440prozrachn" => "references_files/iblock/130/130a051dfcd4dae0158d83459965efdb.jpg",
		"455koral" => "references_files/iblock/9c6/9c6407a409c0acb928ce5c50e4f609c6.jpg",
		"456bordo" => "references_files/iblock/9f6/9f6ce50a862390dbc1fdc8d12344e898.jpg",
		"554orkhideya" => "references_files/iblock/1a6/1a6e50ce818e6d26c99acaf3487de5bf.jpg",
		"651niagarskiyvodopad" => "references_files/iblock/09e/09e396ed197ca78797ba86a41660f127.jpg",
		"652mentol" => "references_files/iblock/659/6597c864713955b821e764cabd7cf51d.jpg",
		"654atlantich" => "references_files/iblock/0d0/0d0d093282ce0d0a758bcfc5bffd324c.jpg",
		"749banan" => "references_files/iblock/375/375c9e1a9e0117cce619c0a72df81163.jpg",
	);
	$sort = 100;
	foreach($arColors as $colorName => $colorFile){
		$arData = array(
			'UF_NAME' => GetMessage("WZD_REF_COLOR_".$colorName),
			'UF_FILE' =>
				array (
					'name' => ToLower($colorName).".png",
					'type' => 'image/png',
					'tmp_name' => WIZARD_ABSOLUTE_PATH."/site/services/iblock/".$colorFile
				),
			'UF_SORT' => $sort,
			'UF_DEF' => ($sort > 100) ? "0" : "1",
			'UF_XML_ID' => ToLower($colorName)
		);
		$USER_FIELD_MANAGER->EditFormAddFields('HLBLOCK_'.$HL_ID, $arData);
		$USER_FIELD_MANAGER->checkFields('HLBLOCK_'.$HL_ID, null, $arData);
		$result = $entity_data_class::add($arData);
		$sort += 100;
	}
}
?>