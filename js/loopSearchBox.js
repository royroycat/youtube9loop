/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function($){
    
    $.widget('youtube9loop.loopSearchBox', {
	// Default options
	options: {
	    prefix: '',
	    maxResult: 20,
	    targetElem: null
	},
	data: {
	    prevPageToken: null,
	    nextPageToken: null,
	    items: [],
	    q: ''
	},
	_create: function() {
	    // create input box and search button
	    var $input = $('<input />');
	    $input.attr('id', this.options.prefix + 'LoopSearchInput');
	    $input.addClass('loopSearchInput');
	    var $button = $('<div />');
	    $button.attr('id', this.options.prefix + 'LoopSearchButton');
	    $button.addClass('loopSearchButton');
	    $button.append('<div>SEARCH</div>');
	    this.element.append($input);
	    this.element.append($button);
	    
	    this._initAutocomplete($input);
	    this._initSubmit();
	},
	_initAutocomplete: function($input) {
	    var self = this;
		// input autocomplete
	    $input.autocomplete({
		source: function (request, response) {
		    $.ajax("//suggestqueries.google.com/complete/search", {
			data: {
			    client: 'youtube',
			    q: request.term,
			    ds: 'yt',
			    hl: 'en',
			    gl: 'hk',
			    hjson: 't',
			    cp: 1,
			    format: 5,
			    alt: 'json'
			},
			dataType: 'jsonp',
			success: function(data, textStatus, request) { 
			   response( $.map( data[1], function(item) {
				return {
				    label: item[0],
				    value: item[0]
				};
			    }));
			}
		    });
		},
	    select: function( event, ui ) {
			var $input = $('.loopSearchInput', this.element);
			$input.val(ui.item.value);	
            self.search(); 
		}
		});
	},
	_initSubmit: function() {
	    var self = this;
	    var $input = $('.loopSearchInput', this.element);
	    var $button = $('.loopSearchButton', this.element);
	    
		var search = function(){
			var q = $input.val();	
			$.ajax("https://www.googleapis.com/youtube/v3/search", {
			   data: {
				   part: 'id,snippet',
				   q: q,
				   type: 'video',
				   key: 'AIzaSyDBvCnEhyXWHSePt9dIRO62WCd7qY1l-EQ',
				   maxResults: self.options.maxResult
			   },
			   dataType: 'json',
			   success: function(data, textStatus, request) {
				   self.data.prevPageToken = data.prevPageToken?data.prevPageToken:null;
				   self.data.nextPageToken = data.nextPageToken?data.nextPageToken:null;
				   self.data.items = data.items;
				   self.data.q = q;
				   self._renderResult();
			   }
			});
		};
		
		self.search = search;
		self.button = $button;
	    $button.on('click', search);
	    $input.on('keypress', function(event){
		var code = event.keyCode || event.which;
		if(code === 13) { //Enter keycode
		    search(self.q);
		    $input.blur();
		}
	    });
	},
	bindSearch:function() {
		this.button.on('click', this.search);
	},
	_renderThumbnail: function(item) {
		var id = item.id.videoId;
		var thumbnailUrl = item.snippet.thumbnails.default.url;
		var title = item.snippet.title;
		var length = 32;
		var title_short = (title.length > 32)?title.substring(0, length) + "...":title;
		var listItem = $('<li  data-id="'+id+'"/>');
		var anchor = $('<a class="video-thumbnail" />');
		anchor.append('<img src="'+thumbnailUrl+'" alt="'+title+'" />');
		anchor.append('<div class="thumbnail-overlay"><img src="img/video-thumbnail-cover.png"/></div>');
		anchor.append('<span>'+title_short+'</span>');
		anchor.attr('href', '/watch?v='+id);
		listItem.append(anchor);
		return listItem;
	},
	_gotoPage: function(pageToken) {
	    var self = this;
	    
	    self.slider.hide(); // hide when loading
	    $.ajax("https://www.googleapis.com/youtube/v3/search", {
			data: {
			    part: 'id,snippet',
			    q: self.data.q,
			    type: 'video',
			    key: 'AIzaSyDBvCnEhyXWHSePt9dIRO62WCd7qY1l-EQ',
			    maxResults: self.options.maxResult,
			    pageToken: pageToken
			},
			dataType: 'json',
			success: function(data, textStatus, request) {
			    console.log(data);
			    self.data.prevPageToken = data.prevPageToken?data.prevPageToken:null;
			    self.data.nextPageToken = data.nextPageToken?data.nextPageToken:null;
			    self.data.items = data.items;
			    //self._renderResult();
				//append the result then refresh the slider and go back to that page
				for (var i=0; i<self.data.items.length; ++i) {
					self.options.targetElem.find("ul").first().append(self._renderThumbnail(self.data.items[i]));
				}
				var currentSlide = self.slider.getCurrentSlide();
				self.slider.reloadSlider({
				    startSlide: currentSlide,
					minSlides: 3,
					maxSlides: 8,
					slideWidth: 120,
					slideMargin: 10,
					pager: false ,
					infiniteLoop: false,
					onSlideNext: function($ele, oldIndex, newIndex) {
						var current = self.slider.getCurrentSlide();
						var slideCount = self.slider.getPagerQty()-1;
						if (current == slideCount && self.data.nextPageToken) {
							// ajax next 20 please
							self._gotoPage(self.data.nextPageToken);
						}
					}
				});
			}
	     });
	},
	_renderResult: function() {
			var self = this;
			var targetElem = this.options.targetElem;
			targetElem.empty();
			var itemList = $('<ul />');
			for(var i = 0; i < this.data.items.length; ++i) {
				var item = self._renderThumbnail(this.data.items[i]);
				itemList.append(item);
			}
			targetElem.append(itemList);
			// trigger event
			targetElem.trigger("render_complete");
			
			var slider = targetElem.find("ul").first().bxSlider({
				minSlides: 3,
				maxSlides: 8,
				slideWidth: 120,
				slideMargin: 10,
				pager: false ,
				infiniteLoop: false,
				onSlideNext: function($ele, oldIndex, newIndex) {
					var current = slider.getCurrentSlide();
					var slideCount = slider.getPagerQty()-1;
					if (current == slideCount && self.data.nextPageToken) {
						// ajax next 20 please
						self._gotoPage(self.data.nextPageToken);
					}
				}
			});
			self.slider = slider;
	}
	
    });
    
})(jQuery);