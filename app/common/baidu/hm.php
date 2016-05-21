<?php

class _HMT {
    private $VERSION = "wap-0-0.2";

    private $VISIT_DURATION = 1800;

    private $VISITOR_MAX_AGE = 31536000;

    private $SEARCH_ENGINE_LIST = array(
        array("1", "baidu.com", "word|wd"), 
        array("2", "google.com", "q"), 
        array("4", "sogou.com", "query"), 
        array("6", "search.yahoo.com", "p"), 
        array("7", "yahoo.cn", "q"), 
        array("8", "soso.com", "w"), 
        array("11", "youdao.com", "q"), 
        array("12", "gougou.com", "search"), 
        array("13", "bing.com", "q"),
        array("14", "so.com", "q"), 
        array("14", "so.360.cn", "q"), 
        array("15", "jike.com", "q"), 
        array("16", "qihoo.com", "kw"), 
        array("17", "etao.com", "q"), 
        array("18", "soku.com", "keyword")
    );

    private $siteId = "";
    private $searchEngine = "";
    private $searchWord = "";

    private $visitUrl = "";
    private $eventType = 0;
    private $eventProperty = "";

    private function getQueryValue($url, $key) {
        preg_match("/(^|&|\\?|#)(" . $key . ")=([^&#]*)(&|$|#)/", $url, $matches);
        return count($matches) > 0 ? $matches[3] : NULL;
    }

    private function getSourceType($path, $referer, $currentPageVisitTime, $lastPageVisitTime) {
        $parsedPath = parse_url($path);
        $parsedReferer = parse_url($referer);
        if (empty($referer) || (!is_array($parsedPath) && !is_array($parsedReferer) && $parsedPath["host"] === $parsedReferer["host"])) {
            return ($currentPageVisitTime - $lastPageVisitTime > $this->VISIT_DURATION) ? 1 : 4;
        } else {
            $sel = $this->SEARCH_ENGINE_LIST;
            for ($i = 0, $l = count($sel); $i < $l; $i++) {
                if (preg_match("/" . $sel[$i][1] . "/", $parsedReferer["host"])) {
                    $this->searchWord = $this->getQueryValue($referer, $sel[$i][2]);
                    if (!is_null($this->searchWord) || $sel[$i][0] === "2" || $sel[$i][0] === "14" || $sel[$i][0] === "17") {
                        $this->searchEngine = $sel[$i][0];
                        return 2;
                    }
                }
            }
            return 3;
        } 
    }

    private function replaceSpecialChars($text) {
        $text = str_replace("'", "'0", $text);
        $text = str_replace("*", "'1", $text);
        $text = str_replace("!", "'2", $text);
        return str_replace("%27", "'", urlencode($text));
    }

    private function getPixelUrl() {
        $path = (isset($_SERVER["HTTPS"]) && ($_SERVER["HTTPS"] === "on") ? 'https://' : 'http://') .
            $_SERVER['SERVER_NAME'] .
            (($_SERVER["SERVER_PORT"] === '80') ? '' : ':' . $_SERVER["SERVER_PORT"]) .
            $_SERVER['REQUEST_URI'];

        // $referer = $_SERVER['HTTP_REFERER'];
        $referer = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:"";

        $currentPageVisitTime = time();

        $lastPageVisitTime = (int)isset($_COOKIE["Hm_lpvt_" . $this->siteId])?$_COOKIE["Hm_lpvt_" . $this->siteId]:0;

        $lastVisitTime = isset($_COOKIE["Hm_lvt_" . $this->siteId])?$_COOKIE["Hm_lvt_" . $this->siteId]:0;

        $sourceType = $this->getSourceType($path, $referer, $currentPageVisitTime, $lastPageVisitTime);
        $isNewVisit = ($sourceType == 4) ? 0 : 1;

        setCookie("Hm_lpvt_" . $this->siteId, $currentPageVisitTime, 0, "/");
        setCookie("Hm_lvt_" . $this->siteId, $currentPageVisitTime, time() + $this->VISITOR_MAX_AGE, "/");

        $pixelUrl = "http://hm.baidu.com/hm.gif" .
            "?si=" . $this->siteId .
            "&et=" . $this->eventType .
            ($this->eventProperty !== "" ? "&ep=" . $this->eventProperty : "") .
            "&nv=" . $isNewVisit .
            "&st=" . $sourceType .
            ($this->searchEngine !== "" ? "&se=" . $this->searchEngine : "") .
            ($this->searchWord !== "" ? "&sw=" . urlencode($this->searchWord) : "") .
            (!is_null($lastVisitTime) ? "&lt=" . $lastVisitTime : "") .
            (!is_null($referer) ? "&su=" . urlencode($referer) : "") .
            ($this->visitUrl !== "" ? "&u=" . urlencode($this->visitUrl) : "") .
            "&v=" . $this->VERSION .
            "&rnd=" . rand(10e8, 10e9);

        return htmlspecialchars($pixelUrl);
    }

    public function __construct($siteId) {
        $this->siteId = $siteId;
    }

    public function setAccount($siteId) {
        $this->siteId = $siteId;
    }

    public function trackPageView($url = NULL) {
        $this->eventType = 0;
        $this->eventProperty = "";
        if (!is_null($url) && strpos($url, "/") === 0) {
            $this->visitUrl = (isset($_SERVER["HTTPS"]) && ($_SERVER["HTTPS"] === "on") ? 'https://' : 'http://') .
                $_SERVER['SERVER_NAME'] .
                (($_SERVER["SERVER_PORT"] === '80') ? '' : ':' . $_SERVER["SERVER_PORT"]) .
                $url;
        } else {
            $this->visitUrl = "";
        }
        return $this->getPixelUrl();
    }

    public function trackEvent($category, $action, $opt_label = NULL, $opt_value = NULL) {
        $this->eventType = 4;
        $this->eventProperty = $this->replaceSpecialChars($category) .
            "*" . $this->replaceSpecialChars($action) .
            (!is_null($opt_label) ? "*" . $this->replaceSpecialChars($opt_label) : "" ) .
            (!is_null($opt_value) ? "*" . $this->replaceSpecialChars($opt_value) : "" );
        $this->visitUrl = "";
        return $this->getPixelUrl();
    }
}

?>
