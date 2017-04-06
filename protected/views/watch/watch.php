<div id="sticker">
	<div id="like_us_img"></div>
	<div id="back_img"></div>
	<div class="fb-like-box" data-href="http://www.facebook.com/youtube9loop" data-colorscheme="light" data-show-faces="true" data-header="true" data-stream="false" data-show-border="false"></div>
</div>

<section id="logo-section">
	<div class="container">
		<span>youtube9loop.com, the missing youtube repeat button</span>
	</div>
</section>

<section id="search-section">
	<div class="container">
		<div class="input_container"> 
			<div id="input-form"></div>
		</div>
	</div>
</section>

<section id="search-result-section">
	<div class="container">
		<div id="search-result">
			<span class="search-result-desc col-xs-12 col-sm-12">search your favourite song now</span>
		</div>
	</div>
</section>

<section id="player-section">
	<div class="container">
		
		<div id='youtube-container' data-video-id="<?php echo htmlentities($youtube_id) ?>" data-origin="<?= htmlentities(Yii::app()->getBaseUrl(true)) ?>"></div>
		
		<div class="song-list list-group col-xs-10 col-xs-offset-1">
		</div>
		
	</div>
	
</section>

<section id="top10-section">
	<div class="container">
		
		<span>top10</span>
		
		<div id="global_rank" class="top10_rank">
			<ul>
			</ul>
		</div>
		
		<div id="local_rank" class="top10_rank" style="display:none;">
			<ul>
			</ul>
		</div>
		
		<div id="hall_of_fame" class="top10_rank" style="display:none;">
			<ul>
			</ul>
		</div>
	</div>
</section>

<script>
	
	// youtube stuff
	var tag = document.createElement('script');
	tag.src = "https://www.youtube.com/iframe_api"; //will call onYouTubeIframeAPIReady after append this js
	var firstScriptTag = document.getElementsByTagName('script')[0];
	firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
		
	function onYouTubeIframeAPIReady() {
		$(function() {
			////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// sticky
			$("#sticker").sticky({ topSpacing: 150 });
			$("#sticker-sticky-wrapper").height(0);
			$("#sticker").click(function() {
				if ($(this).hasClass("clicked")) {
					$(this).removeClass("clicked");
				} else {
					$(this).addClass("clicked");
				}
			});
			
			////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// event handler
			$(window).bind("songRemoved", songRemovedHandler);
			$(window).bind("orderChanged", orderChangedHandler);
			$(window).bind("songAdded", songAddedHandler);
			$('body').on('keydown', bodyKeydownHandler);
			
			////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// custom player config
			$container = $('#youtube-container');
			$container.customPlayer({
				videoId: $container.data('video-id'),
				origin: $container.data('origin')
			});
			
			////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// init, eating url to append songs
			
			//remove hash tag if here (it is not a part of youtubeID !!!
			var loc = window.location.href,
			    index = loc.indexOf('#');
			if (index > 0) 
			 	window.location = loc.substring(0, index);
			var videoList = getParameterByName('v');
			var videoArray = videoList.split(",");
                        // batch load the titles first
                        youtubeUtil.getTitles(videoArray);
                        
			videoArray.shift(); //since first one is already playing :)
			for (v in videoArray) {
				appendSong(videoArray[v], function(e) {
											updateHTMLTitle(); 
											setCookies();  //save cookies for history 
											});
			}
			
			/// end init ///////////////////////////////////////////////////////////////////////////////////////////////////////////
			
			////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// custom player add event handler
			$container.on(CustomPlayer.STATE_CHANGE, function(event, ui){
				if (ui.state === YT.PlayerState.PLAYING) {
					updateHTMLTitle(); 
					// send stat data to server
					statUtil.getInstance().stat(ui.videoId);
				} else if (ui.state === YT.PlayerState.ENDED) {
					// loop current video if the list is empty
					var itemList = $('.list-group a.list-group-item');
					if (itemList.length === 0) {
						$container.customPlayer('play');
					} else {
						// get the active item index
						playNextSong();
					}
				} else if (ui.state === YT.PlayerState.PAUSED) {
					// since when player end will fire this event, the number of songs may not correct since end will remove current song
					$(document).attr('title', $(document).attr("title").replace("\u21bb", "\u25d9"));
				}
			});
			
			$container.on(CustomPlayer.ERROR, function(event, ui){
				// TODO: show error and play next song
				// console.log("ui.code = " + ui.code);
				playNextSong();
			});
			
			// remove the song playing, actually it is NEXT and remove itself
			$container.on(CustomPlayer.TRASH, function(event){
				if ($(".song-list").length > 0) {
					$container.customPlayer('play', $(".song-list a:first-child").data("video-id"));
					$(".song-list a:first-child").remove();
				}
				$(window).trigger("songRemoved");
			});
			
			// click on one song in the list : will bubble up the song to first song, other will still follow the order
			$('.list-group').on('click','a', function(event){
				
				// if user click on trash-button
				if ($(event.target).hasClass("trash-button")) {
					$(this).remove();
					$(window).trigger("songRemoved");
					return false; // no hash at the url end pls
				}
				
				// just normal shift song
				var willPlayID = $(this).data('video-id');
				var index = $(".song-list a").index($(this));
				var songs = [];
				for(var i = 0; i < index; i++) {
					songs[i] = $(".song-list a").eq(i);
				}
				appendSong($container.customPlayer('videoId'), function(e) {
																$(window).trigger("orderChanged");
															}); // put back the playing song to last position then regen the url
				for(s in songs) {
					$(".song-list").append(songs[s]);
				}
															
				$container.customPlayer('play', willPlayID);
				$(this).remove(); // i m going to play, so pls remove the video-item
				return false; // no hash at the url end pls
			});
			
			////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// search box
			var $inputFrom = $('#input-form');
			$inputFrom.loopSearchBox({
			    prefix: 'search',
			    targetElem: $('#search-result'),
			    maxResult: 50
			});
			$("#searchResult").on("render_complete", function() {
				$("#url_container").animate({height:380},1000);
			});
			
			// decorate the search box
			$("#input-form input").addClass("col-xs-10").addClass("col-sm-11");
			$("#input-form input").attr("placeholder", "e.g. Daft Punk or http://www.youtube.com/watch?v=<?php echo $youtube_id?>");
			$("#searchLoopSearchButton").addClass("loop_button").addClass("col-xs-1").addClass("col-sm-1");
			$("#search-result").addClass("col-xs-12").addClass("col-sm-12");
					
			// input_link listener 
			// if it is youtube.com or youtu.be url then go submit loop
			// if it is not, it is keyword, change the button to search, when click, show search result
			$("#searchLoopSearchInput").on("input", function() {
				
				var input = $("#searchLoopSearchInput").val();
				if (input.match(/www.youtube.com\/watch\?v=/) || input.match(/youtu.be\//)) {
					if ($(".loop_button div").text() != "ADD") {
						$(".loop_button div").fadeOut(500, function() {
							$(".loop_button div").text("ADD").fadeIn(500);
						});
					}
					
					var re = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i;
					var result = re.exec(input);
					
					// off the search function and add appendSong function
					$("#searchLoopSearchButton").unbind("click");
					$("#searchLoopSearchButton").bind("click", function(e) { 
																	appendSong(result[1], function() { 
																		$(window).trigger("songAdded", {id:result[1]});
																	})
																});
				} else {
					if ($(".loop_button div").text() != "SEARCH") {
						$(".loop_button div").fadeOut(500, function() {
							$(".loop_button div").text("SEARCH").fadeIn(500);
						});
						
						// off all function, may be appendSong function and turn back on search function defines in the widget
						$("#searchLoopSearchButton").unbind("click");
						$inputFrom.loopSearchBox("bindSearch");
					}
				}
				
			});
			
			////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// search bar click and append to song list
			$("#search-result").on("click","a.video-thumbnail", function(e) {
				e.preventDefault();
				var id = $(e.currentTarget).parent().data("id");
				appendSong(id, function() {
									$(window).trigger("songAdded",{id:id});
								}); 
				return false;
			});
			
			////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// TOP 10
			function Top10Renderer(location, timeRange, $elem){
				var offset = 0;
				var rowCount = 30;  // not set to 10, becoz i think some video will be del or hidden with wtever reason 
									// (since some user may sudden dun want to share)
				var queryList = [];
				var rankList = [];
				var self = this;
				
				// a init queryTop function call is at the end of this Class
				
				// the offset and rowCount here, becoz when asking title ... the video may already del or hidden
				// so need to re-get some more top-video to fill 10 position
				this.queryTop = function() {
					$.ajax("watch/queryTop", {
					   cache: false, 
					   data: {
					       location: location,
					       timeRange: timeRange,
					       offset: offset,
					       rowCount: rowCount
					   },
					   dataType: 'json',
					   success: function(data) {
					       if (data.length > 0) {
								queryList = data;
                                                                loadTitles();
								processQueryList();
					       } else {
					       	  // may be someone recall me due to not enough 10, but i cannot greb any too ... render it
						 	  renderList();
					       }
					   },
					   error: function() {
					       renderList();
					   }
					});
					offset += rowCount; // add it, if recall, i need the next page of top10
				};
                                
                                /**
                                 * A batch load of all titles in query list to minimize the ajax call required
                                 * @returns {void}
                                 */
                                function loadTitles() {
                                    var videoIds = [];
                                    for (var i=0; i<queryList.length; ++i) {
                                        videoIds.push(queryList[i].youtubeId);
                                    }
                                    youtubeUtil.getTitles(videoIds);
                                }
				
				// ask youtube api to get title (dun use customPlayer one ... since i want a simple one is ok)
				function processQueryList() {
					if (queryList.length === 0) {
						self.queryTop();
					} else {
						var queryItem = queryList.shift();
                                                function titleCallback(title, context) {
                                                    context.queryItem.title = title;
                                                    rankList.push(queryItem);
                                                    if (rankList.length === 10) {
                                                            renderList();
                                                    } else {
                                                            processQueryList();
                                                    }
                                                }
                                                function titleErrorCallback(context) {
                                                    processQueryList(); 
                                                    // error then greb next song. if no song, it will queryTop again by the "if" statement at the top of processQueryList
                                                }
                                                
                                                youtubeUtil.getTitles(queryItem.youtubeId, 
                                                        titleCallback, 
                                                        titleErrorCallback,
                                                        {queryItem: queryItem});
					} // end else
				}
				
				function renderList() {
					var $ul = ('ul', $elem.find("ul"));
					for (var i=0; i<rankList.length; ++i) {
					    var rankItem = rankList[i];
					    var $li = $('<li />');
					    var $a = $('<a />');
					    $a.attr('href', '<?=Yii::app()->getBaseUrl(true).'/watch?v='?>' + rankItem.youtubeId);
					    $a.attr("data-video-id", rankItem.youtubeId);
					    $a.text(rankItem.title);
					    $li.append($a);
					    $ul.append($li);
					}
				}
				
				// has a first call, if not enough 10, will call in the function
				self.queryTop();
				
			} // end of top10Renderer
			
			var globalRankLoader = new Top10Renderer('EVERYWHERE', 'WEEKLY', $('#global_rank'));
			var localRankLoader = new Top10Renderer('COUNTRY', 'WEEKLY', $('#local_rank'));
			var hallOfFameLoader = new Top10Renderer('EVERYWHERE', 'ANYTIME', $('#hall_of_fame'));
			
			$(".top10_rank").on("mouseover","a", function(e) {
				$(this).text("+ " + $(this).text()); // add a (+) before the text
			});
			$(".top10_rank").on("mouseout","a", function(e) {
				$(this).text($(this).text().substring(2)); // remove the (+) 
			}); 
			
			$(".top10_rank").on("click","a", function(e) {
				e.preventDefault();
				var id = $(e.currentTarget).data("video-id");
				appendSong(id, function() {
									$(window).trigger("songAdded",{id:id});
								}); 
				return false;
			});
			
			
			// End of Top10
			////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			
			////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// functions
			function appendSong(id, callback) {
				var $html = $('<a href="#" class="list-group-item video-item" data-video-id="'+id+'">'+
							'<div style="float: right; position: relative; top: 3px;" class="button trash-button"></div>'+
							'<div class="title">...</div>'+
							'<div class="clearfix"></div>'+
						'</a>');
				$(".song-list").append($html);
                                // callback when the title is retrieved
                                function titleCallback(title) {
                                    $('.title', $html).html(title);
                                    if (callback) {
                                        callback.apply();
                                    }
                                }
                                youtubeUtil.getTitlesWithRetry(id, titleCallback);
			}
			
			function playNextSong() {
				var currentVideoId = $container.customPlayer("videoId");
				$container.customPlayer('play', $(".song-list a:first-child").data("video-id")); // next song grep and ready to play
				$(".song-list a:first-child").remove(); // i am the next song, i go play now, 88
				//append back the "current" song to queue tail
				appendSong(currentVideoId, function(e){ 
					$(window).trigger("orderChanged");
				});
			}
			
			function genURL() {
				if (history.pushState) {
					var songlist = $container.customPlayer("videoId"); // grep the first ID be the first id in the comma string
					$( ".song-list a" ).each(function( index ) {
					  songlist += "," + $(this).data("video-id");
					});
					history.pushState({}, "", "watch?v=" + songlist);
				}
			}
			
			function updateHTMLTitle() {
				// change the title
				youtubeUtil.getTitlesWithRetry($container.customPlayer("videoId"), function(songTitle){
					if ($container.customPlayer('playerState') == YT.PlayerState.PLAYING)
						$(document).attr('title', "\u21bb " +  (!$(".song-list a").length ? "" : "("+($(".song-list a").length+1)+") ") + songTitle + "  :: youtube9loop");
					else 
						$(document).attr('title', "\u25d9 " +  (!$(".song-list a").length ? "" : "("+($(".song-list a").length+1)+") ") + songTitle + "  :: youtube9loop");
						
					// update facebook og, it is stupid ... since fb will not run yr js to grep the og
					// setFbOg(songTitle);
				});
			}
			
			function setCookies() {
				var songlist = $container.customPlayer("videoId"); // grep the first ID be the first id in the comma string
				$( ".song-list a" ).each(function( index ) {
				  songlist += "," + $(this).data("video-id");
				});
				$.cookie('songlist', songlist);
			}
			
			function setFbOg(firstSongTitle) {
				// update title
				$("meta[property='og:title']").attr("content", $(document).attr('title'));
				
				var songListString = "The song list contains : " + firstSongTitle;
				$( ".song-list a" ).each(function( index ) {
					songListString +=  "," + $(this).find(".title").text();
				}); // add some songs
				
				$("meta[property='og:description']").attr("content", songListString);
			}
			
			function songRemovedHandler(e) {
				updateHTMLTitle();
				genURL();
				setCookies();
			}
			
			function orderChangedHandler(e) {
				updateHTMLTitle();
				genURL();
				setCookies();
			}
			function songAddedHandler(e) {
				updateHTMLTitle();
				genURL();
				setCookies();
			}

			function bodyKeydownHandler(e) {
				// play or pause when user press spacebar
				if(e.keyCode == 32){
					// user has pressed space
					if ($('input:focus').length === 0 && $('textarea:focus').length === 0) {
						e.preventDefault();
						e.stopPropagation();
						$container.customPlayer('playOrPause');
					}
				}
			}
			
			// get Query of url
			function getParameterByName(name) {
			    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
			    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
			        results = regex.exec(location.search);
			    return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
			}
			
		});

	} //end of onYouTubeIframeAPIReady
	
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
</script>
