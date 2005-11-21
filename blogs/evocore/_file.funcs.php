<?php
/**
 * This file implements various File handling functions.
 *
 * This file is part of the b2evolution/evocms project - {@link http://b2evolution.net/}.
 * See also {@link http://sourceforge.net/projects/evocms/}.
 *
 * @copyright (c)2003-2005 by Francois PLANQUE - {@link http://fplanque.net/}.
 * Parts of this file are copyright (c)2004-2005 by Daniel HAHLER - {@link http://thequod.de/contact}.
 *
 * @license http://b2evolution.net/about/license.html GNU General Public License (GPL)
 * {@internal
 * b2evolution is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * b2evolution is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with b2evolution; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 * }}
 *
 * {@internal
 * Daniel HAHLER grants Fran�ois PLANQUE the right to license
 * Daniel HAHLER's contributions to this file and the b2evolution project
 * under any OSI approved OSS license (http://www.opensource.org/licenses/).
 * }}
 *
 * @package evocore
 *
 * {@internal Below is a list of authors who have contributed to design/coding of this file: }}
 * @author blueyed: Daniel HAHLER.
 * @author fplanque: Fran�ois PLANQUE.
 *
 * @version $Id$
 */
if( !defined('EVO_MAIN_INIT') ) die( 'Please, do not access this page directly.' );


/**
 * Converts bytes to readable bytes/kb/mb/gb, like "12.45mb"
 *
 * @param integer bytes
 * @return string bytes made readable
 */
function bytesreadable( $bytes )
{
	static $types = NULL;

	if( !isset($types) )
	{ // generate once:
		$types = array(
			0 => array( 'abbr' => T_('B.'), 'text' => T_('Bytes') ),
			1 => array( 'abbr' => T_('KB'), 'text' => T_('Kilobytes') ),
			2 => array( 'abbr' => T_('MB'), 'text' => T_('Megabytes') ),
			3 => array( 'abbr' => T_('GB'), 'text' => T_('Gigabytes') ),
			4 => array( 'abbr' => T_('TB'), 'text' => T_('Terabytes') )
		);
	}

	for( $i = 0; $bytes > 1024; $i++ )
	{
		$bytes /= 1024;
	}

	$r = round($bytes, 2).'&nbsp;';
	$r .= '<abbr title="'.$types[$i]['text'].'">';
	$r .= $types[$i]['abbr'];
	$r .= '</abbr>';

	return $r;
}


/**
 * Get an array of all directories (and optionally files) of a given
 * directory, either flat (one-dimensional array) or multi-dimensional (then
 * dirs are the keys and hold subdirs/files).
 *
 * Note: there is no ending slash on dir names returned.
 *
 * @param string the path to start
 * @param boolean include files (not only directories)
 * @param boolean include directories (not the directory itself!)
 * @param boolean flat (return an one-dimension-array)
 * @param boolean Get the basename only.
 * @return false|array false if the first directory could not be accessed,
 *                     array of entries otherwise
 */
function get_filenames( $path, $inc_files = true, $inc_dirs = true, $flat = true, $recurse = true, $basename = false )
{
	$r = array();

	$path = trailing_slash( $path );

	if( $dir = @opendir($path) )
	{
		while( ( $file = readdir($dir) ) !== false )
		{
			if( $file == '.' || $file == '..' )
			{
				continue;
			}
			if( is_dir($path.$file) )
			{
				if( $flat )
				{
					if( $inc_dirs )
					{
						$r[] = $basename ? $file : $path.$file;
					}
					if( $recurse )
					{
						$rSub = get_filenames( $path.$file, $inc_files, $inc_dirs, $flat, $recurse, $basename );
						if( $rSub )
						{
							$r = array_merge( $r, $rSub );
						}
					}
				}
				else
				{
					$r[$file] = get_filenames( $path.$file, $inc_files, $inc_dirs, $flat, $recurse, $basename );
				}
			}
			elseif( $inc_files )
			{
				$r[] = $basename ? $file : $path.$file;
			}
		}
		closedir($dir);
	}
	else
	{
		return false;
	}

	return $r;
}


/**
 * Get a list of available admin skins.
 *
 * This checks if there's a _adminUI.class.php in there.
 *
 * @return array|false List of directory names that hold admin skins or
 *         false, if the admin skins driectory does not exist.
 */
function get_admin_skins()
{
	global $core_dirout, $admin_subdir, $adminskins_subdir;

	$skins_dir = dirname(__FILE__).'/'.$core_dirout.$admin_subdir.$adminskins_subdir;
	$dirs_in_adminskins_dir = get_filenames( $skins_dir, false, true, true, false, true );

	if( $dirs_in_adminskins_dir === false )
	{
		return false;
	}

	$r = array();
	if( $dirs_in_adminskins_dir )
	{
		foreach( $dirs_in_adminskins_dir as $l_dir )
		{
			if( !file_exists($skins_dir.$l_dir.'/_adminUI.class.php') )
			{
				continue;
			}
			$r[] = $l_dir;
		}
	}
	return $r;
}


/**
 * A replacement for fnmatch() which needs PHP 4.3
 *
 * @author jcl [atNOSPAM] jcl [dot] name {@link http://php.net/manual/function.fnmatch.php}
 */
function my_fnmatch( $pattern, $file )
{
	$lenpattern = strlen($pattern);
	$lenfile    = strlen($file);

	for($i=0 ; $i<$lenpattern ; $i++)
	{
		if($pattern[$i] == "*")
		{
			for($c=$i ; $c<max($lenpattern, $lenfile) ; $c++)
			{
				if(my_fnmatch(substr($pattern, $i+1), substr($file, $c)))
					return true;
			}
			return false;
		}

		if($pattern[$i] == "[")
		{
			$letter_set = array();
			for($c=$i+1 ; $c<$lenpattern ; $c++)
			{
				if($pattern[$c] != "]")
					array_push($letter_set, $pattern[$c]);
				else
					break;
			}
			foreach($letter_set as $letter)
			{
				if(my_fnmatch($letter.substr($pattern, $c+1), substr($file, $i)))
					return true;
			}
			return false;
		}

		if($pattern[$i] == "?") continue;
		if($pattern[$i] != $file[$i]) return false;
	}

	if(($lenpattern != $lenfile) && ($pattern[$i - 1] == "?")) return false;
	return true;
}



/**
 * Get size of a directory, including anything (especially subdirs) in there.
 *
 * @param string the dir's full path
 */
function get_dirsize_recursive( $path )
{
	$files = get_filenames( $path, true, false );
	$total = 0;

	foreach( $files as $lFile )
	{
		$total += filesize($lFile);
	}

	return $total;
}


/**
 * Deletes a dir recursive, wiping out all subdirectories!!
 *
 * @param string the dir
 */
function deldir_recursive( $dir )
{
	$toDelete = get_filenames( $dir );
	$toDelete = array_reverse( $toDelete );
	$toDelete[] = $dir;

	while( list( $lKey, $lPath ) = each( $toDelete ) )
	{
		if( is_dir( $lPath ) )
		{
			rmdir( $lPath );
		}
		else
		{
			unlink( $lPath );
		}
	}

	return true;
}


/**
 * Get the size of an image file
 *
 * @param string absolute file path
 * @param string what property/format to get: 'width', 'height', 'widthxheight',
 *               'type', 'string' (as for img tags), else 'widthheight' (array)
 * @return mixed false if no image, otherwise what was requested through $param
 */
function imgsize( $path, $param = 'widthheight' )
{
	/**
	 * Cache image sizes
	 */
	global $cache_imgsize;

	if( isset($cache_imgsize[$path]) )
	{
		$size = $cache_imgsize[$path];
	}
	elseif( !($size = @getimagesize( $path )) )
	{
		return false;
	}
	else
	{
		$cache_imgsize[$path] = $size;
	}

	if( $param == 'width' )
	{
		return $size[0];
	}
	elseif( $param == 'height' )
	{
		return $size[1];
	}
	elseif( $param == 'widthxheight' )
	{
		return $size[0].'x'.$size[1];
	}
	elseif( $param == 'type' )
	{
		switch( $size[1] )
		{
			case 1: return 'gif';
			case 2: return 'jpg';
			case 3: return 'png';
			case 4: return 'swf';
			default: return 'unknown';
		}
	}
	elseif( $param == 'string' )
	{
		return $size[3];
	}
	else
	{ // default: 'widthheight'
		return array( $size[0], $size[1] );
	}
}


/**
 * Add a trailing slash, if none present
 *
 * @param string the path/url
 * @return string the path/url with trailing slash
 */
function trailing_slash( $path )
{
	if( empty($path) || substr( $path, -1 ) == '/' )
	{
		return $path;
	}
	else
	{
		return $path.'/';
	}
}


/**
 * Remove trailing slash, if present
 *
 * @param string the path/url
 * @return string the path/url without trailing slash
 */
function no_trailing_slash( $path )
{
	if( substr( $path, -1 ) == '/' )
	{
		return substr( $path, 0, strlen( $path ) );
	}
	else
	{
		return $path;
	}
}


/**
 * Returns canonicalized absolute pathname as with realpath(), except it will
 * also translate paths that don't exist on the system.
 *
 * @param string the path to be translated
 * @return array [0] = the translated path (with trailing slash); [1] = TRUE|FALSE (path exists?)
 */
function check_canonical_path( $path )
{
	$path = str_replace( '\\', '/', $path );
	$pwd = realpath( $path );

	if( !empty($pwd) )
	{ // path exists
		$pwd = str_replace( '\\', '/', $pwd);
		if( substr( $pwd, -1 ) !== '/' )
		{
			$pwd .= '/';
		}
		return array( $pwd, true );
	}
	else
	{ // no realpath
		$pwd = '';
		$strArr = preg_split( '#/#', $path, -1, PREG_SPLIT_NO_EMPTY );
		$pwdArr = array();
		$j = 0;
		for( $i = 0; $i < count($strArr); $i++ )
		{
			if( $strArr[$i] != '..' )
			{
				if( $strArr[$i] != '.' )
				{
					$pwdArr[$j] = $strArr[$i];
					$j++;
				}
			}
			else
			{
				array_pop( $pwdArr );
				$j--;
			}
		}
		return array( implode('/', $pwdArr).'/', false );
	}
}


/**
 * Check for valid filename (no path allowed).
 *
 * @uses $Settings
 * @param string filename to test
 * @return boolean true if the filename is valid according to the regular expression, false if not
 */
function isFilename( $filename )
{
	global $Settings;

	return (boolean)preg_match( ':'.str_replace( ':', '\:', $Settings->get( 'regexp_filename' ) ).':', $filename );
}


/**
 * Check that the file extension is allowed.
 *
 * Case independant.
 *
 * @param filename to check
 * @param string by ref, returns extension
 * @return boolean
 */
function validate_file_extension( $filename, & $extension )
{
	global $Settings, $Messages;
	static $allowedFileExtensions;

	if( !isset($allowedFileExtensions) )
	{
		$allowedFileExtensions = preg_split( '#\s+#', strtolower( trim( $Settings->get( 'upload_allowedext' ) ) ), -1, PREG_SPLIT_NO_EMPTY );
	}

	if( !empty($allowedFileExtensions) )
	{ // check extension
		if( preg_match( '#\.([a-zA-Z0-9\-_]+)$#', $filename, $match ) )
		{
			$extension = strtolower($match[1]);

			if( !in_array( $extension, $allowedFileExtensions ) )
			{
				return false;
			}
		}
		// NOTE: Files with no extension are allowed..
	}

	return true;
}


/*
 * $Log$
 * Revision 1.33  2005/11/21 18:33:19  fplanque
 * Too many undiscussed changes all around: Massive rollback! :((
 * As said before, I am only taking CLEARLY labelled bugfixes.
 *
 * Revision 1.30  2005/11/18 07:53:05  blueyed
 * use $_FileRoot / $FileRootCache for absolute path, url and name of roots.
 *
 * Revision 1.29  2005/11/09 02:53:13  blueyed
 * made bytesreadable() more readable
 *
 * Revision 1.28  2005/11/02 20:11:19  fplanque
 * "containing entropy"
 *
 * Revision 1.27  2005/11/02 00:42:30  blueyed
 * Added get_admin_skins() and use it to perform additional checks (if there's a _adminUI.class.php file in there). Thinkl "CVS".. :)
 *
 * Revision 1.26  2005/11/02 00:03:46  blueyed
 * Fixed get_filenames() $basename behaviour.. sorry.
 *
 * Revision 1.25  2005/11/01 21:55:54  blueyed
 * Renamed retrieveFiles() to get_filenames(), added $basename parameter and fixed inner recursion (wrong params where given)
 *
 * Revision 1.24  2005/09/29 15:07:30  fplanque
 * spelling
 *
 * Revision 1.23  2005/09/06 17:13:54  fplanque
 * stop processing early if referer spam has been detected
 *
 * Revision 1.22  2005/07/26 18:50:47  fplanque
 * enhanced attached file handling
 *
 * Revision 1.21  2005/06/20 17:40:23  fplanque
 * minor
 *
 * Revision 1.20  2005/05/24 15:26:52  fplanque
 * cleanup
 *
 * Revision 1.19  2005/05/17 19:26:07  fplanque
 * FM: copy / move debugging
 *
 * Revision 1.18  2005/05/13 18:41:28  fplanque
 * made file links clickable... finally ! :P
 *
 * Revision 1.17  2005/05/13 16:49:17  fplanque
 * Finished handling of multiple roots in storing file data.
 * Also removed many full paths passed through URL requests.
 * No full path should ever be seen by the user (only the admins).
 *
 * Revision 1.16  2005/05/12 18:39:24  fplanque
 * storing multi homed/relative pathnames for file meta data
 *
 * Revision 1.15  2005/04/29 18:49:32  fplanque
 * Normalizing, doc, cleanup
 *
 * Revision 1.14  2005/04/28 20:44:20  fplanque
 * normalizing, doc
 *
 * Revision 1.13  2005/04/27 19:05:46  fplanque
 * normalizing, cleanup, documentaion
 *
 * Revision 1.12  2005/04/19 16:23:02  fplanque
 * cleanup
 * added FileCache
 * improved meta data handling
 *
 * Revision 1.11  2005/02/28 09:06:33  blueyed
 * removed constants for DB config (allows to override it from _config_TEST.php), introduced EVO_CONFIG_LOADED
 *
 * Revision 1.10  2005/01/15 17:30:08  blueyed
 * regexp_fileman moved to $Settings
 *
 * Revision 1.9  2005/01/13 20:27:07  blueyed
 * doc
 *
 * Revision 1.8  2005/01/05 03:04:00  blueyed
 * refactored
 *
 * Revision 1.7  2004/12/31 17:43:09  blueyed
 * enhanced bytesreadable(), fixed deldir_recursive()
 *
 * Revision 1.6  2004/12/30 16:45:40  fplanque
 * minor changes on file manager user interface
 *
 * Revision 1.5  2004/12/29 02:25:55  blueyed
 * no message
 *
 * Revision 1.3  2004/10/21 00:14:44  blueyed
 * moved
 *
 * Revision 1.2  2004/10/14 18:31:25  blueyed
 * granting copyright
 *
 * Revision 1.1  2004/10/13 22:46:32  fplanque
 * renamed [b2]evocore/*
 *
 * Revision 1.10  2004/10/12 22:33:40  blueyed
 * minor doc formatation
 *
 * Revision 1.9  2004/10/12 17:22:30  fplanque
 * Edited code documentation.
 */
?>