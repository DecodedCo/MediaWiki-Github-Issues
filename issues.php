<?php

// Update lines 23-24

require_once(dirname(__FILE__).'/php-markdown/Michelf/MarkdownExtra.inc.php');
use \Michelf\MarkdownExtra;

$wgExtensionFunctions[] = "wikiGithubIssues";
$wgExtensionCredits['parserhook'][] = array(
        'name' => 'Github Issues',
        'author' => 'Aaron Parecki',
        'description' => 'Adds <nowiki><githubissues src=""></nowiki> tag to embed github issues in the wiki',
        'url' => 'https://github.com/aaronpk/MediaWiki-Github-Issues'
);

function wikiGithubIssues() {
    global $wgParser;
    $wgParser->setHook("githubissues", "embedGithubIssues");
}

function embedGithubIssues($input, $args) {

	$username="organisation";
	$oauth_key="key";

	global $wgParser;

	$wgParser->disableCache();

	ob_start();

	if(!array_key_exists('repo', $args)) {
		echo 'Error! Usage: <githubissues repo="x.decoded.co">';
	} else {

		$repo = $args['repo'];
		$query = isset($args['query']) ? $args['query'] : null;
		$cacheHours = isset($args['cache']) ? $args['cache'] : 2;

		$cacheFile = dirname(__FILE__).'/cache/'.md5(implode($args)) . '.html';
		if(file_exists($cacheFile) && filemtime($cacheFile) >= (time() - (60*60*$cacheHours))) {
			echo file_get_contents($cacheFile);
		} else {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://api.github.com/repos/'.$username.'/'.$repo.'/issues?'.$query);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_USERPWD, $oauth_key.':x-oauth-basic');
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_USERAGENT, 'MediaWiki Github Issues Extension');
			$response = curl_exec($ch);

			$response_info = curl_getinfo($ch);

			if($response_info['http_code'] != '200') {

				echo '<p>Error retrieving content from GitHub: ';
				echo $response.'</p>';exit;

			} else {
				$issues = json_decode($response);

				if($issues) {
					$headerTag = 'h3';
					if(array_key_exists('header', $args)) {
						$headerTag = $args['header'];
					}

					$html = '';
					foreach($issues as $issue) {
						$html .= '<'.$headerTag.'><a href="' . $issue->html_url . '">' . $issue->title . '</a></'.$headerTag.'>';
						$body = MarkdownExtra::defaultTransform($issue->body);
						$html .= '<p>' . $body . '</p>';
						if($issue->comments) {
							$html .= '<p><a href="' . $issue->html_url . '">';
							$html .= $issue->comments . ' comment' . ($issue->comments == 1 ? '' : 's');
							$html .= '</a></p>';
						}
					}

					file_put_contents($cacheFile, $html);
					echo $html;
				} else {
					echo '<p>Error retrieving content from GitHub. Malformed JSON was returned from the API.</p>';
					echo '<p>URL is: <a href="' . $args['src'] . '">' . $args['src'] . '</a></p>';
				}
			}
		}
	}

	return array(ob_get_clean(), 'noparse' => true, 'isHTML' => true);
}
