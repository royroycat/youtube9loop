<section id="youtube_container">
	<div id='test-container' data-video-id="<?php echo htmlentities($youtube_id) ?>" data-origin="<?= htmlentities(Yii::app()->getBaseUrl(true)) ?>"></div>
	<div class="list-group">
	  <a href="#" class="list-group-item active" data-video-id="<?php echo htmlentities($youtube_id) ?>">Song1</a>
	  <a href="#" class="list-group-item" data-video-id="_kqQDCxRCzM">Song2</a>
	  <a href="#" class="list-group-item" data-video-id="LiaYDPRedWQ">Song3</a>
	  <a href="#" class="list-group-item" data-video-id="dhsy6epaJGs">Song4</a>
	</div>
	<div id="showVideoId">Show Video ID</div>
</section>
<script src='js/customPlayer.js' type='text/javascript'></script>
<script src='js/statUtil.js' type='text/javascript'></script>
<script>
	$(function(){
		// youtube stuff
		var tag = document.createElement('script');
		tag.src = "https://www.youtube.com/iframe_api";
		var firstScriptTag = document.getElementsByTagName('script')[0];
		firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
		
	});
	
	function onYouTubeIframeAPIReady() {
		$container = $('#test-container');
		$container.customPlayer({
			videoId: $container.data('video-id'),
			origin: $container.data('origin')
		});
		$container.on(CustomPlayer.STATE_CHANGE, function(event, ui){
			if (ui.state === YT.PlayerState.PLAYING) {
				youtubeUtil.getTitle(ui.videoId, function(title){
					$(document).attr('title', title);
				});
				// send stat data to server
				statUtil.getInstance().stat(ui.videoId);
			} else if (ui.state === YT.PlayerState.ENDED) {
				console.log(ui.videoId);
				// loop current video if the list is empty
				var itemList = $('.list-group a.list-group-item');
				if (itemList.length === 0) {
					$container.customPlayer('play');
				} else {
					// get the active item index
					var activeItem = $('.list-group a.list-group-item.active');
					var activeIndex = activeItem.index();
					var nextItem = (activeIndex+1 < itemList.length)?
							itemList.eq(activeIndex+1) : itemList.eq(0);
					$container.customPlayer('play', nextItem.data('video-id'));
					itemList.removeClass('active');
					nextItem.addClass('active');
				}
			}
		});
		$container.on(CustomPlayer.TRASH, function(event){
			console.log('trashed');
		});
		
		$('.list-group a').on('click', function(event){
			$container.customPlayer('play', $(this).data('video-id'));
			
		});
		
		// get video id
		$('#showVideoId').on('click', function(event){
			alert($container.customPlayer('videoId'));
		});
		// bind space to play pause
		$('body').keydown(function(e){
			if(e.keyCode == 32){
				// user has pressed space
				if ($('input:focus').length === 0 && $('textarea:focus').length === 0) {
					$container.customPlayer('playOrPause');
				}
			}
		});
	}
</script>

<script>

(function($){
	
	var youtubeAPIKey = 'AIzaSyDBvCnEhyXWHSePt9dIRO62WCd7qY1l-EQ';
	
	window.playlistTo9Loop = function(playlistId) {
		// prepare query data
		var queryData = {
			key: youtubeAPIKey,
			playlistId: playlistId,
			part: 'snippet',
			maxResults: 50
		};
		var videoIdArray = [];
		function makeQuery(queryData) {
			// retrieve playlist data
			$.ajax('https://www.googleapis.com/youtube/v3/playlistItems', {
				data: queryData,
				dataType: 'json',
				type: 'GET',
				async: false,
				success: function(data) {
					if (data.items) {
						for (var i=0; i<data.items.length; ++i) {
							var title = data.items[i].snippet.title;
							console.log(title);
							var videoId = data.items[i].snippet.resourceId.videoId;
							console.log(videoId);
							videoIdArray.push(videoId);
						}
					}
					if (data.nextPageToken) {
						queryData.pageToken = data.nextPageToken;
						makeQuery(queryData);
					}

				}
			});
		}
		makeQuery(queryData);
		var loopUrl = 'www.youtube9loop.com/watch?v=' + videoIdArray.join();
		console.log(loopUrl);
	};
	
})(jQuery);

</script>