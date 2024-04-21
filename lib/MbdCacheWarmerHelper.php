<?php
class MbdCacheWarmerHelper
{

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

        foreach ($urlsToWarm as $url) {
            $this->warmer($url);
        }

        $this->writeToLog('MBD CACHE WARMER END');
    }

    public function warmer($url): void
    {
        $page = $this->getDataFromURL($url);
        $this->warmPageAssets($page);
        $this->writeToLog('WARMED URL: ' . $url);
    }

    public function warmPageAssets($html): void
    {
        $assetsToWarm = [];
        $doc = new DOMDocument();
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
                if (!str_starts_with($url, 'http')) {
                    $url = get_site_url() . $url;
                }

                $this->getDataFromURL($url);
                $this->writeToLog('WARMED ASSET: ' . $url);
            }
        }
    }

    public function getDataFromURL($url): bool|string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
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
