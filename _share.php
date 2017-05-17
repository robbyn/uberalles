<div data-role="panel" id="share" data-position="right"
        data-display="overlay" class="js" data-theme="a">
    <ul data-role="listview" data-icon="false">
        <li data-icon="delete">
            <a id="nav-close" href="#" data-rel="close">Fermer</a>
        </li>
        <li data-role="divider" class="ui-bar-a"></li>
        <li><a href="http://www.facebook.com/sharer.php?u=<?php echo $url; ?>" class="share-btn icon-facebook" target="_blank">
            Facebook
        </a></li>
        <li><a href="https://twitter.com/share?url=<?php echo $url; ?>" class="share-btn icon-twitter" target="_blank">
            Twitter
        </a></li>
        <li><a href="https://plus.google.com/share?url=<?php echo $url; ?>" class="share-btn icon-gplus" target="_blank">
            Google+
        </a></li>
        <li><a href="https://pinterest.com/pin/create/bookmarklet/?media=<?php echo $logo_url; ?>&url=<?php echo $url; ?>&is_video=false" class="share-btn icon-pinterest" target="_blank">
            Pinterest
        </a></li>
        <li><a href="http://www.linkedin.com/shareArticle?url=<?php echo $url; ?>" class="share-btn icon-linkedin" target="_blank">
            LinkedIn
        </a></li>
        <!-- li><a href="http://digg.com/submit?url=<?php echo $url; ?>" class="share-btn icon-digg" target="_blank">
            Digg
        </a></li -->
        <li><a href="http://www.tumblr.com/share/link?url=<?php echo $url; ?>" class="share-btn icon-tumblr" target="_blank">
            Tumblr
        </a></li>
        <li><a href="http://reddit.com/submit?url=<?php echo $url; ?>" class="share-btn icon-reddit" target="_blank">
            Reddit
        </a></li>
        <li><a href="http://www.stumbleupon.com/submit?url=<?php echo $url; ?>" class="share-btn icon-stumbleupon" target="_blank">
            Stumble upon
        </a></li>
        <!-- li><a href="https://delicious.com/save?v=5&url=<?php echo $url; ?>" class="share-btn icon-delicious" target="_blank">
            Delicio.us
        </a></li -->
        <li><a href="mailto:?subject=Uberalles.ch&body=<?php echo $url; ?>" class="share-btn icon-email" target="_blank">
            E-mail
        </a></li>
    </ul>
</div>
<script type="text/javascript">
$(function() {
    $("#share").enhanceWithin().panel();
});
</script>
