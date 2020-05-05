# Vidmoly Video Download
Download videos from Vidmoly to your own server. Converts M3U8 files to MP4. So ffmpeg installation is required.


## Requirements
- FFMPEG
- PHP

## Settings
- (PHP) memory_limit => -1
- (PHP) set_time_limit => 0
- (PHP) Exec Function => active

## Installation
The system is coded as class.
You can download it directly and add it to your system.

## Usage
* Variables
  * $path (Save to)
  * $id (Vidmoly ID) 
  * $result (Saved file paths)
  
* Code
  * $vidmoly = new Vidmoly();
  * $result = $vidmoly->download($path,$id);

* Example
  * $vidmoly = new Vidmoly();
  * $result  = $vidmoly->download('/vidmoly/download/','msln7st1y5ij);
