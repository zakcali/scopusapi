<!DOCTYPE html>
<!-- scopusapi V2.4: bu yazılım Dr. Zafer Akçalı tarafından oluşturulmuştur 
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
require_once 'getScopusPublication.php';
$sp=new getScopusPublication ();

if (isset($_POST['scopusid'])) {
	$gelenScopus=trim($_POST["scopusid"]);
		if($gelenScopus!=""){
			$sp->scopusPublication ($gelenScopus);
				}
}
?>
<a href="eid nerede.png" target="_blank"> Scopus numarasına nereden bakılır? </a>
<form method="post" action="">
Scopus makale numarasını (eid) giriniz. <?php echo ' '.$sp->dikkat;?><br/>
<input type="text" name="scopusid" id="scopusid" value="<?php echo $sp->scopusid;?>" >
<input type="submit" value="Scopus yayın bilgilerini PHP ile getir">
</form>
<button id="scopusGoster" onclick="scopusGoster()">Scopus yayınını göster</button>
<button id="scopusAtifGoster" onclick="scopusAtifGoster()">Scopus yayınının atıflarını göster</button>
<button id="pubmedGit" onclick="pubmedGit()">pubmed ile yayına git</button>
<button id="doiGit" onclick="doiGit()">doi ile makaleyi göster</button>
<br/>
Scopus eid: <input type="text" name="eid" size="25" id="eid" value="<?php echo $sp->scopusid;?>" >  
doi: <input type="text" name="doi" size="55"  id="doi" value="<?php echo $sp->doi;?>"> <br/>
Başlık: <input type="text" name="ArticleTitle" size="96"  id="ArticleTitle" value="<?php echo str_replace ('"',  '&#34',$sp->ArticleTitle);?>"> <br>
Dergi ismi: <input type="text" name="Title" size="80"  id="Title" value="<?php echo $sp->dergi;?>"> <br>
ISSN: <input type="text" name="ISSN" size="8"  id="ISSN" value="<?php echo $sp->ISSN;?>">
eISSN: <input type="text" name="eISSN" size="8"  id="eISSN" value="<?php echo $sp->eISSN;?>">
ISBN: <input type="text" name="ISBN" size="30"  id="ISBN" value="<?php echo $sp->ISBN;?>"> <br>
Yıl: <input type="text" name="Year" size="4"  id="Year" value="<?php echo $sp->Year;?>">
Cilt: <input type="text" name="Volume" size="2"  id="Volume" value="<?php echo $sp->Volume;?>">
Sayı: <input type="text" name="Issue" size="2"  id="Issue" value="<?php echo $sp->Issue;?>">
Sayfa/numara: <input type="text" name="StartPage" size="5"  id="StartPage" value="<?php echo $sp->StartPage;?>">
- <input type="text" name="EndPage" size="2"  id="EndPage" value="<?php echo $sp->EndPage;?>">
Yazar sayısı: <input type="text" name="yazarS" size="2"  id="yazarS" value="<?php echo $sp->yazarS;?>"><br>
Yazarlar: <input type="text" name="yazarlar" size="95"  id="yazarlar" value="<?php echo $sp->yazarlar;?>"><br>
Kitap/dergi: <input type="text" name="KitapDergi" size="12"  id="KitapDergi" value="<?php echo $sp->kitapDergi;?>">
Yayınevi:<input type="text" name="Publisher" size="65"  id="Publisher" value="<?php echo $sp->publisher;?>"><br>
Yayın türü: <input type="text" name="PublicationType" size="20"  id="PublicationType" value="<?php echo $sp->PublicationType;?>">
PMID: <input type="text" name="pmid" size="6"  id="pmid" value="<?php echo $sp->PMID;?>">
Aldığı atıf: <input type="text" name="citedBy" size="4"  id="citedBy" value="<?php echo $sp->atif;?>">
<br/>
Özet <br/>
<textarea rows = "20" cols = "100" name = "ozet" id="ozetAlan"><?php echo $sp->AbstractText;?></textarea>  <br/>
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
if ( w != '')
	window.open(urlText,"_blank");
}
function pubmedGit() {
var	w=document.getElementById('pmid').value.replace(/\D/g, "");
urlText = "https://pubmed.ncbi.nlm.nih.gov/"+w;
if ( w != '')
	window.open(urlText,"_blank");
}
</script>
</body>
</html>