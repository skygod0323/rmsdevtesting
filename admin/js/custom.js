// =============================================================================
// Common functions
// =============================================================================

function isIE() {
	return (navigator.userAgent.indexOf("MSIE") != -1) && (navigator.userAgent.indexOf("Opera") == -1);
}

function addEL(element, eventType, listener) {
	if (!element) {
		return;
	}
	if (element.addEventListener) {
		element.addEventListener(eventType, listener, false);
	} else if (element.attachEvent) {
		element.attachEvent('on' + eventType, listener);
	} else {
		element['on' + eventType] = listener;
	}
}

function getEventTarget(e) {
	if (!e) {
		e = window.event;
	}
	var target = e.target;
	if (!target) {
		target = e.srcElement;
	}
	return target;
}

function preventDefault(e) {
	if (!e) {
		e = window.event;
	}
	if (e.preventDefault) {
		e.preventDefault();
	} else {
		e.returnValue = false;
	}
}

function stopBubbling(e) {
	if (!e) {
		e = window.event;
	}
	e.cancelBubble = true;
	if (e.stopPropagation) {
		e.stopPropagation();
	}
}

function fireClickEvent(element) {
	if (document['createEvent']) {
		var event = document.createEvent('MouseEvents');
		event.initEvent('click', true, true);
		element.dispatchEvent(event);
	} else if (isIE()) {
		element.click();
	} else if (document['fireEvent']) {
		element['fireEvent']('onclick');
	}
}

function addStyleClass(element, styleClass) {
	if (!hasStyleClass(element, styleClass)) {
		if (element.className.length == 0) {
			element.className = styleClass;
		} else {
			element.className += ' ' + styleClass;
		}
	}
}

function hasStyleClass(element, styleClass) {
	if (!element.className) {
		return false;
	}
	var classes = element.className.split(' ');
	for (var i = 0; i < classes.length; i++) {
		if (classes[i] == styleClass) {
			return true;
		}
	}
	return false;
}

function removeStyleClass(element, styleClass) {
	var classes = element.className.split(' ');
	var newClass = '';
	for (var i = 0; i < classes.length; i++) {
		if (classes[i] != styleClass) {
			newClass += ' ' + classes[i];
		}
	}
	element.className = newClass;
}

// =============================================================================
// Common functions
// =============================================================================

function buildPostDateResetLogic(year, month, day, hour, minute) {
	var button = document.getElementById('post_date_now');
	if (button) {
		addEL(button, 'click', function() {
			var i;
			var cbYear = document.getElementById('post_date_year');
			for (i = 0; i < cbYear.options.length; i++) {
				if (cbYear.options[i].value == year) {
					cbYear.options[i].selected = true;
				}
			}
			var cbMonth = document.getElementById('post_date_month');
			for (i = 0; i < cbMonth.options.length; i++) {
				if (cbMonth.options[i].value == month) {
					cbMonth.options[i].selected = true;
				}
			}
			var cbDay = document.getElementById('post_date_day');
			for (i = 0; i < cbDay.options.length; i++) {
				if (cbDay.options[i].value == day) {
					cbDay.options[i].selected = true;
				}
			}
			var textTime = document.getElementById('post_date_time');
			textTime.value = hour + ':' + minute;
		});
	}
}

function buildServerGroupChangeLogic(warning) {
	var button = document.getElementById('change_storage_group');
	var select = document.getElementById('server_group_id');
	if (button && select) {
		addEL(button, 'click', function() {
			if (confirm(warning)) {
				select.disabled = null;
			}
		});
	}
}

function buildDeleteReasonChangeLogic() {
	var select = document.getElementById('top_delete_reasons');
	var textarea = document.getElementById('delete_reason');
	if (select && textarea) {
		addEL(select, 'change', function() {
			var value = select.options[select.selectedIndex].value;
			if (value) {
				if (typeof tinymce !== 'undefined') {
					var editor = tinymce.get('delete_reason');
					if (editor) {
						editor.setContent(value);
						return;
					}
				}
				textarea.value = value;
			}
		});
	}
}

function imageListPreviewHook(linkId) {
	if (linkId.indexOf('link_') == 0) {
		var imageId = linkId.substring(5);

		var rMain = document.getElementById('main_' + imageId);
		var cbDelete = document.getElementById('delete_' + imageId);
		var iTitle = document.getElementById('title_' + imageId);

		if (rMain) {
			var tempRMain = document.createElement('INPUT');
			tempRMain.type = 'radio';
			tempRMain.id = 'temp_main';
			tempRMain.checked = rMain.checked;

			addEL(tempRMain, 'click', function() {
				rMain.checked = true;
				tempRMain.blur();

				var items = document.getElementsByClassName('de_img_list_item');
				for (var i = 0; i < items.length; i++) {
					removeStyleClass(items[i], 'main');
				}

				var number = rMain.value;
				var item = document.getElementById('item_' + number);
				if (item) {
					addStyleClass(item, 'main');
				}
			});

			var tempLMain = document.createElement('LABEL');
			tempLMain.htmlFor = 'temp_main';
			tempLMain.style.cursor = 'pointer';
			tempLMain.innerHTML = KTLanguagePack['image_list_main'];
		}

		if (cbDelete) {
			var tempCbDelete = document.createElement('INPUT');
			tempCbDelete.type = 'checkbox';
			tempCbDelete.id = 'temp_delete';
			if (cbDelete) {
				tempCbDelete.checked = cbDelete.checked;
			}

			addEL(tempCbDelete, 'click', function() {
				cbDelete.checked = tempCbDelete.checked;
				tempCbDelete.blur();

				var number = cbDelete.value;
				var item = document.getElementById('item_' + number);
				if (item) {
					if (cbDelete.checked) {
						addStyleClass(item, 'deleted');
					} else {
						removeStyleClass(item, 'deleted');
					}
				}

				var labels = cbDelete.parentNode.getElementsByTagName('label');
				if (labels.length > 0) {
					if (cbDelete.checked) {
						addStyleClass(labels[0], 'selected');
					} else {
						removeStyleClass(labels[0], 'selected');
					}
				}
			});

			var tempLDelete = document.createElement('LABEL');
			tempLDelete.htmlFor = 'temp_delete';
			tempLDelete.innerHTML = KTLanguagePack['image_list_delete'];
		}

		if (iTitle) {
			var tempITitle = document.createElement('INPUT');
			tempITitle.type = 'text';
			tempITitle.id = 'temp_title';
			tempITitle.value = iTitle.value;

			addEL(tempITitle, 'keyup', function() {
				iTitle.value = tempITitle.value;
			});
		}

		var element = document.createElement('SPAN');
		if (tempRMain) {
			element.appendChild(tempRMain);
			element.appendChild(tempLMain);
		}
		if (tempCbDelete) {
			element.appendChild(document.createElement('SPACER'));
			element.appendChild(tempCbDelete);
			element.appendChild(tempLDelete);
		}
		if (tempITitle) {
			element.appendChild(document.createElement('SPACER'));
			element.appendChild(tempITitle);
		}
		return element;
	}
	return null;
}

// =============================================================================
// Video screenshots functions
// =============================================================================

var screenGrabbingFields = [];
var screenGrabbingMaxIndex = 0;

function buildScreenshotsFormatLogic(videoId) {
	var sbGroup = document.getElementById('group_id');
	var sbOverviewFormat = document.getElementById('overview_format_id');
	var sbTimelineVideoFormat = document.getElementById('timeline_video_format_id');
	var sbTimelineFormat = document.getElementById('timeline_format_id');
	var sbPosterFormat = document.getElementById('poster_format_id');
	var spanOverviewFormats = document.getElementById('overview_formats');
	var spanTimelineVideoFormats = document.getElementById('timeline_video_formats');
	var spanTimelineFormats = document.getElementById('timeline_formats');
	var spanPosterFormats = document.getElementById('poster_formats');

	addEL(sbGroup, 'change', function() {
		if (sbGroup.options[sbGroup.selectedIndex].value == 1) {
			addStyleClass(spanTimelineFormats, 'hidden');
			addStyleClass(spanTimelineVideoFormats, 'hidden');
			addStyleClass(spanPosterFormats, 'hidden');
			removeStyleClass(spanOverviewFormats, 'hidden');
			switchToOverview();
		} else if (sbGroup.options[sbGroup.selectedIndex].value == 2) {
			addStyleClass(spanOverviewFormats, 'hidden');
			addStyleClass(spanPosterFormats, 'hidden');
			removeStyleClass(spanTimelineVideoFormats, 'hidden');
			switchToTimeline();
		} else if (sbGroup.options[sbGroup.selectedIndex].value == 3) {
			addStyleClass(spanOverviewFormats, 'hidden');
			addStyleClass(spanTimelineFormats, 'hidden');
			addStyleClass(spanTimelineVideoFormats, 'hidden');
			removeStyleClass(spanPosterFormats, 'hidden');
			switchToPosters();
		}
	});

	function switchToOverview() {
		if (sbOverviewFormat.selectedIndex==0) {
			return;
		}
		var groupId = encodeURIComponent(sbGroup.options[sbGroup.selectedIndex].value);
		var formatId = encodeURIComponent(sbOverviewFormat.options[sbOverviewFormat.selectedIndex].value);
		window.location = window.location.pathname + '?item_id=' + videoId + '&group_id=' + groupId + '&overview_format_id=' + formatId;
	}
	addEL(sbOverviewFormat, 'change', switchToOverview);

	function switchToTimeline() {
		if (sbTimelineVideoFormat.selectedIndex == 0) {
			addStyleClass(spanTimelineFormats, 'hidden');
		} else {
			removeStyleClass(spanTimelineFormats, 'hidden');
			if (sbTimelineFormat.selectedIndex != 0) {
				var groupId = encodeURIComponent(sbGroup.options[sbGroup.selectedIndex].value);
				var formatId = encodeURIComponent(sbTimelineFormat.options[sbTimelineFormat.selectedIndex].value);
				var formatVideoId = encodeURIComponent(sbTimelineVideoFormat.options[sbTimelineVideoFormat.selectedIndex].value);
				window.location = window.location.pathname + '?item_id=' + videoId + '&group_id=' + groupId + '&timeline_video_format_id=' + formatVideoId + '&timeline_format_id=' + formatId;
			}
		}
	}
	addEL(sbTimelineVideoFormat, 'change', switchToTimeline);
	addEL(sbTimelineFormat, 'change', switchToTimeline);

	function switchToPosters() {
		if (sbPosterFormat.selectedIndex==0) {
			return;
		}
		var groupId = encodeURIComponent(sbGroup.options[sbGroup.selectedIndex].value);
		var formatId = encodeURIComponent(sbPosterFormat.options[sbPosterFormat.selectedIndex].value);
		window.location = window.location.pathname + '?item_id=' + videoId + '&group_id=' + groupId + '&poster_format_id=' + formatId;
	}
	addEL(sbPosterFormat, 'change', switchToPosters);

	if (sbGroup.options[sbGroup.selectedIndex].value == 1) {
		removeStyleClass(spanOverviewFormats, 'hidden');
	} else if (sbGroup.options[sbGroup.selectedIndex].value == 2) {
		removeStyleClass(spanTimelineVideoFormats, 'hidden');
		removeStyleClass(spanTimelineFormats, 'hidden');
	} else if (sbGroup.options[sbGroup.selectedIndex].value == 3) {
		removeStyleClass(spanPosterFormats, 'hidden');
	}
	if (sbTimelineVideoFormat.selectedIndex == 0) {
		addStyleClass(spanTimelineFormats, 'hidden');
	}
}

function buildScreenshotsDeleteLogic() {
	var screenshotsContainer = document.getElementById('screenshots_container');
	var cbAll = document.getElementById('delete_all');
	var cbDoNotFade = document.getElementById('delete_do_not_fade');
	var sbClickMode = document.getElementById('screenshots_click_mode');
	var sbDisplayMode = document.getElementById('screenshots_display_mode');
	var clickMode = 'viewer';

	if (cbAll) {
		addEL(cbAll, 'click', function() {
			var checkboxes = cbAll.form['delete[]'];
			if (!checkboxes) {
				checkboxes = cbAll.form['screen_delete[]'];
			}
			for (var i = 0; i < checkboxes.length; i++) {
				if (checkboxes[i].checked != cbAll.checked) {
					fireClickEvent(checkboxes[i]);
				}
			}
		});
	}

	function toogleDoNotFade(event) {
		if (cbDoNotFade.checked) {
			removeStyleClass(screenshotsContainer, 'de_img_list_delete_on_selection');
		} else {
			addStyleClass(screenshotsContainer, 'de_img_list_delete_on_selection');
		}
		if (event) {
			(new Image()).src = 'async/async_settings.php?setting=screenshots_select_fade_disabled&value=' + (cbDoNotFade.checked ? 1 : 0);
		}
	}

	if (cbDoNotFade) {
		if (screenshotsContainer) {
			addEL(cbDoNotFade, 'click', toogleDoNotFade);
			toogleDoNotFade();
		}
	}

	function toogleClickMode(event) {
		clickMode = sbClickMode.options[sbClickMode.selectedIndex].value;
		if (screenshotsContainer) {
			if (clickMode == 'viewer') {
				addStyleClass(screenshotsContainer, 'de_img_list_preview');
			} else {
				removeStyleClass(screenshotsContainer, 'de_img_list_preview');
			}
		}
		if (event && clickMode) {
			(new Image()).src = 'async/async_settings.php?setting=screenshots_click_mode&value=' + encodeURIComponent(clickMode);
		}
	}

	if (sbClickMode) {
		addEL(sbClickMode, 'change', toogleClickMode);
		toogleClickMode();
	}

	function toogleDisplayMode(event) {
		var displayMode = sbDisplayMode.options[sbDisplayMode.selectedIndex].value;
		if (screenshotsContainer) {
			var options = screenshotsContainer.getElementsByClassName('de_img_list_options');
			for (var i = 0; i < options.length; i++) {
				if (!hasStyleClass(options[i], 'basic')) {
					if (displayMode == 'basic') {
						addStyleClass(options[i], 'hidden');
					} else {
						removeStyleClass(options[i], 'hidden');
					}
				}
			}
		}
		if (event && displayMode) {
			(new Image()).src = 'async/async_settings.php?setting=screenshots_display_mode&value=' + encodeURIComponent(displayMode);
		}
	}

	if (sbDisplayMode) {
		addEL(sbDisplayMode, 'change', toogleDisplayMode);
		toogleDisplayMode();
	}

	function toggleDeletion() {
		var item = document.getElementById('item_' + this.value);
		if (item) {
			if (this.checked) {
				addStyleClass(item, 'deleted');
			} else {
				removeStyleClass(item, 'deleted');
			}
		}
	}

	function toggleMain() {
		var items = document.getElementsByClassName('de_img_list_item');
		for (var i = 0; i < items.length; i++) {
			removeStyleClass(items[i], 'main');
		}
		var item = document.getElementById('item_' + this.value);
		if (item) {
			addStyleClass(item, 'main');
		}
	}

	function createListener(checkbox, force) {
		return function(e) {
			if (force || clickMode == 'select') {
				fireClickEvent(checkbox);
				preventDefault(e);
				stopBubbling(e);
			}
		};
	}

	var inputs = document.getElementsByTagName('input');
	for (var j = 0; j < inputs.length; j++) {
		if (inputs[j].type == 'checkbox' && (inputs[j].name == 'delete[]' || inputs[j].name == 'screen_delete[]')) {
			addEL(inputs[j], 'click', toggleDeletion);

			var image = document.getElementById('link_' + inputs[j].value);
			if (image) {
				addEL(image, 'click', createListener(inputs[j]));
				var infos = image.getElementsByTagName('i');
				if (infos.length > 0) {
					addEL(infos[0], 'click', createListener(inputs[j], true));
				}
			}
		}
		if (inputs[j].type == 'radio' && (inputs[j].name == 'main' || inputs[j].name == 'screen_main')) {
			addEL(inputs[j], 'click', toggleMain);
		}
	}
}

function buildScreenshotsGrabbingLogic() {
	var i = 0;
	while (true) {
		var sbAssign = document.getElementById('save_as_screenshot_' + i);
		if (!sbAssign) {
			break;
		}
		addEL(sbAssign, 'change', function(e) {
			var target = getEventTarget(e);
			var index = target.selectedIndex;
			var value = target.options[index].value;
			if ((index > 0) && (value != 'new')) {
				for (var j = 0; j < screenGrabbingFields.length; j++) {
					if ((screenGrabbingFields[j].id != target.id) && (screenGrabbingFields[j].selectedIndex == index)) {
						screenGrabbingFields[j].selectedIndex = 0;
						triggerScreenshotSelected(false, screenGrabbingFields[j].id.substring('save_as_screenshot_'.length));
					}
				}
			}
			triggerScreenshotSelected(value, target.id.substring('save_as_screenshot_'.length));
			calculateMaxScreenshotSelected();
		});
		screenGrabbingFields.push(sbAssign);

		var imageAssign = document.getElementById('image_' + i);
		if (!imageAssign) {
			break;
		}
		addEL(imageAssign, 'click', function(e) {
			var target = getEventTarget(e);
			var imagePos = target.id.substring('image_'.length);
			var sb = document.getElementById('save_as_screenshot_' + imagePos);
			if (sb.selectedIndex == 0) {
				if (sb.options.length > screenGrabbingMaxIndex + 1) {
					screenGrabbingMaxIndex++;
				}
				sb.selectedIndex = screenGrabbingMaxIndex;
			} else {
				sb.selectedIndex = 0;
			}
			triggerScreenshotSelected(sb.options[sb.selectedIndex].value, imagePos);
			calculateMaxScreenshotSelected();
		});
		i++;
	}
}

function triggerScreenshotSelected(selected, imagePos) {
	if (document.getElementById('image_cell_' + imagePos)) {
		var ii = document.getElementById('image_cell_' + imagePos).getElementsByTagName('I');
		if (parseInt(selected) > 0 || selected == 'new') {
			addStyleClass(document.getElementById('image_cell_' + imagePos).parentNode, 'selected');
			if (ii.length > 0) {
				ii[0].innerHTML = selected;
			}
		} else {
			removeStyleClass(document.getElementById('image_cell_' + imagePos).parentNode, 'selected');
			if (ii.length > 0) {
				ii[0].innerHTML = '';
			}
		}
	}
}

function calculateMaxScreenshotSelected() {
	screenGrabbingMaxIndex = 0;
	for (var j = 0; j < screenGrabbingFields.length; j++) {
		var index = screenGrabbingFields[j].selectedIndex;
		if (index > screenGrabbingMaxIndex) {
			screenGrabbingMaxIndex = index;
		}
	}
}

// =============================================================================
// Video export / import functions
// =============================================================================

var eiFields = [];
var eiFieldsStatus = [];
var eiSelectedCount = 0;

function buildExportImportFieldsLogic() {
	var i = 1;
	while (true) {
		var sb = document.getElementById('ei_field_' + i);
		if (sb) {
			eiFields.push(sb);
			eiFieldsStatus.push(false);
			addEL(sb, 'change', function(e) {
				onExportImportFieldChanged(getEventTarget(e));
			});
			addEL(sb, 'keyup', function(e) {
				onExportImportFieldChanged(getEventTarget(e));
			});
			if (sb.selectedIndex > 0) {
				var desc = document.getElementById('ei_field_desc_' + i);
				if (desc) {
					desc.innerHTML = sb.options[sb.selectedIndex].title || '';
				}
			}
		} else {
			break;
		}
		i++;
	}
}

function onExportImportFieldChanged(sb) {
	var desc = null;
	if (sb.id.indexOf('ei_field_') >= 0) {
		desc = document.getElementById(sb.id.replace('ei_field_', 'ei_field_desc_'));
	}
	for (var i = 0; i < eiFields.length; i++) {
		if (sb.id == eiFields[i].id) {
			var valueSelected = (eiFields[i].options[eiFields[i].selectedIndex].value != '');
			if (desc) {
				desc.innerHTML = eiFields[i].options[eiFields[i].selectedIndex].title || ''
			}
			if ((valueSelected) && (!eiFieldsStatus[i])) {
				eiFieldsStatus[i] = true;
				eiSelectedCount++;
				break;
			} else if ((!valueSelected) && (eiFieldsStatus[i])) {
				eiFieldsStatus[i] = false;
				eiSelectedCount--;
			}
			return;
		}
	}
	if ((eiSelectedCount == eiFields.length) || (i == eiFields.length - 1)) {
		createExportImportField();
	}
}

function createExportImportField() {
	var tr = eiFields[eiFields.length - 1].parentNode;
	while ((tr != null) && (tr.tagName != 'TR')) {
		tr = tr.parentNode;
	}
	var newRow = tr.cloneNode(true);
	var newIdx = eiFields.length + 1;
	var cells = newRow.getElementsByTagName('TD');
	for (var i = 0; i < cells.length; i++) {
		var cell = cells[i];
		if (cell.className.indexOf('de_label') >= 0) {
			cell.innerHTML = cell.innerHTML.replace('' + (newIdx - 1), newIdx);
		} else if (cell.className.indexOf('de_control') >= 0) {
			var newSb = cell.getElementsByTagName('SELECT')[0];
			newSb.id = 'ei_field_' + newIdx;
			newSb.name = 'field' + newIdx;
			newSb.selectedIndex = 0;
			eiFields.push(newSb);
			eiFieldsStatus.push(false);
			addEL(newSb, 'change', function(e) {
				onExportImportFieldChanged(getEventTarget(e));
			});
			addEL(newSb, 'keyup', function(e) {
				onExportImportFieldChanged(getEventTarget(e));
			});

			var newDesc = cell.getElementsByTagName('SPAN')[0];
			if (newDesc) {
				newDesc.id = 'ei_field_desc_' + newIdx;
				newDesc.innerHTML = '';
			}
		}
	}
	tr.parentNode.insertBefore(newRow, tr.nextSibling);
}

function buildExportImportPresetLogic() {
	var sbPresets = document.getElementById('preset_id');
	if (sbPresets) {
		addEL(sbPresets, 'change', function() {
			window.location = window.location.pathname + '?' + encodeURIComponent(sbPresets.name) + '=' +
							  encodeURIComponent(sbPresets.options[sbPresets.selectedIndex].value);
		});
	}
	var btnDelete = document.getElementById('delete_preset');
	if (btnDelete) {
		addEL(btnDelete, 'click', function(e) {
			if (!confirm(KTLanguagePack['preset_delete_confirm'])) {
				preventDefault(e);
			}
		});
	}
}

function buildImportDateRandomizationLogic() {
	var cbInterval = document.getElementById('pd_random');
	var cbDays = document.getElementById('pd_random_days');
	if (cbInterval && cbDays) {
		addEL(cbInterval, 'click', function() {
			if (cbInterval.checked && cbDays.checked) {
				fireClickEvent(cbDays);
			}
		});
		addEL(cbDays, 'click', function() {
			if (cbDays.checked && cbInterval.checked) {
				fireClickEvent(cbInterval);
			}
		});
	}
}

// =============================================================================
// Video feeds functions
// =============================================================================

var feedFields = [];
var feedFieldsStatus = [];
var feedSelectedCount = 0;

function buildFeedFieldsLogic(oldPrefix) {
	var i = 1;
	while (true) {
		var sb = document.getElementById('csv_field_' + i);
		if (sb) {
			feedFields.push(sb);
			feedFieldsStatus.push(false);
			addEL(sb, 'change', function(e) {
				onFeedFieldChanged(getEventTarget(e));
			});
			addEL(sb, 'keyup', function(e) {
				onFeedFieldChanged(getEventTarget(e));
			});
		} else {
			break;
		}
		i++;
	}

	var handler = function(e) {
		if (document.getElementById('feed_key_prefix').value != oldPrefix) {
			if (!confirm(KTLanguagePack['feed_duplicate_prefix_change_confirm'])) {
				preventDefault(e);
				return false;
			}
		}
		return true;
	};
	var submitBtn = document.getElementById('feed_save_submit1');
	if (submitBtn) {
		addEL(submitBtn, 'click', handler);
	}
	submitBtn = document.getElementById('feed_save_submit2');
	if (submitBtn) {
		addEL(submitBtn, 'click', handler);
	}
}

function onFeedFieldChanged(sb) {
	for (var i = 0; i < feedFields.length; i++) {
		if (sb.id == feedFields[i].id) {
			var valueSelected = (feedFields[i].options[feedFields[i].selectedIndex].value != '');
			if ((valueSelected) && (!feedFieldsStatus[i])) {
				feedFieldsStatus[i] = true;
				feedSelectedCount++;
				break;
			} else if ((!valueSelected) && (feedFieldsStatus[i])) {
				feedFieldsStatus[i] = false;
				feedSelectedCount--;
			}
			return;
		}
	}
	if ((feedSelectedCount == feedFields.length) || (i == feedFields.length - 1)) {
		createFeedField();
	}
}

function createFeedField() {
	var tr = feedFields[feedFields.length - 1].parentNode;
	while ((tr != null) && (tr.tagName != 'TR')) {
		tr = tr.parentNode;
	}
	var newRow = tr.cloneNode(true);
	var newIdx = feedFields.length + 1;
	var cells = newRow.getElementsByTagName('TD');
	for (var i = 0; i < cells.length; i++) {
		var cell = cells[i];
		if (cell.className.indexOf('de_label') >= 0) {
			cell.innerHTML = cell.innerHTML.replace('' + (newIdx - 1), newIdx);
		} else if (cell.className.indexOf('de_control') >= 0) {
			var newSb = cell.getElementsByTagName('SELECT')[0];
			newSb.id = 'csv_field_' + newIdx;
			newSb.name = 'field' + newIdx;
			newSb.selectedIndex = 0;
			feedFields.push(newSb);
			feedFieldsStatus.push(false);
			addEL(newSb, 'change', function(e) {
				onFeedFieldChanged(getEventTarget(e));
			});
			addEL(newSb, 'keyup', function(e) {
				onFeedFieldChanged(getEventTarget(e));
			});
		}
	}
	tr.parentNode.insertBefore(newRow, tr.nextSibling);
}

// =============================================================================
// Video source file upload functions
// =============================================================================

function videoSourceFileUploadFinished() {
	var options = document.getElementById('video_source_file_options');
	if (options) {
		removeStyleClass(options, 'hidden');
	}
}

// =============================================================================
// Album images upload functions
// =============================================================================

function albumImagesUploadStarted(uploaderId) {
	var index = parseInt(uploaderId.replace('image_uploader_', '')) + 1;
	var row = document.getElementById('image_uploader_row_' + index);
	if (row) {
		removeStyleClass(row, 'hidden');
	}
}

// =============================================================================
// SMS billing functions
// =============================================================================

var smsPackageNewRowIndex = 1;
var smsCountryNewRowIndex = 1;
var smsOperatorNewRowIndex = 1;

function buildSmsConfigLogic() {
	var btnAddPackage = document.getElementById('btn_add_package');
	addEL(btnAddPackage, 'click', function() {
		var table = document.getElementById('table_packages');
		var inputs = table.getElementsByTagName('INPUT');
		var maxOrder = 0;
		for (var i = 0; i < inputs.length; i++) {
			if (inputs[i].name.indexOf('order_') == 0) {
				var order = parseInt(inputs[i].value);
				if (!isNaN(order)) {
					maxOrder = Math.max(maxOrder, order);
				}
			}
		}

		var infoRow = document.getElementById('add_package_info_message');
		if (infoRow) {
			infoRow.parentNode.removeChild(infoRow);
		}

		var packagePrefix = 'new' + smsPackageNewRowIndex;
		var templateRow = document.getElementById('add_package_row_template');
		if (templateRow) {
			var newRow = templateRow.cloneNode(true);
			newRow.id = '';
			removeStyleClass(newRow, 'hidden');

			var fields = newRow.getElementsByTagName('INPUT');
			fields[0].name = 'added_' + packagePrefix;
			fields[0].value = 'true';
			fields[1].name = 'title_' + packagePrefix;
			fields[2].name = 'order_' + packagePrefix;
			fields[2].value = maxOrder + 1;
			fields[5].name = 'delete_' + packagePrefix;

			templateRow.parentNode.appendChild(newRow);
			smsPackageNewRowIndex++;
		}
	});
}

function buildSmsAccessPackageLogic() {
	var btnAddCountry = document.getElementById('btn_add_country');
	addEL(btnAddCountry, 'click', function() {
		var table = document.getElementById('table_countries');
		var inputs = table.getElementsByTagName('INPUT');
		var maxOrder = 0;
		for (var i = 0; i < inputs.length; i++) {
			if (inputs[i].name.indexOf('country_order_') == 0) {
				var order = parseInt(inputs[i].value);
				if (!isNaN(order)) {
					maxOrder = Math.max(maxOrder, order);
				}
			}
		}

		var infoRow = document.getElementById('add_country_info_message');
		if (infoRow) {
			infoRow.parentNode.removeChild(infoRow);
		}

		var countryPrefix = 'new' + smsCountryNewRowIndex;
		var templateRow = document.getElementById('add_country_row_template');
		if (templateRow) {
			var newRow = templateRow.cloneNode(true);
			newRow.id = '';
			removeStyleClass(newRow, 'hidden');

			var fields = newRow.getElementsByTagName('INPUT');
			fields[0].name = 'added_country_' + countryPrefix;
			fields[0].value = 'true';
			fields[1].name = 'country_title_' + countryPrefix;
			fields[2].name = 'country_order_' + countryPrefix;
			fields[2].value = maxOrder + 1;
			fields[3].name = 'country_is_active_' + countryPrefix;
			fields[4].name = 'country_delete_' + countryPrefix;

			fields = newRow.getElementsByTagName('SELECT');
			fields[0].name = 'country_code_' + countryPrefix;

			var links = newRow.getElementsByTagName('A');
			links[0].id = 'country_link_' + countryPrefix;
			addEL(links[0], 'click', function(e) {
				onAddOperatorLinkClick(getEventTarget(e));
			});

			templateRow.parentNode.appendChild(newRow);
			smsCountryNewRowIndex++;
		}

		createOperatorRow(countryPrefix, null);
	});

	var table = document.getElementById('table_countries');
	var links = table.getElementsByTagName('A');
	for (var i = 0; i < links.length; i++) {
		if (links[i].id != 'add_country_row_template_link') {
			addEL(links[i], 'click', function(e) {
				onAddOperatorLinkClick(getEventTarget(e));
			});
		}
	}
}

function createOperatorRow(countryPrefix, insertRowBefore) {
	var table = document.getElementById('table_countries');
	var inputs = table.getElementsByTagName('INPUT');
	var maxOrder = 0;
	var lastCountryPrefix = null;
	for (var i = 0; i < inputs.length; i++) {
		if (inputs[i].name.indexOf('ref_country_') == 0) {
			lastCountryPrefix = inputs[i].value;
		} else if (inputs[i].name.indexOf('order_') == 0) {
			if (lastCountryPrefix == countryPrefix) {
				var order = parseInt(inputs[i].value);
				if (!isNaN(order)) {
					maxOrder = Math.max(maxOrder, order);
				}
			}
		}
	}

	var operatorPrefix = 'new' + smsOperatorNewRowIndex;
	var templateRow = document.getElementById('add_operator_row_template');
	if (templateRow) {
		var newRow = templateRow.cloneNode(true);
		newRow.id = '';
		removeStyleClass(newRow, 'hidden');

		var fields = newRow.getElementsByTagName('INPUT');
		fields[0].name = 'ref_country_' + operatorPrefix;
		fields[0].value = countryPrefix;
		fields[1].name = 'title_' + operatorPrefix;
		fields[2].name = 'phone_' + operatorPrefix;
		fields[3].name = 'prefix_' + operatorPrefix;
		fields[4].name = 'cost_' + operatorPrefix;
		fields[5].name = 'order_' + operatorPrefix;
		fields[5].value = maxOrder + 1;
		fields[6].name = 'is_active_' + operatorPrefix;
		fields[7].name = 'delete_' + operatorPrefix;

		if (insertRowBefore) {
			insertRowBefore.parentNode.insertBefore(newRow, insertRowBefore);
		} else {
			templateRow.parentNode.appendChild(newRow);
		}
		smsOperatorNewRowIndex++;
	}
}

function onAddOperatorLinkClick(link) {
	var countryPrefix = link.id.substring(13);

	var tr = link.parentNode;
	while ((tr != null) && (tr.tagName != 'TR')) {
		tr = tr.parentNode;
	}
	tr = tr.nextSibling;
	while ((tr != null) && (!hasStyleClass(tr, 'eg_group_header'))) {
		tr = tr.nextSibling;
	}
	createOperatorRow(countryPrefix, tr);
}

// =============================================================================
// Credit card billing functions
// =============================================================================

var cardPackageNewRowIndex = 1;

function buildCardConfigLogic() {
	var btnAddPackage = document.getElementById('btn_add_package');
	addEL(btnAddPackage, 'click', function() {
		var table = document.getElementById('table_packages');
		var inputs = table.getElementsByTagName('INPUT');
		var maxOrder = 0;
		for (var i = 0; i < inputs.length; i++) {
			if (inputs[i].name.indexOf('order_') == 0) {
				var order = parseInt(inputs[i].value);
				if (!isNaN(order)) {
					maxOrder = Math.max(maxOrder, order);
				}
			}
		}

		var infoRow = document.getElementById('add_package_info_message');
		if (infoRow) {
			infoRow.parentNode.removeChild(infoRow);
		}

		var packagePrefix = 'new' + cardPackageNewRowIndex;
		var templateRow = document.getElementById('add_package_row_template');
		if (templateRow) {
			var newRow = templateRow.cloneNode(true);
			newRow.id = '';
			removeStyleClass(newRow, 'hidden');

			var fields = newRow.getElementsByTagName('INPUT');
			fields[0].name = 'added_' + packagePrefix;
			fields[0].value = 'true';
			fields[1].name = 'title_' + packagePrefix;
			fields[2].name = 'order_' + packagePrefix;
			fields[2].value = maxOrder + 1;
			fields[3].name = 'is_active_' + packagePrefix;
			fields[4].value = packagePrefix;
			fields[5].name = 'delete_' + packagePrefix;

			fields = newRow.getElementsByTagName('SELECT');
			fields[0].name = 'scope_' + packagePrefix;

			templateRow.parentNode.appendChild(newRow);
			cardPackageNewRowIndex++;
		}
	});
}

// =============================================================================
// Global blocks functions
// =============================================================================

function buildGlobalBlocksLogic() {
	var btnAddGlobalBlock = document.getElementById('add_global_block');
	addEL(btnAddGlobalBlock, 'click', function() {
		var i = 1;
		while (true) {
			var row = document.getElementById('row_' + i);
			if (!row) {
				break;
			}
			if (hasStyleClass(row, 'hidden')) {
				removeStyleClass(row, 'hidden');
				break;
			}
			i++;
		}
	});
}

// =============================================================================
// Servers functions
// =============================================================================

var serverContentUrlValue = null;

function buildServerLogic() {
	setInterval('processServerContentUrlChange()', 100);
}

function processServerContentUrlChange() {
	var tfUrl = document.getElementById("urls");
	var tfResult = document.getElementById('control_script_url');
	if (!tfUrl || !tfResult) {
		return;
	}
	if (!serverContentUrlValue || (serverContentUrlValue != tfUrl.value)) {
		var parser = document.createElement('a');
		parser.href = tfUrl.value;
		if (parser.host) {
			tfResult.value = parser.protocol + "//" + parser.host + '/remote_control.php';
		} else {
			tfResult.value = parser.protocol + "//";
		}
		serverContentUrlValue = tfUrl.value;
	}
}

// =============================================================================
// Player settings functions
// =============================================================================

function buildPlayerAccessLevelLogic() {
	var sbLevels = document.getElementById('access_level');
	if (sbLevels) {
		addEL(sbLevels, 'change', function() {
			window.location = window.location.pathname + '?' + encodeURIComponent(sbLevels.name) + '=' +
							  encodeURIComponent(sbLevels.options[sbLevels.selectedIndex].value);
		});
	}
}

function buildPlayerEmbedProfileLogic() {
	var sbProfiles = document.getElementById('embed_profile_id');
	if (sbProfiles) {
		addEL(sbProfiles, 'change', function() {
			window.location = window.location.pathname + '?page=embed&' + encodeURIComponent(sbProfiles.name) + '=' +
				encodeURIComponent(sbProfiles.options[sbProfiles.selectedIndex].value);
		});
	}
	var btnDelete = document.getElementById('delete_profile');
	if (btnDelete) {
		addEL(btnDelete, 'click', function(e) {
			if (!confirm(KTLanguagePack['profile_delete_confirm'])) {
				preventDefault(e);
			}
		});
	}
}

// =============================================================================
// Album images functions
// =============================================================================

function buildAlbumImagesFormatLogic(albumId) {
	var sbAlbumFormat = document.getElementById('album_format_id');

	addEL(sbAlbumFormat, 'change', function() {
		var formatId = encodeURIComponent(sbAlbumFormat.options[sbAlbumFormat.selectedIndex].value);
		window.location = window.location.pathname + '?action=manage_images&item_id=' + albumId + '&format_id=' + formatId;
	});
}

function buildAlbumImagesDeleteLogic() {
	var imagesContainer = document.getElementById('images_container');
	var cbAll = document.getElementById('delete_all');
	var cbDoNotFade = document.getElementById('delete_do_not_fade');
	var sbClickMode = document.getElementById('images_click_mode');
	var sbDisplayMode = document.getElementById('images_display_mode');
	var clickMode = 'viewer';

	if (cbAll) {
		addEL(cbAll, 'click', function() {
			var checkboxes = cbAll.form['delete[]'];
			for (var i = 0; i < checkboxes.length; i++) {
				if (checkboxes[i].checked != cbAll.checked) {
					fireClickEvent(checkboxes[i]);
				}
			}
		});
	}

	function toogleDoNotFade(event) {
		if (cbDoNotFade.checked) {
			removeStyleClass(imagesContainer, 'de_img_list_delete_on_selection');
		} else {
			addStyleClass(imagesContainer, 'de_img_list_delete_on_selection');
		}
		if (event) {
			(new Image()).src = 'async/async_settings.php?setting=images_select_fade_disabled&value=' + (cbDoNotFade.checked ? 1 : 0);
		}
	}

	if (cbDoNotFade) {
		if (imagesContainer) {
			addEL(cbDoNotFade, 'click', toogleDoNotFade);
			toogleDoNotFade();
		}
	}

	function toogleClickMode(event) {
		clickMode = sbClickMode.options[sbClickMode.selectedIndex].value;
		if (imagesContainer) {
			if (clickMode == 'viewer') {
				addStyleClass(imagesContainer, 'de_img_list_preview');
			} else {
				removeStyleClass(imagesContainer, 'de_img_list_preview');
			}
		}
		if (event && clickMode) {
			(new Image()).src = 'async/async_settings.php?setting=images_click_mode&value=' + encodeURIComponent(clickMode);
		}
	}

	if (sbClickMode) {
		addEL(sbClickMode, 'change', toogleClickMode);
		toogleClickMode();
	}

	function toogleDisplayMode(event) {
		var displayMode = sbDisplayMode.options[sbDisplayMode.selectedIndex].value;
		if (imagesContainer) {
			var options = imagesContainer.getElementsByClassName('de_img_list_options');
			for (var i = 0; i < options.length; i++) {
				if (!hasStyleClass(options[i], 'basic')) {
					if (displayMode == 'basic') {
						addStyleClass(options[i], 'hidden');
					} else {
						removeStyleClass(options[i], 'hidden');
					}
				}
			}
		}
		if (event && displayMode) {
			(new Image()).src = 'async/async_settings.php?setting=images_display_mode&value=' + encodeURIComponent(displayMode);
		}
	}

	if (sbDisplayMode) {
		addEL(sbDisplayMode, 'change', toogleDisplayMode);
		toogleDisplayMode();
	}

	function toggleDeletion() {
		var item = document.getElementById('item_' + this.value);
		if (item) {
			if (this.checked) {
				addStyleClass(item, 'deleted');
			} else {
				removeStyleClass(item, 'deleted');
			}
		}
	}

	function toggleMain() {
		var items = document.getElementsByClassName('de_img_list_item');
		for (var i = 0; i < items.length; i++) {
			removeStyleClass(items[i], 'main');
		}
		var item = document.getElementById('item_' + this.value);
		if (item) {
			addStyleClass(item, 'main');
		}
	}

	function createListener(checkbox, force) {
		return function(e) {
			if (force || clickMode == 'select') {
				fireClickEvent(checkbox);
				preventDefault(e);
				stopBubbling(e);
			}
		};
	}

	var inputs = document.getElementsByTagName('input');
	for (var j = 0; j < inputs.length; j++) {
		if (inputs[j].type == 'checkbox' && inputs[j].name == 'delete[]') {
			addEL(inputs[j], 'click', toggleDeletion);

			var image = document.getElementById('link_' + inputs[j].value);
			if (image) {
				addEL(image, 'click', createListener(inputs[j]));
				var infos = image.getElementsByTagName('i');
				if (infos.length > 0) {
					addEL(infos[0], 'click', createListener(inputs[j], true));
				}
			}
		}
		if (inputs[j].type == 'radio' && inputs[j].name == 'main') {
			addEL(inputs[j], 'click', toggleMain);
		}
	}
}

// =============================================================================
// Content settings functions
// =============================================================================

function buildContentSettingsConfirmLogic() {
	var submitBtn = document.getElementById('system_settings_submit');

	var oldTranslitEnabled = document.getElementById('directories_translit').checked;
	var oldTranslitRules = document.getElementById('directories_translit_rules').value;
	addEL(submitBtn, 'click', function(e) {
		var newTranslitEnabled = document.getElementById('directories_translit').checked;
		var newTranslitRules = document.getElementById('directories_translit_rules').value;
		if (oldTranslitEnabled != newTranslitEnabled || oldTranslitRules != newTranslitRules) {
			if (!confirm(KTLanguagePack['change_translit_rules_confirm'])) {
				preventDefault(e);
				return false;
			}
		}
		return true;
	});
}

// =============================================================================
// Website settings functions
// =============================================================================

function buildWebsiteSettingsConfirmLogic() {
	var submitBtn = document.getElementById('website_settings_submit');

	addEL(submitBtn, 'click', function(e) {
		if (document.getElementById('disable_website').checked) {
			if (!confirm(KTLanguagePack['disable_website_confirm'])) {
				preventDefault(e);
				return false;
			}
		}
		if (document.getElementById('website_caching').selectedIndex == 2) {
			if (!confirm(KTLanguagePack['disable_website_caching_confirm'])) {
				preventDefault(e);
				return false;
			}
		}
		return true;
	});
}