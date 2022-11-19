
# Telegram Bot

This code in php allow to memorize all the user that has started the chat with a bot and send them message or photo.

It use the official [Telegram Bot API](https://core.telegram.org/api) simply by HTTP request.


Run this `.php` file every time you want to check if new user has started the chat with the bot

```
check_updates.php
```

To send a message/photo in a chat run this `.php`. The possibile parameter are:
```
index.php
```
| Parameter | Type     | Description                       |
| :-------- | :------- | :-------------------------------- |
| `chat_id` | `int` | Identifier of the user chat with the bot. |
| `username` | `string` | Identifier of the user (automatically check if the username is memorize in `chat_ids.json` and get his `chat_id`) |
| `text` | `string` | The text message to send |
| `photo` | `string` | URL or path to local photo to send |

**Required**: `chat_id` or `username`

**Required**: `text` or `photo`
