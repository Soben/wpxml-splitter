<?php

namespace Magpie\WPXML;

class Splitter
{
  protected $inputFile;
  protected $outputDirectory;
  protected $settings;

  protected $xmlIn;
  protected $xmlOut;

  public function __construct($input, $output = "./", $settings = array())
  {
    $this->inputFile = $input;
    $this->outputDirectory = $output;
    $this->settings = $settings;

    $this->validate();
    $this->prepare();
  }

  protected function validate()
  {
    if (!file_exists($this->inputFile)) {
      throw new \Exception("Missing Input File");
    }

    if (!is_dir($this->outputDirectory)) {
      throw new \Exception("Missing Output Directory");
    }

    $this->settings = array_merge(array(
      "quiet" => true,
      "perFile" => 500,
      "baseXML" => "../assets/base.xml",
    ), $this->settings);
  }

  protected function prepare()
  {
    $this->xmlIn = new \DOMDocument();
    $this->xmlIn->loadXML(file_get_contents($this->inputFile));

    $this->items = $this->xmlIn->getElementsByTagName("item");
  }

  public function process()
  {
    $totalItems = $this->items->length;
    
    $offset = 0;
    if (!$this->settings["quiet"]) {
      echo "Total Items: " . $totalItems . PHP_EOL;
      echo "Per Batch: " . $this->settings['perFile'] . PHP_EOL;
      echo PHP_EOL;
    }
    while ($offset < $totalItems) {
      $processed = $this->saveBatch($offset, $offset + (int)$this->settings['perFile']);
    
      $offset += $processed;
    }
  }

  protected function saveBatch ($start, $end)
  {
    $batch = $this->getBatch($start, $end);
  
    $preparedBatch = $this->generateFileOutput($batch);
    $imported = $preparedBatch->getElementsByTagName("item");
    if (!$this->settings["quiet"]) {
      echo "Items Processed: {$imported->length} ({$start}-{$end})" . PHP_EOL;
    }
  
    $filename = "{$this->outputDirectory}/batch-{$start}-{$end}.xml";
    $preparedBatch->save($filename);
  
    return (int)$imported->length;
  }

  protected function generateFileOutput($batch)
  {
    $xml = new \DOMDocument();
    $xml->loadXML(file_get_contents($this->settings["baseXML"]));
    $channelNode = $xml->getElementsByTagName("channel")->item(0);

    $channelNode->getElementsByTagName("title")->item(0)->nodeValue = $this->xmlIn->getElementsByTagName("title")->item(0)->nodeValue;
    $channelNode->getElementsByTagName("pubDate")->item(0)->nodeValue = $this->xmlIn->getElementsByTagName("pubDate")->item(0)->nodeValue;
    $channelNode->getElementsByTagName("language")->item(0)->nodeValue = $this->xmlIn->getElementsByTagName("language")->item(0)->nodeValue;
    $channelNode->getElementsByTagName("generator")->item(0)->nodeValue = $this->xmlIn->getElementsByTagName("generator")->item(0)->nodeValue;
  
    $channelNode->getElementsByTagName("base_site_url")->item(0)->nodeValue = $this->xmlIn->getElementsByTagName("base_site_url")->item(0)->nodeValue;
    $channelNode->getElementsByTagName("base_blog_url")->item(0)->nodeValue = $this->xmlIn->getElementsByTagName("base_blog_url")->item(0)->nodeValue;
    $channelNode->getElementsByTagName("link")->item(0)->nodeValue = $this->xmlIn->getElementsByTagName("link")->item(0)->nodeValue;
  
    foreach ($batch as $item) {
      $migratedNode = $xml->importNode($item, true); // I wish this went faster.
      $channelNode->appendChild($migratedNode);
    }
  
    return $xml;
  }
  
  protected function getBatch($start, $end)
  {
    $batchItems = array();
  
    for ($i = $start; $i < $end; $i++) {
      if (isset($this->items[$i])) {
        $batchItems[] = $this->items[$i];
      } else {
        break; // we don"t have any more entries, why bother looping?
      }
    }
  
    return $batchItems;
  }
}
