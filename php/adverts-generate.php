<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* Creates the advertisements that are used throughout the plugin page.
*
* The advertisements are randomly generated every time the page is loaded. The function also handles the CSS for this.
*
* @return $mlw_advert This variable is the main variable of the function and contains the advertisement content.
* @since 4.4.0
*/
function mlw_qmn_show_adverts()
{
	$mlw_advert = "";
	$mlw_advert_text = "";
	if ( get_option('mlw_advert_shows') == 'true' )
	{
		$mlw_random_int = rand(0, 4);
		switch ($mlw_random_int) {
			case 0:
				// Support Advert 1
				$mlw_advert_text = "Need support or features? Check out our Premium Support options! Visit our <a href=\"http://quizandsurveymaster.com/support-levels/?utm_source=qsm-plugin-ads&utm_medium=plugin&utm_content=support-advert-1&utm_campaign=qsm_plugin\">Quiz And Survey Master Support</a> for details!";
				break;
			case 1:
				// Subscribe Newsletter 1
				$mlw_advert_text = "Want 25% off your next addon purchase? Keep updated on our news, updated, and more by <a href=\"http://quizandsurveymaster.com/subscribe-to-our-newsletter/?utm_source=qsm-plugin-ads&utm_medium=plugin&utm_content=subscribe-newsletter-1&utm_campaign=qsm_plugin\">subscribing to our mailing list</a> and receive a 25% discount on your next purchase!";
				break;
			case 2:
				// Continued development 1
				$mlw_advert_text = "Are you finding this plugin very beneficial? Please consider checking out our premium addons which help support continued development of this plugin. Visit our <a href=\"http://quizandsurveymaster.com/addons/?utm_source=qsm-plugin-ads&utm_medium=plugin&utm_content=continued-development-1&utm_campaign=qsm_plugin\">Addon Store</a> for details!";
				break;
			case 3:
				// Reporting and anaylsis 1
				$mlw_advert_text = "Are you receiving a lot of responses to your quizzes and surveys? Consider our Reporting and Anaylsis addon which analyzes the data for you and allows you to filter the data as well as export it! <a href=\"http://quizandsurveymaster.com/downloads/results-analysis/?utm_source=qsm-plugin-ads&utm_medium=plugin&utm_content=reporting-analysis-1&utm_campaign=qsm_plugin\">Click here for more details!</a>";
				break;
			default:
				// Support Advert 2
				$mlw_advert_text = "Need support or features? Check out our Premium Support options! Visit our <a href=\"http://quizandsurveymaster.com/support-levels/?utm_source=qsm-plugin-ads&utm_medium=plugin&utm_content=support-advert-2&utm_campaign=qsm_plugin\">Quiz And Survey Master Support</a> for details!";
		}
		$mlw_advert .= "
			<style>
			div.help_decide
			{
				display: block;
				text-align:center;
				letter-spacing: 1px;
				margin: auto;
				text-shadow: 0 1px 1px #000000;
				background: #0d97d8;
				border: 5px solid #106daa;
				-moz-border-radius: 20px;
				-webkit-border-radius: 20px;
				-khtml-border-radius: 20px;
				border-radius: 20px;
				color: #FFFFFF;
			}
			div.help_decide a
			{
				color: yellow;
			}
			</style>";
		$mlw_advert .= "
			<div class=\"help_decide\">
			<p>$mlw_advert_text</p>
			</div>";
	}
	return $mlw_advert;
}
?>
