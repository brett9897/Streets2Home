<?php
	function update_manifest(){
		$filename = 's2h_survey_offline.manifest';
		if( file_exists($filename) ){			
				//---found manifest file....will get the contents, search for the '##Survey last modified on' and update that line with current time stamp
				$fileStringArrays = file($filename);
				$fileP = fopen( $filename, "w+") or die	("--- " . print_r(error_get_last()) . " ---unable to open offline web application manifest file");			//option 'w+' will open the file for read/write, clear the contents, and place file pointer at start of file.

				if( $fileStringArrays != false  &&  $fileP != false){
					$updated = false;
					foreach ($fileStringArrays as &$stringLine){												//php $stringLine is passed by value...&$stringLine is passed by reference (can modify the element in the array this way)
						//---search for the timestamp line to update
						if( preg_match("/#Survey last modified on/", $stringLine) ){
							$stringLine = '#Survey last modified on (mm/dd/yy) ' . date("m/d/y : H:i:s", time()). "\n";				// "\n"  is read as the new line char '\n' is NOT
							$updated = true;
						}
					}
					//---ensure the manifest file has been updated (appends to end of string contents)...otherwise client browser will not re-cache
					if(!$updated){
							$fileStringArrays[ count($fileStringArrays)] = '#Survey last modified on (mm/dd/yy) ' . date("m/d/y : H:i:s", time()). "\n";
					}
					$newContent = implode('', $fileStringArrays);
					writeAndCloseFile($fileP, $newContent);
				}
				else {
					die ("Unable to get and modify the offline web application manifest file");				
				}
		}
		else {
			//---no manifest files exists...some idiot deleted it.  Have to remake it.
			//---MAY  HAVE  TO  SET  SPECIAL  WRITE  PERMISSIONS  TO  THIS  FOLDER  FOR  PHP (user)
				$fileP = fopen( $filename, "w+") or die	("unable to recreate the offline web application manifest file");
				$contents = 'CACHE MANIFEST';
				$contents = $contents . "\n" . '#Survey last modified on ' . date("d/m/y : H:i:s", time()) . "\n";
				//$contents .= "FALLBACK:\n";
				//$contents .= "survey_offline_static.html\n";
				$contents .= "CACHE:\n";
				$contents .= "style.css\n";
				$contents .= "screen.css\n";
				$contents .= "jquery-1.8.2.js\n";
				
				
				writeAndCloseFile($fileP, $contents);
		}

		if( !file_exists($filename) ){
			die	("unable to verify update of the offline web application manifest file. ERROR: " . print_r(error_get_last()) );
		}
	}



	function writeAndCloseFile( $fp,  $content){
					$writeSuccess = fwrite($fp, $content);
					$closeSuccess = fclose($fp);
					
					if( $writeSuccess == false  ||  $closeSuccess == false ){
						die	("Failed to update offline web application manifest file");
					}
	}

?>
