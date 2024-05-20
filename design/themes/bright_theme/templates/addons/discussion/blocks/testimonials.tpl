{** block-description:discussion_title_home_page **}

{assign var="discussion" value=0|fn_get_discussion:"E":true:$block.properties}

{if $discussion && $discussion.type != "D" && $discussion.posts}

    {assign var="obj_prefix" value="`$block.block_id`000"}

    {if $block.properties.outside_navigation == "Y"}
        <div class="owl-theme ty-owl-controls">
            <div class="owl-controls clickable owl-controls-outside" id="owl_outside_nav_{$block.block_id}">
                <div class="owl-buttons">
                    <div id="owl_prev_{$obj_prefix}" class="owl-prev">{include_ext file="common/icon.tpl" class="ty-icon-left-open-thin"}</div>
                    <div id="owl_next_{$obj_prefix}" class="owl-next">{include_ext file="common/icon.tpl" class="ty-icon-right-open-thin"}</div>
                </div>
            </div>
        </div>
    {/if}

    <div class="ty-scroller-discussion-list">
        <div id="scroll_list_{$block.block_id}" class="owl-carousel ty-scroller-list">
            {foreach from=$discussion.posts item=post}
                <div class="ty-discussion-post__content ty-scroller-discussion-list__item">
                    {hook name="discussion:items_list_row"}
                        <span class="ty-discussion-post__author">{$post.name}</span>
                        <span class="ty-discussion-post__date">{$post.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}</span>
                        <div class="ty-discussion-post {cycle values=", ty-discussion-post_even"}" id="post_{$post.post_id}">
                            <span class="ty-caret"> <span class="ty-caret-outer"></span> <span class="ty-caret-inner"></span></span>

                            {if $discussion.type == "R" || $discussion.type == "B" && $post.rating_value > 0}
                                <div class="clearfix ty-discussion-post__rating">
                                    {include file="addons/discussion/views/discussion/components/stars.tpl" stars=$post.rating_value|fn_get_discussion_rating}
                                </div>
                            {/if}

                            {if $discussion.type == "C" || $discussion.type == "B"}
                                <div class="ty-discussion-post__message">{$post.message|escape|nl2br nofilter}</div>
                            {/if}
                        </div>
                    {/hook}
                </div>
            {/foreach}
        </div>
    </div>

    {include file="common/scroller_init_with_quantity.tpl" prev_selector="#owl_prev_`$obj_prefix`" next_selector="#owl_next_`$obj_prefix`"}

    <div class="ty-mtb-s ty-left ty-uppercase">
        <a href="{"discussion.view?thread_id=`$discussion.thread_id`"|fn_url}">{__("view_all")}</a>
    </div>
{/if}
