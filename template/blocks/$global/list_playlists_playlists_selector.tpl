<div id="modal-playlist" class="modal popup-holder">
	<div class="modal__window">
		<h2 class="title title__modal">{{$lang.select_playlist.title}}</h2>

		<form data-create-playlist-url="{{$lang.urls.memberzone_create_playlist}}">
			<div class="generic-error hidden"></div>

			<div class="modal__window__form  modal__window__form--single cfx">
				<div class="modal__window__row">
					<ul class="price-list">
						{{assign var="has_playlists" value="false"}}
						{{foreach item="item" from=$data}}
							{{if $smarty.request.playlist_id!=$item.playlist_id}}
								{{assign var="has_playlists" value="true"}}
								<li class="price-list__item">
									<input id="playlist{{$item.playlist_id}}" type="radio" name="playlist_id" value="{{$item.playlist_id}}"/>
									<label for="playlist{{$item.playlist_id}}" class="price-list__item__body cfx">
										<span class="price-list__button"></span>
										{{$item.title}} - {{$lang.select_playlist.label_videos|count_format:"%1%":$item.total_videos}}
									</label>
								</li>
							{{/if}}
						{{/foreach}}
					</ul>
				</div>
				<div class="modal__window__row">
					<div class="btn__row">
						<input type="submit" class="btn btn--green btn--big" value="{{$lang.select_playlist.btn_select}}" {{if $has_playlists!='true'}}disabled{{/if}}/>
						<input type="button" class="btn btn--green btn--big" value="{{$lang.select_playlist.btn_new}}" data-action="popup" data-href="{{$lang.urls.memberzone_create_playlist}}">
					</div>
				</div>
			</div>
		</form>
	</div>
</div>