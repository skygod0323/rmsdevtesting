$(document).ready(function() {
	$.fn.exists = function() {
		return this.length > 0;
	};

	var pageContext = window['pageContext'];
	var isMobileDevice = /iphone|ipad|Android|webOS|iPod|BlackBerry|Windows Phone/gi.test(navigator.appVersion) || ('ontouchstart' in window);

	var lazyLoadDefOptions = {
		effect: 'fadeIn',
		placeholder: 'data:image/gif;base64,R0lGODlhAQABAIAAAAUEBAAAACwAAAAAAQABAAACAkQBADs=',
		threshold: 200
	};

	var utilitiesMergeOptions = function(def, contextKey) {
		if (pageContext && pageContext[contextKey]) {
			for (var prop in pageContext[contextKey]) {
				if (pageContext[contextKey].hasOwnProperty(prop)) {
					def[prop] = pageContext[contextKey][prop];
				}
			}
		}
		return def;
	};

	var utilitiesSendStatsReq = function(action, videoId, albumId) {
		var statsUrl = window.location.href;
		if (statsUrl.indexOf('#') > 0) {
			statsUrl = statsUrl.substring(0, statsUrl.indexOf('#'));
		}
		if (statsUrl.indexOf('?') >= 0) {
			statsUrl += '&';
		} else {
			statsUrl += '?';
		}

		if (action == 'js_stats') {
			if (pageContext && pageContext['disableStats']) {
				return;
			}
			if (videoId) {
				statsUrl += 'video_id=' + videoId + '&';
			}
			if (albumId) {
				statsUrl += 'album_id=' + albumId + '&';
			}
		}

		var img = new Image();
		img.src = statsUrl + 'mode=async&action=' + action + '&rand=' + new Date().getTime();
	};

	var utilitiesScrollTo = function($obj, speed) {
		if (typeof speed == 'undefined') {
			speed = 400;
		}
		if ($obj.exists()) {
			var windowTop = $(document).scrollTop();
			var windowBottom = windowTop + $(window).height();
			var objectTop = $obj.offset().top;
			if (objectTop > windowTop && objectTop < windowBottom) {
				return;
			}
		}
		$.scrollTo($obj, speed, { offset: -100 });
	};

	var utilitiesAjaxRequest = function(sender, params, successCallback) {
		var url = window.location.href;
		if (url.indexOf('#') > 0) {
			url = url.substring(0, url.indexOf('#'));
		}
		$.ajax({
			url: url + (url.indexOf('?') >= 0 ? '&' : '?') + 'mode=async&format=json&' + $.param(params),
			type: 'GET',
			beforeSend: function() {
				$(sender).block({ message: null });
			},
			complete: function() {
				$(sender).unblock();
			},
			success: function(json) {
				if ((typeof json === 'undefined' ? 'undefined' : typeof(json)) != 'object') {
					json = JSON.parse(json);
				}
				if (json && successCallback) {
					successCallback(json);
				}
			}
		});
	};

	var utilitiesGetBlock = function(blockId, sender, args, params) {
		var url = args.url ? args.url : window.location.href;
		if (url.indexOf('#') > 0) {
			url = url.substring(0, url.indexOf('#'));
		}
		$.ajax({
			url: url + (url.indexOf('?') >= 0 ? '&' : '?') + 'mode=async&function=get_block&block_id=' + blockId + (params ? '&' + $.param(params) : ''),
			type: 'GET',
			cache: false,
			beforeSend: function() {
				$(sender).block({ message: null });
				if (args.beforeSend) {
					args.beforeSend(sender);
				}
			},
			complete: function() {
				$(sender).unblock();
				if (args.complete) {
					args.complete(sender);
				}
			},
			success: function(html) {
				if (args.success) {
					args.success(sender, html);
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				if (args.error) {
					args.error(sender, jqXHR.status, errorThrown);
				}
			}
		});
	};

	var utilitiesRecaptcha = function($container) {
		if (!$container) {
			$container = $(document);
		}
		if (typeof grecaptcha == 'object') {
			$container.find('[data-recaptcha-key]').each(function() {
				var $outer = $(this);
				if (!$outer.attr('data-recaptcha-id')) {
					$outer.html('');
					var recaptchaId = grecaptcha.render($outer.get(0), {
						sitekey: $outer.attr('data-recaptcha-key'),
						theme: $outer.attr('data-recaptcha-theme') || 'light',
						size: $outer.attr('data-recaptcha-size') || 'normal',
						callback: function() {
							var $errorContainer = $outer.parent().find('.field-error');
							$errorContainer.fadeOut();
							$outer.parent().find('.error').removeClass('error');
						}
					});
					$outer.attr('data-recaptcha-id', recaptchaId);
				}
			});
		}
	};

	var utilitiesAjaxForm = function($form, callbacks) {
		var considerFormBlocking = function($form, isBlock) {
			isBlock ? $form.block({ message: null }) : $form.unblock();
		};

		var defaultErrorMessage = 'Unexpected server response received. Please contact support.';
		if (pageContext && pageContext['server_error']) {
			defaultErrorMessage = pageContext['server_error'];
		}

		$form.ajaxForm({
			data: {
				format: 'json',
				mode: 'async'
			},

			beforeSerialize: function() {
				var $autoPopulates = $form.find('[data-form-populate-from]');
				$autoPopulates.each(function() {
					var populateFromName = $(this).attr('data-form-populate-from');
					if (populateFromName) {
						var $populateFrom = $form.find('[name="' + populateFromName + '"]');
						if ($populateFrom.exists()) {
							$(this).val($populateFrom.val());
						}
					}
				});
				if (callbacks && callbacks['beforeSerialize']) {
					callbacks['beforeSerialize']($form);
				}
			},

			beforeSubmit: function(data) {
				var confirmText = $form.attr('data-confirm') || '';
				if (confirmText && !confirm(confirmText)) {
					return false;
				}

				var result = true;
				if (callbacks && callbacks['beforeSubmit']) {
					result = callbacks['beforeSubmit']($form, data);
				}
				considerFormBlocking($form, result);
				return result;
			},

			uploadProgress: function(event, position, total, percent) {
				if (callbacks && callbacks['uploadProgress']) {
					callbacks['uploadProgress']($form, percent);
				}
			},

			success: function(response, statusText, xhr) {
				$form.find('.generic-error').empty().hide();
				considerFormBlocking($form, false);

				if (xhr.getResponseHeader('Content-Type').indexOf('application/json') >= 0) {
					if ((typeof response === 'undefined' ? 'undefined' : typeof(response)) != 'object') {
						response = JSON.parse(response);
					}

					if (response['status'] == 'failure') {
						for (var i = 0; i < response['errors'].length; i++) {
							var error = response['errors'][i];

							var fieldName = error['field'];
							var errorCode = error['code'];
							var errorMessage = error['message'];

							var $errorContainer = null;
							if (fieldName) {
								var $field = $form.find('[name="' + fieldName + '"]');
								if (!$field.exists()) {
									$field = $form.find('[data-name="' + fieldName + '"] [type="text"]');
								}
								if (!$field.exists()) {
									$field = $form.find('[data-name="' + fieldName + '"] select');
								}
								if (!$field.exists()) {
									$field = $form.find('[data-name="' + fieldName + '"]');
								}
								if ($field.exists()) {
									$field.addClass('error');
									$field.parents('.file-control').find('[type="text"]').addClass('error');
									$errorContainer = $field.parent().find('.field-error');
									if (!$errorContainer.exists()) {
										var fieldTitle = $field.parent().find('label').text();
										if (fieldTitle) {
											errorMessage += ' (' + fieldTitle + ')';
										}
									}
									if (i == 0) {
										$field.focus();
									}
								} else {
									errorMessage += ' (' + fieldName + ')';
								}
							}
							if (!$errorContainer || !$errorContainer.exists()) {
								$errorContainer = $form.find('.generic-error');
							}

							$errorContainer.empty().html(errorMessage).fadeIn();

							if (fieldName == 'code' && errorCode != 'required') {
								var $captcha = $form.find('.captcha-control img');
								if ($captcha.exists()) {
									$captcha.attr('src', $captcha.attr('src').replace(new RegExp('rand=\\d+'), 'rand=' + new Date().getTime()));
									$form.find('.captcha-control .textfield').val('');
								}
							}
						}

						if (typeof grecaptcha == 'object') {
							$form.find('[data-recaptcha-key]').each(function() {
								var recaptchaId = $(this).attr('data-recaptcha-id');
								if (recaptchaId) {
									if (grecaptcha.getResponse(recaptchaId)) {
										grecaptcha.reset(recaptchaId);
									}
								}
							});
						}

						if (callbacks && callbacks['error']) {
							callbacks['error']($form);
						}
						utilitiesScrollTo($form, 0);
					} else if (response['status'] == 'success') {
						if (callbacks && callbacks['success']) {
							callbacks['success']($form, response['data']);
						} else if (response['redirect']) {
							window.location = response['redirect'];
						} else if ($form.attr('data-success-message')) {
							$form.empty().append($('<div/>').addClass($form.attr('data-success-message-class') || 'success').html($form.attr('data-success-message')));
						} else {
							window.location.reload();
						}
					} else {
						$form.find('.generic-error').html(defaultErrorMessage).show();
						utilitiesScrollTo($form, 0);
						if (callbacks && callbacks['error']) {
							callbacks['error']($form);
						}
					}
				} else if (xhr.getResponseHeader('Content-Type').indexOf('text/html') >= 0) {
					if (callbacks && callbacks['success']) {
						callbacks['success']($form, response);
					} else {
						if ($(response).attr('data-action') == 'popup' || $(response).find('[data-action="popup"]').exists()) {
							$.fancybox($(response), {
								topRatio: 0.3,

								beforeClose: function() {
									var $redirectTo = this.inner.find('[data-action-redirect-to]');
									if ($redirectTo.exists()) {
										window.location = $redirectTo.attr('data-action-redirect-to');
									} else {
										window.location.reload();
									}
									return true;
								}
							});
						} else {
							$form.empty().append(response);
						}
					}
				} else {
					$form.find('.generic-error').html(defaultErrorMessage).show();
					utilitiesScrollTo($form, 0);
					if (callbacks && callbacks['error']) {
						callbacks['error']($form);
					}
				}
			},

			error: function() {
				considerFormBlocking($form, false);
				$form.find('.generic-error').html(defaultErrorMessage).show();
				utilitiesScrollTo($form, 0);
				if (callbacks && callbacks['error']) {
					callbacks['error']($form);
				}
			},

			complete: function() {
				if (callbacks && callbacks['complete']) {
					callbacks['complete']($form);
				}
			}
		});

		$form.find('input, select, textarea').each(function() {
			var $field = $(this);

			var hideErrorFunction = function() {
				var $errorContainer = $field.parent().find('.field-error');
				$errorContainer.fadeOut();
				$field.removeClass('error');
				$field.parents('fieldset').removeClass('error');
				$field.parents('.file-control').find('[type="text"]').removeClass('error');
			};

			$field.change(hideErrorFunction);
			if ($field.get(0).tagName.toLowerCase() == 'textarea' || $field.get(0).type == 'text' || $field.get(0).type == 'password') {
				$field.keypress(hideErrorFunction);
			}
		});

		$form.find('[type="file"]').change(function() {
			var $input = $(this);
			var value = $input.val();
			if (value.lastIndexOf('/') >= 0) {
				value = value.substring(value.lastIndexOf('/') + 1);
			}
			if (value.lastIndexOf('\\') >= 0) {
				value = value.substring(value.lastIndexOf('\\') + 1);
			}
			var files = $input.prop("files");
			if (files && files.length > 1) {
				value = '';
				for (var i = 0; i < files.length; i++) {
					if (value) {
						value += ', ';
					}
					if (i >= 3) {
						value += '...';
						break;
					}
					value += files[i].name;
				}
			}

			var $container = $input.parents().first();
			if ($input.attr('multiple') && (!files || files.length == 1)) {
				var $clone = $container.clone(true, true);
				$clone.wrap('<form>').parent('form').trigger('reset');
				$clone.unwrap();
				$container.parent().append($clone);
			}
			$container.find('[type="text"]').val(value);
		});

		utilitiesRecaptcha($form);
	};

	var utilitiesAjaxFancyBox = function ($sender, url, afterShowCallback) {
		$('body').removeClass('menu-opened');
		$.fancybox([{href: url, type: 'ajax'}], {
			afterShow: function() {
				this.inner.find('[data-action="popup"]').each(function() {
					$(this).click(function(e) {
						e.preventDefault();
						utilitiesAjaxFancyBox($(this), this.href || $(this).attr('data-href'));
					});
				});
				if (!afterShowCallback) {
					this.inner.find('[data-form="ajax"]').each(function () {
						utilitiesAjaxForm($(this));
					});
				}
				if (afterShowCallback) {
					afterShowCallback.call(this);
				}
			},

			beforeClose: function() {
				if (this.inner.find('[data-action="refresh"]').exists()) {
					window.location.reload();
				}
				return true;
			},

			helpers: {
				overlay: {closeClick: false}
			},

			type: 'ajax',
			topRatio: 0.3
		});
	};

	var initLists = function($container) {
		if (!$container) {
			$container = $(document);
		}

		if ($.fn.lazyload) {
			if (isMobileDevice) {
				$container.find('img.lazy-load').each(function() {
					var originalSrc = $(this).attr('data-original');
					if (originalSrc) {
						this.src = originalSrc;
					}
				});
			} else {
				$container.find('img.lazy-load').lazyload(utilitiesMergeOptions(lazyLoadDefOptions, 'lazyload'));
			}
		}

		if (!isMobileDevice) {
			if ($.fn.thumbs) {
				$container.find('img[data-cnt]').thumbs();
			}

			$container.find('[data-hover="true"]').hover(function() {
				$(this).addClass('hover');
			}, function() {
				$(this).removeClass('hover');
			});
		}
		if ($.fn.videopreview) {
			$container.find('img[data-preview]').videopreview();
		}

		$container.find('[data-rt]').on('mousedown click', function() {
			var rotatorParams = $(this).attr('data-rt');
			if (rotatorParams) {
				var url = window.location.href;
				if (url.indexOf('#') > 0) {
					url = url.substring(0, url.indexOf('#'));
				}
				var img = new Image();
				img.src = url + (url.indexOf('?') >= 0 ? '&' : '?') + 'mode=async&action=rotator_videos&pqr=' + rotatorParams;
				$(this).attr('data-rt', '');
			}
		});
	};

	var initListForms = function($container) {
		if (!$container) {
			$container = $(document);
		}

		var handleSelection = function($form) {
			$form.find('[data-mode="selection"]').prop('disabled', $form.find('[data-action="select"]:checked').length == 0);
			$form.find('[data-action="select_all"]').toggleClass('active', $form.find('[data-action="select"]:checked').length == $form.find('[data-action="select"]').length - $form.find('[data-action="select"][disabled]').length);
		};

		$container.find('[data-form="list"]').each(function() {
			var $form = $(this);

			utilitiesAjaxForm($form, {
				success: function($form) {
					var prevUrl = $form.attr('data-prev-url');
					var blockId = $form.attr('data-block-id');

					if (blockId && prevUrl) {
						utilitiesGetBlock(blockId, null, {
								success: function() {
									window.location.reload();
								},
								error: function() {
									window.location.href = prevUrl;
								}
							}
						);
					} else {
						window.location.reload();
					}
				},

				error: function($form) {
					$form.find('input[name="move_to_playlist_id"]').remove();
				}
			});

			$form.on('change', '[data-action="select"]', function() {
				handleSelection($form);
			});

			$form.find('[data-action="select_all"]').on('click', function() {
				var toggle = !$(this).hasClass('active');
				$form.find('[data-action="select"]').each(function() {
					if (!$(this).prop('disabled')) {
						$(this).prop('checked', toggle);
					}
				});
				handleSelection($form);
			});

			$form.find('[data-action="delete_multi"]').on('click', function() {
				var confirmText = $(this).attr('data-confirm') || '';
				if (confirmText) {
					var selectedNumber = $form.find('[data-action="select"]:checked').length;
					confirmText = confirmText.replace(/\[count\](.*)\[\/count\]/gi, function(match, p1) {
						var defaultValue = '';
						var values = p1.split('||');
						for (var i = 0; i < values.length; i++) {
							var temp = values[i].split(':', 2);
							if (temp.length == 1) {
								defaultValue = temp[0].trim();
							} else {
								var compareExamples = temp[0].split(',');
								for (var j = 0; j < compareExamples.length; j++) {
									var compareExample = compareExamples[j].trim();
									if (compareExample.indexOf('//') == 0) {
										if (selectedNumber % 100 == parseInt(compareExample.substring(2))) {
											return temp[1].trim().replace('%1%', '' + selectedNumber);
										}
									} else if (compareExample.indexOf('/') == 0) {
										if (selectedNumber % 10 == parseInt(compareExample.substring(1))) {
											return temp[1].trim().replace('%1%', '' + selectedNumber);
										}
									} else if (selectedNumber == parseInt(temp[0].trim())) {
										return temp[1].trim().replace('%1%', '' + selectedNumber);
									}
								}
							}
						}
						return defaultValue;
					}).replace('%1%', '' + selectedNumber);
				}

				if (!confirmText || confirm(confirmText)) {
					$form.submit();
				}
			});

			$form.find('[data-action="move_multi"]').on('click', function() {
				utilitiesAjaxFancyBox($(this), $(this).attr('data-href'), function() {
					var $inner_form = this.inner.find('form');
					utilitiesAjaxForm($inner_form, {
						beforeSubmit: function($inner_form) {
							$.fancybox.close();

							var playlistId = parseInt($inner_form.find('[name="playlist_id"]:checked').val());
							if (playlistId) {
								$('<input type="hidden" name="move_to_playlist_id" value="' + playlistId + '">').insertAfter($form.find('input[name="action"]'));
								$form.submit();
							}
							return false;
						}
					});
				});
			});

			$form.find('[data-action="delete_playlist"]').on('click', function() {
				var $button = $(this);
				var confirmText = $button.attr('data-confirm') || '';
				if (!confirmText || confirm(confirmText)) {
					var playlistId = $button.attr('data-id');
					if (!playlistId) {
						return;
					}

					var params = {};
					params['action'] = 'delete_playlists';
					params['delete'] = [playlistId];
					utilitiesAjaxRequest($button, params, function() {
						if ($button.attr('data-redirect-url')) {
							window.location = $button.attr('data-redirect-url');
						}
					});
				}
			});
		});
	};

	var initAjaxForms = function($container) {
		if (!$container) {
			$container = $(document);
		}

		$container.find('[data-form="ajax"]').each(function() {
			utilitiesAjaxForm($(this));
		});
	};

	var initStats = function() {
		$.cookie('kt_tcookie', '1', {expires: 7, path: '/'});
		if ($.cookie('kt_tcookie') == '1') {
			var videoId, albumId;
			if (pageContext && pageContext['videoId']) {
				videoId = pageContext['videoId']
			}
			if (pageContext && pageContext['albumId']) {
				albumId = pageContext['albumId']
			}
			utilitiesSendStatsReq('js_stats', videoId, albumId);
		}

		if (pageContext && pageContext['userId']) {
			var reporter = function() {
				utilitiesSendStatsReq('js_online_status');
			};
			reporter();
			setInterval(reporter, 60 * 1000);
		}
	};

	var initDrop = function() {
		$(document).on('click', '[data-action="drop"]', function(e) {
			try {
				e.preventDefault();
				var $target = $(this);
				var $dropBlock = $('#' + $target.attr('data-drop-id'));

				if ($dropBlock.hasClass('opened')) {
					$dropBlock.slideUp().removeClass('opened').off('clickout');
				} else {
					setTimeout(function() {
						$dropBlock.slideDown().addClass('opened').on('clickout', function (e) {
							$target = $(e.target);
							if (!$target.closest('.fancybox-overlay').exists()) {
								$dropBlock.slideUp().removeClass('opened').off('clickout');
							}
						});
					}, 0);
				}
			} catch (e) {
				console.error(e);
			}
		});
	};

	var initToggle = function() {
		$(document).on('click', '[data-action="toggle"]', function(e) {
			try {
				e.preventDefault();
				var $target = $(this);
				var id = $target.attr('data-toggle-id');
				$('#' + id).toggle();
				$target.toggleClass('active');
				if ($target.hasClass('active')) {
					$('[data-toggle-id="' + id + '"]').addClass('active');
					setTimeout(function() {
						utilitiesScrollTo($('#' + id));
					}, 0)
				} else {
					$('[data-toggle-id="' + id + '"]').removeClass('active');
				}
				if ($target.attr('data-toggle-save') == 'true') {
					if (typeof Storage != 'undefined') {
						if ($target.hasClass('active')) {
							localStorage.setItem('toggle.' + id, 'true');
						} else {
							localStorage.removeItem('toggle.' + id);
						}
					}
				}
			} catch (e) {
				console.error(e);
			}
		});
		if (typeof Storage != 'undefined') {
			var processedTogglers = {};

			$('[data-action="toggle"]').each(function() {
				var $target = $(this);
				var id = $target.attr('data-toggle-id');
				if (!processedTogglers[id]) {
					if (localStorage.getItem('toggle.' + id) == 'true') {
						$('#' + id).toggle();
						$target.toggleClass('active');
					}
					processedTogglers[id] = true;
				}
			});
		}
	};

	var initSearch = function () {
		var doSubmit = function(form) {
			if (form['q'].value == '') {
				form['q'].focus();
				return;
			}
			if ($(form).attr('data-url')) {
				var value = form['q'].value.replace(/[-]/g, '[dash]').replace(/[ ]+/g, '-').replace(/[?]/g, '').replace(/[&]/g, '%26').replace(/[?]/g, '%3F').replace(/[/]/g, '%2F').replace(/\[dash\]/g, '--');
				window.location = $(form).attr('data-url').replace('%QUERY%', encodeURIComponent(value));
			}
		};

		$('#search_form').submit(function(e) {
			try {
				e.preventDefault();
				doSubmit(this);
			} catch (e) {
				console.error(e);
			}
		}).find('input[name="for"]').click(function() {
				var iconClass = $(this.form).find('label[for="' + $(this).attr('id') + '"]').find('[data-search-type-icon]').attr('class');
				$(this.form).attr('data-url', $(this).attr('data-url'));
				$(this.form).find('[data-action="drop"]').find('[data-search-type-icon]').attr('class', iconClass);
				doSubmit(this.form);
		});
	};

	var initSignupForm = function () {
		$(document).on('change', '#modal-signup input[type="radio"]', function() {
			var $radio = $(this);
			if ($radio.prop('checked')) {
				if ($radio.prop('name') == 'payment_option') {
					$radio.closest('form').find('.captcha-control').removeClass('hidden');
					$radio.closest('form').find('input[type="radio"][name="card_package_id"]').prop('checked', false);
				} else if ($radio.prop('name') == 'card_package_id') {
					$radio.closest('form').find('.captcha-control').addClass('hidden');
					$radio.closest('form').find('input[type="radio"][name="payment_option"]').prop('checked', false);
				}
			}
		});
	};

	var initPopups = function () {
		$('[data-action="popup"]').each(function() {
			$(this).click(function(e) {
				e.preventDefault();
				utilitiesAjaxFancyBox($(this), this.href || $(this).attr('data-href'));
			});
		});

		if (window.location.href.indexOf('?login') > 0) {
			$('#login_link').click();
		}
	};

	var initRating = function() {
		$(document).on('click', '[data-action="rating"] [data-vote]', function(e) {
			try {
				e.preventDefault();

				var $link = $(this);
				var $ratingContainer = $(this).parents('[data-action="rating"]');
				if ($link.hasClass('disabled') || $link.hasClass('voted')) {
					return;
				}
				var vote = parseInt($link.attr('data-vote')) || 0;
				var videoId = $link.attr('data-video-id');
				var albumId = $link.attr('data-album-id');
				var playlistId = $link.attr('data-playlist-id');
				var postId = $link.attr('data-post-id');
				var modelId = $link.attr('data-model-id');
				var csId = $link.attr('data-cs-id');
				var dvdId = $link.attr('data-dvd-id');
				var flagId = $link.attr('data-flag-id');
				if (videoId || albumId || playlistId || modelId || csId || postId || dvdId) {
					utilitiesAjaxRequest($link, { action: 'rate', video_id: videoId, album_id: albumId, playlist_id: playlistId, model_id: modelId, cs_id: csId, post_id: postId, dvd_id: dvdId, vote: vote }, function(json) {
						if (json['status'] == 'success') {
							$ratingContainer.find('[data-vote]').addClass('disabled');
							$link.removeClass('disabled').addClass('voted');
							$ratingContainer.find('[data-show="success"]').show();

							if (json['data'] && json['data']['rating']) {
								var newRating = Math.round(json['data']['rating'] / 5 * 100);
								if (newRating > 100) {
									newRating = 100;
								}
								$ratingContainer.find('[data-rating="percent"]').html(newRating + '%');
							}
						} else {
							$ratingContainer.find('[data-vote]').addClass('disabled');
							$ratingContainer.find('[data-show="error"]').show();
						}
					});
					if (flagId) {
						utilitiesAjaxRequest($link, { action: 'flag', video_id: videoId, album_id: albumId, playlist_id: playlistId, postId: postId, dvdId: dvdId, flag_id: flagId }, function() {});
					}
				}
			} catch (e) {
				console.error(e);
			}
		});
	};

	var initSubscriptions = function () {
		$(document).on('click', '[data-subscribe-to], [data-unsubscribe-to]', function(e) {
			try {
				e.preventDefault();

				var $btn = $(this);
				if ($btn.hasClass('disabled')) {
					return;
				}
				var subscriptionTo = $btn.attr('data-subscribe-to') || $btn.attr('data-unsubscribe-to');
				var subscriptionId = $btn.attr('data-id');
				if (subscriptionTo && subscriptionId) {
					var params = {action: 'subscribe'};
					if (!$btn.attr('data-subscribe-to')) {
						params['action'] = 'unsubscribe';
					}
					if (subscriptionTo == 'category') {
						params[params['action'] + '_category_id'] = subscriptionId;
					} else if (subscriptionTo == 'model') {
						params[params['action'] + '_model_id'] = subscriptionId;
					} else if (subscriptionTo == 'content_source') {
						params[params['action'] + '_cs_id'] = subscriptionId;
					} else if (subscriptionTo == 'user') {
						params[params['action'] + '_user_id'] = subscriptionId;
					} else if (subscriptionTo == 'playlist') {
						params[params['action'] + '_playlist_id'] = subscriptionId;
					} else if (subscriptionTo == 'dvd') {
						params[params['action'] + '_dvd_id'] = subscriptionId;
					}
					utilitiesAjaxRequest($btn, params, function(json) {
						if (json['status'] == 'success') {
							$btn.toggleClass('subscribed').addClass('disabled');
							var $count = $btn.find('[data-subscribers="count"]');
							if ($count.exists()) {
								if (params['action'] == 'subscribe') {
									$count.html(parseInt($count.html()) + 1);
								} else {
									$count.html(parseInt($count.html()) - 1);
								}
							}
						}
					});
				}
			} catch (e) {
				console.error(e);
			}
		});
	};

	var initAddToFavourites = function () {
		$(document).on('click', '[data-fav-list-id] a', function(e) {
			try {
				var $link = $(this);
				var videoId = $link.attr('data-video-id');
				var albumId = $link.attr('data-album-id');
				var favType = $link.attr('data-fav-type') || 0;
				var createPlaylistUrl = $link.attr('data-create-playlist-url');
				var playlistId = $link.attr('data-playlist-id') || 0;
				var action = $link.attr('data-action');

				if (action && (videoId || albumId)) {
					e.preventDefault();
					if (action == 'delete') {
						utilitiesAjaxRequest($link.closest('[data-fav-list-id]'), {action: 'delete_from_favourites', video_id: videoId, album_id: albumId, fav_type: favType, playlist_id: playlistId}, function(json) {
							if (json['status']=='success') {
								if (playlistId > 0) {
									$link.closest('[data-fav-list-id]').addClass('hidden');
									$link.closest('ul').find('[data-fav-list-id="add_playlist_' + playlistId + '"]').removeClass('hidden');
								} else {
									$link.closest('[data-fav-list-id]').addClass('hidden');
									$link.closest('ul').find('[data-fav-list-id="add_fav_' + favType + '"]').removeClass('hidden');
								}

								var $counter = $('[data-favourites="count"]');
								var count = parseInt($counter.html()) - 1;
								if (count >= 0) {
									$counter.html(count);
								}

								var hasMoreFavourites = false;
								$('[data-fav-list-id]').each(function() {
									if ($(this).attr('data-fav-list-id').indexOf('delete_') == 0 && !$(this).hasClass('hidden')) {
										hasMoreFavourites = true;
									}
								});
								$('[data-drop-id="fav_list"]').toggleClass('subscribed', hasMoreFavourites);
							}
						});
					} else if (action == 'add') {
						if (favType == 10 && !playlistId) {
							if (createPlaylistUrl) {
								utilitiesAjaxFancyBox($link, createPlaylistUrl, function () {
									var $form = this.inner.find('form');
									utilitiesAjaxForm($form, {
										success: function($form, newPlaylistData) {
											$.fancybox.close();

											newPlaylistData = $(newPlaylistData);
											playlistId = newPlaylistData.find('[data-playlist-id]').attr('data-playlist-id');
											var playlistTitle = newPlaylistData.find('[data-playlist-title]').attr('data-playlist-title');

											if (playlistId) {
												utilitiesAjaxRequest($link.closest('[data-fav-list-id]'), {action: 'add_to_favourites', video_id: videoId, album_id: albumId, fav_type: favType, playlist_id: playlistId}, function(json) {
													if (json['status']=='success') {
														var $newItem = $link.closest('ul').find('[data-fav-list-id="add_playlist_"]').clone(true, true);
														$newItem.find('a').each(function() {
															if ($(this).attr('data-playlist-id')) {
																$(this).attr('data-playlist-id', playlistId);
															}
															if ($(this).attr('href')) {
																$(this).attr('href', $(this).attr('href').replace('%ID%', playlistId));
															}
															$(this).html($(this).html().replace('%1%', playlistTitle));
														});
														$newItem.attr('data-fav-list-id', 'add_playlist_' + playlistId);
														$newItem.insertBefore($link.closest('li'));

														$newItem = $link.closest('ul').find('[data-fav-list-id="delete_playlist_"]').clone(true, true);
														$newItem.find('a').each(function() {
															if ($(this).attr('data-playlist-id')) {
																$(this).attr('data-playlist-id', playlistId);
															}
															if ($(this).attr('href')) {
																$(this).attr('href', $(this).attr('href').replace('%ID%', playlistId));
															}
															$(this).html($(this).html().replace('%1%', playlistTitle));
														});
														$newItem.attr('data-fav-list-id', 'delete_playlist_' + playlistId);
														$newItem.removeClass('hidden');
														$newItem.insertBefore($link.closest('li'));

														var $counter = $('[data-favourites="count"]');
														var count = parseInt($counter.html()) + 1;
														$counter.html(count);

														$('[data-drop-id="fav_list"]').addClass('subscribed');
													}
												});
											}
										}
									});
								});
							}
						} else {
							utilitiesAjaxRequest($link.closest('[data-fav-list-id]'), {action: 'add_to_favourites', video_id: videoId, album_id: albumId, fav_type: favType, playlist_id: playlistId}, function(json) {
								if (json['status']=='success') {
									if (playlistId > 0) {
										$link.closest('li').addClass('hidden');
										$link.closest('ul').find('[data-fav-list-id="delete_playlist_' + playlistId + '"]').removeClass('hidden');
									} else {
										$link.closest('li').addClass('hidden');
										$link.closest('ul').find('[data-fav-list-id="delete_fav_' + favType + '"]').removeClass('hidden');
									}

									var $counter = $('[data-favourites="count"]');
									var count = parseInt($counter.html()) + 1;
									$counter.html(count);

									$('[data-drop-id="fav_list"]').addClass('subscribed');
								}
							});
						}
					}
				}
			} catch (e) {
				console.error(e);
			}
		});
	};

	var initComments = function () {
		$('[data-form="comments"]').each(function() {
			utilitiesAjaxForm($(this), {
				success: function($form, newCommentData) {
					var $anonymousUsernameField = $form.find('[name="anonymous_username"]');
					var anonymousUsername = $anonymousUsernameField.val();
					if (anonymousUsername) {
						$.cookie('kt_anonymous_username', anonymousUsername, {path: '/'});
					}

					$form.get(0).reset();
					$anonymousUsernameField.val(anonymousUsername || '');

					var $captcha = $form.find('.captcha-control img');
					if ($captcha.exists()) {
						$captcha.attr('src', $captcha.attr('src').replace(new RegExp('rand=\\d+'),'rand=' + new Date().getTime()));
					}

					var commentsListId = $form.attr('data-block-id');
					var $commentsList = $('#' + commentsListId);
					if (newCommentData && newCommentData['approved'] && commentsListId && $commentsList) {
						var args = {
							success: function(sender, html) {
								var resultElement = document.createElement('DIV');
								resultElement.innerHTML = html;

								var $newItem = $(resultElement).find('[data-comment-id="' + (newCommentData['comment_id'] || newCommentData['entry_id']) + '"]').addClass('hidden');
								$commentsList.prepend($newItem);

								setTimeout(function() {
									$commentsList.show();
									$newItem.fadeIn();
								}, 200);
							}
						};
						utilitiesGetBlock(commentsListId, null, args);
					} else {
						var showOnSuccess = $form.attr('data-success-show-id');
						if (showOnSuccess) {
							$('#' + showOnSuccess).show();
						}
					}

					var hideOnSuccess = $form.attr('data-success-hide-id');
					if (hideOnSuccess) {
						$('#' + hideOnSuccess).hide();
					}
				}
			});

			$(this).find('[name="anonymous_username"]').val($.cookie('kt_anonymous_username') || '');
		});

		$(document).on('click', '[data-action="show_comments"]', function(e) {
			try {
				e.preventDefault();
				$(this).remove();
				$('[data-comment-id]').show();
			} catch (e) {
				console.error(e);
			}
		});
	};

	var initFancybox = function() {
		$('[data-fancybox-type]').fancybox({
			openEffect: 'none',
			closeEffect: 'none',
			prevEffect: 'none',
			nextEffect: 'none',
			helpers: {
				title: {
					type: 'inside'
				},
				buttons: {
					position: 'bottom'
				}
			}
		});
	};

	var initDownloadLinks = function() {
		$('[data-attach-session]').each(function() {
			var sessionName = $(this).attr('data-attach-session');
			if (sessionName) {
				var sessionId = $.cookie(sessionName);
				if (sessionId) {
					if ($(this).attr('href')) {
						$(this).attr('href', $(this).attr('href') + ($(this).attr('href').indexOf('?') > 0 ? '&' : '?') + sessionName + '=' + sessionId);
					}
					if ($(this).attr('src')) {
						$(this).attr('src', $(this).attr('src') + ($(this).attr('src').indexOf('?') > 0 ? '&' : '?') + sessionName + '=' + sessionId);
					}
					if ($(this).attr('data-src')) {
						$(this).attr('data-src', $(this).attr('data-src') + ($(this).attr('data-src').indexOf('?') > 0 ? '&' : '?') + sessionName + '=' + sessionId);
					}
				}
			}
		});
	};

	var initPurchase = function() {
		$(document).on('click', '[data-action="purchase"]', function(e) {
			try {
				e.preventDefault();

				var $link = $(this);
				var videoId = $link.attr('data-video-id');
				var albumId = $link.attr('data-album-id');
				var action = 'purchase_video';
				if (albumId) {
					action = 'purchase_album';
				}

				if (videoId || albumId) {
					utilitiesAjaxRequest($link, {action: action, video_id: videoId, album_id: albumId}, function(json) {
						if (json['status']=='success') {
							window.location.reload();
						} else {
							if (json['errors'] && json['errors'].length > 0) {
								$link.find('.generic-error').empty().html(json['errors'][0]['message']).fadeIn();
							}
						}
					});
				}
			} catch (e) {
				console.error(e);
			}
		});
	};

	var initPlaylist = function() {
		var $playlist = $('[data-playlist="true"]');

		if ($playlist.exists()) {
			var slider = $playlist.flexslider({
				animation: "slide",
				animationLoop: true,
				slideshow: false,
				controlNav: false,
				itemWidth: parseInt($playlist.attr('data-playlist-thumb-width')) || 150,
				itemMargin: parseInt($playlist.attr('data-playlist-thumb-margin')) || 5,
				prevText: "<div class='rotated'><i class='icon-chevron-left'></i></div>",
				nextText: "<div class='rotated'><i class='icon-chevron-right'></i></div>",
				start: function() {
					$playlist.addClass('ready');
				}
			}).data('flexslider');

			var activeSlideIndex = parseInt($playlist.attr('data-playlist-active-slide'));
			if (activeSlideIndex && slider && slider.move) {
				var sliderPage = Math.ceil(activeSlideIndex / slider.move);
				if (sliderPage) {
					slider.flexslider(sliderPage - 1);
				}
			}

			var videoId = $playlist.attr('data-playlist-active-video-id');
			var playlistId = $playlist.attr('data-playlist-id');

			var currentIndex = -1;
			var $playlistVideos = $playlist.find('[data-playlist-video-id]');
			$playlistVideos.each(function(index) {
				if ($(this).attr('data-playlist-video-id') == videoId) {
					currentIndex = index;
				}
			});
			var nextIndex = currentIndex + 1;
			if (nextIndex >= $playlistVideos.length) {
				nextIndex = 0;
			}
			var nextVideoUrl = $playlistVideos.eq(nextIndex).attr('href');

			if (window['player_obj']) {
				window['player_obj'].listen('ktVideoFinished', function() {
					if (nextVideoUrl) {
						window.location = nextVideoUrl;
					}
				});
			}

			$playlist.find('[data-playlist-action="delete"]').on('click', function(e) {
				e.preventDefault();

				if (videoId && playlistId) {
					var confirmText = $(this).attr('data-confirm');
					if (!confirmText || confirm(confirmText)) {
						utilitiesAjaxRequest($(this), {action: 'delete_from_favourites', video_id: videoId, fav_type: 10, playlist_id: playlistId}, function(json) {
							if (json['status']=='success') {
								if (nextVideoUrl) {
									window.location = nextVideoUrl;
								} else {
									window.location.reload();
								}
							}
						});
					}
				}
			});
		}
	};

	var initAlbumSliders = function() {
		var $sliderImages = $('[data-slider="images"]');
		var $sliderAlbum = $('[data-slider="album"]');

		var avgImageWidth = 0;
		var imagesAmount = $sliderImages.find('.slides > li').each(function() {
			avgImageWidth += $(this).outerWidth(true);
		}).length;
		avgImageWidth = avgImageWidth / imagesAmount;

		$sliderImages.flexslider({
			animation: "slide",
			controlNav: false,
			slideshow: false,
			itemWidth: avgImageWidth,
			asNavFor: $sliderAlbum,
			prevText: "<div class='rotated'><i class='icon-chevron-left'></i></div>",
			nextText: "<div class='rotated'><i class='icon-chevron-right'></i></div>",
			start: function() {
				$sliderImages.addClass('ready');
				$sliderImages.find('[data-src]').each(function() {
					$(this).attr('src', $(this).attr('data-src')).removeAttr('data-src');
				});
			}
		});

		$sliderAlbum.flexslider({
			animation: "slide",
			controlNav: false,
			slideshow: false,
			prevText: "<div class='rotated'><i class='icon-chevron-left'></i></div>",
			nextText: "<div class='rotated'><i class='icon-chevron-right'></i></div>",
			sync: $sliderImages,
			initDelay: 0,
			start: function() {
				$sliderAlbum.addClass('ready');
			},
			before: function(slider) {
				var slides = slider.slides,
					index = slider.animatingTo,
					$slide = $(slides[index])
					;
				$slide.find('[data-src]').each(function() {
					$(this).attr('src', $(this).attr('data-src')).removeAttr('data-src');
				});
			}
		});
	};

	var initIndexSlider = function() {
		var $sliderIndex = $('[data-slider="index"]');
		var video = $sliderIndex.closest('[data-slider-container="index"]').find('video').get(0);

		$sliderIndex.flexslider({
			controlNav: false,
			slideshow: false,
			animation: "slide",
			prevText: "<i class='icon-arr-left'></i>",
			nextText: "<i class='icon-arr-right'></i>",
			before: function(slider) {
				var slides = slider.slides,
					index = slider.animatingTo,
					$slide = $(slides[index])
					;
				$slide.find('[data-src]').each(function() {
					$(this).attr('src', $(this).attr('data-src')).removeAttr('data-src');
				});

				if (video) {
					video.pause();
					$(video).removeClass('video_load');
					$sliderIndex.find('[data-action="play"]').show();
				}
			},
			start: function() {
				$sliderIndex.addClass('ready');
			}
		});

		if (video) {
			$(video).on('click', function() {
				console.log(video);
				if (video.paused) {
					video.play();
				} else {
					video.pause();
				}
			});
			$sliderIndex.on('click', '[data-action="play"]', function(e) {
				var videoUrl = $(this).closest('[data-video-url]').attr('data-video-url');
				if (videoUrl) {
					video.src = videoUrl;
					video.load();
					video.play();
					$(video).addClass('video_load');
					$(this).hide();

					e.preventDefault();
				}
			});
		}
	};

	var initMobileMenu = function() {
		$(document).on('click', '[data-action="mobile"]', function() {
			try {
				var $body = $('body');
				if ($body.hasClass('menu-opened')) {
					$body.removeClass('menu-opened');
				} else {
					$body.addClass('menu-opened');
					$('[data-navigation="true"]').mCustomScrollbar({
						theme: "minimal"
					});
				}
			} catch (e) {
				console.error(e);
			}
		});

		$(document).on('mousedown touchstart', function(e) {
			try {
				if (!$(e.target).closest('[data-action="mobile"]').exists() && !$(e.target).closest('[data-navigation="true"]').exists()) {
					$('body').removeClass('menu-opened');
				}
			} catch (e) {
				console.error(e);
			}
		});
	};

	var initRecaptcha = function() {
		$(document).on('recaptchaloaded', function() {
			utilitiesRecaptcha();
		});
	};

	var initMethods = [
		initLists,
		initAjaxForms,
		initListForms,
		initStats,
		initDrop,
		initToggle,
		initSearch,
		initSignupForm,
		initPopups,
		initRating,
		initSubscriptions,
		initAddToFavourites,
		initComments,
		initFancybox,
		initDownloadLinks,
		initPurchase,
		initPlaylist,
		initAlbumSliders,
		initIndexSlider,
		initMobileMenu,
		initRecaptcha
	];
	for (var i = 0; i < initMethods.length; i++) {
		if (typeof initMethods[i] == 'function') {
			try {
				initMethods[i].call(this);
			} catch (e) {
				if (console && console.error) {
					console.error(e);
				}
			}
		}
	}
});