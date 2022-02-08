/**
 *
 * Messages Component
 *
 */
"use strict";
/* global app, user, messengerVars, pusher, FileUpload, Lists, Pusher, PusherBatchAuthorizer, updateButtonState, mswpScanPage, trans, bootstrapDetectBreakpoint  */

$(function () {

    if(messengerVars.bootFullMessenger){
        messenger.boot();
        messenger.fetchContacts();
        messenger.initAutoScroll();
        messenger.initMarkAsSeen();
        messenger.resetTextAreaHeight();
        if(messengerVars.lastContactID !== false){
            messenger.fetchConversation(messengerVars.lastContactID);
        }
        FileUpload.initDropZone('.dropzone','/attachment/upload/message');
        messenger.initSelectizeUserList();
    }

});

var messenger = {

    state : {
        contacts:[],
        conversation:[],
        activeConversationUserID:null,
        activeConversationUser:null,
        currentBreakPoint: 'lg'
    },

    pusher: null,

    /**
     * Boots up the main messenger functions
     */
    boot: function(){
        Pusher.logToConsole = typeof messengerVars.pusherDebug !== 'undefined' ? messengerVars.pusherDebug : false;
        messenger.pusher = new Pusher(pusher.key, {
            authorizer: PusherBatchAuthorizer,
            authDelay: 200,
            cluster: messengerVars.pusherCluster,
            forceTLS: true,
            authEndpoint:  app.baseUrl + '/my/messenger/authorizeUser',
            auth: {
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                }
            }
        });
    },

    /**
     * Instantiates pusher sockets for each conversation (batched)
     */
    initLiveSockets: function(){
        $.each(messenger.state.contacts, function (k,v) {
            const minID = Math.min(v.receiverID,v.senderID);
            const maxID = Math.max(v.receiverID,v.senderID);
            const keyID = ("" + minID + '-' + maxID);
            let channel = messenger.pusher.subscribe('private-chat-channel-'+keyID);
            channel.bind('new-message', function(data) {
                const message = jQuery.parseJSON(data.message);
                if(message.sender_id === messenger.state.activeConversationUserID){
                    messenger.state.conversation.push(message);
                    messenger.reloadConversation();
                }
                messenger.updateUnreadMessagesCount(parseInt($('#unseenMessages').html()) + 1);
                messenger.addLatestMessageToConversation(message.sender_id,message);
                messenger.markConversationAsRead(message.sender_id,'unread');
                messenger.reloadContactsList();
                messenger.setActiveContact(messenger.state.activeConversationUserID);
            });
        });
    },

    /**
     * Initiate chatbox scroll to bottom event
     */
    initAutoScroll: function(){
        $(".messageBoxInput").keydown(function(e){
            // Enter was pressed without shift key
            if (e.keyCode === 13)
            {
                if(!e.shiftKey){
                    e.preventDefault();
                    $('.send-message').trigger('click');
                }
            }
        });
    },

    /**
     * Fetches all messenger contacts
     */
    fetchContacts: function () {
        $.ajax({
            type: 'GET',
            url: app.baseUrl + '/my/messenger/fetchContacts',
            dataType: 'json',
            success: function (result) {
                if(result.status === 'success'){
                    messenger.state.contacts = result.data.contacts;
                    messenger.reloadContactsList();
                    messenger.initLiveSockets();
                }
                else{
                    // messenger.state.contacts = result.data
                }
            }
        });
    },

    /**
     * Switches between layout having horiznatal scroll for contacts or not
     */
    makeContactsHeaderResponsive: function(){
        const breakPoint = bootstrapDetectBreakpoint();
        if(breakPoint.name === 'xs'){
            $('.conversations-list').mCustomScrollbar({
                theme: "minimal-dark",
                axis:'x',
                scrollInertia: 200,
            });
            $('.conversations-list').addClass('border-top');
        }
        else{
            $('.conversations-list').mCustomScrollbar("destroy");
            $('.conversations-list').removeClass('border-top');
        }
    },

    /**
     * Fetches conversation with certain user
     * @param userID
     */
    fetchConversation: function (userID) {

        // Setting up loading and clearign up conv content
        $('.conversation-loading-box').removeClass('d-none');
        $('.conversation-header-loading-box').removeClass('d-none');
        $('.conversation-header').addClass('d-none');

        // Setting up loading and clearign up conv content
        $('.conversation-loading-box').removeClass('d-none');
        $('.conversation-content').html('');
        $.ajax({
            type: 'GET',
            url: app.baseUrl + '/my/messenger/fetchMessages/' + userID,
            dataType: 'json',
            success: function (result) {
                if(result.status === 'success'){
                    messenger.state.conversation = result.data.messages;
                    messenger.reloadConversation();
                    messenger.state.activeConversationUserID = userID;
                    messenger.setActiveContact(userID);
                    messenger.reloadConversationHeader();
                }
                else{
                    // messenger.state.contacts = result.data
                }
            }
        });
    },

    /**
     * Sends the message
     * @returns {boolean}
     */
    sendMessage: function() {
        updateButtonState('loading',$('.send-message'));
        if($('.messageBoxInput').val().length === 0){
            $('.messageBoxInput').addClass('is-invalid');
            updateButtonState('loaded',$('.send-message'));
            return false;
        }else{
            $('.messageBoxInput').removeClass('is-invalid');
        }
        $.ajax({
            type: 'POST',
            url: app.baseUrl + '/my/messenger/sendMessage',
            data: {
                'message': $('.conversation-writeup .messageBoxInput').val(),
                'attachments' : FileUpload.attachaments,
                'receiverID' : $('.conversation-writeup #receiverID').val()
            },
            dataType: 'json',
            success: function (result) {
                messenger.state.conversation.push(result.data.message);
                messenger.reloadConversation();
                messenger.clearMessageBox();
                messenger.addLatestMessageToConversation(result.data.message.receiverID,result.data.message);
                messenger.reloadContactsList();
                messenger.hideEmptyChatElements();
                messenger.clearFileUploadsState();
                messenger.resetTextAreaHeight();
                updateButtonState('loaded',$('.send-message'));
            }
        });
    },

    /**
     * Clears up uploaded files
     */
    clearFileUploadsState: function(){
        FileUpload.attachaments = [];
        $('.dropzone-previews').html('');
    },

    /**
     * Creates initial (new) conversation
     * @returns {boolean}
     */
    createConversation: function() {
        let data = $("#userMessageForm").serialize()+'&new=true';

        if($('#userMessageForm #select-repo').val() === ""){
            $('#userMessageForm .mfv-errorBox').html('<div class="alert alert-dismissable alert-danger text-white">\
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">						<span aria-hidden="true">&times;</span>					</button>'
                +trans('Please select an user first')+'. </div>');
            return false;
        }

        if($('#userMessageForm #messageText').val() === ""){
            $('#userMessageForm .mfv-errorBox').html('<div class="alert alert-dismissable alert-danger text-white">\
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">						<span aria-hidden="true">&times;</span>					</button>'
                +trans('Please enter your message')+'.</div>');
            return false;
        }

        $.ajax({
            type: 'POST',
            url: app.baseUrl + '/my/messenger/sendMessage',
            data: data,
            success: function (result) {
                $("textarea[name=message]").val("");
                $('#messageModal').modal('hide');
                let contactID = result.data.contact[0].contactID;
                if(!messenger.isExistingContact(contactID)){
                    messenger.state.contacts.unshift(result.data.contact[0]);
                }
                else{
                    // add latest contact details
                    $.map(messenger.state.contacts,function (contact,k) {
                        if(contactID === contact.contactID){
                            let newContact = result.data.contact[0];
                            messenger.state.contacts[k] = newContact;
                        }
                    });
                }
                messenger.reloadContactsList();
                messenger.state.activeConversationUserID = contactID;
                messenger.fetchConversation(contactID);
                messenger.hideEmptyChatElements();
                messenger.initLiveSockets();
            }
        });
    },

    /**
     * Method used for starting a conversation from the profile page
     */
    sendDMFromProfilePage: function(){
        let data = $("#userMessageForm").serialize()+'&new=true';
        $.ajax({
            type: 'POST',
            url: app.baseUrl + '/my/messenger/sendMessage',
            data: data,
            success: function () {
                $("textarea[name=message]").val("");
                $('#messageModal').modal('hide');
                window.location.assign(app.baseUrl + '/my/messenger');
            }
        });
    },

    /**
     * Marks message as seen
     */
    initMarkAsSeen:function(){
        $( ".messageBoxInput" ).on('click', function() {
            if($('#unseenValue').val() !== 0){
                $.ajax({
                    type: 'POST',
                    url: app.baseUrl + '/my/messenger/markSeen',
                    data: {userID:messenger.state.activeConversationUserID},
                    dataType: 'json',
                    success: function (result) {
                        messenger.markConversationAsRead(messenger.state.activeConversationUserID,'read');
                        messenger.updateUnreadMessagesCount(parseInt($('#unseenMessages').html()) - result.data.count);
                        messenger.reloadContactsList();
                    }
                });
            }
        });
    },

    /**
     * Checks if user already has a conversation with certain user
     * @param contactID
     * @returns {boolean}
     */
    isExistingContact: function(contactID){
        // Search if contact is present
        let isNewContact = false;
        $.map(messenger.state.contacts,function (contact) {
            if(contactID === contact.contactID){
                isNewContact = true;
            }
        });
        return isNewContact;
    },

    /**
     * Reloads conversation list
     */
    reloadContactsList: function () {
        let contactsHtml = '';
        $.each( messenger.state.contacts, function( key, value ) {
            contactsHtml += contactElement(value);
        });
        if(messenger.state.contacts.length > 0){
            $('.conversations-list').html('<div class="row">'+contactsHtml+'</div>');
        }
    },

    /**
     * Reloads convesation header
     */
    reloadConversationHeader: function(){
        if(typeof messenger.state.conversation[0] !== 'undefined'){
            const contact = messenger.state.conversation[0];
            const userID = (contact.receiver_id !== messenger.state.activeConversationUserID ? contact.sender.id : contact.receiver.id);
            const username = (contact.receiver_id !== messenger.state.activeConversationUserID ? contact.sender.username : contact.receiver.username);
            const avatar = (contact.receiver_id !== messenger.state.activeConversationUserID ? contact.sender.avatar : contact.receiver.avatar);
            const name = contact.receiver_id !== messenger.state.activeConversationUserID ? `${contact.sender.name} ` : `${contact.receiver.name}`;
            const profile = contact.receiver_id !== messenger.state.activeConversationUserID ? contact.sender.profileUrl : contact.receiver.profileUrl;
            $('.conversation-header').removeClass('d-none');
            $('.conversation-header-loading-box').addClass('d-none');
            $('.conversation-header-avatar').attr('src',avatar);
            $('.conversation-header-user').html(name);
            $('.conversation-profile-link').attr('href',profile);

            $('.details-holder .unfollow-btn').unbind('click');
            $('.details-holder .block-btn').unbind('click');
            $('.details-holder .report-btn').unbind('click');


            $('.details-holder .unfollow-btn').on('click',function () {
                Lists.showListManagementConfirmation('unfollow', userID);
            });
            $('.details-holder .block-btn').on('click',function () {
                Lists.showListManagementConfirmation('block', userID);
            });
            $('.details-holder .report-btn').on('click',function () {
                Lists.showReportBox(userID,null);
            });

            $('.details-holder .tip-btn').attr('data-username','@'+username);
            $('.details-holder .tip-btn').attr('data-name',name);
            $('.details-holder .tip-btn').attr('data-avatar',avatar);
            $('.details-holder .tip-btn').attr('data-recipient-id',userID);

        }
    },

    /**
     * Reloads conversation
     */
    reloadConversation: function () {
        let conversationHtml = '';
        $.each( messenger.state.conversation, function( key, value ) {
            conversationHtml += messageElement(value);
        });
        $('.conversation-content').html(conversationHtml);
        // Scrolling down to last message
        if($('.conversation-content .message-box').length){
            $(".conversation-content").animate({ scrollTop: $('.conversation-content')[0].scrollHeight}, 800);
        }
        $('.conversation-loading-box').addClass('d-none');
        messenger.initLinks();
        messenger.initMessengerGalleries();
    },

    /**
     * Method used for auto adjusting textarea message height on resize
     * @param el
     */
    textAreaAdjust: function(el) {
        el.style.height = (el.scrollHeight > el.clientHeight) ? (el.scrollHeight)+"px" : "40px";
    },

    /**
     * Resets the send new message text area height
     */
    resetTextAreaHeight: function(){
        $(".messageBoxInput").css('height',45);
    },

    /**
     * Set currently active contact
     * @param userID
     */
    setActiveContact: function (userID) {
        $('.messageBoxInput').focus();
        $('#receiverID').val(userID);
        $('.contact-box').each(function (k,el) {
            $(el).removeClass('contact-active');
        });

        setTimeout(function(){ $('.contact-'+userID).addClass('contact-active'); }, 100);

    },

    /**
     * Clears up the new message field
     */
    clearMessageBox: function(){
        $(".messageBoxInput").val('');
    },

    /**
     * Updates the unread messages count
     * TODO: Might not be used anymore atm
     * @param val
     * @returns {boolean}
     */
    updateUnreadMessagesCount: function (val) {
        $("#unseenMessages").html(val);
        return true;
    },

    /**
     * Marks conversation as being read
     * @param userID
     * @param type
     */
    markConversationAsRead: function (userID, type) {
        $.map(messenger.state.contacts,function (contact,k) {
            if(userID === contact.contactID){
                let newContact = contact;
                newContact.isSeen = type === 'read' ? 1 : 0;
                messenger.state.contacts[k] = newContact;
            }
        });
        // eslint-disable-next-line no-unused-vars
        let newContactsList = messenger.state.contacts; // These kinds of stuff should be immutable
    },

    /**
     * Appends latest message to the conversation
     * @param contactID
     * @param message
     */
    addLatestMessageToConversation: function (contactID, message) {
        // add latest contact details
        let contactKey = null;
        // eslint-disable-next-line no-unused-vars
        let contactObj = null;
        let newContact = null;
        $.map(messenger.state.contacts,function (contact,k) {
            if(contactID === contact.contactID){
                newContact = contact;
                contactKey = k;
                newContact.lastMessage = message.message;
                newContact.dateAdded = message.dateAdded;
                newContact.dateAdded = message.dateAdded;
                newContact.senderID = message.sender_id;
                newContact.lastMessageSenderID = message.sender_id;
                messenger.state.contacts[k] = newContact;
            }
        });

        let newContactsList = messenger.state.contacts; // These kinds of stuff should be immutable
        if(contactKey !== null){
            newContactsList.splice(contactKey, 1);
            newContactsList.unshift(newContact);
            messenger.state.contacts = newContactsList;
        }

    },

    /**
     * Globally instantiates all href links within a conversation
     */
    initLinks: function(){
        $('.conversation-content .message-bubble').html(function(i, text) {
            var body = text.replace(
                // eslint-disable-next-line no-useless-escape
                /\bhttps:\/\/([\w\.-]+\.)+[a-z]{2,}\/.+\b/gi,
                '<a target="_blank" class="text-white" href="$&">$&</a>'
            );
            return body.replace(
                // eslint-disable-next-line no-useless-escape
                /\bhttp:\/\/([\w\.-]+\.)+[a-z]{2,}\/.+\b/gi,
                '<a target="_blank" class="text-white" href="$&">$&</a>'
            );
        });
    },

    /**
     * Globally instantiates all message attachments and groups them into individual galleries
     */
    initMessengerGalleries: function(){
        $('.message-box').each(function (index, item) {
            if($(item).find('.attachments-holder').children().length > 0){
                mswpScanPage($(item),'mswp');
            }
        });
    },

    /**
     * Replaces message's newlines with html break lines
     * @param text
     * @returns {*}
     */
    parseMessage: function(text){
        return text.replaceAll('\n','<br/>');
    },

    /**
     * Loads UI elements for loaded messenger
     */
    hideEmptyChatElements: function () {
        $('.conversation-writeup').removeClass('hidden');
        $('.no-contacts').addClass('hidden');
    },

    /**
     * Instantiates & applies selectize on the new conversation modal
     */
    initSelectizeUserList: function(){
        if(typeof Selectize !== 'undefined') {
            $('#select-repo').selectize({
                valueField: 'id',
                labelField: [],
                searchField: 'label',
                preload: true,
                options: [],
                create: false,
                render: {
                    option: function (item, escape) {
                        return '<div>' +
                            '<img class="searchAvatar mx-2 my-1" src="' + escape(item.avatar) + '" alt="">' +
                            '<span class="name">' + escape(item.name) + '</span>' +
                            '</div>';
                    },
                    item: function (item, escape) {
                        return '<div>' +
                            '<img class="searchAvatar mx-2" src="' + escape(item.avatar) + '" alt="">' +
                            '<span class="name">' + escape(item.name) + '</span>' +
                            '</div>';
                    }
                },
                load: function (query, callback) {
                    // if (!query.length) return callback();
                    $.ajax({
                        url:  app.baseUrl + '/my/messenger/getUserSearch',
                        type: 'POST',
                        data: {q: encodeURIComponent(query)},
                        dataType: 'json',
                        error: function () {
                            callback();
                        },
                        success: function (res) {
                            callback(Object.values(res));
                        }
                    });
                }
            });
        }
    },

    /**
     * Shows up new conversation modal in UI
     */
    showNewMessageDialog: function () {
        $('#messageModal').modal('show');
    }

};

/**
 * Messenger contact component
 * @param contact
 * @returns {string}
 */
function contactElement(contact){
    const avatar = contact.receiverID === user.user_id ? contact.senderAvatar : contact.receiverAvatar;
    const name = contact.receiverID === user.user_id ? contact.senderName : contact.receiverName;
    return `
      <div class="col-12 d-flex pt-2 pb-2 contact-box contact-${contact.contactID}" onclick="messenger.fetchConversation(${contact.contactID})">
        <img src="${ avatar }" class="contact-avatar rounded-circle"/>
        <div class="m-0 ml-md-3 d-none d-lg-flex d-md-flex d-xl-flex justify-content-center flex-column text-truncate">
            <div class="m-0 text-truncate overflow-hidden contact-name ${contact.lastMessageSenderID !== user.user_id && contact.isSeen === 0 ? 'font-weight-bold' : ''}">${name}</div>
            <small class="message-excerpt-holder d-flex text-truncate">
                <span class="text-muted mr-1 ${contact.lastMessageSenderID !== user.user_id ? 'd-none' : ''}"> You: </span>
                <div class="m-0 text-muted contact-message text-truncate ${contact.lastMessageSenderID !== user.user_id && contact.isSeen === 0 ? 'font-weight-bold' : ''}" >${contact.lastMessage}</div>
                <div class="d-flex"> <div class="font-weight-bold ml-1">∙</div>&nbsp;${contact.created_at}</div>
            </small>
        </div>
      </div>
    `;
}

/**
 * Messenger message component
 * @param message
 * @returns {string}
 */
function messageElement(message){
    let isSender = false;
    if(message.sender_id === user.user_id){
        isSender = true;
    }

    let attachmentsHtml = '';
    message.attachments.map(function (file) {
        switch (file.type) {
            case 'avi':
            case 'mp4':
            case 'wmw':
            case 'mpeg':
            case 'm4v':
            case 'moov':
            case 'mov':
                attachmentsHtml += `
                <a href="${file.path}" rel="mswp" title="" class="mr-2 mt-2">
                    <div class="video-wrapper">
                     <video class="video-preview" src="${file.path}" width="150" height="150" controls autoplay muted></video>
                    </div>
                 </a>`;
                break;
            case 'mp3':
            case 'wav':
            case 'ogg':
                attachmentsHtml += `
                <a href="${file.path}" rel="mswp" title="" class="mr-2 mt-2 d-flex align-items-center">
                    <div class="video-wrapper">
                         <audio id="video-preview" src="${file.path}" controls type="audio/mpeg" muted></audio>
                    </div>
                 </a>`;
                break;
            case 'png':
            case 'jpg':
            case 'jpeg':
                attachmentsHtml += `
                    <a href="${file.path}" rel="mswp" title="">
                        <img src="${file.thumbnail}" class="mr-2 mt-2">
                    </a>`;
                break;
            default:
                attachmentsHtml += `<img src="${file.thumbnail}" class="mr-2 mt-2">`;
                break;
        }

    });

    return `
      <div class="col-12 no-gutters pt-1 pb-1 message-box px-0" data-messageid="${message.id}">
        <div class="col-12 d-flex  ${isSender ? 'sender d-flex flex-row-reverse pr-1' : 'pl-0'}">
            <div class="m-0 message-bubble alert alert-primary text-white">${messenger.parseMessage(message.message)}</div>
        </div>
        <div class="col-12 d-flex  ${isSender ? 'sender d-flex flex-row-reverse pr-1' : 'pl-0'}">
            <div class="attachments-holder row no-gutters flex-row-reverse">
                ${attachmentsHtml}
            </div>
        </div>
      </div>
    `;
}
