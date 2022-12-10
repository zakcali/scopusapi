<!DOCTYPE html>
<!-- scopusapi V1.0: bu yazılım Dr. Zafer Akçalı tarafından oluşturulmuştur 
programmed by Zafer Akçalı, MD -->
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Scopus numarasından makaleyi bul</title>
</head>

<body>
<?php
// scopusapi
// By Zafer Akçalı, MD
// Zafer Akçalı tarafından programlanmıştır
$scopusid=$doi=$ArticleTitle=$dergi=$ISOAbbreviation=$ISSN=$eISSN=$Year=$Volume=$Issue=$StartPage=$EndPage=$yazarlar=$PublicationType=$AbstractText=$PMID=$atif=$ISBN="";
$yazarS=0;
if (isset($_POST['scopusid'])) {
$gelenScopus=trim($_POST["scopusid"]);

if($gelenScopus!=""){

if( substr($gelenScopus,0,7) == '2-s2.0-')
	$gelenScopus=str_replace('2-s2.0-','',$gelenScopus); // sadece rakamlı kısım
$preText="https://api.elsevier.com/content/abstract/scopus_id/";
$postText='?view=META_ABS&field=dc:description,authors,title,pubmed-id,eid,publicationName,volume,subtypeDescription, issueIdentifier,prism:issn,prism:isbn,prism:pageRange,coverDate,article-number,doi,citedby-count,prism:aggregationType';
$url = $preText.$gelenScopus.$postText;
// echo ($url);
// echo ("<br>");

// $proxy = 'proxy.x:xx';
// $proxyauth = 'xx:xx';

$ch = curl_init();
// curl_setopt($ch, CURLOPT_PROXY, $proxy);
// curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyauth);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Accept: application/json',
    'X-ELS-APIKey: your-api-key'));
$data=curl_exec($ch);
curl_close($ch);
// print_r ($data);
$scopusBilgi=(json_decode($data, true));
// var_dump ($scopusBilgi);

// print_r ($scopusBilgi);
if ( !isset ($scopusBilgi['error-response'])) {// message:Forbidden
// Makalenin başlığı
$ArticleTitle=$scopusBilgi['abstracts-retrieval-response']['coredata']['dc:title'];
// yayın türü, çok güvenmemek gerek, vaka takdimleri de makale olabiliyor
if (isset($scopusBilgi['abstracts-retrieval-response']['coredata']['subtypeDescription']))
	$PublicationType=$scopusBilgi['abstracts-retrieval-response']['coredata']['subtypeDescription'];
// Özet
if (isset($scopusBilgi['abstracts-retrieval-response']['coredata']['dc:description']))
	$AbstractText=$scopusBilgi['abstracts-retrieval-response']['coredata']['dc:description'];
// doi
if (isset($scopusBilgi['abstracts-retrieval-response']['coredata']['prism:doi']))
	$doi= $scopusBilgi['abstracts-retrieval-response']['coredata']['prism:doi'];
// PMID
if (isset($scopusBilgi['abstracts-retrieval-response']['coredata']['pubmed-id']))
	$PMID= $scopusBilgi['abstracts-retrieval-response']['coredata']['pubmed-id'];
// scopus numarası = eid
$scopusid=$scopusBilgi['abstracts-retrieval-response']['coredata']['eid'];

// Dergi ismi
$dergi=$scopusBilgi['abstracts-retrieval-response']['coredata']['prism:publicationName'];
// Aldığı atıf sayısı
if (isset ($scopusBilgi['abstracts-retrieval-response']['coredata']['citedby-count']))
	$atif=$scopusBilgi['abstracts-retrieval-response']['coredata']['citedby-count'];

// dergi kısa ismi
// $ISOAbbreviation = $scopusBilgi['source']['abbreviatedSourceTitle'];

// issn ve eissn
if (isset($scopusBilgi['abstracts-retrieval-response']['coredata']['prism:issn'])) {
	$issntext=$scopusBilgi['abstracts-retrieval-response']['coredata']['prism:issn'];
	$ISSN=substr ($issntext,0,4).'-'.substr ($issntext,4,4);
	if (strlen ($issntext)==17)
		$eISSN=substr ($issntext,9,4).'-'.substr ($issntext,13,4);
}
// isbn, kitaplar için
if (isset($scopusBilgi['abstracts-retrieval-response']['coredata']['prism:isbn']))
		$ISBN=$scopusBilgi['abstracts-retrieval-response']['coredata']['prism:isbn'];
// Derginin basıldığı / yayımlandığı yıl
if (isset($scopusBilgi['abstracts-retrieval-response']['coredata']['prism:coverDate']))
	$Year= substr ($scopusBilgi['abstracts-retrieval-response']['coredata']['prism:coverDate'],0,4);
// cilt
if (isset($scopusBilgi['abstracts-retrieval-response']['coredata']['prism:volume']))
	$Volume= $scopusBilgi['abstracts-retrieval-response']['coredata']['prism:volume'];
// sayı
if (isset($scopusBilgi['abstracts-retrieval-response']['coredata']['prism:issueIdentifier']))
	$Issue= $scopusBilgi['abstracts-retrieval-response']['coredata']['prism:issueIdentifier'];

// başlangıç-bitiş sayfası
if (isset($scopusBilgi['abstracts-retrieval-response']['coredata']['prism:pageRange'])) {
	$sayfalar=explode ("-", $scopusBilgi['abstracts-retrieval-response']['coredata']['prism:pageRange']);
	$StartPage= $sayfalar[0];
	$EndPage=$sayfalar[1];
}
else if (isset($scopusBilgi['abstracts-retrieval-response']['coredata']['article-number'])) {
		$StartPage=$scopusBilgi['abstracts-retrieval-response']['coredata']['article-number'];
}
// yazarlar
$yazarlar="";
// yazar sayısı
$yazarS=0;
foreach( $scopusBilgi['abstracts-retrieval-response']['authors']['author'] as $name) {
	$soyisim=$name['preferred-name']['ce:surname'];
//	if (($name['firstName']))
		$isim=$name['preferred-name']['ce:given-name'];
//	else $isim=$name['initials'];
	$yazarlar=$yazarlar.$isim." ".$soyisim.", ";
	$yazarS=$yazarS+1;
		}
$yazarlar=substr ($yazarlar,0,-2);
		} // {"message":"Forbidden"} hatası gelmedi
	} 
}
?>
<a href="eid nerede.png" target="_blank"> Scopus numarasına nereden bakılır? </a>
<form method="post" action="">
Scopus makale numarasını (eid) giriniz<br/>
<input type="text" name="scopusid" id="scopusid" value="<?php echo $scopusid;?>" >
<input type="submit" value="Scopus yayın bilgilerini PHP ile getir">
</form>
<button id="scopusGoster" onclick="scopusGoster()">Scopus yayınını göster</button>
<button id="scopusAtifGoster" onclick="scopusAtifGoster()">Scopus yayınının atıflarını göster</button>
<button id="doiGit" onclick="doiGit()">doi ile makaleyi göster</button>
<br/>
Scopus eid: <input type="text" name="eid" size="25" id="eid" value="<?php echo $scopusid;?>" >  
doi: <input type="text" name="doi" size="55"  id="doi" value="<?php echo $doi;?>"> <br/>
Makalenin başlığı: <input type="text" name="ArticleTitle" size="85"  id="ArticleTitle" value="<?php echo $ArticleTitle;?>"> <br/>
Dergi ismi: <input type="text" name="Title" size="50"  id="Title" value="<?php echo $dergi;?>"> 
Kısa ismi: <input type="text" name="ISOAbbreviation" size="26"  id="ISOAbbreviation" value="<?php echo $ISOAbbreviation;?>"> <br/>
ISSN: <input type="text" name="ISSN" size="8"  id="ISSN" value="<?php echo $ISSN;?>">
eISSN: <input type="text" name="eISSN" size="8"  id="eISSN" value="<?php echo $eISSN;?>">
ISBN: <input type="text" name="ISBN" size="8"  id="ISBN" value="<?php echo $ISBN;?>"> <br/><br/>
Yıl: <input type="text" name="Year" size="4"  id="Year" value="<?php echo $Year;?>">
Cilt: <input type="text" name="Volume" size="2"  id="Volume" value="<?php echo $Volume;?>">
Sayı: <input type="text" name="Issue" size="2"  id="Issue" value="<?php echo $Issue;?>">
Sayfa/numara: <input type="text" name="StartPage" size="5"  id="StartPage" value="<?php echo $StartPage;?>">
- <input type="text" name="EndPage" size="2"  id="EndPage" value="<?php echo $EndPage;?>">
Yazar sayısı: <input type="text" name="yazarS" size="2"  id="yazarS" value="<?php echo $yazarS;?>"><br/>
Yazarlar: <input type="text" name="yazarlar" size="95"  id="yazarlar" value="<?php echo $yazarlar;?>"><br/>
Yayın türü: <input type="text" name="PublicationType" size="20"  id="PublicationType" value="<?php echo $PublicationType;?>">
PMID: <input type="text" name="PMID" size="6"  id="PMID" value="<?php echo $PMID;?>">
Aldığı atıf: <input type="text" name="citedBy" size="4"  id="citedBy" value="<?php echo $atif;?>">
<br/>
Özet <br/>
<textarea rows = "20" cols = "90" name = "ozet" id="ozetAlan"><?php echo $AbstractText;?></textarea>  <br/>
<script>
function scopusGoster() {
var	w=document.getElementById('eid').value.replace('2-s2.0-','');
	urlText = "https://www.scopus.com/inward/record.uri?partnerID=HzOxMe3b&scp=" + w+"&origin=inward";
	window.open(urlText,"_blank");
}
function scopusAtifGoster() {
var	w=document.getElementById('eid').value;
	urlText = "https://www.scopus.com/search/submit/citedby.uri?eid="+w+"&src=s&origin=resultslist";
	window.open(urlText,"_blank");
}
function doiGit() {
var	w=document.getElementById('doi').value;
	urlText = "https://doi.org/"+w;
	window.open(urlText,"_blank");
}
</script>
</body>
</html>
