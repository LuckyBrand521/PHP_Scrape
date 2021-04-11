<!DOCTYPE html>
<html>
	<head>
		<title>Email Scraping</title>
		<link rel="stylesheet" href="css/style.css">
	</head>
	<body>
		<?php
		$index_url='index.php';

		session_start();

		include("include/navbar.php");
		include("include/bootstrap_cdn.php");
		include("include/config.php");

		?>
		

		<div class="row">
			<div class="bg text-center col-md-6">
				<?php
					if(isset($_SESSION['uploaded']) && $_SESSION['uploaded']){
						
						echo "<span>The file has been uploaded.</span>";
						$_SESSION['uploaded'] = FALSE;
					}
					else{
					}
				?>
				
				<form enctype="multipart/form-data" action="include/uploader.php" method="POST">
					
					Choose a file to upload: 
					<input name="uploadedfile" type="file" /><br />
					<input type="submit" value="Upload File" />
				</form>
			</div>
			<div class="col-md-6 text-center">
				<form action="index.php" method="POST">
					Choose a email csv file to scrape: 
					<input name="email_file" type="file" class="custom-file-input" id="customFile"><br>
					<input type="submit" value = "Scrape!">
				</form>
			</div>
		</div>
		<!-- scraping emails -->
		<?php
			if(isset($_POST['email_file']) && $_POST['email_file'] != ''){
				$h = fopen("uploads/csv/".$_POST['email_file'], "r");
				// array of curl handles
				$multiCurl = array();
				// data to be returned
				$result = array();
				// multi handle
				$mh = curl_multi_init();
				$slot = 0;
				$time_start = microtime(true); $row_index = 0;
				while (($data = fgetcsv($h)) !== FALSE) 
				{
				// Read the data
					$row_index++;
					if($row_index == 1){
						continue;
					} else {
						if($slot < 100){
							$fetchurl = 'https://trello.com/1/search/members/?idOrganization=606dcc09d9621c73c43f01b3&query='.$data[5].'&invitationTokens=';
							
							$multiCurl[$slot] = curl_init();
							curl_setopt_array($multiCurl[$slot], array(
							
								CURLOPT_URL => $fetchurl,
								CURLOPT_RETURNTRANSFER => true,
								CURLOPT_ENCODING => '',
								CURLOPT_MAXREDIRS => 10,
								CURLOPT_TIMEOUT => 0,
			
								CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
								CURLOPT_CUSTOMREQUEST => 'GET',
								CURLOPT_HEADER => 0,
								CURLOPT_HTTPHEADER => array(
									'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
									'accept-encoding: gzip, deflate, br',
									'accept-language: en-US,en;q=0.9',
									'cache-control: max-age=0',
									'cookie: dsc=2bc183c0086c6b97f5990fd51bef020658275b0cb48f3cbb7b799acd6449c8db; lang=en-US; ajs_anonymous_id=%22f7e1c156-36d8-44a5-8944-c2ddb6f11646%22; ajs_group_id=null; _sp_ses.dc4d=*; _ga=GA1.2.2117070702.1618096817; _gid=GA1.2.830520331.1618096817; _gcl_au=1.1.441467361.1618096818; G_ENABLED_IDPS=google; _biz_sid=3a790e; _mkto_trk=id:594-ATC-127&token:_mch-trello.com-1618096823797-84295; _biz_uid=4bf4ff3ef3d244b49c2bfa7165945550; _uetsid=50d4c1c09a5311eb8bc0bbe20fb01bd5; _uetvid=50d4f8309a5311eb8059e56bf9082796; _biz_nA=4; _biz_flagsA=%7B%22Version%22%3A1%2C%22ViewThrough%22%3A%221%22%2C%22XDomain%22%3A%221%22%2C%22Mkto%22%3A%221%22%7D; _biz_pendingA=%5B%5D; gdpr-cookie-consent=accepted; token=606dcd9527b8e80a7548e1ed%2FvKWTMTqecfWsdMkx8J4QBEtGJ6TQkOBVJYJ9TJSyrFbgBE3Ldp3F9HyfMpc1fwTq; hasAccount=atlassian; loggedIn=1; mab=606dcd9527b8e80a7548e1ed; __cid=WFhgtoQsoXbyFOrQwIQAticXXRF2KcMBdTXnEWU08kFRs2wVTjD8PG4B6C98cwwROTAf3udbuxJuyRChKTLTdQV5g2sfWIBwWQHCIVYcu3gYUINmBRSiRVYF3D9GD8xGH1raJU0UlCdCHcxQBkSAdCFRjlofQMMkRQPCIkAUxFo-YKFdWhSAeB1RzFYTV4d-XxSveQRbgXRZDNQ_RhrYIkQAwiBGAMxCF1KNYx8b2SJBGt8nGjzYKBQHjiNBAJsSdr5sMXY07BF2NOwRdjTsEXbL; _sp_id.dc4d=6229b041-14e8-4859-b083-8092225e41d3.1618096814.1.1618096875.1618096814.44bfabfd-5cdf-4a78-bf75-281df38eaba6',
									'sec-ch-ua: "Chromium";v="88", "Google Chrome";v="88", ";Not A Brand";v="99"',
									'sec-ch-ua-mobile: ?0',
									'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.104 Safari/537.36'
								),
							));

						} else {
							break;
						}
											
						curl_multi_add_handle($mh, $multiCurl[$slot]);
						$slot++;
					}
				}
				fclose($h);
				//multi curl execution 
				$index=null;
				do {
					curl_multi_exec($mh,$index);
				} while($index > 0);
				// get content and remove handles
				foreach($multiCurl as $k => $ch) {
					$result[$k] = curl_multi_getcontent($ch);
					curl_multi_remove_handle($mh, $ch);
				}
				// close
				curl_multi_close($mh);
				echo $result[$slot-1];
				$time_end = microtime(true);

				$execution_time = ($time_end - $time_start);
				
				echo "Full Execution time $execution_time";
			}
		?>
	</body>
</html>