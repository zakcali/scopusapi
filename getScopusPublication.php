<?php
class getScopusPublication {
	
	function __construct() {
		$this->initialize();
		}
	function initialize () {
		$this->scopusid=''; $this->doi=''; $this->ArticleTitle=''; $this->dergi=''; $this->publisher=''; $this->ISSN=''; $this->eISSN=''; $this->Year=''; $this->Volume=''; $this->Issue=''; $this->StartPage=''; $this->EndPage=''; $this->yazarlar=''; $this->PublicationType=''; $this->AbstractText=''; $this->PMID=''; $this->atif=''; $this->ISBN=''; $this->kitapDergi=''; $this->dikkat='';
		$this->yazarS=0; 
		}
			
	final function scopusPublication ($sid) {
	$this->initialize();
		if( substr($sid,0,7) == '2-s2.0-')
			$sid=str_replace('2-s2.0-','',$sid); // sadece rakamlı kısım
			$preText="https://api.elsevier.com/content/abstract/scopus_id/";
			$postText='?view=META_ABS&field=dc:description,authors,title,pubmed-id,eid,publicationName,volume,subtypeDescription, issueIdentifier,prism:issn,prism:isbn,prism:pageRange,publisher,coverDate,article-number,doi,citedby-count,prism:aggregationType';
			$url = $preText.$sid.$postText;
// http://schema.elsevier.com/dtds/document/bkapi/search/SCIDIRSearchViews.htm
// https://pybliometrics.readthedocs.io/en/stable/classes/AbstractRetrieval.html

// $proxy = 'proxy.x:xx';
// $proxyauth = 'xx:xx';
		$ch = curl_init();
// curl_setopt($ch, CURLOPT_PROXY, $proxy);
// curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyauth);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Accept: application/json',
		'X-ELS-APIKey: your-API-KEY'));
		$data=curl_exec($ch);
		curl_close($ch);
// print_r ($data);
		$scopusBilgi=(json_decode($data, true));
// print_r ($scopusBilgi);
		if ( isset ($scopusBilgi['error-response'])) {
			$this->dikkat = 'siteye bağlanamadı'; // message:Forbidden
			return;	}
		if ( isset ($scopusBilgi['service-error'])) {
			$this->dikkat = 'siteye bağlanamadı'; //  AUTHORIZATION_ERROR
			return;	}
		if (!isset ($scopusBilgi['abstracts-retrieval-response']['coredata']['dc:title']) ) {
			$this->dikkat='yayın bulunamadı';
			return; }
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
		if (isset($scopusBilgi['abstracts-retrieval-response']['coredata']['prism:isbn'])) { // dizi şeklinde birden fazla isbn
			if (is_array($scopusBilgi['abstracts-retrieval-response']['coredata']['prism:isbn'])) {
				foreach ($scopusBilgi['abstracts-retrieval-response']['coredata']['prism:isbn'] as $eleman) {
					$this->ISBN.=$eleman['$'].'; ';
					}
				$this->ISBN=substr ($this->ISBN,0,-2);
		} else $this->ISBN=$scopusBilgi['abstracts-retrieval-response']['coredata']['prism:isbn']; // bir tek isbn var, dizi yok
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
// yazar sayısı
		$yazarS=0;
// yazarlar
		$yazarlar="";
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
	} // final function scopusPublication
}
