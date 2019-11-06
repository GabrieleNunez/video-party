<?php namespace App\Classes;

use Library\Application;

class VideoEncoder
{
    private $video_file = '';
    private $ffmpeg_bin = '';

    /**
     * Set
     */
    public function __construct($ffmpeg_bin, $videoFile)
    {
        $this->videoFile = $videoFile;
        $this->ffmpeg_bin = $ffmpeg_bin;
    }

    public function makeHLS($output)
    {
        $ffmpeg_command =
            $ffmpeg_bin .
            ' ffmpeg -i "' .
            $this->videoFile .
            '" -codec: copy -start_number 0 -hls_list_size 0 -f hls "' .
            $output .
            '"';
        $cmdOutput = '';
        $cmdResult = null;
        exec($ffmpeg_command, $cmdOutput, $cmdResult);
        return $cmdResult;
    }
}

?>
