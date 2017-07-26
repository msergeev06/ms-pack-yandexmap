<?php

namespace MSergeev\Packages\Yandexmap\Lib;

use MSergeev\Core\Lib as CoreLib;

class YandexMap
{
	private static $staticMapService = null;

	private static function init()
	{
		if (is_null(self::$staticMapService))
		{
			if ($service=CoreLib\Options::getOptionStr('yandexmap_service'))
			{
				self::$staticMapService = $service;
			}
			else
			{
				self::$staticMapService = 'https://static-maps.yandex.ru/1.x/?';
				CoreLib\Options::setOption('yandexmap_service','https://static-maps.yandex.ru/1.x/?');
			}
		}
	}

	public static function showImgPoint ($lat=null, $lon=null, $radius=500, $width=450, $height=450)
	{
		//Обрабатываем входные параметры
		if (is_null($lat) || is_null($lon) || floatval($lat)<=0 || floatval($lon)<=0)
		{
			return '';
		}
		else
		{
			$lon = floatval($lon);
			$lat = floatval($lat);
		}
		if (intval($radius)<=0)
		{
			$radius = 500;
		}
		else
		{
			$radius = intval($radius);
		}
		$spn = $radius/20000;
		if (intval($width)<=0)
		{
			$width = 450;
		}
		else
		{
			$width = intval($width);
		}
		if (intval($height)<=0)
		{
			$height = 450;
		}
		else
		{
			$height = intval($height);
		}

		//Создаем массив параметров
		$arParams = array(
			'l' => 'map',
			'll' => $lon.','.$lat,
			'spn' => $spn.','.$spn,
			'size' => $width.','.$height,
			'pt' => $lon.','.$lat.',pm2blm'
		);

		return self::showImg($arParams);
	}

	public static function showImg ($arParams=array())
	{
		self::init();
		if (empty($arParams))
		{
			return '';
		}

		$img = '<img src="'.self::getStaticUrlParams($arParams);
		$imgWidth=450;
		$imgHeight=450;
		if (isset($arParams['size']))
		{
			list($imgWidth,$imgHeight) = explode(',',$arParams['size']);
		}

		$img.= '" width="'.$imgWidth.'" height="'.$imgHeight.'">';

		return $img;
	}

	public static function getStaticUrl ($lat=null, $lon=null, $radius=500, $width=450, $height=450)
	{
		//Обрабатываем входные параметры
		if (is_null($lat) || is_null($lon) || floatval($lat)<=0 || floatval($lon)<=0)
		{
			return '';
		}
		else
		{
			$lon = floatval($lon);
			$lat = floatval($lat);
		}
		if (intval($radius)<=0)
		{
			$radius = 500;
		}
		else
		{
			$radius = intval($radius);
		}
		$spn = $radius/20000;
		if (intval($width)<=0)
		{
			$width = 450;
		}
		else
		{
			$width = intval($width);
		}
		if (intval($height)<=0)
		{
			$height = 450;
		}
		else
		{
			$height = intval($height);
		}

		//Создаем массив параметров
		$arParams = array(
			'l' => 'map',
			'll' => $lon.','.$lat,
			'spn' => $spn.','.$spn,
			'size' => $width.','.$height,
			'pt' => $lon.','.$lat.',pm2blm'
		);

		return self::getStaticUrlParams($arParams);
	}

	public static function getStaticUrlParams ($arParams=array())
	{
		if (empty($arParams))
		{
			return '';
		}
		self::init();

		$url = self::$staticMapService;
		if (!isset($arParams['l']) || ($arParams['l']!='sat' && $arParams['l']!='sat,skl'))
		{
			$url.= 'l=map';
		}
		else
		{
			$url.= 'l='.$arParams['l'];
		}
		if (isset($arParams['ll']))
		{
			$url.='&ll='.$arParams['ll'];
		}
		if (isset($arParams['spn']))
		{
			$url.='&spn='.$arParams['spn'];
		}
		if (isset($arParams['z']))
		{
			$url.='&z='.$arParams['z'];
		}
		if (isset($arParams['size']))
		{
			$url.='&size='.$arParams['size'];
		}
		if (isset($arParams['scale']))
		{
			$url.='&scale='.$arParams['scale'];
		}
		if (isset($arParams['pt']))
		{
			$url.='&pt='.$arParams['pt'];
		}
		if (isset($arParams['pl']))
		{
			$url.='&pl='.$arParams['pl'];
		}
		if (isset($arParams['lang']))
		{
			$url.='&lang='.$arParams['lang'];
		}
		if (isset($arParams['key']))
		{
			$url.='&key='.$arParams['key'];
		}

		return $url;
	}

	public static function showMapForClick ($mapName='map',$latId='lat', $lonId='lon', $lat=null,$lon=null,$width=400,$height=300)
	{
		if (is_null($lat) || floatval($lat)<=0)
		{
			$lat=37.621256;
		}
		else
		{
			$lat=floatval($lat);
		}
		if (is_null($lon) || floatval($lon)<=0)
		{
			$lon=55.753806;
		}
		else
		{
			$lon=floatval($lon);
		}

		CoreLib\Buffer::addJs(CoreLib\Config::getConfig('PACKAGES_ROOT').'yandexmap/js/api-maps.js');

		$echo = "<div id=\"".$mapName."\" style=\"width:".$width."px; height:".$height."px;\"></div>\n"
			."<script>\n\t"
				//."//ymaps.ready(init);\n\t"
				."var ".$mapName.";\n\t"
				."function init_".$mapName." () {\n\t\t"
					.$mapName." = new ymaps.Map(\"".$mapName."\",{center: [".$lon.", ".$lat."],zoom: 12},{balloonMaxWidth: 200});\n\t\t"
					.$mapName.".controls.add('zoomControl', { left: 5, top: 5 }).add('typeSelector');\n\t\t"
					.$mapName.".events.add('click', function (e){\n\t\t\t"
					."if (!".$mapName.".balloon.isOpen()) {\n\t\t\t\t"
						."var coords = e.get('coordPosition');\n\t\t\t\t"
						."document.getElementById('".$latId."').value = coords[0].toFixed(6);\n\t\t\t\t"
						."document.getElementById('".$lonId."').value = coords[1].toFixed(6);\n\t\t\t\t"
						.$mapName.".balloon.open(coords, {contentBody: '".CoreLib\Loc::getPackMessage('yandexmap','all_coordinates_save').": ['+[coords[0].toFixed(6),coords[1].toFixed(6)].join(', ') + ']'});\n\t\t\t"
					."}else {"
						.$mapName.".balloon.close();}"
					."});}"
			."</script>";


		return $echo;
	}
}