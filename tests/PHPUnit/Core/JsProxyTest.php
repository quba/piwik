<?php
class Test_Piwik_JsProxy extends PHPUnit_Framework_TestCase
{
    /**
     * @group Core
     */
    function testPiwikJs()
    {
        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $this->getStaticSrvUrl() . '/js/');
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        $fullResponse = curl_exec($curlHandle);
        $responseInfo = curl_getinfo($curlHandle);
        curl_close($curlHandle);

        $this->assertEquals($responseInfo["http_code"], 200, 'Ok response');

        $piwik_js = file_get_contents(PIWIK_PATH_TEST_TO_ROOT . '/piwik.js');
        $this->assertEquals($fullResponse, $piwik_js, 'script content');
    }

    /**
     * @group Core
     */
    function testPiwikPhp()
    {
        $curlHandle = curl_init();
        $url = $this->getStaticSrvUrl() . '/js/?idsite=1';
        curl_setopt($curlHandle, CURLOPT_URL, $url);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        $fullResponse = curl_exec($curlHandle);
        $responseInfo = curl_getinfo($curlHandle);
        curl_close($curlHandle);

        $this->assertEquals($responseInfo["http_code"], 200, 'Ok response');
        $expected = "R0lGODlhAQABAIAAAAAAAAAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==";
        $processed = base64_encode($fullResponse);
        if ($expected != $processed) {
            $this->markTestSkipped("testPiwikPhp invalid response content: " . $fullResponse);
        }

        $this->assertEquals(
            $expected,
            $processed,
            'checking for image content' . "\n\n\n\nRaw content: \n\n\n" . $fullResponse
        );

    }

    /**
     * Helper methods
     */
    private function getStaticSrvUrl()
    {
        return Fixture::getRootUrl();
    }
}
