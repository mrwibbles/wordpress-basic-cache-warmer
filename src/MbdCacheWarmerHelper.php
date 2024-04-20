<?php

namespace Mbd\CacheWarmerWordpress;

use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Exception\CommunicationException;
use HeadlessChromium\Exception\CommunicationException\CannotReadResponse;
use HeadlessChromium\Exception\CommunicationException\InvalidResponse;
use HeadlessChromium\Exception\CommunicationException\ResponseHasError;
use HeadlessChromium\Exception\NavigationExpired;
use HeadlessChromium\Exception\NoResponseAvailable;
use HeadlessChromium\Exception\OperationTimedOut;
use HeadlessChromium\Page;

class MbdCacheWarmerHelper
{

    /**
     * @throws CommunicationException
     * @throws OperationTimedOut
     * @throws NoResponseAvailable
     * @throws NavigationExpired
     * @throws InvalidResponse
     * @throws CannotReadResponse
     * @throws ResponseHasError
     * @throws \Exception
     */
    public function startWarmer(): void
    {
        $this->writeToLog('MBD CACHE WARMER START');

        $url = get_site_url() . '/sitemap.xml';
        $sitemap = $this->getDataFromURL($url);
        $individualSitemaps = $this->getURLSFromXML($sitemap);

        $urlsToWarm = [];

        foreach ($individualSitemaps as $value) {
            $individualSitemap = $this->getDataFromURL($value);
            $xml = simplexml_load_string($individualSitemap);

            foreach ($xml as $map) {
                $urlsToWarm[] = $map->loc;
            }
        }

        $browserFactory = new BrowserFactory();
        $browser = $browserFactory->createBrowser();

        foreach ($urlsToWarm as $url) {
            $page = $browser->createPage();
            $page->navigate($url)->waitForNavigation(Page::DOM_CONTENT_LOADED, 10000);
            $this->writeToLog('WARMED URL: ' . $url);
        }
        $browser->close();

        $this->writeToLog('MBD CACHE WARMER END');
    }

    /**
     * @throws OperationTimedOut
     * @throws CommunicationException
     * @throws NoResponseAvailable
     * @throws NavigationExpired
     * @throws InvalidResponse
     * @throws CannotReadResponse
     * @throws ResponseHasError
     */
    public function warmer($url): void
    {
//        $arrContextOptions = array(
//            "ssl" => array(
//                "verify_peer" => false,
//                "verify_peer_name" => false,
//            )
//        );
//
//        $context = stream_context_create($arrContextOptions);
//        $page = file_get_contents($url,false, $context);
//        $this->warmPageAssets($page);


    }

    public function warmPageAssets($html): void
    {
        $assetsToWarm = [];
        $doc = new \DOMDocument();
        @$doc->loadHTML($html);
        $links = $doc->getElementsByTagName('link');

        foreach ($links as $link) {
            if($link->getAttribute('rel') === "stylesheet") {
                $href = $link->getAttribute('href');
                $assetsToWarm[] = $href;
            }
        }

        $scripts = $doc->getElementsByTagName('script');

        foreach ($scripts as $script) {
            if($script->getAttribute('src')) {
                $href = $script->getAttribute('src');
                $assetsToWarm[] = $href;
            }
        }

        foreach ($assetsToWarm as $url) {
            if($url) {
                $this->getDataFromURL($url);
                $this->writeToLog('WARMED ASSET: ' . $url);
            }
        }
    }

    public function getDataFromURL($url): bool|string
    {
        $arrContextOptions = array(
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            )
        );

        $context = stream_context_create($arrContextOptions);
        return file_get_contents($url,false, $context);
    }

    public function getURLSFromXML($data): array
    {
        $xml = simplexml_load_string($data);
        $urls = [];
        foreach ($xml->sitemap as $sitemap) {
            $urls[] = $sitemap->loc;
        }

        return $urls;
    }

    public function writeToLog(string $log): void
    {
        if (true === WP_DEBUG) {
            if (is_array($log) || is_object($log)) {
                error_log(print_r($log, true));
            } else {
                error_log($log);
            }
        }
    }
}
