ffmpeg -i  "SampleMoviee.mp4" -codec: copy -start_number 0 -hls_time 10 -hls_list_size 0 -f hls "SampleMoviee.m3u8"