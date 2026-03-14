## Chats

System built-in Messaging app

### 1. Conversation
  * can be archived
  * shows the last message sent/received in the list
  * shows datetime of the last message sent or received
  * badge/or indicator the conversation has unread messages
  * search and filter for conversations in the list
  * can create group conversation with 1 or more people
  * can rename custom group conversation name
  * can change conversation avatar
  * participant of the group conversation can forcely kicked-out or removed
  * able to add participants in an existing group conversation
  * should have list of attachments categorized by "Links", "Media" and "Location"
  * able to mention any participants of the conversation, mentioned person will be notified he/she is not currently in the chatbox

### 2. Messaging
  * shows indicator "typing..." when other participant is currently typing a message 
  * shows datetime of a message was sent
  * can be edited by sender
  * Can be forwarded to any of your contacts or addressbook. forwarding message to other person will create new conversation if both party doesnt have one yet.
  * Can be deleted with confirmation message.
  * shows indicator to the sender if the message was seen/read by recipient
  * Able to send media (e.g videos, pdfs, or images) as attachment. (NOTE: The messages that sent as attachments have their own unique appearance)
  * integrate emoticons
  * recipient active/online indicator
  * able to do voice calling?
  
  #### 1.1. Message appearance based on attachment type:
      * when user tap it should play the video, read pdf, or view the image in larger
      * attachment can mix together with text/caption. if you select the file to attach user able to also write some "caption" or "notes" for that attachment.
      * attachments can be downloaded by recipient and store to media library.
    ##### 1.1.1. Video
          * can sent by opening media library or recording from camera
          * Mini media player that can be played inside chatbox/conversation
    ##### 1.1.2. PDF
          * Thumbnail image of the document's first page
    ##### 1.1.3. Image
          * Thumbnail of the image in lower quality version
          * can sent by opening media library or taking from camera
          * image viewer have simple image editing before and/or after send:
            * crop/resize the image
            * rotate image clock or counter clockwise, flip vertically/horizontally
            * write using pencil and changing stoke color and size
            * add emoticons/stickers/shapes in top of the image and can be drag to change the position
            * shapes can be resized
    ##### 1.1.4. Links
          * external links - browser links that serve outside sowidu - crawled create a thumbnail based on the page metadata  
          * internal links - e.g specific delivery ticket, order, person/organization pofile page, invoice, catalogue and items or any other links inside sowidu network.
    ##### 1.1.5. Reply
          * quote the message sent by others indicating you are replying in that specific message
          * most likely skype reply message
    ##### 1.1.6. Location
          * can be send by opening a map and draging the marker position or send the current location of the user
          * thumbnail of the map location and a marker of the coordinates
          * can be sent from system's address records
          * when pressed, will open mobile map application (google map or ios map) with marked coordinates
    ##### 1.1.6. Voice
          * works like voice mail or facebook messenger voice message
          * will access and open mobile's mic, record the voice comming in then send.
          * should be display like video player but without the video cover. only the play/pause button and the duration.

### 3. Contacts
  * list of people you can chat to
  * search and filter of the list
  * when press, will create conversation with that person or redirect to the existing conversation chatbox 

### 4. Notification
  * notify about newly arrived messages
  * notify when mentioned from any conversation
  * notification alert sound

### 5. Settings
  * can mute notifications
  * other settings
