/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function($){
	
	window.statUtil = (function(){
		var statUtilInstance;
		var createStatUtil = function() {
			var statUrl = '/watch/stat';
			var idCache = {};
			// send stat data to server when the first play of the video
			var stat = function(videoId) {
				if (!idCache[videoId]) {
					idCache[videoId] = true;
					$.ajax(statUrl, {
						type: 'POST',
						data: {
							id: videoId
						}
					});
				}
			};
			return {
				stat: stat
			};
		};
		return {
			getInstance: function() {
				if (!statUtilInstance) {
					statUtilInstance = createStatUtil();
				}
				return statUtilInstance;
			}
		};
	})();
})(jQuery);