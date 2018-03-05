<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace libraries;

use \Doctrine\ORM\EntityManager;
use \Telegram\Bot\Api;
use \Telegram\Bot\Exceptions\TelegramResponseException;

/**
 * Description of Postinformer
 *
 * @author Olga Pshenichnikova <olga@technofractal.org>
 */
class Postinformer 
{
	public static function informRequesters(
		EntityManager $orm,
		\orm\Post $post
	) {
		$config = new \Config();
		$token = $config->getToken();
		$api = new Api($token);		
		
		//$expr = Criteria::expr();		
		
		//$criteria = Criteria::create();
		//$criteria
		//	->where($expr->eq("isLast", true))
		//	->andWhere($expr->eq("category", $post->getCategory()));
		
		/* @var $queuepointers array */
		//$queuepointers = $orm
		//	->getRepository(\orm\Queuepointer::class)
		//	->matching($criteria)
		//	->toArray();
		
		/* @var $queuepointers array */
		$requesters = $orm
			->getRepository(\orm\Requester::class)
			->findAll();
			
		/* @var $requester orm\Requester */
		foreach ($requesters as $requester)
		{
			$categoryId = $post->getCategory()->getId();
			$categoryName = \bot\DataHelper::getCategoryName($categoryId);
			$text = \bot\DataHelper::getNewTasks($categoryName);
			$chatId = $requester->getChatId();
			
			if ($chatId)
			{			
				try {
					$api->sendMessage([ 
						'chat_id' => $chatId,
						'parse_mode' => 'HTML',
						'text' => $text
					]);
				} catch (TelegramResponseException $e) {
					error_log($e->getMessage());
				}
			}
		}
	}
}