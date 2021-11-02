<div class="comments__list {{if count($data)==0}}hidden{{/if}}" id="{{$block_uid}}">
	{{assign var="has_hidden_comments" value="false"}}
	{{foreach item="item" from=$data name="comments"}}
		<div class="item comments__item {{if $smarty.foreach.comments.iteration>2}}hidden{{assign var="has_hidden_comments" value="true"}}{{/if}}" data-comment-id="{{$item.comment_id}}">
			<div class="comments__item__avatar rotated">
				{{if $item.avatar_url!=''}}
					<img src="{{$item.avatar_url}}" width="45" height="45" alt="{{$item.display_name}}"/>
				{{/if}}
			</div>
			<div class="comments__item__body">
				<div class="comments__item__heading">
					<span class="comments__item__name">{{$item.display_name|default:$lang.comments.label_anonymous_user}}</span>
					<span class="comments__item__date">{{$item.added_date|date_format:$lang.global.date_format}}</span>
				</div>
				<p class="comments__item__text">{{if $item.comment!=''}}{{$item.comment|replace:"\n":"<br>"}}{{else}}{{$lang.comments.label_comment_deleted}}{{/if}}</p>
			</div>
		</div>
	{{/foreach}}
	{{if $has_hidden_comments=='true'}}
		<div class="pagination">
			<a class="btn" href="#more" data-action="show_comments">{{$lang.common_list.load_more}}</a>
		</div>
	{{/if}}
</div>