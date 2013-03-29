<?php
/*
Nucleus Codeviewer Plugin V1.2
(C) 2004 by Edelpils om3ega.com / ditnetwel.nl / Lord Matt
[With edits by Lord Matt]


Version history
	v1.2 LM
	- NOTE: As the prior version but with a few fixes
	- NEW FEATURE: You can now use [PHPCODE] as many times as you wish
	- CODING STYLE: Long tag used and closing tag removed
	v1.0 First release of this PHP source code viewer
Known issues (for now ;) )
	- You cannot use [PHPCODE] in the comments
	- You can use [PHPCODE] only 2 times per post (1 time in the body , and 1 time in the extended text)
	- You cannot display ASP code or C++ , only PHP

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
Contact me : om3ega@walla.com
You may use and distribute this freely as long as you leave the copyrights intact.
*/

class NP_Codeviewer extends NucleusPlugin {
	function getName() {
			return 'Codeviewer';
	}
	function getAuthor()  {
			return 'Edelpils / OM3EGA / www.ditnetwel.nl / Lord Matt';
	}
	function getURL(){
			return 'http://www.ditnetwel.nl/plugins/';
	}
	
	function getVersion() {
			return '1.2 LM';
	}
	
	function getDescription() {
			return 'Will display PHP source-code in colour without giving a DISALLOWED (text) warning. Write your PHP code between [phpcode] and [/phpcode] as many times as you want';
	}
	
	function install() {
		$this->createOption('enablephp','Enable PHP sourcecode display in posts?','yesno','yes');
	}
	
	function getEventList() { 
		return array('PreItem'); 
	}
	
	function doconvert(&$data) {		
		$nomore = false;
		$i = 0	;	
		$startsearch = "[phpcode]";
		$endsearch = "[/phpcode]";
		$search = array ("[phpcode]", "[/phpcode]");
		$replace = array ("", "");
		while(($nomore === false) AND ($i < 30)){

			$String = $data;
			$StartCode = strpos($String, $startsearch);
			$EndCode = strpos($String, $endsearch) + strlen($endsearch); // 
			//Lord Matt's bug escape section
			//This little hack detects the itteration of junk onto the front of your string and cuts it short.
			if ($EndCode  == strlen($endsearch)){
				// failed to find anything
				$EndCode  = 0;
				$nomore = True;
			}else{
				$EndCode2 = ($EndCode - $StartCode);
				$content2 = substr($String, $StartCode, $EndCode2);
				$String1 = substr($String, 0, $StartCode); //before
				$String2 = substr($String, $EndCode); // after
				$content1 = str_replace($search, $replace, $String1); //before with any stray tags removed
				//$content3 = str_replace($search, $replace, $String2);
				//$content1 = $String1;
				$content3 = $String2; // we don't clean this because we should find more in theory.
				$content2 = str_replace($search, $replace, $content2); //the section we are dealing with needs it's tags nomore
				$content2 = str_replace("<br />\r","",$content2); // end of line <br /> is not needed (added by NucleusCMS)
				if ($content2 != '') {
					$content2 = highlight_string($content2, true); //colourize it all
				}else{
					$nomore = True;	//stop		
				}
				$i++; //error sanity check MAX 30
				$data = $content1.$content2."<!-- $i -->".$content3;	
			}	
		}
		return $data;
	}
	
	function event_PreItem($data) {
		if ($this->getOption('enablephp') != 'yes') 
		return;
		$this->doconvert($data['item']->body);
		$this->doconvert($data['item']->more);	
	}
}
