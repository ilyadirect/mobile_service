<?php
/**
 * A helper file for Yii2, to provide autocomplete information to your IDE
 */

namespace {

    exit("This file should not be included, only analyzed by your IDE");


    /**
     * Class Yii
     */
    class Yii extends \yii\BaseYii {
        /** @var IDE_PHPDocConsoleApp|IDE_PHPDocWebApp */
        public static $app;
    }

    /**
     * Class IDE_PHPDocWebApp
     *
     */
    class IDE_PHPDocWebApp extends \yii\web\Application {}

    /**
     * Class IDE_PHPDocConsoleApp
     */
    class IDE_PHPDocConsoleApp extends \yii\console\Application {}

}