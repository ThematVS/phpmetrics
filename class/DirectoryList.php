<?php

class DirectoryList
{
//    ls -R | grep ":$" | sed -e 's/:$//' -e 's/[^-][^\/]*\//--/g' -e 's/^/   /' -e 's/-/|/'
    //    ls -R | grep ":$" | sed -e 's/:$//' -e 's/[^-][^\/]*\//\*/g'
    //phpmetrics --report-html=myreport.html /path/of/your/sources
    //php /home/vsav/.composer/vendor/bin/phpmetrics --report-html=metric-report
    private static $baseDirs = [
        'Cars Local' => '/var/www/m-ksl-cars',
        'Cars' => '/export/marketplace/m-ksl-cars',
        'Homes' => '/export/marketplace/m-ksl-homes',
        'Jobs' => '/export/marketplace/m-ksl-jobs',
        'General' => '/export/marketplace/m-ksl-classifieds',
        'API' => '/export/marketplace/ksl-api3',
        'API Cars' => '/export/marketplace/ksl-api3/public_html/classifieds/cars',
        'API Homes' => '/export/marketplace/ksl-api3/public_html/classifieds/homes',
        'API Jobs' => '/export/marketplace/ksl-api3/public_html/classifieds/jobs',
        'API General' => '/export/marketplace/ksl-api3/public_html/classifieds/general',
        'My Account' => '/export/marketplace/m-ksl-myaccount',
        'KSL' => '/export/ksl/v2',
    ];

    public function getBaseDirs()
    {
        return self::$baseDirs;
    }

    public function getLsByAlias($alias)
    {
        if (!isset(self::$baseDirs[$alias])) {
            return false;
        }
        $path = self::$baseDirs[$alias];
        return `ls -R {$path} | grep ":$" | sed -e 's/:$//' -e 's/[^-][^\/]*\//\*/g' -e 's/\*\*//'`;
    }

    public function getLsByPath($path)
    {
        return `ls -R {$path} | grep ":$" | sed -e 's/:$//' -e 's/[^-][^\/]*\//\*/g' -e 's/\*\*//'`;
    }

}
