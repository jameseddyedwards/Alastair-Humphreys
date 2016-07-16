<?php
/**
 * The template for determining what layout to use for hierarchical pages, defaulting to a standard post layout.
 *
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package WordPress
 * @subpackage AlastairHumphreys
 * @since Alastair Humphreys 1.0
 */

get_header();

$currentPageId = get_the_ID();
$books = get_page_by_title('Books and Film');
$booksId = $books->ID;
$speaking = get_page_by_title('Speaking');
$speakingId = $speaking->ID;
$schools = get_page_by_title('Schools');
$schoolsId = $schools->ID;
$videos = get_page_by_title('Video');
$videosId = $videos->ID;

switch ($currentPageId) {
	case $booksId:
		$layout = "books";
		break;
	case $speakingId:
		$layout = "speaking";
		break;
	case $schoolsId:
		$layout = "speaking";
		break;
	case $videosId:
		$layout = "videos";
		break;
	default:
		$layout = "";
		break;
}

?>

<?php get_template_part('content', $layout); ?>

<?php get_footer(); ?>