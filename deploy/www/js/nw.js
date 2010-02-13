/* Load all the js functionality here, mostly */

// Sections are, in order: SETTINGS | FUNCTIONS | READY

// Url Resources:
// http://www.jslint.com/
// http://yuiblog.com/blog/2007/06/12/module-pattern/
// http://www.javascripttoolbox.com/bestpractices/
// TODO: change generated vars to square bracket notation.

// TODO: Create a dummy con sole dot log functionality to avoid errors on live?


/*  GLOBAL SETTINGS & VARS */

function createNW(){
    var innerNW = {};
    innerNW.firstLoad = 1; 
    // Counter starts at 1 to indicate newly refreshed page, as opposed to ajax loads.
    return innerNW;
}

$ = typeof window.$ == 'object' ? window.$ : jQuery;
NW = typeof window.NW == 'object' ? window.NW : createNW();

// GLOBAL FUNCTIONS

// Not secure, just convenient.
function isLoggedIn(){
	return NW.loggedIn;
}

function setLoggedIn(){
	NW.loggedIn = 1;
}

function clearLoggedIn(){
	NW.loggedIn = 0;
}

// Returns true when debug bit set or localhost path used.
function debug(arg){
	if(NW.debug || !isLive()){
		if(console){console.log(arg);}
		return true;
	} 
	return false;
}

// Update the chat page without refresh.
//function updateChat(){
// store a latest chat id to check against the chat.
// Get the chat data.
// If the latest hasn't changed, just return nothing.
// If the latest has changed, return
// If the latest has changed, cycle through the data
//saving non-matches until you get back to the "latest" match.
// Update the chat's ui.
// update the "latest chat id" var.
//}

// Display an event.
function writeLatestEvent(event){
    debug('Event display requested.');
	$('#recent-events', top.document).html("<div class='latest-event' title='"+event.sender+": "+event.event+"'><div id='latest-event-title'>Latest Event via <a href='player.php?player="+event.send_from+"' target='main'>"+event.sender+"</a>:</div><span class='latest-event-text "+(event.unread? "message-unread" : "")+"'>"+event.event.substr(0, 12)+"...</span></div>");
    // if unread, Add the unread class until next update.
    // Pull a message with a truncated length of 12.
}

// Pull the event from the data store and request it be displayed.
function updateLatestEvent(){
	var feedback = false;
	var event = getEvent();
	if(!event){
	    feedbackSpeedUp(); // Make the interval to try again shorter.
		debug('No event data to use.');
	}else{
	     if(NW.visibleEventId == event.event_id){
	        if(!NW.visibleEventRead){
        		// Makes any unread event marked as read after a second update, even if it wasn't really read.
                $('#recent-events .latest-event-text', top.document).removeClass('message-unread');
                debug('Requested that latest event be marked as read.');
        		NW.visibleEventRead = true;
        	}
    	} else {
    	    debug('Request to write out the latest event.');
    		feedback = true;
    		NW.visibleEventId = event.event_id;
    		NW.visibleEventRead = false;
            writeLatestEvent(event);
    	}
    }
	return feedback;
}


function writeLatestMessage(message){
		// TODO: Transform the appended html into hidden html that gets fleshed out and marked visible by this function.
        // if unread, Add the unread class until next update.
        // Pull a message with a truncated length of 12.
		$('#recent-mail', top.document).html("<div class='latest-message' title='"+message.send_from+": "+message.message+"'><div id='latest-message-title'>Latest Message:</div><a href='player.php?player="+message.send_from+"' target='main'>"+message.sender+"</a>: <span class='latest-message-text "+(message.unread? "message-unread" : "")+"'>"+message.message.substr(0, 12)+"...</span> </div>");
}

// Update the message that gets displayed.
function updateLatestMessage(){
	var updated = false;
	var message = getMessage();
	if(!message){
		updated = true; // Check for info again shortly.
	}else if(NW.visibleMessageId == message.message_id){
        if(!message.unread){ // Only turn a message read if it actually has been viewed on the message page.
			$('#recent-mail .latest-message-text', top.document).removeClass('message-unread');
		}
	} else {
		updated = true;
		NW.visibleMessageId = message.message_id;
		writeLatestMessage(message);
	}
	return updated;
}


// Update the display of the health.
function updateHealthBar(health){
    // Should only update when a change occurs.
    if(health != NW.visibleHealth){
        var mess = health+' health';
        var span = $('#health-status', top.document);
        // Keep in mind the need to use window.parent when calling from an iframe.
        span.text(mess);
        if(health<100){
            span.addClass('injured');
        } else {
            span.removeClass('injured');
        }
        NW.visibleHealth = health;
        return true;
    }
    return false;
}

// Update the displayed health from the javascript-stored current value.
function getAndUpdateHealth(){
    var updated = false;
    NW.playerInfo.health = NW.playerInfo.health ? NW.playerInfo.health : null;
    if(NW.playerInfo.health !== null && NW.visibleHealth != NW.playerInfo.health){
    	updateHealthBar(NW.playerInfo.health);
    	updated = true;
    }
    return updated;
}



// Update display elements that live on the index page.
function updateIndex(){
    var messageUpdated = updateLatestMessage();
    var eventUpdated = updateLatestEvent();
    // update chat
    // health bar.
    var healthUpdated = getAndUpdateHealth();
    var res = (!!(messageUpdated || eventUpdated || healthUpdated));
    debug("Message Updated: "+messageUpdated);
    debug("Event Updated: "+eventUpdated);
    debug("Health Updated: "+healthUpdated);
    return res;
}

function getEvent(){
    return NW.latestEvent? NW.latestEvent : false;
}

function getMessage(){
    return NW.latestMessage? NW.latestMessage : false;
}

function getPlayerInfo(){
    return NW.playerInfo ? NW.playerInfo : false;
}

// Pull in the new info, update display only on changes.
function checkAPI(){
    // NOTE THAT THIS CALLBACK DOES NOT TRIGGER IMMEDIATELY.
    $.getJSON('api.php?type=index&jsoncallback=?', function(data){
    	var updated = false;
        // Update global data stores if an update is needed.
        if(updateDataStore(data.player, 'player_id', 'playerInfo', 'health', 'last_attacked')){
            updated = true;
        }
        if(updateDataStore(data.message, 'message_id', 'latestMessage', 'message_id')){
            updated = true;
        }
        if(updateDataStore(data.event, 'event_id', 'latestEvent', 'event_id')){
            updated = true;
        }
    	debug('Update requested: '+updated);
        if(updated){
        	updateIndex(); // Always redisplay for any poll that has information updates.
	        feedbackSpeedUp(updated);
        }
        // Since this callback isn't immediate, the feedback has to occur whenever the callback finishes.
	}); // End of getJSON function call.
}

// Saves stuff to global data storage.
function updateDataStore(datum, property_name, global_store, comparison_name, comparison_name_2){
    if(datum){
        if(datum[property_name]){
            if(!NW[global_store] || (NW[global_store][comparison_name] != datum[comparison_name] || 
                    comparison_name_2 && NW[global_store][comparison_name_2] != datum[comparison_name_2])){
                // If the data isn't there, or doesn't match, update the store.
                NW[global_store] = datum;
                debug(datum);
                return true;
            }
        }
    }
    return false; // Input didn't contain the data, or the data hasn't changed.
}


// Determines the update interval, 
//increases when feedback == false, rebaselines when feedback == true
function getUpdateInterval(feedback){
	var maxInt = 180;
	var min = 10; // Changes push the interval to this minimum.
	var first = 20;  // The starting update interval.
	if(!NW.updateInterval){
		NW.updateInterval = first; // Starting.
	} else if(feedback){
		NW.updateInterval = min; // Speed up to minimum.
	} else if(NW.updateInterval>=maxInt){
		NW.updateInterval = maxInt; // Don't get any slower than max.
	} else {
		NW.updateInterval++; // Slow down updates slightly.
	}
    return NW.updateInterval;
}

// JS Update Heartbeat
function chainedUpdate(chainCounter){
    var chainCounter = !!chainCounter ? chainCounter : 1;
    // Skip the heartbeat the first time through, and skip it if not logged in.
    if(isLoggedIn() && chainCounter != 1){
        checkAPI(); // Check for new information.
    }
    
    var furtherIntervals = getUpdateInterval(feedback());
    debug("Next Update will be in:"+furtherIntervals);
    debug("chainCounter: "+chainCounter);
    chainCounter++;
    setTimeout(function (){
            chainedUpdate(chainCounter);
        }, furtherIntervals*1000); // Repeat once the interval has passed.
    // If we need to cancel the updating down the line for some reason, store the id that setTimeout returns.
}


function feedbackSpeedUp(){
    NW.feedback = true;
}

// Get the feedback value.
function feedback(){
    var res = (NW.feedback? NW.feedback : false);
    NW.feedback = false; // Start slowing down after getting the value.
    return res;
}


// When clicking frame links, load a section instead of the iframe.
function frameClickHandlers(links, div){
    links.click(function(){
        div.load(this.href, 'section_only=1');
        return false;
    });
}

function toggle_visibility(id) {
    var tog = $("#"+id);
    tog.toggle();
    return false;
}

// Begin the cycle of refreshing the mini chat after the standard delay.
function startRefreshingMinichat(){
    var secs = 20;
    setTimeout(function (){
        startRefreshingMinichat();
    }, secs*1000); 
}

// Load the chat section, or if that's not available & nested iframe, refresh iframe
function refreshMinichat(msg){
    var container = $('#mini-chat-frame-container');
    var data;
    if(msg){
        data = {'message':msg,'command':'postnow', 'section_only':'1'};
    } else {
        data = {'section_only':'1'};
    }
    if(container){
        container.load("mini_chat.php", data);
        return false;
    }
}

function sendChatContents(domform){
    var chatbox = $(domform).find('#message');
    if(chatbox){
        refreshMinichat(chatbox.val()); // Send message to refreshMinichat.
        chatbox.val(''); // Clear the chat message box.
        return false;
    } else{
        iframe = $('#mini-chat-frame-container iframe #mini_chat');
        if(iframe){
            // Return true only if the iframe is actually set.
            return true;
        } else {
            // No iframe or chatbox found, just don't navigate away from the main page.
            return false;
        }
    }
}

// For refreshing quickstats from inside main.
function refreshQuickstats(quickView){
    // Accounts for ajax section.
    var url = 'quickstats.php?command='+quickView;
    if(top.window.NW.firstLoad > 1){
        if(top.window.NW.quickDiv){
            top.window.NW.quickDiv.load(url, 'section_only=1');
        } else {
            // Use parent to indicate the parent global variable.
            parent.quickstats.location=url;
        }
    }
    top.window.NW.firstLoad++;
}

function isIndex(){ // Return true if the index page.
	// Not great because it doesn't allow for pretty urls, down the line.  
	return (window.location.pathname.substr(-9,9) == 'index.php')  || $('body').hasClass('main-body');
}

function isLive(){
	return window.location.host  != 'localhost';
}

function isRoot(){
	return (window.location.pathname == '/');
}

function isSubpage(){
	return !isIndex() && !isRoot() && (window.parent == window);
}

/**
 * Add a class to the body of any solo pages, which other css can then key off of.
**/
function soloPage(){
	if(isSubpage()){
		$('body').addClass('solo-page'); // Added class to solo-page bodies.
	}
}


// Just for fun.
function april1stCheck(){
    if(isIndex()){
        var currentTime = new Date();
        var day = currentTime.getDay();
        var month = currentTime.getMonth();
        var randomnumber=Math.floor(Math.random()*(10+1));
        if(randomnumber == 10 && (debug() || (day == 0 && month == 3))){
            $('body').css({'-webkit-transform':'rotate(20deg)','-moz-transform':'rotate(20deg)'});
        }
    }
}


// Initial load of everything, run at the bottom to allow everything else to be defined beforehand.
$(document).ready(function() {
   
    // INDEX ONLY CHANGES 
    if(isIndex() || isRoot()){ 

		chainedUpdate(); // Start the periodic index update.
       	
       /* Collapse the following parts of the index */
        $("#links-menu").toggle();
        
        /* Expand the chat when it's clicked. */
        $("#expand-chat").click(function () {
            $("#mini-chat-frame-container").removeClass('chat-collapsed').addClass('chat-expanded');/*.height(780);*/
            $(this).hide();  // Hide the clicked section after expansion.
        });
        
        
        var quickstatsLinks = $("a[target='quickstats']");
        quickstatsLinks.css({'font-style':'italic'}); // Italicize 
        var quickDiv =  $('div#quickstats-frame-container');
        //quickDiv.load('quickstats.php');
        // Add the click handlers for loading the quickstats frame.
        frameClickHandlers(quickstatsLinks, quickDiv); // Load new contents into the div when clicked.
        NW.quickDiv = quickDiv;
        
        
    }
    
    $('#index-chat form').submit(function (){return sendChatContents(this)}).css({'font-weight':'bold','color':'maroon'});
    // When chat form is submitted, send the message, load() the chat section and then clear the textbox text.
    
    
    /* THIS CODE RUNS FOR ALL SUBPAGES */
    soloPage(); // Append a link back to main page for any lone subpages not in iframes.
        
    // GOOGLE ANALYTICS
    /* There's a script include that goes with this, but I just put it in the head directly.*/
    try {
    var pageTracker = _gat._getTracker("UA-707264-2");
    pageTracker._trackPageview();
    } catch(err) {}

    april1stCheck();
   
 });
