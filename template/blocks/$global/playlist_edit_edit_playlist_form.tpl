{{if $async_submit_successful=='true'}}
	<div class="modal__window__form  modal__window__form--single cfx">
		{{if $smarty.post.action=='change_complete'}}
			<div class="success" data-action="refresh">
				{{$lang.edit_playlist.success_message|replace:"%1%":$async_object_data.title}}
			</div>
		{{else}}
			<div class="success" data-action="refresh" data-playlist-id="{{$async_object_data.playlist_id}}" data-playlist-title="{{$async_object_data.title}}">
				{{$lang.edit_playlist.success_message_create|replace:"%1%":$async_object_data.title}}
			</div>
		{{/if}}
	</div>
{{else}}
	<div id="modal-playlist" class="modal popup-holder">
		<div class="modal__window">
			<h2 class="title title__modal">{{if $smarty.post.playlist_id>0}}{{$lang.edit_playlist.title_edit_playlist}}{{else}}{{$lang.edit_playlist.title_create_playlist}}{{/if}}</h2>

			<form {{if $smarty.post.playlist_id>0}}action="{{$lang.urls.memberzone_edit_playlist|replace:"%ID%":$smarty.post.playlist_id}}" {{else}}action="{{$lang.urls.memberzone_create_playlist}}"{{/if}} data-form="ajax" method="post">
				<div class="generic-error {{if $smarty.post.is_locked==0}}hidden{{/if}}">
					{{if $smarty.post.is_locked==1}}
						{{$lang.validation.common.playlist_locked}}
					{{/if}}
				</div>

				<div class="modal__window__form  modal__window__form--single cfx">
					<div class="modal__window__row">
						<label for="playlist_edit_title" class="modal__window__label">{{$lang.edit_playlist.field_title}} (*):</label>
						<div class="relative">
							<input id="playlist_edit_title" type="text" name="title" class="input" value="{{$smarty.post.title}}" placeholder="{{$lang.edit_playlist.field_title_hint}}" {{if $lang.playlists.truncate_title_to>0}}maxlength="{{$lang.playlists.truncate_title_to}}"{{/if}} {{if $smarty.post.is_locked==1}}readonly{{/if}}>
							<div class="field-error down"></div>
						</div>
					</div>

					<div class="btn__row">
						<input type="hidden" name="is_private" value="1"/>
						{{if $smarty.post.playlist_id>0}}
							<input type="hidden" name="action" value="change_complete"/>
							<input type="submit" class="btn btn--green btn--big" value="{{$lang.edit_playlist.btn_edit_playlist}}" {{if $smarty.post.is_locked==1}}disabled{{/if}}/>
						{{else}}
							<input type="hidden" name="action" value="add_new_complete"/>
							<input type="submit" class="btn btn--green btn--big" value="{{$lang.edit_playlist.btn_create_playlist}}"/>
						{{/if}}
					</div>
				</div>
			</form>
		</div>
	</div>
{{/if}}