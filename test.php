<?php
//Todo: Make the program in OOP
$response = get_web_page("https://api.github.com/search/repositories?q=php+language:php&sort=stars&order=desc");
$resArr = array();
$resArr = json_decode($response, true);
$resArr = $resArr['items'];

$servername = 'localhost';
$username = '***';
$password = '***';
$db = '***';

// Create connection
$conn = mysqli_connect($servername, $username, $password, $db);

// Check connection
if (mysqli_connect_errno())
{
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$query = "REPLACE INTO test (repo_id, name, url,created_date,last_push_date,description,starts) VALUES ";
$query_values = '';

//Logic to insert multiple rows with one statement
foreach ($resArr as $rr)
{
	$id = $rr['id'];
	$name = $rr['name'];
	$url = $rr['url'];
	$created_date = $rr['created_at'];
	$last_push_date = $rr['pushed_at'];
	$description = $rr['description'];
	$starts = $rr['stargazers_count'];

	if ($query_values != '')
	{
		$query_values .= ',';
	}
	$query_values .= "('$id', '$name','$url','$created_date', '$last_push_date','$description','$starts')";
}

//Todo: Use PDO/Prepare statements instead of using mysqli_query.

if (mysqli_query($conn, $query.$query_values) === TRUE) {
    echo "Records created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}
function get_web_page($url)
{
	$options = array(
		CURLOPT_RETURNTRANSFER => true, // return web page
		CURLOPT_HEADER => false, // don't return headers
		CURLOPT_FOLLOWLOCATION => true, // follow redirects
		CURLOPT_MAXREDIRS => 10, // stop after 10 redirects
		CURLOPT_ENCODING => "", // handle compressed
		CURLOPT_USERAGENT => "test", // name of client
		CURLOPT_AUTOREFERER => true, // set referrer on redirect
		CURLOPT_CONNECTTIMEOUT => 120, // time-out on connect
		CURLOPT_TIMEOUT => 120, // time-out on response
	);

	$ch = curl_init($url);
	curl_setopt_array($ch, $options);

	$content = curl_exec($ch);

	curl_close($ch);

	return $content;
}
?>
<!--Add the view in a separate file-->
<html>
	<head>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
		<style>
			.mr5p{margin-right: 5px}
		</style>
	</head>
	<body>
		<div class="col-md-12 col-sm-12 col-xs-12">
			<ul class="list-group">
				<?php
				$sql = "SELECT repo_id, name, url,created_date,last_push_date,description,starts FROM test";
				$result = $conn->query($sql);

				if ($result->num_rows > 0)
				{
					// output data of each row
					while ($row = $result->fetch_assoc())
					{
						?>
						<a class="clearfix" role="button" data-toggle="collapse" data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
							<li class="list-group-item">
								<span class="badge"><?= $row['starts'] ?></span>
								<?= $row['name'] ?>
							</li>
						</a>
						<div class="collapse clearfix" id="collapseExample">
							<div class="col-md-4">
								<label class="mr5p">Repo id:</label><?= $row['repo_id'] ?>
							</div>
							<div class="col-md-4">
								<label class="mr5p">Created Date: </label><?= $row['created_date'] ?>
							</div>
							<div class="col-md-4">
								<label class="mr5p">Last Push Date: </label><?= $row['last_push_date'] ?>
							</div>
							<div class="col-md-12">
								<label class="mr5p">Description: </label><?= $row['description'] ?>
							</div>
							<div class="col-md-12">
								<label class="mr5p">URL: </label><?= $row['url'] ?>
							</div>
						</div>
						<?php
					}
				}
				else
				{
					echo "0 results";
				}
				?>
			</ul>
		</div>
		<script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
	</body>
</html>
<?php
mysqli_close($conn);
