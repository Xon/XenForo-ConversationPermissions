<?php

class SV_ConversationPermissions_XenForo_Model_Conversation extends XFCP_SV_ConversationPermissions_XenForo_Model_Conversation
{
    const FETCH_PERMISSIONS = 0x10000;

    public function canReplyToConversation(array $conversation, &$errorPhraseKey = '', array $viewingUser = null)
    {
        $this->standardizeViewingUserReference($viewingUser);

        if (!XenForo_Permission::hasPermission($viewingUser['permissions'], 'conversation', 'canReply'))
            return false;

        $replylimit = XenForo_Permission::hasPermission($viewingUser['permissions'], 'conversation', 'replyLimit');
        if ($replylimit >= 0 && $conversation['reply_count'] >= $replylimit)
            return false;

        return parent::canReplyToConversation($conversation, $errorPhraseKey, $viewingUser);
    }


    public function insertConversationAlert(array $conversation, array $alertUser, $action,
        array $triggerUser = null, array $extraData = null, array &$messageInfo = null
    )
    {
        if (empty($alertUser['permissions']))
        {
            if (empty($alertUser['global_permission_cache']))
            {
                $alertUser['global_permission_cache'] = $this->_getDb()->fetchOne('
                    SELECT cache_value
                    FROM xf_permission_combination
                    WHERE permission_combination_id = ?
                ', $alertUser['permission_combination_id']);


            }
            $alertUser['permissions'] = XenForo_Permission::unserializePermissions($alertUser['global_permission_cache']);
        }

        $emailParticipantLimit = XenForo_Permission::hasPermission($alertUser['permissions'], 'conversation', 'emailParticipantLimit');
        if ($emailParticipantLimit >= 0 && count($conversation['recipients']) >= $emailParticipantLimit)
        {
            $alertUser['email_on_conversation'] = false;
        }

        parent::insertConversationAlert($conversation,$alertUser,$action,$triggerUser,$extraData);
    }

    public function getConversationRecipients($conversationId, array $fetchOptions = array())
    {
        if (empty($fetchOptions['join']))
        {
            $fetchOptions['join'] = 0;
        }
        $fetchOptions['join'] = $fetchOptions['join'] | self::FETCH_PERMISSIONS;

        return parent::getConversationRecipients($conversationId,$fetchOptions);
    }

    public function prepareConversationFetchOptions(array $fetchOptions)
    {
        $conversationFetchOptions = parent::prepareConversationFetchOptions($fetchOptions);

        if (!empty($fetchOptions['join']))
        {
            if ($fetchOptions['join'] & self::FETCH_PERMISSIONS)
            {
                $conversationFetchOptions['selectFields'] .= ',
                    permission_combination.cache_value AS global_permission_cache';
                $conversationFetchOptions['joinTables'] .= '
                    LEFT JOIN xf_permission_combination AS permission_combination ON
                        (permission_combination.permission_combination_id = user.permission_combination_id)';
            }
        }

        return $conversationFetchOptions;
    }
}