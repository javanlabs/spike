<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PlaystoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement("CREATE TABLE IF NOT EXISTS `playstore_payload` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `payload` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`payload`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=133 ;");

        \DB::statement("CREATE TABLE IF NOT EXISTS `playstore_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `order_id` varchar(255) NOT NULL,
  `signature` text NOT NULL,
  `product_id` varchar(50) NOT NULL,
  `package_name` varchar(50) NOT NULL,
  `purchase_time` bigint(20) NOT NULL,
  `payload` varchar(255) NOT NULL,
  `token` text NOT NULL,
  `created` datetime NOT NULL,
  `purchase_state` int(1) NOT NULL,
  `status` int(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=37 ;");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement("drop table playstore_order");
        \DB::statement("drop table playtore_payload");
    }
}
