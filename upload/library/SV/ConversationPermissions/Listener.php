<?php

class SV_ConversationPermissions_Listener
{	
	public static function loadClassModel($class, &$extend)
	{
		switch ($class)
		{
            case 'XenForo_Model_Conversation':
                $extend[] = 'SV_ConversationPermissions_XenForo_Model_Conversation';
                break;
		}      
	}  
}