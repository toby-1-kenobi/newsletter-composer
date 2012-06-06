// user rego form validation
function validatePassword()
{
	var pass1 = $('#new_pwd');
	var pass2 = $('#conf_pwd');
	if( pass1.val() != pass2.val() ){  
        pass2.addClass("error");  
        return false;  
    } else{  
        pass2.removeClass("error");  
        return true;  
    }  
}

// simply pad a number with leading zeros
function pad(number, length)
{
    var str = '' + number;
    while (str.length < length) {
        str = '0' + str;
    }
    return str;
}

// encode string for HTML
/*Author: R Reid
 * source: http://www.strictly-software.com/htmlencode
 */
function encodeHTML(str)
{
	var encoded = "";
	for (var i = 0; i < str.length; i++)
	{
		var c = str.charAt(i);
		if (c < ' ' || c > '~' || c == '"')
		{
			c = "&#" + c.charCodeAt() + ";";
		}
		encoded += c;
	}
	return encoded;

}

// decode string encoded by encodeHTML
/*Author: R Reid
 * source: http://www.strictly-software.com/htmlencode
 */
function decodeHTML(str)
{
		var c,m,decoded = str;
		
		// look for numerical entities &#34;
		arr=decoded.match(/&#[0-9]{1,5};/g);
		
		// if no matches found in string then skip
		if(arr!=null){
			for(var x=0;x<arr.length;x++){
				m = arr[x];
				c = m.substring(2,m.length-1); //get numeric part which is reference to unicode character
				// if its a valid number we can decode
				if(c >= -32768 && c <= 65535){
					// decode every single match within string
					decoded = decoded.replace(m, String.fromCharCode(c));
				}else{
					decoded = decoded.replace(m, ""); //invalid so replace with nothing
				}
			}			
		}

		return decoded;
}

// clear form elements handling each type properly
jQuery.fn.clearForm = function() {
  return this.each(function() {
    var type = this.type, tag = this.tagName.toLowerCase();
    if (tag == 'form')
      return $(':input',this).clearForm();
    if (type == 'text' || type == 'password' || tag == 'textarea')
      this.value = '';
    else if (type == 'checkbox' || type == 'radio')
      this.checked = false;
    else if (tag == 'select')
      this.selectedIndex = -1;
  });
};

// cookies expire 1 year from now.
var oneYear = new Date();
oneYear.setDate(oneYear.getDate() + 365);
jQuery.cookies.setOptions({expiresAt: oneYear});

var MAX_WIDTH = 600; // for image uploaded to server
var MAX_WIDTH_UI = 200; // for preview of image displayed in the user interface

// settings for TinyMCE editors
var TMCEsettings = {
			// Location of TinyMCE script
			script_url : 'js/tiny_mce/tiny_mce.js',

			// General options
			theme : "advanced",
		   onchange_callback : setNewsletterCookie,

			// Theme options
			theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,link,unlink,",
			theme_advanced_buttons2 : "",
			theme_advanced_buttons3 : "",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_resizing : true,

			// Example content CSS (should be your site CSS)
			content_css : "css/newsletterComposer.css"
		};

// read image data from file, then use canvas element to set max size then upload and save on server
var imageUploadHandler = function(event){
	var file = $(this)[0].files[0]; // get file from file selector input that triggered this
	var uploadedDiv = $(this).next(); // div that displays the uploaded image
	var canvas = document.createElement("canvas"); // canvas to change image size
	var context2d = canvas.getContext("2d");
	if (!file.type.match(/image.*/)) {
		alert('that file is not an image.');
	} else {
		var img = document.createElement("img"); // need an img element to read file into
		var reader = new FileReader(); // HTML 5 goodness
		img.onload = function(e) { // when the file has finished being read into the img element
			// size the canvas to receive the image
			if (img.width > MAX_WIDTH) {
				canvas.height = img.height * MAX_WIDTH / img.width; // keep aspect ratio of image
				canvas.width = MAX_WIDTH;
			} else {
				canvas.height = img.height;
				canvas.width = img.width;
			}
			// while we're at it resize the preview image size
			var preview = uploadedDiv.children('img');
			if (img.width > MAX_WIDTH_UI) {
				preview.css('height', img.height * MAX_WIDTH_UI / img.width); // keep aspect ratio of image
				preview.css('width', MAX_WIDTH_UI);
			} else {
				preview.css('height', img.height);
				preview.css('width', img.width);
			}
			// draw image to the canvas
			context2d.drawImage(img, 0, 0, canvas.width, canvas.height);
			// if the file is jpeg then upload it as one
			// otherwise upload it as a png
			var uploadType = file.type;
			var extension = 'jpg';
			if (uploadType != 'image/jpeg') {
				uploadType = 'image/png';
				extension = 'png'
			}
			// post the image data to php script using ajax
			jQuery.post('save_image.php', { 'filename': file.fileName, 'ext': extension, 'data': canvas.toDataURL(uploadType) }, function(data_returned) {
				// when it's uploaded to the server and saved then set the image preview to source from there.
				uploadedDiv.find('input').val(data_returned); // this is so the uploaded image src can be saved in the cookies
				uploadedDiv.find('img').attr('src', 'users/' + userName + '/images/' + encodeURIComponent(data_returned));
				setNewsletterCookie();
			});
		}
		reader.onload = function(e) {
			// when the reader has read the file get it into the img element
			img.src = e.target.result;
		}
		// use the reader to read the file
		reader.readAsDataURL(file);
	}
};

// a whole bunch of html is inserted dynamically. It's defined here.
var controls = '<div class="controls">';
controls += '<img class="up" src="images/move_up.png" />';
controls += '<img class="down" src="images/move_down.png" />';
controls += '<img class="delete" src="images/delete.png" />';
controls += '</div>';

var addTitleButton = '<button class="addTitle">Add title</button>';
var addParaButton = '<button class="addPara">Add paragraph</button>';
var addListItemButton = '<button class="addLI">Add list item</button>';
var addImageButton = '<button class="addImage">Add image</button>';
var articleField = "<fieldset class=\"article moveable\"><legend>Article</legend>\n";
articleField += controls + "\n";
articleField += "<div class=\"article_buttons\">\n";
articleField += addTitleButton + "\n";
articleField += addParaButton + "\n";
articleField += '<br/>';
articleField += addListItemButton + "\n";
articleField += addImageButton + "\n";
articleField += "</div>\n</fieldset>\n";

var titleField = '<div class="moveable">' + controls + '<label>Title</label> <input type="text" class="articleTitle input-issue save" /></div>';

var paraField = '<div class="moveable">' + controls + '<textarea class="articlePara input-issue save" rows="8" cols="40"></textarea></div>';

var lIField = '<div class="moveable">' + controls + '<img src="images/li.png" alt="bullet" /><textarea class="articleList input-issue save" rows="8" cols="40"></textarea></div>';

var imageField = '<div class="moveable">' + controls + "\n";
imageField += "<input type=\"file\" class=\"imageUpload input-issue\" name=\"fileSelect\" />\n";
imageField += '<div><input type="hidden" class="imageLoaded input-issue save" /><img class="preview" /></div>';
imageField += "</div>\n";

var recipientRow = "<tr class=\"newRecipient\">\n";
recipientRow += "<td><input type=\"text\" class=\"name input-send save\" /></td>\n";
recipientRow += "<td><input type=\"text\" class=\"email input-send save\" /></td>\n";
recipientRow += "<td><button class=\"addGreeting\">Add personal greeting</button>\n";
recipientRow += "<input type=\"text\" class=\"greeting input-send save\" /></td></tr>\n";
// the delete button added when the row is not the "new" one at the bottom that triggers new rows to be added
recipientControl = "<td class=\"controls\"><img class=\"delete\" src=\"images/delete.png\" /></td>\n";


// functions for controls
var deleteElement = function() {
	$(this).parent().parent().remove();
	setNewsletterCookie();
};
var moveUp = function() {
	$(this).parent().parent().insertBefore($(this).parent().parent().prev());
	setNewsletterCookie();
};
var moveDown = function() {
	$(this).parent().parent().insertAfter($(this).parent().parent().next());
	setNewsletterCookie();
};
function bindControls(elements) {
	elements.find('.up').click(moveUp);
	elements.find('.down').click(moveDown);
	elements.find('.delete').click(deleteElement);
}

// harvest the personal data entered
function collectPersonalData() {
	var data = {
		"addressLine1": encodeHTML($('#address_line_1').val()),
		"addressLine2": encodeHTML($('#address_line_2').val()),
		"phone": encodeHTML($('#phone').val()),
		"skype": encodeHTML($('#skype').val()),
		"website": encodeHTML($('#personal_web').val()),
		"org": encodeHTML($('#org_name').val()),
		"websiteOrg": encodeHTML($('#org_web').val())
	};
	return data;
}
	
// harvest the data entered for the newsletter
function collectNewsletterData() {
	// make a JSON object for the form data
	var saveData = {
		"template": $('#template').val(),
		"title": encodeHTML($('#newsletterTitle').val()),
		"subscribe": $('#subscribeURI').val(),
		"unsubscribe": $('#unsubscribeURI').val(),
		"number": encodeHTML($('#issuenum').val()),
		"date": encodeHTML($('#issuedate').val()),
		"mainArticles": [],
		"sideArticles": []
	};
	// now we need to gather the data from the articles
	$('#leftPanel').find('.article').each(function() {
		var article = {"article": []};
		$(this).find('.save').each(function() {
			var itemType = '';
			var itemValue = '';
			if ($(this).hasClass('articlePara')) { itemType = "para"; }
			else if ($(this).hasClass('articleTitle')) { itemType = "title"; }
			else if ($(this).hasClass('articleList')) { itemType = "list"; }
			else if ($(this).hasClass('imageLoaded')) { itemType = "image"; }
			else { itemType = "undefined" }
			// strip off the <p> tags that TinyMCE adds
			if (itemType == "para" || itemType == "list") {
				itemValue = encodeHTML($(this).val().replace('<p>','').replace('</p>',''));
			} else if (itemType == "title") {
				itemValue = encodeHTML($(this).val());
			} else itemValue = $(this).val();
			var item = {"type": itemType, "value": itemValue};
			article.article.push(item);
		});
		saveData.mainArticles.push(article);
	});
	$('#rightPanel').find('.article').each(function() {
		var article = {"article": []};
		$(this).find('.save').each(function() {
			var itemType = '';
			var itemValue = '';
			if ($(this).hasClass('articlePara')) { itemType = "para"; }
			else if ($(this).hasClass('articleTitle')) { itemType = "title"; }
			else if ($(this).hasClass('articleList')) { itemType = "list"; }
			else if ($(this).hasClass('imageLoaded')) { itemType = "image"; }
			else { itemType = "undefined" }
			// strip off the <p> tags that TinyMCE adds
			if (itemType == "para" || itemType == "list") {
				itemValue = encodeHTML($(this).val().replace('<p>','').replace('</p>',''));
			} else if (itemType == "title") {
				itemValue = encodeHTML($(this).val());
			} else itemValue = $(this).val();
			var item = {"type": itemType, "value": itemValue};
			article.article.push(item);
		});
		saveData.sideArticles.push(article);
	});
	return saveData;
}

// harvest the send data entered
function collectSendData() {
	var data = {
		"from": $('#from').val(),
		"subject": $('#subject').val(),
		"greeting": $('#greeting').val(),
		"smtp_host": $('#smtpHost').val(),
		"smtp_port": $('#smtpPort').val(),
		"smtp_user": $('#smtpUser').val(),
		"smtp_pass": $('#smtpPass').val(),
		"recipients": []
	};
	// now gather the recipients data
	$('.recipient').each(function() {
		data.recipients.push({
		   "name": $(this).find('.name').val(),
		   "email": $(this).find('.email').val(),
		   "greeting": $(this).find('.greeting').val()
		});
	});
	return data;
}

// these functions save the form data so the user can come back to it
var setNewsletterCookie = function() {
	var saveData = collectNewsletterData();
	// save it all in one cookie
	jQuery.cookies.set($('#newsletterTitle').val() + '_' + $('#issuenum').val(), saveData);
	// also set a cookie to tell us the name of the cookie containing the most recent saved
	jQuery.cookies.set('latest', $('#newsletterTitle').val() + '_' + $('#issuenum').val());
};
var setPersonalCookie = function() {
	var saveData = collectPersonalData();
	jQuery.cookies.set('personal', saveData);
}
var setSendCookie = function() {
	var saveData = collectSendData();
	jQuery.cookies.set('send', saveData);
}

// when content is added dynamically we have to remember to also bind event handlers, etc
function bindArticleButtons(article) {
	article.find('.addTitle').click(function(){
		var field = $(titleField);
		bindControls(field);
		field.find('.input-issue.save').change(setNewsletterCookie);
		field.insertBefore($(this).parent());
	});
	article.find('.addPara').click(function(){
		var field = $(paraField);
		bindControls(field);
		field.find('.input-issue.save').change(setNewsletterCookie).tinymce(TMCEsettings);
		field.insertBefore($(this).parent());
	});
	article.find('.addLI').click(function(){
		var field = $(lIField);
		bindControls(field);
		field.find('.input-issue.save').change(setNewsletterCookie).tinymce(TMCEsettings);
		field.insertBefore($(this).parent());
	});
	article.find('.addImage').click(function(){
		var field = $(imageField);
		bindControls(field);
		field.find('.imageUpload').bind('change', imageUploadHandler);
		field.find('.input-issue.save').change(setNewsletterCookie);
		field.insertBefore($(this).parent());
	});
}

// build a filled in set of form articles from an array
// insert them all before the following element
function buildArticles(array, followingElement) {
	// go through each article in the array
	for (var a = 0; a < array.length; ++a) {
		var art = $(articleField);
		// within the article go through each field
		for (var i = 0; i < array[a].article.length; ++i) {
			if (array[a].article[i].type == "title") {
				var title = $(titleField)
				bindControls(title);
				title.find('input').val(decodeHTML(array[a].article[i].value));
				art.find('.article_buttons').before(title);
			} else if (array[a].article[i].type == "para") {
				var paragraph = $(paraField)
				bindControls(paragraph);
				paragraph.find('textarea').text(decodeHTML(array[a].article[i].value)).tinymce(TMCEsettings);
				art.find('.article_buttons').before(paragraph);
			} else if (array[a].article[i].type == "list") {
				var listItem = $(lIField)
				bindControls(listItem);
				listItem.find('textarea').text(decodeHTML(array[a].article[i].value)).tinymce(TMCEsettings);
				art.find('.article_buttons').before(listItem);
			} else if (array[a].article[i].type == "image") {
				var image = $(imageField)
				bindControls(image);
				image.find('input').val(array[a].article[i].value);
				image.find('.imageLoaded').val(array[a].article[i].value);
				image.find('img.preview').attr('src', 'users/' + userName + '/images/' + encodeURIComponent(array[a].article[i].value));
		      image.find('.imageUpload').bind('change', imageUploadHandler);
				art.find('.article_buttons').before(image);
			}
		}
		bindControls(art);
		bindArticleButtons(art);
		followingElement.before(art);
	}
	
}

var addRecipientHandler = function(){
		$("tr.newRecipient > td > input").unbind('change.addRecipient');
		var addedRow = $(recipientRow);
		addedRow.find('input.greeting').hide();
		addedRow.find('button.addGreeting').click(function(){
			$(this).parent().parent().find('input.greeting').show();
			$(this).hide();
		});
		$("tr.newRecipient").after(addedRow);
		$("tr.newRecipient:first").append(recipientControl);
		$("tr.newRecipient:first").addClass('recipient').removeClass('newRecipient').find('.input-send.save').change(setSendCookie);
		$("tr.newRecipient > td > input").bind('change.addRecipient', addRecipientHandler);
		setSendCookie();
};

// restore form content from JSON
function restore(jsonData) {
	$('#newsletterTitle').val(decodeHTML(jsonData.title));
	$('#subscribeURI').val(jsonData.subscribe);
	$('#unsubscribeURI').val(jsonData.unsubscribe);
	$('#issuenum').val(decodeHTML(jsonData.number));
	$('#issuedate').val(decodeHTML(jsonData.date));
	buildArticles(jsonData.mainArticles, $('#leftPanel .addArticle'));
	buildArticles(jsonData.sideArticles, $('#rightPanel .addArticle'));
}
function restorePersonal(jsonData) {
	$('#address_line_1').val(decodeHTML(jsonData.addressLine1));
	$('#address_line_2').val(decodeHTML(jsonData.addressLine2));
	$('#phone').val(decodeHTML(jsonData.phone));
	$('#skype').val(decodeHTML(jsonData.skype));
	$('#personal_web').val(decodeHTML(jsonData.website));
	$('#org_name').val(decodeHTML(jsonData.org));
	$('#org_web').val(decodeHTML(jsonData.websiteOrg));
}
function restoreSend(jsonData) {
	$('#from').val(jsonData.from);
	$('#subject').val(jsonData.subject);
	$('#greeting').val(jsonData.greeting);
	$('#smtpHost').val(jsonData.smtp_host);
	$('#smtpPort').val(jsonData.smtp_port);
	$('#smtpUser').val(jsonData.smtp_user);
	$('#smtpPass').val(jsonData.smtp_pass);
	for (var a = 0; a < jsonData.recipients.length; ++a)
	{
		var row = $(recipientRow);
		row.append(recipientControl);
		row.find('.name').val(jsonData.recipients[a].name);
		row.find('.email').val(jsonData.recipients[a].email);
		if (jsonData.recipients[a].greeting)
		{
			row.find('.addGreeting').hide();
		   row.find('.greeting').val(jsonData.recipients[a].greeting);
		} else {
			row.find('.greeting').hide();
		}
		row.addClass('recipient').removeClass('newRecipient').find('.input-send.save').change(setSendCookie);
		$('.newRecipient').before(row);
	}
}

var sendScript = "send_newsletter.php";
var sendEmail = function(recipient) {
	var greeting;
	if (recipient.find('.greeting').val() == '') greeting = $('#greeting').val();
	else greeting = recipient.find('.greeting').val()
	var data = {
			to_address: recipient.find('.email').val(),
			from_address: $('#from').val(),
			name: encodeURIComponent(recipient.find('.name').val()),
			greeting: encodeURIComponent(greeting),
			email_file: $('#newsletter_file_name').val(),
			//email_content: file_get_contents($('#newsletter_file_name').val()),
			subject_line: encodeURIComponent($('#subject').val()),
			smtp_host: $('#smtpHost').val(),
			smtp_port: $('#smtpPort').val(),
			smtp_user: $('#smtpUser').val(),
			smtp_pass: $('#smtpPass').val()
	};
	alert(data);
	
	jQuery.post(sendScript, data, function(returnData){
		$('#sentEmails').append(returnData);
	});
		  
};

$(document).ready(function() {

	//debugger; 
	
	// bind rego form validation
	$('#conf_pwd').blur(validatePassword);  
	$('#conf_pwd').keyup(validatePassword); 
	$('#rego_form').submit(function(){  
	    if(validatePassword()) return true;
	    else return false;  
	}); 
	
	// get content already in form from cookie
	try {
		var latest = jQuery.cookies.get('latest');
		if (latest) restore(jQuery.cookies.get(latest));
	} catch(e) {
		alert('Cannot restore newsletter content. ' + e.message)
	}
	try {
		var personalData = jQuery.cookies.get('personal');
		if (personalData) restorePersonal(personalData);
	} catch(e) {
		alert('Cannot restore personal content. ' + e.message)
	}
	try {
		var sendData = jQuery.cookies.get('send');
		if (sendData) restoreSend(sendData);
	} catch(e) {
		alert('Cannot restore send data. ' + e.message)
	}
	
	// bind changes to the newsletter to get saved in cookies
	$('.input-issue.save').change(setNewsletterCookie);
	$('.input-personal.save').change(setPersonalCookie);
	$('.input-send.save').change(setSendCookie);
	
	// bind the new article buttons
	$('.addArticle').click(function() {
		var newArticle = $(articleField);
		bindControls(newArticle);
		bindArticleButtons(newArticle);
		newArticle.insertBefore($(this));
	});
	
	// bind the "save issue" buttons to allow the user to receive the form data in a file
	$('.saveIssue').click(function() {
		var saveData = collectNewsletterData();
		var saveFileName = $('#newsletterTitle').val() + ' ' +$('#issuenum').val() + '.txt';
		// As far as I can tell you have to send with a form to get the client to receive the file.
		var form = $('<form method="post" action="giveFileToClient.php"></form>');
		form.append($('<input type="hidden" name="type" value="text/plain" />'));
		form.append($('<input type="hidden" name="filename" value="' + saveFileName +'" />'));
		// it doesn't work unless the content is URI encoded.
		form.append($('<input type="hidden" name="content" value="' + encodeURIComponent(JSON.stringify(saveData)) +'" />'));
		form.trigger('submit');
		// TODO: Try to work out some way of letting the user choose where to save the file.
	});
	
	// bind the "load issue" buttons to allow the user to restore form data from a file
	$('.loadIssue').change(function() {
		$('.input-issue').clearForm();
		$('.article').remove();
		var file = $(this)[0].files[0]; // get file from file selector input that triggered this
		// use a fileReader to read the file
		var reader = new FileReader(); // HTML 5 goodness
		reader.onload = function(e) {
			// when the reader has read the file decode it, parse it as JSON and use it to restore the form data
			restore(JSON.parse(decodeURIComponent(e.target.result)));
		}
		reader.readAsText(file);
	});
	
	// fix up the hover over the sneaky file input
	$('.sneaky-file-input input').mouseenter(function() {
		$(this).siblings('button').addClass('hover');
	});
	$('.sneaky-file-input input').mouseleave(function() {
		$(this).siblings('button').removeClass('hover');
	});
	
	// bind the clear form buttons
	$('.clear').click(function() {
		$('.input-issue').clearForm();
		$('.article').remove();
	});
	
	// once the data is entered into the form the user can click to generate the html newsletter
	$('#generate').click(function(){
		
		// send the content to the php code that generates the newsletter
		var data = {
			personal: JSON.stringify(collectPersonalData()),
			newsletter: JSON.stringify(collectNewsletterData())
		};
		// the generate_newsletter php returns some html links to the generated file that get loaded into our user interface
		$('#generateResults').html('Generating ...').load('generate_newsletter.php', data, function(){
			// once it's done get ready to send the newsletter
			$('#newsletter_file_name').val($('#email_file').attr('href'))
			$('#send').removeAttr('disabled');
		});
		
	});
	
	// the empty row in the recipients table ".newRecipient" should trigger when changed a new empty row to appear
	$("tr.newRecipient > td > input").bind('change.addRecipient', addRecipientHandler);
	
	// bind delete buttons for any recipients that came in via Excel import
	bindControls($('#all_recipients'));
	// hide all the empty personal greeting fields until the "add personal greeting" button is clicked for that recipient
	$('input.greeting').each(function(){
		if ($(this).val() == '') $(this).hide();
	});
	$('button.addGreeting').click(function(){
			// hide the "add greeting" button once it is used
			$(this).hide();
			// show the personal greeting field.
			$(this).parent().parent().find('input.greeting').show();
	});
	
	$('#send').click(function(){
		$('#sendMessage').html('Sending...');
		var recipients = $('.recipient');
		var index = 0;
		// wait a second before sending each email out.
		// I think it's only polite not to flood the mail server
		var sendIntervalID = setInterval(function() {
			if (index == recipients.length) clearInterval(sendIntervalID);
			if (index > 0) recipients.eq(index - 1).removeClass('sending');
			recipients.eq(index).addClass('sending');
			sendEmail(recipients.eq(index));
			++index;
			if (index == recipients.length) {
				clearInterval(sendIntervalID);
				recipients.eq(index - 1).removeClass('sending');
				$('#sendMessage').html('Sent');
			}
		}, 1000);
		
	});
	
	$(window).bind('beforeunload', function(){
		setNewsletterCookie();
	});
	
/*	
	$('#loading_splash')
    .hide()  // hide it initially
    .ajaxStart(function() {
        $(this).show();
    })
    .ajaxStop(function() {
        $(this).hide();
    });
*/



});

// when images are loaded we need to make sure they're not too big.
$(window).load(function() {
	$('img.preview').each(function() {
		if (this.width > MAX_WIDTH_UI) {
			this.height = this.height * MAX_WIDTH_UI / this.width;
			this.width = MAX_WIDTH_UI;
		}
	});
});
