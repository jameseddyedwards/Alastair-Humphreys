<?php
/**
 * The template for displaying the Videos page
 *
 * @package WordPress
 * @subpackage AlastairHumphreys
 * @since Alastair Humphreys 1.0
 */

// Pull in RSS feed from Vimeo
$content = file_get_contents('http://vimeo.com/channels/alastairhumphreys/videos/rss');
$x = new SimpleXmlElement($content);	

function video_image($videoId){
	$hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/" . $videoId . ".php"));
	echo $hash[0]["thumbnail_large"];
}

?>

<div class="feature-video container">
	<?php the_content(); ?>
</div>

<div class="container white content">
	<div class="row">
		<div class="span12">
			<h1><?php the_title(); ?></h1>
			<div class="row posts">
				<?php foreach($x->channel->item as $video) { ?>
					<div class="span3">
						<?php //print_r($video); ?>
						<?php
						//$hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".substr($image_url['path'], 1).".php"));
        				//return $hash[0]["thumbnail_small"];

						//$description = $video->description;
						//$imageUrl = explode('"', $description, 2);
						//$imageUrl = $imageUrl[1];
						//print_r($imageUrl);
						//video_image($video->id)
						 ?>
						<?php $videoId = explode("/", $video->link);
						//echo video_image($videoId[5]);
						?>
						<a class="post-thumb" href="<?php echo $video->link ?>">
							<img src="<?php video_image($videoId[5]); ?>" alt="<?php echo $video->title; ?>" />
							<span class="title"><?php echo $video->title; ?></span>
						</a>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>

