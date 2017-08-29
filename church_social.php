<?php

/*
Plugin Name: Church Social
Plugin URI: https://github.com/churchsocial/wordpress-plugin
Description: This plugin allows churches to display content from their Church Social account on their WordPress website.
Author: Church Social
Author URI: https://churchsocial.com
Version: 1.0.3
*/

include 'libs/Styles.php';
include 'libs/Settings.php';
include 'libs/Calendar.php';
include 'libs/UpcomingEventsWidget.php';
include 'libs/SermonArchive.php';
include 'libs/RecentSermonsWidget.php';

(new ChurchSocial\Styles(__FILE__));
(new ChurchSocial\Settings(__FILE__));
(new ChurchSocial\Calendar());
(new ChurchSocial\UpcomingEventsWidget());
(new ChurchSocial\SermonArchive());
(new ChurchSocial\RecentSermonsWidget());
