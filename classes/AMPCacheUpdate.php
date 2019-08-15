<?php
/**
 * @author Bilal Demir
 */
namespace Classes;
class AMPCacheUpdate {
    private $domain;
    private $urls;
    private $pemKey;
    private $ampUrl;
    private $signatureUrl;
    /**
     * Constructor
     *
     * @param string $domain  Your domain.
     * @param array $urls  Your url list. Must be Array.
     * @param string $pemKey  Your pem filename.
     * @return void
     */
    public function __construct($domain = "", $urls = [], $pemKey = "") {
        if(empty($domain)) exit("Domain is required!");
        if(count($urls)===0) exit("Url is required!");
        if(empty($pemKey)) exit("Pem key is required!");
        $this->domain = $this->parseUrl($domain);
        $this->urls = $this->parseUrl($urls);
        $this->pemKey = $pemKey;
        $this->domainAndUrlControl($this->domain, $this->urls);
    }
    private function parseUrl($link, $isAMPUrl = false) {
        if(gettype($link) === "string") {
            if(!filter_var($link, FILTER_VALIDATE_URL)) exit($link." not valid URL!");

            $parsedUrl = parse_url($link);
            if(!empty($parsedUrl["path"])) {
                $parsedUrl["path"] = rtrim($parsedUrl["path"], "/");
            }
            return $parsedUrl;
        } else if(gettype($link) === "array") {
            $parsedUrl = [];
            foreach($link as $key => $val) {
                array_push($parsedUrl, $this->parseUrl($val, true));
            }
            return $parsedUrl;
        }
    }
    private function domainAndUrlControl($domain = "", $urls) {
        $errUrls = "";
        foreach($urls as $key => $val) {
            if($domain["host"] !== $val["host"]) {
                $errUrls .= "- ".$val["scheme"]."://".$val["host"].$val["path"]."\n";
            }
        }
        if(!empty($errUrls)) {
            exit("Domain and url host don't match! Errors;\n".$errUrls);
        }
    }

    private function getPemContent($key = "") {
        if (!empty($key)) {
            $pKeyId = openssl_pkey_get_private($key);

            if($pKeyId != false) {
                openssl_sign($this->signatureUrl, $signature, $pKeyId, OPENSSL_ALGO_SHA256);
                openssl_free_key($pKeyId);
                return $this->urlSafe_b64Encode($signature);
            }

            return "signature_error";
        }
    }
    private function urlSafe_b64Encode($string) {
        return str_replace(array('+','/','='),array('-','_',''), base64_encode($string));
    }
    private function getAMPDomain($domain = "") {
        $domain["host"] = str_replace("-","--",$domain["host"]);
        return "https://".str_replace('.', '-', $domain["host"]);
    }
    private function getAMPUrl($url = "") {
        return $url["host"].$url["path"];
    }
    private function generateUrls($url = "") {
        $timestamp=time();
        $this->ampUrl =  "/update-cache/c/s/";
        $this->ampUrl .= $this->getAMPUrl($url)."/article?amp_action=flush&amp_ts=".$timestamp;
        $this->signatureUrl .= $this->ampUrl;
        $this->ampUrl = $this->getAMPDomain($this->domain).".cdn.ampproject.org".$this->ampUrl;
        $this->ampUrl .= "&amp_url_signature=".$this->getPemContent($this->pemKey);
        return $this->ampUrl;
    }
    private function writeUrls($urls = []) {
        echo "List:\n\n";
        foreach ($urls as $key=>$url){
            echo $url.(($key+1) <  count($urls) ? "\n\n" : "");
        }
    }
    /**
     * Update
     *
     * @param boolean $isPrint Print screen?
     * @return array|void
     */
    public function update($isPrint = false) {
        $generatedUrls = [];
        foreach($this->urls as $key => $val) {
            array_push($generatedUrls,$this->generateUrls($val));
            $this->signatureUrl = "";
        }
        if($isPrint) {
            $this->writeUrls($generatedUrls);
        } else {
            return $generatedUrls;
        }
    }
}