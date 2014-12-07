/**
    A jQuery version of window.resizeStop.
    https://github.com/misteroneill/resize-stop
*/

;(function($,c){var d=$(window),cache=$([]),last=0,timer=0,size={};function onWindowResize(){last=$.now();timer=timer||c(checkTime,10)}function checkTime(){var a=$.now();if(a-last<$.resizestop.threshold){timer=c(checkTime,10)}else{clearTimeout(timer);timer=last=0;size.width=d.width();size.height=d.height();cache.trigger('resizestop')}}$.resizestop={propagate:false,threshold:500};$.event.special.resizestop={setup:function(a,b){cache=cache.not(this);cache=cache.add(this);if(cache.length===1){d.bind('resize',onWindowResize)}},teardown:function(a){cache=cache.not(this);if(!cache.length){d.unbind('resize',onWindowResize)}},add:function(a){var b=a.handler;a.handler=function(e){if(!$.resizestop.propagate){e.stopPropagation()}e.data=e.data||{};e.data.size=e.data.size||{};$.extend(e.data.size,size);return b.apply(this,arguments)}}}})(jQuery,setTimeout);