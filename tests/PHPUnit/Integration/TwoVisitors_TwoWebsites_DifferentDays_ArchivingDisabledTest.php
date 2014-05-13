<?php
/**
 * Piwik - Open source web analytics
 *
 * @link    http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

/**
 * Tests IndexedBySite optimizations when archiving is disabled.
 */
class Test_Piwik_Integration_TwoVisitors_TwoWebsites_DifferentDays_ArchivingDisabled extends IntegrationTestCase
{
    public static $fixture = null; // initialized below class definition

    /**
     * @dataProvider getApiForTesting
     * @group        Integration
     * 
     */
    public function testApi($api, $params)
    {
        $this->runApiTests($api, $params);
    }

    public function getApiForTesting()
    {
        $dateTime = self::$fixture->dateTime;

        $periods = array('day', 'week', 'month', 'year');

        $dateStart = \Piwik\Date::factory($dateTime)->subDay(10)->toString();
        $dateEnd = \Piwik\Date::factory($dateTime)->addDay(15)->toString();
        $dateRange = $dateStart . "," . $dateEnd;

        return array(
            // disable archiving & check that there is no archive data
            array('VisitsSummary.get', array('idSite'           => 'all',
                                             'date'             => $dateTime,
                                             'periods'          => $periods,
                                             'disableArchiving' => true,
                                             'testSuffix'       => '_disabledBefore')),

            // re-enable archiving & check the output
            array('VisitsSummary.get', array('idSite'           => 'all',
                                             'date'             => $dateTime,
                                             'periods'          => $periods,
                                             'disableArchiving' => false,
                                             'testSuffix'       => '_enabled')),

            // diable archiving again & check the output
            array('VisitsSummary.get', array('idSite'           => 'all',
                                             'date'             => $dateTime,
                                             'periods'          => $periods,
                                             'disableArchiving' => true,
                                             'testSuffix'       => '_disabledAfter')),


            // Testing this particular bug: http://dev.piwik.org/trac/ticket/4532
            // ENABLE ARCHIVING and Process this custom date range.
            array('VisitsSummary.get', array('idSite'           => 'all',
                                             'date'             => $dateRange,
                                             'periods'          => array('range'),
                                             'disableArchiving' => false,
                                             'testSuffix'       => '_enabledBefore_isDateRange')),

            // DISABLE Archiving + DELETE Date range archives before this test.
            // This should return the same data as the test above!
            array('VisitsSummary.get', array('idSite'           => 'all',
                                             'date'             => $dateRange,
                                             'periods'          => array('range'),
                                             'disableArchiving' => true,
                                             'hackDeleteRangeArchivesBefore' => true,
                                             'testSuffix'       => '_disabledBefore_isDateRange')),


        );
    }

    public static function getOutputPrefix()
    {
        return 'TwoVisitors_twoWebsites_differentDays_ArchivingDisabled';
    }
}

Test_Piwik_Integration_TwoVisitors_TwoWebsites_DifferentDays_ArchivingDisabled::$fixture =
    new Test_Piwik_Fixture_TwoSitesTwoVisitorsDifferentDays();
Test_Piwik_Integration_TwoVisitors_TwoWebsites_DifferentDays_ArchivingDisabled::$fixture->allowConversions = true;

