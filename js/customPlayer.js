/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function($){
	
	var youtubeApiKey = '***youtube-api-key***';
	window.CustomPlayer = window.CustomPlayer || {};
	var eventPrefix = "customplayer";
	var eventStateChange = "statechange";
	var eventError = "error";
	var eventTrash = "trash";
	var animationDuration = 500;
	var updateInterval = 100;
	CustomPlayer.STATE_CHANGE = eventPrefix + eventStateChange;
	CustomPlayer.ERROR = eventPrefix + eventError;
	CustomPlayer.TRASH = eventPrefix + eventTrash;
	
	$.widget('youtube9loop.customPlayer', {
		// Default options
		options: {
			prefix: '',
			playerVars:{
				autoplay: 1, // off for testing ... repeat music so annoying
				loop: 1,
				autohide: 0,
				showinfo: 0,
				rel: 0,
				controls: 0
			},
			hideYoutubePlayer: true,
			height: '315',
			width: '560',
			videoId: '',
			origin: null
		},
		data: {
                        playerInit: false,
			sliderInit: false,
			sliderTimer: null,
			currentTimeTimer: null
		},
		_create: function() {
			this._renderElement();
			this._initYoutubePlayer();
		},
		_renderElement: function() {
			// youtube wrapper
			var $youtubeWrapper = $('<div />');
			$youtubeWrapper.addClass('video-wrapper');
			// player
			var $player = $('<div />');
			$player.attr('id', this.options.prefix + 'player');
			// video item
			var $videoItem = $('<div />');
			$videoItem.addClass('video-item');
			$videoItem.addClass('current-video');
			// title
			var $title = $('<div />');
			$title.addClass('title');
			// meta data container
			var $metaDataContainer = $('<div />');
			$metaDataContainer.addClass('meta-data-container');
			// button container
			var $buttonContainer = $('<div />');
			$buttonContainer.addClass('button-container');
			// trash-button
			var $trashButton = $('<div />');
			$trashButton.addClass('button');
			$trashButton.addClass('trash-button');
			// video button
			var $videoButton = $('<div />');
			$videoButton.addClass('button');
			$videoButton.addClass('video-button');
			// play/pause button
			var $playButton = $('<div />');
			$playButton.addClass('button');
			$playButton.addClass('play-pause-button');
			$playButton.addClass('play-button');
			// clearfix
			var $clearfix = $('<div />');
			$clearfix.addClass('clearfix');
			// current time container
			var $currentTimeContainer = $('<div />');
			$currentTimeContainer.addClass('current-time-container');
			// current time
			var $currentTime = $('<span />');
			$currentTime.addClass('current-time');
			// slash
			var $slash = $('<span />');
			$slash.text('/');
			// duration
			var $duration = $('<span />');
			$duration.addClass('duration');
			// clearfix
			var $clearfix2 = $('<div />');
			$clearfix2.addClass('clearfix');
			// custom slider
			var $customSlider = $('<div />');
			$customSlider.addClass('custom-slider');
			var $customSliderProgressBar = $('<div />');
			$customSliderProgressBar.addClass('slider-color-bar');
			
			// button-container
			$buttonContainer.append($trashButton);
			$buttonContainer.append($videoButton);
			$buttonContainer.append($playButton);
			// current time container
			$currentTimeContainer.append($currentTime);
			$currentTimeContainer.append($slash);
			$currentTimeContainer.append($duration);
			// meta data container
			$metaDataContainer.append($buttonContainer);
			$metaDataContainer.append($clearfix);
			$metaDataContainer.append($currentTimeContainer);
			// video-item
			$videoItem.append($metaDataContainer);
			$videoItem.append($title);
			$videoItem.append($clearfix2);
			$videoItem.append($customSlider);
			$videoItem.append($customSliderProgressBar);
			// youtube wrapper
			$youtubeWrapper.append($player);
			
			// append to element
			this.element.append($youtubeWrapper);
			this.element.append($videoItem);
			this.element.addClass('youtube-container');
			
			// store the slider reference
			this.data.slider = $customSlider;
			this.data.playButton = $playButton;
			this.data.title = $title;
			this.data.currentTime = $currentTime;
			this.data.duration = $duration;
			this.data.sliderProgressBar = $customSliderProgressBar;
			
			// hide the youtube player according to options
			if (this.options.hideYoutubePlayer) {
				$youtubeWrapper.hide();
			}
		},
		_syncTitle: function() {
			var self = this;
			youtubeUtil.getTitlesWithRetry(this.options.videoId, function(title){
				self.data.title.text(title);
			});
		},
		_syncTimeLabel: function(player) {
			this._displayCurrentTime(player.getCurrentTime());
		},
		_syncButton: function(player) {
			// play button
			var $playButton = $('.play-pause-button', this.element);
			if (player.getPlayerState() === YT.PlayerState.PLAYING) {
				$playButton.removeClass('play-button');
				$playButton.addClass('pause-button');
			} else {
				$playButton.removeClass('pause-button');
				$playButton.addClass('play-button');
			}
		},
		_syncSlide: function(player) {
			this.data.slider.slider('value', Math.round(player.getCurrentTime() * 10));
			this.data.slider.slider('option', 'max', player.getDuration() * 10);
			var percent = 100 * player.getCurrentTime() / player.getDuration();
			this.data.sliderProgressBar.css('width', percent+'%');
		},
		_initTitle: function() {
			var self = this;
			var player = this.data.player;
			player.addEventListener('onStateChange', function(state){
				// update duration
				self._syncTitle();
			});
		},
		_initTimeLabel: function() {
			var self = this;
			var player = this.data.player;
			player.addEventListener('onStateChange', function(state){
				// update duration
				var duration = player.getDuration();
				var min = Math.floor(duration/60);
				var sec = Math.floor(duration % 60);
				if (isNaN(min) || isNaN(sec)) {
					// display 0:00 when nan
					self.data.duration.text('0:00');
				} else {
					self.data.duration.text(min + ':' + (sec<10?'0'+sec:sec));
				}
				// trigger current time timer
				if (state.data === YT.PlayerState.PLAYING) {
					if (self.data.currentTimeTimer === null) {
						self.data.currentTimeTimer = setInterval(function(){
							self._syncTimeLabel(player);
						}, updateInterval);
					}
				} else {
					if (self.data.currentTimeTimer !== null) {
						clearInterval(self.data.currentTimeTimer);
						self.data.currentTimeTimer = null;
					}
				}
			});
		},
		_initButton: function() {
			var self = this;
			var player = this.data.player;
			// play button
			$('.play-pause-button', this.element).on('click', function(){
				self.playOrPause();
			});
			player.addEventListener('onStateChange', function(){
				self._syncButton(player);
			});
			// trash button
			$('.trash-button', this.element).on('click', function(){
				self._trigger(eventTrash);
			});
			// video button
			$('.video-button', this.element).on('click', function(){
				self.hideYoutubePlayer(!self.options.hideYoutubePlayer);
			});
		},
		_initSlider: function() {
			var player = this.data.player;
			var self = this;
			if (!this.data.sliderInit) {
				this.data.slider.slider({
					create: function(event) {
						self.data.sliderTimer = setInterval(function(){
							self._syncSlide(player);
						}, updateInterval);
						player.addEventListener('onStateChange', function(state){
							if (state.data === YT.PlayerState.PLAYING) {
								if (self.data.sliderTimer === null) {
									self.data.sliderTimer = setInterval(function(){
										self._syncSlide(player);
									}, updateInterval);
								}
							} else {
								if (self.data.sliderTimer !== null) {
									clearInterval(self.data.sliderTimer);
									self.data.sliderTimer = null;
								}
							}
						});
					},
					slide: function(event, ui) {
						// stop the timer
						clearInterval(self.data.sliderTimer);
						self.data.sliderTimer = null;
						// stop current time timer
						clearInterval(self.data.currentTimeTimer);
						self.data.currentTimeTimer = null;
						// stop the player
						player.pauseVideo();
						// current time ratio
						var currentTimeRatio = 
								ui.value / $(event.target).slider('option', 'max');
						// update color bar
						self.data.sliderProgressBar.css('width', 
								100* currentTimeRatio + '%');
						// update current time
						self._displayCurrentTime(player.getDuration() * currentTimeRatio);
					},
					change: function(event, ui) {
						if (self.data.sliderTimer === null) {
							player.seekTo((ui.value/10), true);
							player.playVideo();
						}
					}
				});
				this.data.sliderInit = true;
			}
		},
		_initYoutubePlayer: function() {
			var self = this;
			if (this.options.origin !== null) {
				this.options.playerVars.origin = this.options.origin;
			}
			this.data.player = new YT.Player(this.options.prefix+'player', {
				height: this.options.height,
				width: this.options.width,
				videoId: this.options.videoId,
				playerVars: this.options.playerVars,
				events: {
					onStateChange: function(event){
						self._onPlayerStateChange(event);
					},
					onError: function(event) {
						self._onPlayerError(event);
					},
					onReady: function(event){
						event.target.customPlayer = self;
                                                self.data.playerInit = true;
						// init all the remaining components
						self._initSlider();
						self._initButton();
						self._initTimeLabel();
						self._initTitle();
					}
				}
			});
		},
		_onPlayerError: function(event) {
			var self = this;
			// reset the ui
			self._syncTimeLabel(self.data.player);
			self._syncButton(self.data.player);
			// trigger error event
			this._trigger(eventError, event, {
				videoId: self.options.videoId,
				code: event.data
			});
		},
		_onPlayerStateChange: function(event) {
			var self = this;
			this._trigger(eventStateChange, event, {
				videoId: self.options.videoId,
				state: event.data
			});
		},
		_displayCurrentTime: function(currentTime) {
			var min = Math.floor(currentTime/60);
			var sec = Math.floor(currentTime % 60);
			if (isNaN(min) || isNaN(sec)) {
				// display 0:00 when NaN
				this.data.currentTime.text('0:00');
			} else {
				this.data.currentTime.text(min + ':' + (sec<10?'0'+sec:sec));
			}
		},
		play: function(videoId) {
			if (videoId) {
				this.options.videoId = videoId;
				this.data.player.loadVideoById({'videoId': videoId});
			} else {
				this.data.player.playVideo();
			}
		},
		hideYoutubePlayer: function(hideYoutubePlayer) {
			if (hideYoutubePlayer) {
				$('.video-wrapper', this.element).finish().slideUp(animationDuration);
			} else {
				$('.video-wrapper', this.element).finish().slideDown(animationDuration);
			}
			this.options.hideYoutubePlayer = hideYoutubePlayer;
		},
		videoId: function() {
			return this.options.videoId;
		},
		playOrPause: function() {
			var player = this.data.player;
			if (player.getPlayerState() === YT.PlayerState.PLAYING) {
				player.pauseVideo();
			} else {
				player.playVideo();
			}
		},
		playerState: function() {
			return this.data.playerInit?this.data.player.getPlayerState():null;
		}
	});
	
  function YoutubeUtil(){
    var titleCache = {};
    var youtubeVideoApiUri = 'https://www.googleapis.com/youtube/v3/videos';
    
    /**
     * Get the video titles with auto re-trials
     * @param {(string|string[])} videoIds
     * @param {getTitlesCallback} callback
     * @param {*} callbackContext
     * @returns {void}
     */
    this.getTitlesWithRetry = function(videoIds, callback, callbackContext) {
      var self = this;
      // for error re-trial
      var interval = 500;
      var growthRate = 2;
      var trial = 0;
      var maxTrial = 5;
      function titleErrorCallback() {
        trial++;
        if (trial < maxTrial) {
          setTimeout(function () {
            self.getTitles(videoIds, callback, titleErrorCallback);
          }, trial * growthRate * interval);
        }
      }
      self.getTitles(videoIds, callback, titleErrorCallback);
    };

    /**
     * `getTitlesCallback` which will be called when ajaxTitle succeeded.
     *
     * @callback getTitlesCallback
     * @param {(string|Array)} result the result title if the input is single id or associative array of id to title if the input is an id array
     * @param {*} callbackContext
     */

    /**
     * `getTitlesErrorCallback` which will be called when ajaxTitle throws Error.
     *
     * @callback getTitlesErrorCallback
     * @param {*} callbackContext
     */

    /**
     * Get the Video Titles.
     * Retrieve the titles from cache if available; or ajax it otherwise
     * Will async call the callback after loading (even if it is available from cache)
     * 
     * @param {(string|string[])} videoIds
     * @param {getTitlesCallback} callback
     * @param {getTitlesErrorCallback} errorCallback
     * @param {*} callbackContext
     * @returns {void}
     */
    this.getTitles = function(videoIds, callback, errorCallback,
        callbackContext) {
      // try to retrieve all videoIds from cache
      var ajaxArray = [];
      var inputArray = ($.isArray(videoIds)) ? videoIds.slice() : [videoIds];
      var resultObject = {};
      for (var i=0; i<inputArray.length; ++i) {
        var vid = inputArray[i];
        if (titleCache[vid]) {
          resultObject[vid] = titleCache[vid];
        } else {
          ajaxArray.push(vid);
        }
      }
      // the result callback
      function resultCallback () {
        if (callback) {
          if ($.isArray(videoIds)) {
            callback(resultObject, callbackContext);
          } else {
            callback(resultObject[videoIds], callbackContext);
          }  
        }
      }

      // call ajax if some titles are not available in cache
      if (ajaxArray.length > 0) {

        function ajaxSuccessCallback(ajaxResult) {
          // merge ajaxArray to cache
          $.extend(titleCache, ajaxResult);
          // merge ajaxArray to result array
          $.extend(resultObject, ajaxResult);
          resultCallback();
        }

        function ajaxErrorCallback() {
          if (errorCallback) {
            errorCallback(callbackContext);
          }
        }
        ajaxTitles(ajaxArray, ajaxSuccessCallback, ajaxErrorCallback);
      } else {
        setTimeout(resultCallback, 1);
      }
    };          

    /**
     * `ajaxCallback` which will be called when ajaxTitle succeeded.
     *
     * @callback ajaxCallback
     * @param {Array} result The associative array of id to title
     */

    /**
     * 
     * @param {(string|string[])} videoIds the youtube video ID or the list of ID for title retrieval
     * @param {ajaxCallback} callback the callback function which will be called when ajax success
     * @param {*} errorCallback jquery error callback; please refer to jquery api
     * @returns {void}
     */
    function ajaxTitles(videoIds, callback, errorCallback) {
      // prepare the ajax data
      var ajaxData = {
        part: 'snippet',
        key: youtubeApiKey,
        id: videoIds.join()
      };
      // perform the ajax call
      $.ajax(youtubeVideoApiUri, {
        data: ajaxData,
        dataType: 'json',
        success: function (data) {
          // prepare the result object
          var result = [];
          if (data && data.items) {
            for (var i = 0; i < data.items.length; ++i) {
              var item = data.items[i];
              result[item.id] = item.snippet.title;
            }
          }
          if (callback) {
            callback(result);
          }
        },
        error: errorCallback
      });
    }
  }

  window.youtubeUtil = new YoutubeUtil();
	
})(jQuery);
