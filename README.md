XenForo-ConversationPermissions
======================

New Conversation Permissions.

- Can Reply to Conversation.
- Reply Limit for Conversation.
- Participant limit for email.

Just takes away a user's "reply" button, no banners.

The reply limit is for the entire conversation, but the limit is per user group. Consider when User A & User B are members of a conversation.

User A can have a reply limit of 5.
User B can have a reply limit of 10.

Once the conversation has >5 replies, User A can no longer post.
Once the conversation has >10 replies, User A and User B can no longer post.

Email Participant Limit permits disabling email notifications if the numbers of recipients in the conversation is greater than some threshold.
