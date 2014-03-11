// add a startsWith function to strings
if (typeof String.prototype.startsWith != 'function') {
  String.prototype.startsWith = function (str){
    return this.slice(0, str.length) == str;
  };
}

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

// change password form validation
function validateChangePassword()
{
	var pass1 = $('#ch_pwd');
	var pass2 = $('#conf_ch_pwd');
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

// javascript Date objects don't allow you to easily format the string for the date
// so do that here
//function dateToString(d) {
//	var month = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'][d.getMonth()];
//	return month + ' ' + d.getDate() + ' ' + d.getFullYear() + ' ' + d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();
//}

// encode string for HTML
/*Author: R Reid
 * source: http://www.strictly-software.com/htmlencode
 */
function encodeHTML(str)
{
	if (typeof str == 'string' || str instanceof String)
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
	else
	{ // if the input is not valid still return a valid output (empty string)
		return "";
	}

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

var MAX_WIDTH = 600; // for image uploaded to server
var MAX_WIDTH_UI = 200; // for preview of image displayed in the user interface

// read image data from file, then use canvas element to set max size then upload and save on server
var imageUploadHandler = function(event){
	var file = $(this)[0].files[0]; // get file from file selector input that triggered this
	var uploadedDiv = $(this).siblings('div.uploadedData'); // div that displays the uploaded image
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
			//alert(file.name);
			jQuery.post('save_image.php', { 'filename': file.name, 'ext': extension, 'data': canvas.toDataURL(uploadType) }, function(data_returned) {
				// when it's uploaded to the server and saved then set the image preview to source from there.
				uploadedDiv.find('input').val(data_returned); // this is so the uploaded image src can be saved in the cookies
				uploadedDiv.find('img').attr('src', 'users/' + userName + '/images/' + encodeURIComponent(data_returned));
				saveNewsletter();
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
controls += '<button class="up">Move it up</button>';
controls += '<button class="down">Move it down</button>';
controls += '<button class="delete">Remove this</button>';
controls += '</div>';

var addParaButton = '<button class="addPara" title="Add some text">Add text</button>';
var addImageButton = '<button class="addImage" title="Add an image">Add image</button>';
var articleField = "<fieldset class=\"article moveable ui-corner-all\"><legend>Article</legend>\n";
articleField += '<div><label>Title</label> <input type="text" class="articleTitle input-issue save" /></div>';
articleField += controls + "\n";
articleField += "<div class=\"article_buttons\">\n";
articleField += addParaButton + "\n";
articleField += addImageButton + "\n";
articleField += "</div>\n</fieldset>\n";

var paraField = '<div class="moveable">' + controls + '<textarea class="articlePara input-issue save" rows="8" cols="40"></textarea></div>';

var imageField = '<div class="moveable">' + controls + "\n";
imageField += "<input type=\"file\" class=\"imageUpload input-issue\" name=\"fileSelect\" />\n";
imageField += '<div class="uploadedData"><input type="hidden" class="imageLoaded input-issue save" /><img class="preview" /></div>';
imageField += "</div>\n";

var recipientRow = "<tr class=\"newRecipient\">\n";
recipientRow += "<td><input type=\"text\" class=\"name input-send save\" /></td>\n";
recipientRow += "<td><input type=\"text\" class=\"email input-send save\" /></td>\n";
recipientRow += "<td><button class=\"addGreeting\">Add personal greeting</button>\n";
recipientRow += "<textarea  class=\"greeting greetingA hidden\" rows=\"3\" cols=\"30\"></textarea></td>\n";
recipientRow += "<td><textarea  class=\"greeting greetingB hidden\" rows=\"3\" cols=\"30\"></textarea></td>n";
recipientRow += "<td class=\"send_result\"></td></tr>\n";
// the delete button added when the row is not the "new" one at the bottom that triggers new rows to be added
recipientControl = "<td class=\"controls\"><button class=\"delete\">Remove recipient</button></td>\n";


// functions for controls
var deleteElement = function() {
	$(this).parent().parent().remove();
	saveNewsletter();
	setSendCookie();
};
var moveUp = function() {
	$(this).parent().parent().insertBefore($(this).parent().parent().prev());
	saveNewsletter();
};
var moveDown = function() {
	$(this).parent().parent().insertAfter($(this).parent().parent().next());
	saveNewsletter();
};
function bindControls(elements) {
	elements.find('button.up').button({icons:{primary: "ui-icon-arrowthick-1-n"},text:false}).click(moveUp);
	elements.find('button.down').button({icons:{primary: "ui-icon-arrowthick-1-s"},text:false}).click(moveDown);
	elements.find('button.delete').button({icons:{primary: "ui-icon-trash"},text:false}).click(deleteElement);
}

function logoMugshotHandler(container)
{
		//var parent = $(this).parent();
		var uploadControl = "<input type=\"file\" class=\"imageUpload input-issue\" name=\"fileSelect\" />\n";
		uploadControl += '<button class="removeImage">Remove</button>';
		uploadControl += '<div class="uploadedData"><input type="hidden" class="imageLoaded input-issue save" /><img class="preview" /></div>';
		container.find('button').replaceWith(uploadControl);
		container.find('.imageUpload').bind('change', imageUploadHandler);
		container.find('.input-issue.save').change(saveNewsletter);
		container.find('button.removeImage').button({icons:{primary: "ui-icon-trash"},text:false}).click(function(){
			logoMugshotReset($(this).parent());
		});
}

function logoMugshotReset(container)
{
	container.find('input.imageUpload').remove();
	container.find('input.imageLoaded').parent().remove();
	var freshButton = '<button>Upload</button>';
	$(this).replaceWith(freshButton);
	container.find('button').button({icons:{primary: "ui-icon-image"},text:false}).click(function(){
		logoMugshotHandler($(this).parent());
	});
	saveNewsletter();
}

function generatePreviousNewsletter(newsletter_data, files)
{	
	var element = $('<div class="previous_newsletter section left"></div>');
	element.append('<div class="prev_news_title">' + newsletter_data['name'] + ' ' + newsletter_data['issue'] + '</div>');
	var files_fieldset = $('<fieldset><legend>Files online</legend></fieldset>');
	var files_container = $('<div class="files_container"></div>');
	if (files != null)
	{
		if (newsletter_data['issue'].length < 3)
		{
			newsletter_data['issue'] = ("000" + newsletter_data['issue']).slice(-3);
		}
		var filename_start = newsletter_data['name'] + '_' + newsletter_data['issue'];
		//alert (filename_start);
		
		for (var i = 0; i < files.length; ++i)
		{
			if (files[i].startsWith(filename_start))
			{
				files_container.append('<div><div>' + files[i] + '</div><div><a href="users/' + $('#username').text() + '/' + files[i] + '">view</a></div></div>');
			}
		}
		$('.files_container div a').button({
			icons: {
				primary: "ui-icon-search"
			},
			text: true
		});
	}
	if (files_container.children().length == 0)
	{
		files_container.append('<p>No files online</p>');
	}
	files_fieldset.append(files_container);
	element.append(files_fieldset);
	var operations = $('<fieldset><legend>Actions</legend></fieldset>');
	operations.append('<button class="load_newsletter">Load</button>');
	operations.append('<button>Delete all</button>');
	operations.find('.load_newsletter').click(function(){
		restoreById(newsletter_data['id'], true);
	});
	element.append(operations);
	return element;
}

function populatePreviousNewsletters()
{
	// get a list of files in the user's directory
	var files = null;
	jQuery.post('file_ops.php', {task: "list_files"}, function(file_data) {
		if (file_data.indexOf('Fail') >= 0) {alert (file_data);}
		else {files = jQuery.parseJSON(file_data);}
	});
	
	jQuery.post('db_interface_newsletters.php', {task: "get_all_newsletters"}, function(data) {
		
		
		// remove any that might be there
		$('dev.previous_newsletter').remove();
		
		// then fill it
		if (data != '')
		{
			var prev_newsletters = jQuery.parseJSON(data);
			for (var i = 0; i < prev_newsletters.length; ++i)
			{
				$('#previous_newsletters_container').append(generatePreviousNewsletter(prev_newsletters[i], files));
			}
		}
		
	});
}

// harvest the data entered for the newsletter
function collectNewsletterData() {
	// make a JSON object for the form data
	//debugger;
	var logo = '';
	var mugshot = '';
	if ($('#logo .imageLoaded').length > 0) logo = $('#logo .imageLoaded').val();
	if ($('#mugshot .imageLoaded').length > 0) mugshot = $('#mugshot .imageLoaded').val();
	var saveData = {
		"template": $('#template').val(),
		//"title": encodeHTML($('#newsletterTitle').val()),
		//"number": encodeHTML($('#issuenum').val()),
		"date": encodeHTML($('#issuedate').val()),
		"logo": logo,
		"mugshot": mugshot,
		"header": {
			"email": encodeHTML($('#emailHeader > textarea').val()),
			"web": encodeHTML($('#webHeader > textarea').val()),
			"print": encodeHTML($('#printHeader > textarea').val())
		},
		"footer": {
			"email": encodeHTML($('#emailFooter > textarea').val()),
			"web": encodeHTML($('#webFooter > textarea').val()),
			"print": encodeHTML($('#printFooter > textarea').val())
		},
		"privacy": $('input:radio[name=privacy]:checked').attr('id'),
		"privacy_user": encodeHTML($('#privacy_username').val()),
		"privacy_pass": encodeHTML($('#privacy_password').val()),
		"mainArticles": [],
		"sideArticles": []
	};
	
	// now we need to gather the data from the articles
	$('#leftPanel').find('.article').each(function() {
		var article = {"title": encodeHTML($(this).find('.articleTitle').val()), "article": []};
		$(this).find('.save').not('.articleTitle').each(function() {
			var itemType = '';
			var itemValue = '';
			if ($(this).hasClass('articlePara')) { itemType = "para"; }
			//else if ($(this).hasClass('articleList')) { itemType = "list"; }
			else if ($(this).hasClass('imageLoaded')) { itemType = "image"; }
			else { itemType = "undefined" }
			itemValue = encodeHTML($(this).val());
			var item = {"type": itemType, "value": itemValue};
			article.article.push(item);
		});
		saveData.mainArticles.push(article);
	});
	$('#rightPanel').find('.article').each(function() {
		var article = {"title": encodeHTML($(this).find('.articleTitle').val()), "article": []};
		$(this).find('.save').not('.articleTitle').each(function() {
			var itemType = '';
			var itemValue = '';
			if ($(this).hasClass('articlePara')) { itemType = "para"; }
			//else if ($(this).hasClass('articleList')) { itemType = "list"; }
			else if ($(this).hasClass('imageLoaded')) { itemType = "image"; }
			else { itemType = "undefined" }
			itemValue = encodeHTML($(this).val());
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
		   "greetingA": $(this).find('.greetingA').val(),
		   "greetingB": $(this).find('.greetingB').val()
		});
	});
	return data;
}

var newsletterAutoSaveEnabled = true;

// these functions save the form data so the user can come back to it
var saveNewsletter = function() {
	//alert ('save newsletter');
	if (newsletterAutoSaveEnabled)
	{
		var saveData = collectNewsletterData();
		// save it in the database using the php code that acts as a db interface
		jQuery.post('db_interface_newsletters.php', {task: "autosave", newsletter_id: $('#newsletterID').val(), content: saveData}, function(data) {
			if (data.indexOf('Fail') >= 0) {alert (data);}
			else {
				//alert (data);
				// data should be a date string in UTC
				// convert it to local time and display
				var lastSaveDate = new Date(data);
				$('.last_save_date').text(lastSaveDate.toLocaleString());
				$('.newsletter_redo').button('disable');
				$('.newsletter_undo').button('enable');
			}
		});
	}
};

var setSendCookie = function() {
	var saveData = collectSendData();
	jQuery.cookies.set('send', saveData);
}

var newsletterUndo = function()
{
	jQuery.post('db_interface_newsletters.php', {task: "undo", newsletter_id: $('#newsletterID').val()}, function(data) {
		if (data === 'no data')
		{
			$('.newsletter_undo').button('disable');
		}
		else
		{
			//alert(data);
			restoreById($('#newsletterID').val(), false);
			$('.newsletter_redo').button('enable');
		}
	});
}

var newsletterRedo = function()
{
	jQuery.post('db_interface_newsletters.php', {task: "redo", newsletter_id: $('#newsletterID').val()}, function(data) {
		if (data === 'no data')
		{
			$('.newsletter_redo').button('disable');
		}
		else
		{
			restoreById($('#newsletterID').val(), false);
			$('.newsletter_undo').button('enable');
		}
	});
}

var changeNewsletter = function()
{
	var content = collectNewsletterData();
	//alert ('current content ' + JSON.stringify(content));
	jQuery.post('db_interface_newsletters.php', {task: "change_newsletter", newsletter_title: $('#newsletterTitle').val(), newsletter_issue: $('#issuenum').val(), content: content}, function(data){
		//alert('next id ' + data);
		restoreById(data, true);
	});
}

// when content is added dynamically we have to remember to also bind event handlers, etc
function bindArticleButtons(article) {
	article.find('.addPara').click(function(){
		var field = $(paraField);
		bindControls(field);
		field.find('.input-issue.save').change(saveNewsletter);
		field.insertBefore($(this).parent());
	});
	article.find('.addImage').click(function(){
		var field = $(imageField);
		bindControls(field);
		field.find('.imageUpload').bind('change', imageUploadHandler);
		field.find('.input-issue.save').change(saveNewsletter);
		field.insertBefore($(this).parent());
	});
	article.find('button.addImage').button({icons:{primary: "ui-icon-image"},text:false});
	article.find('button.addPara').button({icons:{primary: "ui-icon-document"},text:false});
}

// build a filled in set of form articles from an array
// insert them all before the following element
function buildArticles(array, followingElement) {
	// go through each article in the array
	//debugger;
	for (var a = 0; a < array.length; ++a) {
		var art = $(articleField);
		// put the title in first
		art.find('.articleTitle').val(decodeHTML(array[a].title));
		// within the article go through each field
		for (var i = 0; i < array[a].article.length; ++i) {
			if (array[a].article[i].type == "para") {
				var paragraph = $(paraField)
				bindControls(paragraph);
				paragraph.find('textarea').text(decodeHTML(array[a].article[i].value));
				art.find('.article_buttons').before(paragraph);
			} else if (array[a].article[i].type == "image") {
				var image = $(imageField)
				bindControls(image);
				//image.find('input').val(array[a].article[i].value);
				image.find('.imageLoaded').val(array[a].article[i].value);
				image.find('img.preview').attr('src', 'users/' + userName + '/images/' + encodeURIComponent(array[a].article[i].value));
				image.find('.imageUpload').bind('change', imageUploadHandler);
				art.find('.article_buttons').before(image);
			} else {
				alert('Could not restore article item. Unknown type: ' + array[a].article[i].type);
			}
		}
		bindControls(art);
		bindArticleButtons(art);
		art.find('.input-issue.save').change(saveNewsletter);
		followingElement.before(art);
	}
	
}

function populateLoadRevisions()
{
	jQuery.post('db_interface_newsletters.php', {task: "get_all_saves", newsletter_id: $('#newsletterID').val()}, function(data) {
		
		//alert (data);
		// first empty the select box
		$('option.revision').remove();
		
		// then fill it with the revisions
		if (data != '')
		{
			var instances = jQuery.parseJSON(data);
			for (var i = 0; i < instances.length; ++i)
			{
				// timestamp comes through as a UTC datetime so convert it to local time by making a date object
				var datetime = new Date(instances[i]['timestamp']);
				$('.load_revision').append("<option class=\"revision\" value=\"" + instances[i]['id'] + "\">" + datetime.toLocaleString() + "</option>");
			}
		}
		
	});
}

function restoreById(nl_id, saving_old) {
	// load the selected revision
	jQuery.post('db_interface_newsletters.php', {task: "restore", newsletter_id: nl_id}, function(data) {
			debugger;
			if (data.indexOf('Fail') >= 0) {alert (data);}
			else if (data === '') {
				// do nothing if there's no data to get
			}
			else
			{
				//alert ('next content ' + data);
				if (saving_old)
				{
					// before loading a new revision save this one
					var saveData = collectNewsletterData();
					//alert ('current content ' + JSON.stringify(saveData));
					jQuery.post('db_interface_newsletters.php', {task: "autosave", newsletter_id: $('#newsletterID').val(), content: saveData}, function(response) {
						if (response.indexOf('Fail') >= 0) {
							// if the save fails then just alert the error and keep going
							alert (response);
						}
					});
				}
				newsletterAutoSaveEnabled = false;
				$('#newsletterID').val(nl_id);
				$('.input-issue').clearForm();
				$('.article').remove();
				logoMugshotReset($('#logo'));
				logoMugshotReset($('#mugshot'));
				restore(data);
				newsletterAutoSaveEnabled = true;
			}
	});
}

var addRecipientHandler = function(){
		$("tr.newRecipient > td > input").unbind('change.addRecipient');
		var addedRow = $(recipientRow);
		addedRow.find('textarea.greeting').hide();
		addedRow.find('button.addGreeting').click(function(){
			$(this).parent().parent().find('textarea.greeting').show();
			$(this).hide();
		});
		addedRow.find('button').button();
		$("tr.newRecipient").after(addedRow);
		$("tr.newRecipient:first").append(recipientControl);
		bindControls($("tr.newRecipient"));
		$("tr.newRecipient:first").addClass('recipient').removeClass('newRecipient').find('.input-send.save').change(setSendCookie);
		$("tr.newRecipient > td > input").bind('change.addRecipient', addRecipientHandler);	
		setSendCookie();
};

// restore form content from JSON 
function restore(jsonData, isString) {
	//debugger;
	if(typeof(isString)==='undefined') isString = true;
	if (isString)
	{
		//alert ("restoring: " + jsonData);
		jsonData = jQuery.parseJSON(jsonData);
	}
	$('#newsletterTitle').val(decodeHTML(jsonData.title));
	$('#issuenum').val(decodeHTML(jsonData.number));
	$('#issuedate').val(decodeHTML(jsonData.date));
	if (jsonData.logo.length > 0)
	{
		logoMugshotHandler($('#logo'));
		$('#logo > .uploadedData').find('input').val(jsonData.logo);
		$('#logo > .uploadedData').find('img').attr('src', 'users/' + userName + '/images/' + encodeURIComponent(jsonData.logo));
	}
	if (jsonData.mugshot.length > 0)
	{
		logoMugshotHandler($('#mugshot'));
		$('#mugshot > .uploadedData').find('input').val(jsonData.mugshot);
		$('#mugshot > .uploadedData').find('img').attr('src', 'users/' + userName + '/images/' + encodeURIComponent(jsonData.mugshot));
	}
	$('#emailHeader > textarea').val(decodeHTML(jsonData.header.email));
	$('#webHeader > textarea').val(decodeHTML(jsonData.header.web));
	$('#printHeader > textarea').val(decodeHTML(jsonData.header.print));
	$('#emailFooter > textarea').val(decodeHTML(jsonData.footer.email));
	$('#webFooter > textarea').val(decodeHTML(jsonData.footer.web));
	$('#printFooter > textarea').val(decodeHTML(jsonData.footer.print));
	$('#' + jsonData.privacy).attr('checked', true).button("refresh");
	$('#privacy_username').val(decodeHTML(jsonData.privacy_user));
	$('#privacy_password').val(decodeHTML(jsonData.privacy_pass));
	if (jsonData.privacy === 'privacy_protected') $('#privacy_credentials').show();
	if (typeof jsonData.mainArticles !== 'undefined') buildArticles(jsonData.mainArticles, $('#leftPanel .addArticle'));
	if (typeof jsonData.sideArticles !== 'undefined') buildArticles(jsonData.sideArticles, $('#rightPanel .addArticle'));
	
	// populate the load revision select box with previously saved newsletters
	populateLoadRevisions();
}

function restoreSend(jsonData) {
	
	$('#from').val(jsonData.from);
	$('#subject').val(jsonData.subject);
	$('#greeting').val(jsonData.greeting);
	$('#smtpHost').val(jsonData.smtp_host);
	$('#smtpPort').val(jsonData.smtp_port);
	$('#smtpUser').val(jsonData.smtp_user);
	$('#smtpPass').val(jsonData.smtp_pass);
	
	// if there are already recipients in the table (from an import) insert these new ones before those
	// otherwise just put them before the newRecipient field
	var following;
	if ($('#all_recipients .recipient').length)
	{
		following = $('#all_recipients .recipient').first();
	}
	else
	{
		following = $('.newRecipient');
	}
	
	for (var a = 0; a < jsonData.recipients.length; ++a)
	{
		var row = $(recipientRow);
		row.append(recipientControl);
		row.find('.name').val(jsonData.recipients[a].name);
		row.find('.email').val(jsonData.recipients[a].email);
		if (jsonData.recipients[a].greetingA || jsonData.recipients[a].greetingB)
		{
			row.find('.addGreeting').hide();
			row.find('.greetingA').val(jsonData.recipients[a].greetingA);
			row.find('.greetingB').val(jsonData.recipients[a].greetingB);
		} else {
			row.find('.greetingA').hide();
			row.find('.greetingB').hide();
		}
		row.addClass('recipient').removeClass('newRecipient').find('.input-send.save').change(setSendCookie);
		following.before(row);
	}
	
	$( "button" ).button();
	
	$( "button.delete" ).button({
		icons: {
			primary: "ui-icon-trash"
		},
		text: false
	});
}

//TODO: provide feedback to users about when they are entering a valid or invalid username or password

function validPrivacyUsername(username) {
	if (username.length == 0) return false;
	// username cannot start with a hash or it will be a comment in the .htpasswd file
	if (username.charAt(0) == '#') return false;
	// username cannot contain a colon as this has special meaning in the .htpasswd file
	if (username.indexOf(':') >= 0) return false;
	return true;
}

function validPrivacyPassword(password) {
	if (password.length == 0) return false;
	return true;
}

var htaccessSetUnset = function() {
	var username = jQuery.trim($('#privacy_username').val());
	var password = jQuery.trim($('#privacy_password').val());
	if (validPrivacyUsername(username) && validPrivacyPassword(password))
	{
		$('#privacy_msg').load('htaccess.php', {'username': username, 'password':password, 'action':'set'}, function(){
			$('#privacy_msg').show().fadeOut(3000);
		});
	}
	else
	{
		$('#privacy_msg').load('htaccess.php', {'action':'unset'}, function(){
			$('#privacy_msg').show().fadeOut(3000);
		});
	}
};

var sendScript = "send_newsletter.php";
var sendEmail = function(recipient) {
	
	var greetingA;
	if (recipient.find('.greetingA').val() == '') greetingA = $('#generic_a').val();
	else greetingA = recipient.find('.greetingA').val();
	
	var greetingB;
	if (recipient.find('.greetingB').val() == '') greetingB = $('#generic_b').val();
	else greetingB = recipient.find('.greetingB').val();
	
	//TODO: if online content is protected add username and password to email_file
	
	var data = {
			to_address: recipient.find('.email').val(),
			from_address: $('#from').val(),
			name: encodeURIComponent(recipient.find('.name').val()),
			greetingA: encodeURIComponent(greetingA),
			greetingB: encodeURIComponent(greetingB),
			email_file: $('#newsletter_file_name').val(),
			//email_content: file_get_contents($('#newsletter_file_name').val()),
			subject_line: encodeURIComponent($('#subject').val()),
			smtp_host: $('#smtpHost').val(),
			smtp_port: $('#smtpPort').val(),
			smtp_user: $('#smtpUser').val(),
			smtp_pass: $('#smtpPass').val()
	};
	
	jQuery.post(sendScript, data).done(function(returnData){
		$('#sentEmails').append(returnData);
		if (returnData.indexOf('Fail' >= 0)) {recipient.addClass('send_fail');}
		else {recipient.addClass('send_succeed');}
		//alert(returnData);
	}).fail(function(jqXHR, textStatus){
		//alert('Sending error: ' . textStatus);
		recipient.addClass('send_fail');
	});
		  
};


$(document).ready(function() {

	//debugger; 
	
	// hide everything that should be hidden
	$('.hidden').hide();
	
	// apply jQueryUI elements
	$( ".tabs" ).tabs();
	$( "#accordion" ).accordion({ active: 1, heightStyle: "content" });
	$( "button" ).button();
	$( ".button" ).button();
	$( "#logo > button, #mugshot > button" ).button({
		icons: {
			primary: "ui-icon-image"
		},
		text: false
	});
	$( "button.newsletter_undo" ).button({
		icons: {
			primary: "ui-icon-arrowreturnthick-1-w"
		},
		text: false
	});
	$( "button.newsletter_redo" ).button({
		icons: {
			primary: "ui-icon-arrowreturnthick-1-e"
		},
		text: false
	});
	$( "#privacy_radioset" ).buttonset();
	
	// put a show toggle button on password fields that want one
	// this code swaps out the entire input field
	// adapted from code by Aaron Saray
	$('#show_privacy_password').click(function() {
		var inputField = $('#privacy_password');
		var change = $(this).is(":checked") ? "text" : "password";
		var rep = $("<input type='" + change + "' />");
		rep.attr("id", inputField.attr("id"));
		rep.attr("name", inputField.attr("name"));
		rep.attr('class', inputField.attr('class'));
		rep.val(inputField.val());
		rep.change(htaccessSetUnset);
		rep.insertBefore(inputField);
		inputField.remove();
        inputField = rep;
    });
	
	// bind buttons that reveal things
	$('.reveal_trigger').click(function(){
		$(this).parent().find('.hidden').show();
	});
	
	$('#privacy_protected').click(function(){
		$('#privacy_credentials').show();
	});
	
	$('#privacy_public').click(function(){
		$('#privacy_credentials').hide();
		$('#privacy_msg').load('htaccess.php', {'action':'unset'});
	});
	
	// bind button to bring up file upload controls for logo and mugshot
	$('#logo > button, #mugshot > button').click(function(){
		logoMugshotHandler($(this).parent());
	});
	
	// bind rego form validation
	$('#conf_pwd').blur(validatePassword);  
	$('#conf_pwd').keyup(validatePassword); 
	$('#rego_form').submit(function(){  
	    if(validatePassword()) return true;
	    else return false;  
	}); 
	
	// bind change password form validation
	$('#ch_pwd').blur(validateChangePassword);  
	$('#conf_ch_pwd').keyup(validateChangePassword); 
	$('#chpwd_form').submit(function(){  
	    if(validateChangePassword()) return true;
	    else return false;  
	}); 
	
	// get the newsletter id
	jQuery.post('db_interface_newsletters.php', {task: "get_newsletter_id"}, function(data) {
		//alert ("first newsletter id: " + data);
		$('#newsletterID').val(data);
	});
	
	// get existing content from db
	jQuery.post('db_interface_newsletters.php', {task: "restore", newsletter_id: $('#newsletterID').val()}, function(data) {
		if (data.indexOf('Fail') >= 0) {alert (data);}
		else if (data === '') {
			alert ("no data to restore");
			// do nothing if there's no data to get
		}
		else 
		{
			//alert (data);
			restore(data);
		}
	});
	
	populatePreviousNewsletters();
	
	try {
		var sendData = jQuery.cookies.get('send');
		if (sendData) restoreSend(sendData);
	} catch(e) {
		alert('Cannot restore send data. ' + e.message)
	}
	
	// in case user has just imported recipients from a file
	setSendCookie();
	
	// bind changes to newsletter key fields to change the newsletter
	$('.input-issue.key').change(changeNewsletter);
	
	// bind changes to the newsletter to get saved
	$('.input-issue.save').not('.key').change(saveNewsletter);
	$('.input-send.save').change(setSendCookie);
	
	// bind undo and redo buttons
	$('.newsletter_undo').click(newsletterUndo);
	$('.newsletter_redo').click(newsletterRedo);
	
	// bind the new article buttons
	$('.addArticle').click(function() {
		var newArticle = $(articleField);
		bindControls(newArticle);
		bindArticleButtons(newArticle);
		newArticle.insertBefore($(this));
	});
	
	// bind the "save issue" buttons to allow the user to save this revision for later retrieval
	$('.saveIssue').click(function() {
		var saveData = collectNewsletterData();
		jQuery.post('db_interface_newsletters.php', {task: "save", newsletter_id: $('#newsletterID').val(), content: saveData}, function(data) {
			if (data.indexOf('Fail') >= 0) {
				alert (data);
			}
			else
			{
				// data should be a date string in UTC
				// convert it to local time and display
				var lastSaveDate = new Date(data);
				$('.last_save_date').text(lastSaveDate.toLocaleString());
				
			}
			// populate the load revision select box so it includes the new save
			populateLoadRevisions();
		});
	});
	
	$('select.load_revision').change(function(){
		restoreById($(this).val(), true);
	});
	
	// bind the clear form buttons
	$('.clear').click(function() {
		$('.input-issue').clearForm();
		$('.article').remove();
	});
	
	// bind the privacy fields to call the script that creates or deletes the .htaccess files
	$('#privacy_radioset input').change(function(){
		if($('#privacy_radioset input:checked')[0].id === 'privacy_public')
		{
			$('#privacy_msg').load('htaccess.php', {'action':'unset'}, function(){
				$('#privacy_msg').show().fadeOut(3000);
			});
		}
		if($('#privacy_radioset input:checked')[0].id === 'privacy_protected')
		{
			htaccessSetUnset();
		}
	});
	$('#privacy_username, #privacy_password').change(htaccessSetUnset);
	
	// once the data is entered into the form the user can click to generate the html newsletter
	$('#generate').click(function(){
		// send the content to the php code that generates the newsletter
		var data = {
			newsletter: JSON.stringify(collectNewsletterData())
		};
		// the generate_newsletter php returns some html links to the generated file that get loaded into our user interface
		$('#generateResults').html('Generating ...').load('generate_newsletter.php', data, function(response, status, xhr){
			if (status == "error") {
				$('#generateResults').html("Could not generate: " + xhr.status + " " + xhr.statusText);
			} else {
				// once it's done get ready to send the newsletter
				$('#newsletter_file_name').val($('#email_file').attr('href'));
				//$('#send').removeAttr('disabled');
				//$('#send').removeAttr('aria-disabled');
			}
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
			$(this).parent().parent().find('textarea.greeting').show();
	});
	
	$('#send').click(function(){
		//TODO: add mode to check all is ready before sending
		//$('#sendMessage').html('Sending...');
		var recipients = $('.recipient');
		// remove all send_fail and send_succeed classes that might be there from a previous send
		recipients.removeClass('send_fail');
		recipients.removeClass('send_succeed');
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
				//$('#sendMessage').html('Sent');
			}
		}, 1000);
		
	});
	
	$(window).bind('beforeunload', function(){
		saveNewsletter();
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
