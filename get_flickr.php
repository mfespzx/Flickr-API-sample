class getFlickr 
{
	private $keyword; 

	function __construct($keyword) {
		$this->keyword = $keyword;
	}
	

	function getFlickr()
	{
		$last60day = date('U', strtotime("-60 day"));
		$limit = 12;
		$api_key  = 'APIKEY';
		$method   = 'flickr.photos.search';
		$text = urlencode($this->keyword);
		$min_upload_date = $last60day;
		$sort = 'relevance';//'interestingness-desc'; //'date-posted-desc';
		$per_page = $limit;
		$url = 'http://api.flickr.com/services/rest/?'.
			'method=' . $method.
			'&api_key=' . $api_key.
			'&text=' . $text.
			'&min_upload_date=' . $min_upload_date.
			'&sort=' . $sort.
			'&per_page=' . $per_page.
			'&license=1,2,6';

		$data = simplexml_load_file($url)
		or die("XML parse error");
		$size = "_t";
		$sizeL = "_b";

		// DIRからイメージファイルのみ抽出
		$fileArray = scandir("test");
		$imgArray = array();
		foreach ($fileArray as $file) {
			if (preg_match("/(.+?).jpg/", $file)) {
				preg_match("/(.+?).jpg/", $file, $match);
				$imgArray[] = $match[0];
			} else {
				continue;
			}
		}
		foreach($data->photos->photo as $photo){
			$imgsrc = "http://farm" . $photo['farm'] . ".static.flickr.com/" . $photo['server'] . "/" . $photo['id'] . "_" . $photo['secret'] . $sizeL .".jpg";
			$imginfo = getimagesize($imgsrc);
			$imgname = $photo['id'] . '_' . $photo['secret'] . '.jpg';
			if (in_array($imgname, $imgArray)) {
				echo "image already <br>";
				continue;
			} 
			if ($imginfo[0] < 800) {
				continue;
			}
			$imgData = file_get_contents($imgsrc);
			file_put_contents('./test/' . $imgname, $imgData);
		}

	}
}
