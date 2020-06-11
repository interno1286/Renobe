<?php

$config['news_upload_video_folder'] = $config['path']['public'].'news/video';
$config['news_upload_audio_folder'] = $config['path']['public'].'news/audio';

$config['news_upload_video_path'] = $config['url']['base'].'public/news/video';
$config['news_upload_audio_path'] = $config['url']['base'].'public/news/audio';

if (!isset($config['news_photo']))
	$config['news_photo'] = array (
		'dir' => $config['path']['public'].'news/',
		'big' => array(
			'width' => '800',
			'height' => '600',
			'quality' => '80',
			'cut' => false,
			'border' => false,
			'up_cut' => 2,
			'nophoto' => '',
		),
		'medium' => array(
			'width' => '362',
			'height' => '341',
			'quality' =>'70',
			'cut' => true,
			'border' => false,
			'up_cut' => 2,
			'nophoto' => '',
		),
		'small' => array(
			'width' => '218',
			'height' => '110',
			'quality' => '65',
			'cut' => true,
			'border' => false,
			'up_cut' => 2
		),
		'micro' => array(
			'width' => '50',
			'height' => '50',
			'quality' => '60',
			'cut' => true,
			'border' => false,
			'up_cut' => 2,
			'nophoto' => '',
		),
	);



if (!isset($config['news_gallery']))
	$config['news_gallery'] = array (
		'dir'	=> $config['path']['public'].'news_gallery/',
		'big'	=> array(
			'width'		=> 2000,
			'height'	=> 2000,
			'quality'	=> 90,
			'cut'		=> false
		),
		'small'	=> array(
			'width'		=> 167,
			'height'	=> 107,
			'quality'	=> 90,
			'cut'		=> false
		)
		
	);
