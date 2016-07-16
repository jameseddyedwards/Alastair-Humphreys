<?php

$carouselImages = get_field('carousel');
$counter = 1;

if (count($carouselImages) > 0 && wp_is_mobile()) {
	$noImages 		= count($carouselImages);
	$imageNo 		= rand(1, $noImages);
	$mobileImage 	= $carouselImages[$imageNo];
}

?>

<?php if ($carouselImages && !wp_is_mobile()) { ?>

<div class="carousel js-flickity"
  data-flickity-options='{ "imagesLoaded": true, "autoPlay": true, "wrapAround": true, "pageDots": false }'>
	<?php foreach($carouselImages as $image) { ?>
		<img class="carousel-cell-image" src="<?php echo $image['sizes']['feature-wide']; ?>" data-flickity-lazyload="<?php echo $image['sizes']['feature-wide']; ?>" alt="<?php echo $image['alt']; ?>" />
		<?php $counter = $counter + 1; ?>
	<?php } ?>
</div>

	<!--
	<div class="carousel-wrapper clearfix">
		<div id="carousel" class="carousel clearfix">
		</div>
		<a id="next" class="next" href="#"></a>
		<a id="previous" class="previous" href="#"></a>
	</div>
	-->

<?php } ?>

<?php if ($carouselImages && wp_is_mobile()) { ?>
	<img src="<?php echo $mobileImage['sizes']['medium_large']; ?>" alt="<?php echo $mobileImage['alt']; ?>" height="<?php echo $mobileImage['sizes']['medium_large']; ?>" width="100%" />
<?php } ?>