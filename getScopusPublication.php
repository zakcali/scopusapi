<?php
class getScopusPublication {
	public $scopusid='', $doi='', $ArticleTitle='', $dergi='', $ISOAbbreviation='', $publisher='', $ISSN='', $eISSN='', $Year='', $Volume='', $Issue='', $StartPage='', $EndPage='', $yazarlar='', $PublicationType='', $AbstractText='', $PMID='', $atif='', $ISBN='', $kitapDergi='';
	public $yazarS=0;
		    function __construct() {

		}
	final function scopusPublication ($sid) {
	
		if( substr($sid,0,7) == '2-s2.0-')
			$sid=str_replace('2-s2.0-','',$sid); // sadece rakamlı kısım
			$preText="https://api.elsevier.com/content/abstract/scopus_id/";
			$postText='?view=META_ABS&field=dc:description,authors,title,pubmed-id,eid,publicationName,volume,subtypeDescription, issueIdentifier,prism:issn,prism:isbn,prism:pageRange,publisher,coverDate,article-number,doi,citedby-count,prism:aggregationType';
			$url = $preText.$sid.$postText;
// http://schema.elsevier.com/dtds/document/bkapi/search/SCIDIRSearchViews.htm
// https://pybliometrics.readthedocs.io/en/stable/classes/AbstractRetrieval.html
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
		'X-ELS-APIKey: Your-API-KEY'));
		$data=curl_exec($ch);
		curl_close($ch);
// print_r ($data);
		$scopusBilgi=(json_decode($data, true));
// var_dump ($scopusBilgi);

// print_r ($scopusBilgi);
		if ( !isset ($scopusBilgi['error-response']) && isset ($scopusBilgi['abstracts-retrieval-response']['coredata']['dc:title']) ) {// message:Forbidden
// Makalenin başlığı
			$this->ArticleTitle=$scopusBilgi['abstracts-retrieval-response']['coredata']['dc:title'];
// publisher
			if (isset($scopusBilgi['abstracts-retrieval-response']['coredata']['dc:publisher']))
				$this->publisher=$scopusBilgi['abstracts-retrieval-response']['coredata']['dc:publisher'];
// kaynak: kitap veya dergi
			if (isset($scopusBilgi['abstracts-retrieval-response']['coredata']['prism:aggregationType']))
				$this->kitapDergi=$scopusBilgi['abstracts-retrieval-response']['coredata']['prism:aggregationType'];
// yayın türü, çok güvenmemek gerek, vaka takdimleri de makale olabiliyor
			if (isset($scopusBilgi['abstracts-retrieval-response']['coredata']['subtypeDescription']))
				$this->PublicationType=$scopusBilgi['abstracts-retrieval-response']['coredata']['subtypeDescription'];
// Özet
			if (isset($scopusBilgi['abstracts-retrieval-response']['coredata']['dc:description']))
				$this->AbstractText=$scopusBilgi['abstracts-retrieval-response']['coredata']['dc:description'];
// doi
			if (isset($scopusBilgi['abstracts-retrieval-response']['coredata']['prism:doi']))
				$this->doi= $scopusBilgi['abstracts-retrieval-response']['coredata']['prism:doi'];
// PMID
			if (isset($scopusBilgi['abstracts-retrieval-response']['coredata']['pubmed-id']))
				$this->PMID= $scopusBilgi['abstracts-retrieval-response']['coredata']['pubmed-id'];
// scopus numarası = eid
			$this->scopusid=$scopusBilgi['abstracts-retrieval-response']['coredata']['eid'];

// Dergi ismi
			$this->dergi=$scopusBilgi['abstracts-retrieval-response']['coredata']['prism:publicationName'];
// Aldığı atıf sayısı
			if (isset ($scopusBilgi['abstracts-retrieval-response']['coredata']['citedby-count']))
				$this->atif=$scopusBilgi['abstracts-retrieval-response']['coredata']['citedby-count'];

// dergi kısa ismi
// $ISOAbbreviation = $scopusBilgi['source']['abbreviatedSourceTitle'];

// issn ve eissn
			if (isset($scopusBilgi['abstracts-retrieval-response']['coredata']['prism:issn'])) {
				$issntext=$scopusBilgi['abstracts-retrieval-response']['coredata']['prism:issn'];
				$this->ISSN=substr ($issntext,0,4).'-'.substr ($issntext,4,4);
				if (strlen ($issntext)==17)
					$this->eISSN=substr ($issntext,9,4).'-'.substr ($issntext,13,4);
}
// isbn, kitaplar için, birden fazla olabilir
			if (isset($scopusBilgi['abstracts-retrieval-response']['coredata']['prism:isbn'])) {
				foreach ($scopusBilgi['abstracts-retrieval-response']['coredata']['prism:isbn'] as $eleman) {
					$this->ISBN.=$eleman['$'].'; ';
					}
				$this->ISBN=substr ($this->ISBN,0,-2);
				}
// Derginin basıldığı / yayımlandığı yıl
			if (isset($scopusBilgi['abstracts-retrieval-response']['coredata']['prism:coverDate']))
				$this->Year= substr ($scopusBilgi['abstracts-retrieval-response']['coredata']['prism:coverDate'],0,4);
// cilt
			if (isset($scopusBilgi['abstracts-retrieval-response']['coredata']['prism:volume']))
				$this->Volume= $scopusBilgi['abstracts-retrieval-response']['coredata']['prism:volume'];
// sayı
			if (isset($scopusBilgi['abstracts-retrieval-response']['coredata']['prism:issueIdentifier']))
				$this->Issue= $scopusBilgi['abstracts-retrieval-response']['coredata']['prism:issueIdentifier'];

// başlangıç-bitiş sayfası
			if (isset($scopusBilgi['abstracts-retrieval-response']['coredata']['prism:pageRange'])) {
				$sayfalar=explode ("-", $scopusBilgi['abstracts-retrieval-response']['coredata']['prism:pageRange']);
				$this->StartPage= $sayfalar[0];
				$this->EndPage=$sayfalar[1];
}
			else if (isset($scopusBilgi['abstracts-retrieval-response']['coredata']['article-number'])) {
				$this->StartPage=$scopusBilgi['abstracts-retrieval-response']['coredata']['article-number'];
}
// yazarlar
			$yazarlar="";
// yazar sayısı
			$yazarS=0;
			if (isset ($scopusBilgi['abstracts-retrieval-response']['authors'])) { // bu yayının yazarı yok: 85129231682
				foreach( $scopusBilgi['abstracts-retrieval-response']['authors']['author'] as $name) {
					$soyisim=$name['preferred-name']['ce:surname'];
//	if (($name['firstName']))
					$isim=$name['preferred-name']['ce:given-name'];
//	else $isim=$name['initials'];
					$this->yazarlar.=$isim." ".$soyisim.", ";
					$this->yazarS+=1;
					}
			}
			$this->yazarlar=substr ($this->yazarlar,0,-2);
		} // {"message":"Forbidden"} hatası gelmedi
		
		
	} // final function scopusPublication
}