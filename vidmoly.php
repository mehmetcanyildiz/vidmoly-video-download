<?php


/**
 * Github: @mehmetcanyildiz
 */
class Vidmoly
{
  private $path; // Save As File
  private $id; // Vidmoly ID
  private $url; // Vidmoly URL
  private $master; // Master Url
  private $label; // Quality Label
  private $qualityURL; // Quality URL

  public function download($path='',$id='')
  {
    $this->id = $id;
    $this->url = $this->url($id);
    preg_match_all('/file:"(.*?)"/si', $this->connect($this->url), $sources);
    if(!empty($sources[1][0])){
        $this->master = $sources[1][0];
        if(strstr($this->master,'master.m3u8')){

          // Get Master M3U8
          $result = $this->connect($this->master,$this->url);
          $result = array_filter(explode("\n",$result));
          array_splice($result, 0, 1); $i=0;

          // Master M3U8 Shape
          if(!empty($result)){
            foreach ($result as $key => $value) {
               if(strstr($value,'m3u8')){
                 $source[$i]['file'] = trim($value); $i++;
               }else{
                 preg_match_all('/RESOLUTION=.*?x(.*?),/si', $value, $quality);
                 $source[$i]['label'] = trim($quality[1][0]);
               }
            }
          }else return json_encode(array('status' => 'M3U8 File in Source Not Found'));

            // Download M3U8
          if(!empty($source)){
            foreach ($source as $key => $value) {
                 $this->qualityURL = trim($value['file']);
                 $this->label = trim($value['label']);
                 $this->path = $this->pathCreate($path);
                 $this->downloadM3U8File();
                 if(!file_exists($this->path.$this->label.'.mp4')) return json_encode(array('status' => 'The video has an access block.'));
                 $status[] = array('label' => $this->label , 'path' => $this->path.$this->label.'.mp4' );
            }
            return json_encode($status);
          }else return json_encode(array('status' => 'MP4 Url Not Found'));

          // Download MP4
        }else{
          $this->qualityURL = $sources[1][0];
          $this->label = '720';
          $this->path = $this->pathCreate($path);
          $status = $this->downloadMP4File();
          return json_encode($status);
        }
    }else return json_encode(array('status' => 'Source Not Found'));
  }



  // M3U8 URL to Mp4  (Required FFMPEG)
  private function downloadM3U8File(){
    exec('ffmpeg -i '.$this->qualityURL.' -c copy -bsf:a aac_adtstoasc '.$this->path.$this->label.'.mp4');
  }


  // Save As MP4
  private function downloadMP4File(){
    $file = fopen($this->path.$this->label.'.mp4','a+');
    fwrite($file,file_get_contents($this->qualityURL));
    fclose($file);
    return array('label' => $this->label , 'path' => $this->path.$this->label.'.mp4' );
  }


  // Return Vidmoly URL
  private function url($value='')
  {
    return 'https://vidmoly.to/embed-'.$value.'.html';
  }


  // CURL Connection
  private function connect($url='',$referer='')
  {
      if(empty($referer)) $referer = $url;
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
      curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
      $headers = array();
      $headers[] = 'Connection: keep-alive';
      $headers[] = 'Pragma: no-cache';
      $headers[] = 'Cache-Control: no-cache';
      $headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.129 Safari/537.36';
      $headers[] = 'Accept: */*';
      $headers[] = 'Origin: https://vidmoly.to';
      $headers[] = 'Sec-Fetch-Site: cross-site';
      $headers[] = 'Sec-Fetch-Mode: cors';
      $headers[] = 'Sec-Fetch-Dest: empty';
      $headers[] = 'Referer: '.$referer;
      $headers[] = 'Accept-Language: tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7';
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

      $result = curl_exec($ch);
      if (curl_errno($ch)) {
          echo 'Error:' . curl_error($ch);
      }
      curl_close($ch);
      return $result;
  }

  //  Create Save As Path 
  private function pathCreate($tempPath){
    $paths = array_filter(explode('/',$tempPath.'/'.$this->id.'/'));
    $realPath = realpath('.').'/';
    foreach ($paths as $key => $path) {
      $realPath .= $path.'/';
      if(!is_dir($realPath)) mkdir($realPath,777);
    }
    return $realPath;
  }



}


 ?>
